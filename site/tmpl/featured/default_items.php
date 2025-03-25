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

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('core')->useScript('searchtools');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<div class="com-vkmdb-featured__items">
	<?php if (empty($this->items)) : ?>
		<p class="com-vkmdb-featured__message"> <?php echo Text::_('COM_VKMDB_NO_CONTACTS'); ?>	 </p>
	<?php else : ?>
	<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm">
		<table class="com-vkmdb-featured__table table">
			<thead class="thead-default">
				<tr>
					<th class="item-num">
						<?php echo Text::_('JGLOBAL_NUM'); ?>
					</th>

					<th class="item-title">
						<?php echo HTMLHelper::_('grid.sort', 'COM_VKMDB_CONTACT_TITLE_LABEL', 'a.title', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>

			<tbody>
				<?php foreach ($this->items as $i => $item) : ?>
					<tr class="<?php echo ($i % 2) ? 'odd' : 'even'; ?>">
						<td class="item-num">
							<?php echo $i; ?>
						</td>

						<td class="item-title">
                            <a href="<?php echo Route::_(RouteHelper::getContactRoute($item->slug, $item->catid)); ?>">
                                <?php echo $item->title; ?>
                            </a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
        <input type="hidden" name="filter_order" value="">
		<input type="hidden" name="filter_order_Dir" value="">
		<input type="hidden" name="task" value="">
	</form>
	<?php endif; ?>
</div>
