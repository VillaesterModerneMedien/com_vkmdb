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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('multiselect');

$user      = Factory::getApplication()->getIdentity();
$userId    = $user->get('id');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo Route::_('index.php?option=com_vkmdb&view=contacts'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <?php
                // Search tools bar
                echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
                ?>
                <?php if (empty($this->items)) : ?>
                    <div class="alert alert-info">
						<span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
						<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
					</div>
                <?php else : ?>

                    <table class="table" id="contactsList">
                        <caption class="visually-hidden">
                            <?php echo Text::_('COM_VKMDB_TABLE_CAPTION'); ?>,
                            <span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
                            <span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
                        </caption>
                        <thead>
                            <tr>
                                <td class="w-1 text-center">
                                    <?php echo HTMLHelper::_('grid.checkall'); ?>
                                </td>
                                <th scope="col">
                                    <?php echo HTMLHelper::_('searchtools.sort', Text::_('COM_VKMDB_HEADING_CONTACT_TITLE'), 'a.title', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-1 text-center">
                                    <?php echo HTMLHelper::_('searchtools.sort', Text::_('JSTATUS'), 'a.published', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-5 d-none d-md-table-cell">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($this->items as $i => $item) :
                            $canCreate  = $user->authorise('core.create',     'com_vkmdb.category.' . $item->catid);
							$canEdit    = $user->authorise('core.edit',       'com_vkmdb.category.' . $item->catid);
                            $canEditOwn = $user->authorise('core.edit.own',   'com_vkmdb.category.' . $item->catid) && $item->created_by == $userId;
							$canChange  = $user->authorise('core.edit.state', 'com_vkmdb.category.' . $item->catid);
 
                        ?>
                            <tr class="row<?php echo $i % 2; ?>">
                                <td class="center">
                                    <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                                </td>
                                <th scope="row" class="has-context">
                                    <div>
                                        <?php if ($canEdit) : ?>
                                        <a class="hasTooltip" href="<?php echo Route::_('index.php?option=com_vkmdb&task=contact.edit&id=' . (int) $item->id); ?>" title="<?php echo Text::_('JACTION_EDIT'); ?> <?php echo $this->escape($item->title); ?>">
                                            <?php echo $item->title; ?>
                                        </a>
                                        <?php else : ?>
                                            <?php echo $this->escape($item->title); ?>
                                        <?php endif; ?>

                                        <?php if (!empty($item->note)) : ?>
                                            <div class="small">
                                                <?php echo Text::sprintf('JGLOBAL_LIST_NOTE', $this->escape($item->note)); ?>
                                            </div>
                                        <?php endif; ?>
                                                                        
                                        <div class="small">
                                            <?php echo Text::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
                                        </div>
                                        <div class="small">
                                            <?php echo Text::_('JCATEGORY') . ': ' . $this->escape($item->category_title); ?>
                                        </div>
                                    </div>
                                </th>
                                <td>
                                    <?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'contacts.', $canChange, 'cb'); ?>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <?php echo $item->id; ?>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                    <?php // load the pagination.
					echo $this->pagination->getListFooter(); 
                    ?>

                <?php endif; ?>
                <?php echo HTMLHelper::_('form.token'); ?>
                <input type="hidden" name="filter_order" value="<?php echo $this->sortColumn; ?>" />
                <input type="hidden" name="filter_order_Dir" value="<?php echo $this->sortDirection; ?>" />
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="0" />
			</div>
		</div>
	</div>
</form>
