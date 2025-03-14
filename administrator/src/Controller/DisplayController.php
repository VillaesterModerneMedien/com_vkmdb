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

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Vkmdb Component Controller.
 *
 * @since  1.0.0
 */
class DisplayController extends BaseController
{
	/**
	 * The default view for the display method.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $default_view = 'eintraege'; // or default
	
    /**
	 * Constructor.
	 *
	 * @param   array                $config   An optional associative array of configuration settings.
	 * Recognized key values include 'name', 'default_task', 'model_path', and
	 * 'view_path' (this list is not meant to be comprehensive).
	 * @param   MVCFactoryInterface  $factory  The factory.
	 * @param   CMSApplication       $app      The JApplication for the dispatcher
	 * @param   \JInput              $input    Input
	 *
	 * @since   1.0.0
	 */
	public function __construct($config = [], MVCFactoryInterface $factory = null, $app = null, $input = null)
	{
		parent::__construct($config, $factory, $app, $input);
	}
    
	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link \JFilterInput::clean()}.
	 *
	 * @return  BaseController|bool  This object to support chaining.
	 *
	 * @since   1.0.0
	 *
	 * @throws  \Exception
	 */
	public function display($cachable = false, $urlparams = array())
	{
		return parent::display();
	}
	
	/**
	 * Fetch and process ajax calls
     * Delete if you don't use ajax
     *
     * @since 1.0.0
	 */
	public function ajax()
	{
		// Set whether a client disconnect should abort script execution
		ignore_user_abort(true);
		$app = Factory::getApplication();
		$input = $app->input;
		
		$action = $this->input->get('action', '', 'CMD');
		
		switch ($action)
		{
			// case 'my_action': ...
			//		$result = doSomethingImportant();
			//		if ($result !== null)
            //        {
            //            echo '{"result" : "true", "data" : "' . $result . '"}';
            //        }
            //        else {
            //            echo '{"result" : "true", "data" : "false"}';
            //        }
			//			break;
			default: echo '{"result" : "true", "data" : "false"}';
		}
		
		return;
	}
}
