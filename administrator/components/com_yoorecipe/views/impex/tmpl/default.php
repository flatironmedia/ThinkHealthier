<?php
/*------------------------------------------------------------------------
# com_yoorecipe -  YooRecipe! Joomla 2.5 & 3.x recipe component
# ------------------------------------------------------------------------
# author    YooRock!
# copyright Copyright (C) 2011 yoorock.fr. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.yoorecipe.com
# Technical Support:  Forum - http://www.yoorecipe.com/
-------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

JHtml::_('behavior.tooltip');

$script = "window.addEvent('domready', function() {

	launchImport = function() {
	
		// check file has been selected
		if ($('jform_import_file').value == '') {
			alert('".JText::_('JGLOBAL_VALIDATION_FORM_FAILED', true)."');
		} else {
			Joomla.submitform('impex.import');
		}
	}
	
	launchExport = function() {
		Joomla.submitform('impex.export');
	}
});";

$document=  JFactory::getDocument();
$document->addScriptDeclaration($script);
?>
<form action="<?php echo JRoute::_('index.php?option=com_yoorecipe&view=impex'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<?php if (!empty( $this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
		</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>

		<input id="jform_import_file" class="" type="file" size="40" accept="application/zip" value="" name="import_file"/>
		
		<select id="jform_mode" name="mode">
			<option value="<?php echo ImportModeEnum::UPDATE ;?>"><?php echo JText::_('COM_YOORECIPE_UPDATE'); ?></option>
			<option value="<?php echo ImportModeEnum::INSERT ;?>"><?php echo JText::_('COM_YOORECIPE_INSERT'); ?></option>
			<option value="<?php echo ImportModeEnum::DELETE_INSERT ;?>"><?php echo JText::_('COM_YOORECIPE_TRUNCATE_AND_INSERT'); ?></option>
		</select>
		
		<button type="button" onclick="launchImport();" class="btn"><?php echo JText::_('COM_YOORECIPE_IMPORT'); ?></button>
		
		<hr/>
		
		<div class="alert alert-success">
			<i class="icon-ok"></i> <?php echo JText::_('COM_YOORECIPE_EXPORT_DATA_INCLUDES'); ?>
		</div>
		<div class="alert alert-danger">
			<i class="icon-remove"></i> <?php echo JText::_('COM_YOORECIPE_EXPORT_DATA_EXCLUDES'); ?>
		</div>
		<button type="button" onclick="launchExport();" class="btn"><?php echo JText::_('COM_YOORECIPE_EXPORT'); ?></button>
		
		<div>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="" />
			<input type="hidden" name="filter_order_Dir" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</div>
</form>