<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_vkmdb
 *
 * @copyright   Copyright (C) 2025 Mario Hewera. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('com_vkmdb.script');
// $wa->useAsset('script', 'jquery');
?>
<div class="com-vkmdb-item view-item<?php echo $this->pageclass_sfx; ?>">
<?php if ($this->params->get('show_page_heading') != 0) : ?>
	<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
<?php else: ?>
    <h1>
		<?php echo $this->escape($this->item->title); ?>
	</h1>
<?php endif; ?>

<div class="item-content">
    <?php echo $this->item->content; ?>
</div>
