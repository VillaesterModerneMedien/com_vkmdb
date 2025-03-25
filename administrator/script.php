<?php
/**
 * @package     Joomla.Administrator 
 * @subpackage  com_vkmdb
 *
 * @copyright   Copyright (C) 2025 Mario Hewera. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScript;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Helper\ModuleHelper;

/**
 * Script file of vkmdb component.
 *
 * The name of this class is dependent on the component being installed.
 * The class name should have the component's name, directly followed by
 * the text InstallerScript (ex:. com_vkmdbInstallerScript).
 *
 * This class will be called by Joomla!'s installer, if specified in your component's
 * manifest file, and is used for custom automation actions in its installation process.
 *
 * In order to use this automation script, you should reference it in your component's
 * manifest file as follows:
 * <scriptfile>script.php</scriptfile>
 */
class Com_VkmdbInstallerScript extends InstallerScript
{
	/**
	 * Minimum Joomla version to check
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	private $minimumJoomlaVersion = '4.0';
    
    /**
	 * Minimum PHP version to check
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	private $minimumPHPVersion = JOOMLA_MINIMUM_PHP;
    
	/**
	 * Method to install the extension
     * This method is called after a component is installed.
	 *
	 * @param   InstallerAdapter  $parent  The class calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since  1.0.0
	 */
	public function install($parent): bool
	{
		$this->addDashboardMenu('vkmdb', 'vkmdb');
        
        return true;
	}

	/**
	 * Method to uninstall the extension
     * This method is called after a component is uninstalled.
	 *
	 * @param   InstallerAdapter  $parent  The class calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since  1.0.0
	 */
	public function uninstall($parent): bool
	{
		// delete dashboard modules
        $db      = Factory::getContainer()->get('DatabaseDriver');
		$query   = $db->getQuery(true);
        $query->delete($db->quoteName('#__modules'));
        $query->where($db->quoteName('position') . ' = ' . $db->quote('cpanel-vkmdb'));
        $db->setQuery($query);
        $db->execute();
		echo Text::_('COM_VKMDB_INSTALLERSCRIPT_UNINSTALL');
        return true;
	}

	/**

	 * Method to update the extension
     * This method is called after a component is updated.
	 *
	 * @param   InstallerAdapter  $parent  The class calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since  1.0.0
	 *
	 */
	public function update($parent): bool 
	{
		echo Text::_('COM_VKMDB_INSTALLERSCRIPT_UPDATE');
        return true;
	}

	/**
	 * Function called before extension installation/update/removal procedure commences
	 *
	 * @param   string            $type    The type of change (install, update or discover_install, not uninstall)
	 * @param   InstallerAdapter  $parent  The class calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since  1.0.0
	 *
	 * @throws Exception
	 */
	public function preflight($type, $parent): bool
	{
        if ($type !== 'uninstall')
        {
			// Check for the minimum PHP version before continuing
			if (!empty($this->minimumPHPVersion) && version_compare(PHP_VERSION, $this->minimumPHPVersion, '<'))
            {
				Log::add(
					Text::sprintf('JLIB_INSTALLER_MINIMUM_PHP', $this->minimumPHPVersion),
					Log::WARNING,
					'jerror'
				);

				return false;
			}

			// Check for the minimum Joomla version before continuing
			if (!empty($this->minimumJoomlaVersion) && version_compare(JVERSION, $this->minimumJoomlaVersion, '<'))
            {
				Log::add(
					Text::sprintf('JLIB_INSTALLER_MINIMUM_JOOMLA', $this->minimumJoomlaVersion),
					Log::WARNING,
					'jerror'
				);
				return false;
			}
		}

        echo Text::_('COM_VKMDB_INSTALLERSCRIPT_PREFLIGHT');
		return true;
	}

