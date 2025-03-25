<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_vkmdb
 *
 * @copyright   Copyright (C) 2025 Mario Hewera. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace VkmdbNamespace\Component\Vkmdb\Administrator\View\Contacts;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 *  * View class for a list of contacts.
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * An array of items
	 *
	 * @var  array
	 */
	protected $items;

	/**
	 * The pagination object
	 *
	 * @var  \Joomla\CMS\Pagination\Pagination
	 */
	protected $pagination;

	/**
	 * The model state
	 *
	 * @var  \Joomla\CMS\Object\CMSObject
	 */
	protected $state;

	/**
	 * Form object for search filters
	 *
	 * @var  \Joomla\CMS\Form\Form
	 */
	public $filterForm;

	/**
	 * The active search filters
	 *
	 * @var  array
	 */
	public $activeFilters;

	/**
	 * Is this view an Empty State
	 *
	 * @var   boolean
	 *
	 * @since 1.0.0
	 */
	private $isEmptyState = false;
	
	/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  A template file to load. [optional]
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
    public function display($tpl = null): void
    {
        $this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->state         = $this->get('State');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters'); 
        
        if (!\count($this->items) && $this->isEmptyState = $this->get('IsEmptyState'))
        {
			$this->setLayout('emptystate');
		}
        
        // We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			$this->addToolbar();
        }

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		$user = Factory::getApplication()->getIdentity();
        $canDo = ContentHelper::getActions('com_vkmdb', 'category', $this->state->get('filter.category_id'));
        
		ToolbarHelper::title(Text::_('COM_VKMDB_HEADLINE_CONTACTS'), 'list com_vkmdb');
        
		// Get the toolbar object instance
		$toolbar = Toolbar::getInstance('toolbar');
        if ($canDo->get('core.create') || \count($user->getAuthorisedCategories('com_vkmdb', 'core.create')) > 0)
		{
			$toolbar->addNew('contact.add');
		}
        
        if (!$this->isEmptyState && $canDo->get('core.edit.state'))
		{
            $dropdown = $toolbar->dropdownButton('status-group')
				->text('JTOOLBAR_CHANGE_STATUS')
				->toggleSplit(false)
				->icon('icon-ellipsis-h')
				->buttonClass('btn btn-action')
				->listCheck(true);

			$childBar = $dropdown->getChildToolbar();

			$childBar->publish('contacts.publish')->listCheck(true);

			$childBar->unpublish('contacts.unpublish')->listCheck(true);
            
            $childBar->archive('contacts.archive')->listCheck(true);
            
            if ($this->state->get('filter.published') != -2)
			{
				$childBar->trash('contacts.trash')->listCheck(true);
			}
        }
        
        if (!$this->isEmptyState && $this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
		{
			$toolbar->delete('contacts.delete')
				->text('JTOOLBAR_EMPTY_TRASH')
				->message('JGLOBAL_CONFIRM_DELETE')
				->listCheck(true);
		}
		
		if ($user->authorise('core.admin', 'com_vkmdb') || $user->authorise('core.options', 'com_vkmdb'))
		{
			$toolbar->preferences('com_vkmdb');
		}
		
		
	}
}