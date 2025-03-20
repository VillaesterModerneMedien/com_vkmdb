<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_vkmdb
 *
 * @copyright   Copyright (C) 2025 Mario Hewera. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;


use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Text;

$displayData = [
	'textPrefix' => 'COM_VKMDB',
	'formURL' => 'index.php?option=com_vkmdb',
	'icon' => 'icon-file',
];

$user = Factory::getApplication()->getIdentity();

if ($user->authorise('core.create', 'com_vkmdb') || count($user->getAuthorisedCategories('com_vkmdb', 'core.create')) > 0)
{
	$displayData['createURL'] = 'index.php?option=com_vkmdb&task=item.add';
}

echo LayoutHelper::render('joomla.content.emptystate', $displayData);