	/**
	 * Function called after extension installation/update/removal procedure commences
	 *
	 * @param   string            $type    The type of change (install, update or discover_install, not uninstall)
	 * @param   InstallerAdapter  $parent  The class calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since  1.0.0
	 *
	 */
	public function postflight($type, $parent)
	{
		echo Text::_('COM_VKMDB_INSTALLERSCRIPT_POSTFLIGHT');
	
        // add categories
		$this->addCategory();
        
		// add content types
		//$this->saveContentTypes();
        return true;
	}
    
	
    /**
     *
     */
    public function addCategory()
	{
		// Initialize a new category
		/** @type  Joomla\CMS\Table\Category $category */
		$category = Table::getInstance('Category', 'Joomla\\CMS\\Table\\');

		// Check if the Uncategorised category exists before adding it
		if (!$category->load(array('extension' => 'com_vkmdb', 'title' => 'Uncategorised')))
		{
			$category->extension        = 'com_vkmdb';
			$category->title            = 'Uncategorised';
			$category->description      = '';
			$category->published        = 1;
			$category->access           = 1;
			$category->params           = '{"category_layout":"","image":""}';
			$category->metadata         = '{"author":"","robots":""}';
			$category->metadesc         = '';
			$category->metakey          = '';
			$category->language         = '*';
			$category->checked_out_time = null;
			$category->version          = 1;
			$category->hits             = 0;
			$category->modified_user_id = 0;
			$category->checked_out      = null;

			// Set the location in the tree
			$category->setLocation(1, 'last-child');

			// Check to make sure our data is valid
			if (!$category->check())
			{
				Factory::getApplication()->enqueueMessage(Text::sprintf('COM_VKMDB_ERROR_INSTALL_CATEGORY', $category->getError()));

				return;
			}

			// Now store the category
			if (!$category->store(true))
			{
				Factory::getApplication()->enqueueMessage(Text::sprintf('COM_VKMDB_ERROR_INSTALL_CATEGORY', $category->getError()));

				return;
			}

			// Build the path for our category
			$category->rebuildPath($category->id);
		}
	}
    
	/**
	 * Adding content_type for tags.
	 *
	 * @return  integer|boolean  One Administrator ID.
	 *
	 * @since   1.0.0
	 */
	private function saveContentTypes()
	{
		$table = Table::getInstance('Contenttype', 'JTable');

		$table->load(['type_alias' => 'com_vkmdb.contacts']);

		$tablestring = '{
			"special": {
			  "dbtable": "#__vkmdb_contacts",
			  "key": "id",
			  "type": "ContactTable",
			  "prefix": "VkmdbNamespace\\\\Component\\\\Vkmdb\\\\Administrator\\\\Table\\\\",
			  "config": "array()"
			},
			"common": {
			  "dbtable": "#__ucm_content",
			  "key": "ucm_id",
			  "type": "Corecontent",
			  "prefix": "JTable",
			  "config": "array()"
			}
		  }';

		$fieldmapping = '{
			"common": {
			  "core_content_item_id": "id",
			  "core_title": "name",
			  "core_state": "published",
			  "core_alias": "alias",
			  "core_publish_up": "publish_up",
			  "core_publish_down": "publish_down",
			  "core_access": "access",
			  "core_params": "params",
			  "core_featured": "featured",
			  "core_language": "language",
			  "core_ordering": "ordering",
			  "core_catid": "catid",
			  "asset_id": "null"
			},
			"special": {
			}
		  }';

		$contenttype = [];
		$contenttype['type_id'] = ($table->type_id) ? $table->type_id : 0;
		$contenttype['type_title'] = 'Contacts';
		$contenttype['type_alias'] = 'com_vkmdb.contact';
		$contenttype['table'] = $tablestring;
		$contenttype['rules'] = '';
		$contenttype['router'] = 'RouteHelper::getContactRoute';
		$contenttype['field_mappings'] = $fieldmapping;
		$contenttype['content_history_options'] = '';

		$table->save($contenttype);

		return;
	}
}
