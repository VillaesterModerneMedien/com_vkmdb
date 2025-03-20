<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_vkmdb
 *
 * @copyright   Copyright (C) 2025 Mario Hewera. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
namespace VkmdbNamespace\Component\Vkmdb\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Factory;

/**
 * Vkmdb Component Controller.
 *
 * @since  1.0.0
 */
class DisplayController extends BaseController
{
/**
	 * The default view.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $default_view = 'items';
    
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
	 * @return  static  This object to support chaining.
	 *
	 * @since   1.0.0
	 */
	public function display($cachable = false, $urlparams = [])
	{
        $safeurlparams = array(
            'catid' => 'INT', 
            'id' => 'INT', 
            'cid' => 'ARRAY', 
			'limit' => 'UINT', 
            'limitstart' => 'UINT', 
            'return' => 'BASE64', 
            'filter' => 'STRING',
			'filter_order' => 'CMD', 
            'filter_order_Dir' => 'CMD', 
            'filter-search' => 'STRING'
            );
            
		parent::display($cachable, $safeurlparams);

		if (Factory::getApplication()->getIdentity()->get('id')) {
			$cachable = false;
		}

		return $this;
	}
}
