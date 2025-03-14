<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_vkmdb
 *
 * @copyright   Copyright (C) 2025 Mario Hewera. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace VkmdbNamespace\Component\Vkmdb\Site\Helper;

\defined('_JEXEC') or die;

use Joomla\CMS\Categories\CategoryNode;
use Joomla\CMS\Language\Multilanguage;

/**
 * Vkmdb Component Route Helper
 *
 * @static
 * @package     Joomla.Site
 * @subpackage  com_vkmdb * @since       1.0.0
 */
abstract class RouteHelper
{
	/**
	 * Get the URL route for a eintrag from a eintrag ID, eintraege category ID and language
	 *
	 * @param   integer  $id        The id of the eintraege
	 * @param   integer  $catid     The id of the eintraege's category
	 * @param   mixed    $language  The id of the language being used.
	 *
	 * @return  string  The link to the eintraege
	 *
	 * @since   1.0.0
	 */
	public static function getEintragRoute($id, $catid = 0, $language = 0)
	{
		// Create the link
		$link = 'index.php?option=com_vkmdb&view=eintrag&id=' . $id;
        
		if ($catid > 1)
		{
			$link .= '&catid=' . $catid;
		}
        
		if ($language && $language !== '*' && Multilanguage::isEnabled())
		{
			$link .= '&lang=' . $language;
		}

		return $link;
	}
	/**
	 * Get the URL route for a eintraege category from a eintraege category ID and language
	 *
	 * @param   mixed  $catid     The id of the eintraege's category either an integer id or an instance of CategoryNode
	 * @param   mixed  $language  The id of the language being used.
	 *
	 * @return  string  The link to the eintraege
	 *
	 * @since   1.0.0
	 */
	public static function getCategoryRoute($catid, $language = 0)
	{
		if ($catid instanceof CategoryNode)
		{
			$id = $catid->id;
		}
		else
		{
			$id = (int) $catid;
		}

		if ($id < 1)
		{
			$link = '';
		}
		else
		{
			// Create the link
			$link = 'index.php?option=com_vkmdb&view=category&id=' . $id;

			if ($language && $language !== '*' && Multilanguage::isEnabled())
			{
				$link .= '&lang=' . $language;
			}
		}

		return $link;
	}
}
