<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_vkmdb
 *
 * @copyright   Copyright (C) 2025 Mario Hewera. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace VkmdbNamespace\Component\Vkmdb\Administrator\Service\HTML;

defined('JPATH_BASE') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
/**
 * Vkmdb HTML class.
 *
 * @since  1.0.0
 */
class AdministratorService
{
    /**
	 * Display the published or unpublished state of an item.
	 *
	 * @param   int      $value      The state value.
	 * @param   int      $i          The ID of the item.
	 * @param   boolean  $canChange  An optional prefix for the task.
	 *
	 * @return  string
	 *
	 * @since   1.6
	 *
	 * @throws  \InvalidArgumentException
	 */
	public function published($value = 0, $i = null, $canChange = true)
    {
        // Note: $i is required but has to be an optional argument in the function call due to argument order
		if (null === $i)
		{
			throw new \InvalidArgumentException('$i is a required argument in JHtmlRedirect::published');
		}
        if ($value==1)
        {
            return '<span class="icon-publish" aria-hidden="true"></span>';
        }
        else
        {
            return '<span class="icon-unpublish" aria-hidden="true"></span>';
        }
    }
}
