<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_vkmdb
 *
 * @copyright   Copyright (C) 2025 Mario Hewera. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace VkmdbNamespace\Component\Vkmdb\Administrator\Service\HTML;

\defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use VkmdbNamespace\Component\Vkmdb\Site\Helper\RouteHelper;
use Joomla\Registry\Registry;

/**
 * Content Component HTML Helper
 *
 * @since  1.0.0
 */
class Icon
{
	/**
	 * The application
	 *
	 * @var    CMSApplication
	 *
	 * @since  1.0.0
	 */
	private $application;

	/**
	 * Service constructor
	 *
	 * @param   CMSApplication  $application  The application
	 *
	 * @since   1.0.0
	 */
	public function __construct(CMSApplication $application)
	{
		$this->application = $application;
	}

	/**
	 * Method to generate a link to the create item page for the given category
	 *
	 * @param   object    $category  The category information
	 * @param   Registry  $params    The item parameters
	 * @param   array     $attribs   Optional attributes for the link
	 *
	 * @return  string  The HTML markup for the create item link
	 *
	 * @since  1.0.0
	 */
	public static function create($category, $params, $attribs = array())
	{
		$uri = Uri::getInstance();

		$url = 'index.php?option=com_vkmdb&task=item.add&return=' . base64_encode($uri) . '&id=0&catid=' . $category->id;

		$text = LayoutHelper::render('joomla.content.icons.create', array('params' => $params, 'legacy' => false));

		// Add the button classes to the attribs array
		if (isset($attribs['class']))
		{
			$attribs['class'] .= ' btn btn-primary';
		}
		else
		{
			$attribs['class'] = 'btn btn-primary';
		}

		$button = HTMLHelper::_('link', Route::_($url), $text, $attribs);

		$output = '<span class="hasTooltip" title="' . HTMLHelper::_('tooltipText', 'COM_VKMDB_CREATE_ITEM') . '">' . $button . '</span>';

		return $output;
	}

	/**
	 * Display an edit icon for the item.
	 *
	 * This icon will not display in a popup window, nor if the item is trashed.
	 * Edit access checks must be performed in the calling code.
	 *
	 * @param   object    $item  The item information
	 * @param   Registry  $params   The item parameters
	 * @param   array     $attribs  Optional attributes for the link
	 * @param   boolean   $legacy   True to use legacy images, false to use icomoon based graphic
	 *
	 * @return  string   The HTML for the item edit icon.
	 *
	 * @since   1.0.0
	 */
	public static function edit($item, $params, $attribs = array(), $legacy = false)
	{
		$user = Factory::getApplication()->getIdentity();
		$uri  = Uri::getInstance();

		// Ignore if in a popup window.
		if ($params && $params->get('popup'))
		{
			return '';
		}

		// Ignore if the state is negative (trashed).
		if ($item->published < 0)
		{
			return '';
		}

		// Set the link class
		$attribs['class'] = 'dropdown-item';

		// Show checked_out icon if the item is checked out by a different user
		if (property_exists($item, 'checked_out')
			&& property_exists($item, 'checked_out_time')
			&& $item->checked_out > 0
			&& $item->checked_out != $user->get('id'))
		{
			$checkoutUser = Factory::getApplication()->getIdentity($item->checked_out);
			$date         = HTMLHelper::_('date', $item->checked_out_time);
			$tooltip      = Text::_('JLIB_HTML_CHECKED_OUT') . ' :: ' . Text::sprintf('COM_VKMDB_CHECKED_OUT_BY', $checkoutUser->name)
				. ' <br /> ' . $date;

			$text = LayoutHelper::render('joomla.content.icons.edit_lock', array('tooltip' => $tooltip, 'legacy' => $legacy));

			$output = HTMLHelper::_('link', '#', $text, $attribs);

			return $output;
		}

		if (!isset($item->slug))
		{
			$item->slug = "";
		}

		$itemUrl = RouteHelper::getItemRoute($item->slug, $item->catid, $item->language);
		$url        = $itemUrl . '&task=item.edit&id=' . $item->id . '&return=' . base64_encode($uri);

		if ($item->published == 0)
		{
			$overlib = Text::_('JUNPUBLISHED');
		}
		else
		{
			$overlib = Text::_('JPUBLISHED');
		}

		if (!isset($item->created))
		{
			$date = HTMLHelper::_('date', 'now');
		}
		else
		{
			$date = HTMLHelper::_('date', $item->created);
		}

		if (!isset($created_by_alias) && !isset($item->created_by))
		{
			$author = '';
		}
		else
		{
			$author = $item->created_by_alias ?: Factory::getApplication()->getIdentity($item->created_by)->name;
		}

		$overlib .= '&lt;br /&gt;';
		$overlib .= $date;
		$overlib .= '&lt;br /&gt;';
		$overlib .= Text::sprintf('COM_VKMDB_WRITTEN_BY', htmlspecialchars($author, ENT_COMPAT, 'UTF-8'));

		$icon = $item->published ? 'edit' : 'eye-slash';

		if (strtotime($item->publish_up) > strtotime(Factory::getDate())
			|| ((strtotime($item->publish_down) < strtotime(Factory::getDate())) && $item->publish_down != Factory::getContainer()->get('DatabaseDriver')->getNullDate()))
		{
			$icon = 'eye-slash';
		}

		$text = '<span class="hasTooltip fa fa-' . $icon . '" title="'
			. HTMLHelper::tooltipText(Text::_('COM_VKMDB_EDIT_ITEM'), $overlib, 0, 0) . '"></span> ';
		$text .= Text::_('JGLOBAL_EDIT');

		$attribs['title'] = Text::_('COM_VKMDB_EDIT_ITEM');
		$output           = HTMLHelper::_('link', Route::_($url), $text, $attribs);

		return $output;
	}
}
