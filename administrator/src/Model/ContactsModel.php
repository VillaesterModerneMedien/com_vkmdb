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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Factory;
use Joomla\Database\ParameterType;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Table\Table;
use VkmdbNamespace\Component\Vkmdb\Administrator\Helper\VkmdbHelper;


/**
 * Methods supporting a list of contacts records.
 *
 * @since  1.0.0
 */
class ContactsModel extends ListModel
{	
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.0.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
                'published', 'a.published',
                'created', 'a.created',
                'created_by', 'a.created_by',
				'catid', 'a.catid', 'category_id', 'category_title',
				'level', 'c.level',
			);
		}

		parent::__construct($config);
	}
	

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A database object
	 *
	 * @since   1.0.0
	 */
	public function getTable($type = 'Contact', $prefix = 'Administrator', $config = array())
	{
		return parent::getTable($type, $prefix, $config);
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getItems()
	{

		try {
			$items = VkmdbHelper::ninoxApi('contacts', 'GET', []);
			foreach ($items as $item){
				$item = VkmdbHelper::prepareNinoxData($item);
			}
		} catch (\RuntimeException $e) {
			$this->setError($e->getMessage());

			return false;
		}

		return $items;
	}


    
}
