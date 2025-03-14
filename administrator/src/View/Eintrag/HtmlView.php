<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_vkmdb
 *
 * @copyright   Copyright (C) 2025 Mario Hewera. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace VkmdbNamespace\Component\Vkmdb\Administrator\View\Eintrag;
 
\defined('_JEXEC') or die;

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View to edit a Eintrag.
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * The \JForm object
	 *
	 * @var  \JForm
	 */
	protected $form;


	/**
	 * The active item
	 *
	 * @var  object
	 */
	protected $item;

    /**
     * The model state
     *
     * @var  object
     */
    protected $state;
	
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{
		$this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $this->state = $this->get('State');
		
		$this->addToolbar();

		return parent::display($tpl);
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
		// disable Joomla main menue
		Factory::getApplication()->input->set('hidemainmenu', true);
		
		$user = Factory::getApplication()->getIdentity();
		$canDo = ContentHelper::getActions('com_vkmdb');
		
		$isNew = ($this->item->id == 0);
		
		if ($isNew)
		{
			ToolbarHelper::title(Text::_('COM_VKMDB_MANAGER_EINTRAG_NEW'), 'home com_vkmdb');
		}
		else
		{
			ToolbarHelper::title(Text::_('COM_VKMDB_MANAGER_EINTRAG_EDIT'), 'home com_vkmdb');
		}
		
		$toolbarButtons = [];

		// If a new eintrag, can save the eintrag.  Allow users with edit permissions to apply changes to prevent returning to grid.
		if ($isNew && $canDo->get('core.create'))
		{
			if ($canDo->get('core.edit'))
			{
				ToolbarHelper::apply('eintrag.apply');
			}

			$toolbarButtons[] = ['save', 'eintrag.save'];
		}

		// If not checked out, can save the eintrag.
		if (!$isNew && $canDo->get('core.edit'))
		{
			ToolbarHelper::apply('eintrag.apply');

			$toolbarButtons[] = ['save', 'eintrag.save'];
		}

		// If the user can create new eintraege, allow them to see Save & New
		if ($canDo->get('core.create'))
		{
			$toolbarButtons[] = ['save2new', 'eintrag.save2new'];
		}

		// If an existing eintrag, can save to a copy only if we have create rights.
		if (!$isNew && $canDo->get('core.create'))
		{
			$toolbarButtons[] = ['save2copy', 'eintrag.save2copy'];
		}

		ToolbarHelper::saveGroup(
			$toolbarButtons,
			'btn-success'
		);

		if (empty($this->item->id))
		{
			ToolbarHelper::cancel('eintrag.cancel');
		}
		else
		{
			ToolbarHelper::cancel('eintrag.cancel', 'JTOOLBAR_CLOSE');
		}
		
		ToolbarHelper::divider();
        
        if (version_compare(JVERSION, 4.2, '>='))
		{
            // inline help button
            $inlinehelp  = (string) $this->form->getXml()->config->inlinehelp['button'] == 'show' ?: false;
            if ($inlinehelp)
            {
                ToolbarHelper::inlinehelp();
            }
        }
        
		
	}
}
