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
use Joomla\CMS\Router\Route;
use VkmdbNamespace\Component\Vkmdb\Site\Helper\RouteHelper;

if ($this->maxLevel != 0 && count($this->children[$this->category->id]) > 0) :
?/>
<ul class="com-vkmdb-category__children list-striped list-condensed">
	<?php foreach ($this->children[$this->category->id] as $id => $child) : ?>
		<?php if ($this->params->get('show_empty_categories') || $child->numitems || count($child->getChildren())) : ?>
	<li>
		<h4 class="item-title">
			<a href="<?php echo Route::_(RouteHelper::getCategoryRoute($child->id, $child->language)); ?>">
			<?php echo $this->escape($child->title); ?>
			</a>

			<?php if ($this->params->get('show_cat_items') == 1) : ?>
				<span class="badge badge-info float-right" title="<?php echo Text::_('COM_VKMDB_CAT_NUM'); ?>"><?php echo $child->numitems; ?></span>
			<?php endif; ?>
		</h4>

			<?php if ($this->params->get('show_subcat_desc') == 1) : ?>
				<?php if ($child->description) : ?>
				<div class="category-desc">
					<?php echo HTMLHelper::_('content.prepare', $child->description, '', 'com_vkmdb.category'); ?>
				</div>
				<?php endif; ?>
			<?php endif; ?>

			<?php if (count($child->getChildren()) > 0) :
				$this->children[$child->id] = $child->getChildren();
				$this->category = $child;
				$this->maxLevel--;
				echo $this->loadTemplate('children');
				$this->category = $child->getParent();
				$this->maxLevel++;
			endif; ?>
	</li>
		<?php endif; ?>
	<?php endforeach; ?>
</ul>
<?php endif; ?>
