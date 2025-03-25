<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_vkmdb
 *
 * @copyright   Copyright (C) 2025 Mario Hewera. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace VkmdbNamespace\Component\Vkmdb\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Input\Input;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Router\Route;

/**
 * The Contacts list controller class.
 *
 * @since  1.0.0
 */
class ContactsController extends AdminController
{	
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_VKMDB_CONTACTS';
	
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The name of the model.
	 * @param   string  $prefix  The prefix for the PHP class name.
	 * @param   array   $config  Array of configuration parameters.
	 *
	 * @return  \Joomla\CMS\MVC\Model\BaseDatabaseModel
	 *
	 * @since   1.0.0
	 */
	public function getModel($name = 'Contact', $prefix = 'Administrator', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}
    
	/**
	 * Method to delete a record.
	 *
	 * @return  void
	 */
	public function delete()
	{
		// Check for request forgeries.
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$ids    = $this->input->get('cid', array(), 'array');
		
		if (empty($ids))
		{
			JError::raiseWarning(500, Text::_('COM_VKMDB_NO_ITEM_SELECTED'));
		}
		else
		{
			// Get the model.
			$model = $this->getModel();
			
			foreach ($ids as $id)
			{
				$model->delete($id);
			}
		}

		$this->setRedirect('index.php?option=com_vkmdb&view=contacts');
	}
}
