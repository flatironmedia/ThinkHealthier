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
	
	launchdeleteOldMealEntries = function() {
		Joomla.submitform('maintenance.deleteOldMealEntries');
	}
	
	launchMigrateTags = function() {
		Joomla.submitform('maintenance.migrateTags');
	}
	
	launchMigrateIngredients = function() {
		Joomla.submitform('maintenance.migrateingredients');
	}
	
	launchMigrateServingTypes = function() {
		Joomla.submitform('maintenance.migrateServingTypes');
	}
});";

$document=  JFactory::getDocument();
$document->addScriptDeclaration($script);
?>
<form action="<?php echo JRoute::_('index.php?option=com_yoorecipe&view=maintenance'); ?>" method="post" name="adminForm" id="adminForm">
	<?php if (!empty( $this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
		</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
		
		<div class="row-fluid">
			<div class="span3">
				<button type="button" onclick="launchdeleteOldMealEntries();" class="btn"><?php echo JText::_('COM_YOORECIPE_DELETE_OLD_MEALPLANNER_ENTRIES'); ?></button>
			</div>
			<div class="span9">
				<div class="alert alert-info"><?php echo JText::_('COM_YOORECIPE_DELETE_OLD_MEALPLANNER_ENTRIES_DESC'); ?></div>
			</div>
		</div>
		
		<div class="row-fluid">
			<div class="span3">
				<button type="button" onclick="launchMigrateTags();" class="btn"><?php echo JText::_('COM_YOORECIPE_MIGRATE_TAGS'); ?></button>
			</div>
			<div class="span9">
				<div class="alert alert-info"><?php echo JText::_('COM_YOORECIPE_MIGRATE_TAGS_DESC'); ?></div>
			</div>
		</div>
		
		<div class="row-fluid">
			<div class="span3">
				<button type="button" onclick="launchMigrateServingTypes();" class="btn"><?php echo JText::_('COM_YOORECIPE_MIGRATE_SERVING_TYPES'); ?></button>
			</div>
			<div class="span9">
				<div class="alert alert-info"><?php echo JText::_('COM_YOORECIPE_MIGRATE_SERVING_TYPES_DESC'); ?></div>
			</div>
		</div>
		
		<div class="row-fluid">
			<div class="span3">
				<button type="button" onclick="launchMigrateIngredients();" class="btn"><?php echo JText::_('COM_YOORECIPE_MIGRATE_INGREDIENTS'); ?></button>
			</div>
			<div class="span9">
				<div class="alert alert-info"><?php echo JText::_('COM_YOORECIPE_MIGRATE_INGREDIENTS_DESC'); ?></div>
			</div>
		</div>
		
		<div>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="" />
			<input type="hidden" name="filter_order_Dir" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</div>
</form>