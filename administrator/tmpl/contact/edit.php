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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

$app = Factory::getApplication();
$input = $app->input;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
   ->useScript('form.validate');

$layout  = 'edit';
$tmpl = $input->get('tmpl', '', 'cmd') === 'component' ? '&tmpl=component' : '';
?>
<div class="vkmdb vkmdb_contact">
	<form action="<?php echo Route::_('index.php?option=com_vkmdb&layout=' . $layout . $tmpl . '&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
		<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>
        
        <div class="main-card">
            <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'details', 'recall' => true, 'breakpoint' => 768)); ?>
            
            <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', empty($this->item->id) ? Text::_('COM_VKMDB_NEW_CONTACT') : Text::_('COM_VKMDB_EDIT_CONTACT')); ?>
            <div class="row">
                <div class="col-lg-3">
                    <?php echo $this->form->renderFieldset('contactdetails'); ?>
                </div>
            </div>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>
            <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'moreInformations', Text::_('COM_VKMDB_MORE_INFORMATIONS')); ?>
            <div class="row">
                <div class="col-lg-3">
                    <?php echo $this->form->renderFieldset('moreInformations'); ?>
                </div>
            </div>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>
	        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'hiddenFields', Text::_('COM_VKMDB_HIDDEN_FIELDS')); ?>
            <div class="row">
                <div class="col-lg-3">
			        <?php echo $this->form->renderFieldset('hiddenFields'); ?>
                </div>
            </div>
	        <?php echo HTMLHelper::_('uitab.endTab'); ?>

                
            <?php echo HTMLHelper::_('uitab.endTabSet'); ?>
        </div>
        
		<?php echo HTMLHelper::_('form.token'); ?>
		<input type="hidden" name="task" value="">
	</form>
</div>
