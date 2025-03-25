<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_vkmdb
 *
 * @copyright   Copyright (C) 2025 Mario Hewera. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use VkmdbNamespace\Component\Vkmdb\Site\Helper\RouteHelper;

HTMLHelper::_('behavior.core');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<div class="com-vkmdb-list__items">
	<?php if (empty($this->items)) : ?>
		<p class="com-vkmdb-items__message"> <?php echo Text::_('COM_VKMDB_NO_CONTACTS'); ?>	 </p>
	<?php else : ?>

        <?php foreach ($this->items as $i => $item) : ?>
            <p>
                <a href="<?php echo Route::_(RouteHelper::getContactRoute($item->slug, $item->catid)); ?>">
                    <?php echo $item->title; ?>
                </a>
            </p>
        <?php endforeach; ?>

	<?php endif; ?>
</div>
