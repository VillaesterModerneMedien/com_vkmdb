<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_vkmdb
 *
 * @copyright   Copyright (C) 2025 Mario Hewera. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace VkmdbNamespace\Component\Vkmdb\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\String\PunycodeHelper;
use Joomla\CMS\Versioning\VersionableModelTrait;
use Joomla\Component\Categories\Administrator\Helper\CategoriesHelper;
use Joomla\Database\ParameterType;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

/**
 * Item Model for a Eintrag.
 *
 * @since  1.0.0
 */
class EintragModel extends AdminModel
{
	use VersionableModelTrait;
    
	/**
	 * The type alias for this content type.
	 *
	 * @var      string
	 * @since    1.0.0
	 */
	public $typeAlias = 'com_vkmdb.eintrag';

	/**
	 * @var    string  The prefix to use with controller messages.
	 * @since  1.0.0
	 */
	protected $text_prefix = 'COM_VKMDB';
    
    /**
	 * Name of the form
	 *
	 * @var string
	 * @since  4.0.0
	 */
	protected $formName = 'eintrag';

	/**
	 * @var    string  The help screen base URL for the component.
	 * @since  1.0.0
	 */
	// protected $helpURL;
	
	/**
	 * Constructor.
	 *
	 * @param   array                 $config       An array of configuration options (name, state, dbo, table_path, ignore_request).
	 * @param   MVCFactoryInterface   $factory      The factory.
	 * @param   FormFactoryInterface  $formFactory  The form factory.
	 *
	 * @since   1.0.0
	 * @throws  \Exception
	 */
	public function __construct($config = array(), MVCFactoryInterface $factory = null, FormFactoryInterface $formFactory = null)
	{
		parent::__construct($config, $factory, $formFactory);
	}
	
	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  Table  A Table object
	 *
	 * @since   1.0.0
	 * @throws  \Exception
	 */
	public function getTable($type = 'Eintrag', $prefix = 'Administrator', $config = array())
	{
		return parent::getTable($type, $prefix, $config);
	}
	
	/**
	 * Method to get the row form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  \JForm|boolean  A \JForm object on success, false on failure
	 *
	 * @since   1.0.0
	 */
	public function getForm($data = [], $loadData = true)
	{
		// Get the form.
        $form = $this->loadForm(
            'com_vkmdb.' . $this->formName,
            $this->formName,
            array(
                'control' => 'jform',
                'load_data' => $loadData
            )
        );

        if (empty($form))
        {
            return false;
        }
        
        // Modify the form based on access controls.
		if (!$this->canEditState((object) $data))
        {
            $form->setFieldAttribute('published', 'disabled', 'true');
            
            // Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('published', 'filter', 'unset');
        }
        
        // Don't allow to change the created_by user if not allowed to access com_users.
		if (!Factory::getApplication()->getIdentity()->authorise('core.manage', 'com_users'))
		{
			$form->setFieldAttribute('created_by', 'filter', 'unset');
		}

        return $form;
	}
	
	/**
	 * Preprocess the form.
	 *
	 * @param   Form    $form   Form object.
	 * @param   object  $data   Data object.
	 * @param   string  $group  Group name.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	protected function preprocessForm(Form $form, $data, $group = 'content')
	{
    	if ($this->canCreateCategory())
		{
			$form->setFieldAttribute('catid', 'allowAdd', 'true');

			// Add a prefix for categories created on the fly.
			$form->setFieldAttribute('catid', 'customPrefix', '#new#');
		}
        parent::preprocessForm($form, $data, $group);
	}
    
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.0.0
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_vkmdb.edit.eintrag.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}
		$this->preprocessData($this->typeAlias, $data, 'eintrag');

		return $data;
	}
    
    /**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.0.0
	 */
	public function save($data)
	{
        $input = Factory::getApplication()->input;
        // Create new category, if needed.
		$createCategory = true;

		// If category ID is provided, check if it's valid.
		if (is_numeric($data['catid']) && $data['catid'])
		{
			$createCategory = !CategoriesHelper::validateCategoryId($data['catid'], 'com_vkmdb');
		}

		// Save New Category
		if ($createCategory && $this->canCreateCategory())
		{
			$category = [
				// Remove #new# prefix, if exists.
				'title'     => strpos($data['catid'], '#new#') === 0 ? substr($data['catid'], 5) : $data['catid'],
				'parent_id' => 1,
				'extension' => 'com_vkmdb',
				'language'  => $data['language'],
				'published' => 1,
			];

			/** @var \Joomla\Component\Categories\Administrator\Model\CategoryModel $categoryModel */
			$categoryModel = Factory::getApplication()->bootComponent('com_categories')
				->getMVCFactory()->createModel('Category', 'Administrator', ['ignore_request' => true]);

			// Create new category.
			if (!$categoryModel->save($category))
			{
				$this->setError($categoryModel->getError());

				return false;
			}

			// Get the Category ID.
			$data['catid'] = $categoryModel->getState('category.id');
		}
        return parent::save($data);
    }
    
    /**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   \Joomla\CMS\Table\Table  $table  The Table object
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	protected function prepareTable($table)
	{
		$date = Factory::getDate()->toSql();
        
		$table->generateAlias();
        
        if (empty($table->id))
		{
			// Set the values
			$table->created = $date;
        }
        else
		{
			// Set the values
			$table->modified = $date;
			$table->modified_by = Factory::getApplication()->getIdentity()->id;
		}
	}
    
    
    
    /**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canDelete($record)
	{
		if (empty($record->id) || $record->published != -2)
		{
			return false;
		}
        return Factory::getApplication()->getIdentity()->authorise('core.delete', 'com_vkmdb.category.' . (int) $record->catid);
 
	}

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canEditState($record)
	{
		// Check against the category.
		if (!empty($record->catid))
		{
            return Factory::getApplication()->getIdentity()->authorise('core.edit.state', 'com_vkmdb.category.' . (int) $record->catid);
		}

		// Default to component settings if category not known.
		return parent::canEditState($record);
	}
    /**
	 * Is the user allowed to create an on the fly category?
	 *
	 * @return  boolean
	 *
	 * @since   1.0.0
	 */
	private function canCreateCategory()
	{
		return Factory::getApplication()->getIdentity()->authorise('core.create', 'com_vkmdb');
	}
}
