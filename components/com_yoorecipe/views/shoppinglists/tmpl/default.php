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
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
JHtml::_('bootstrap.framework');

$top_modules 	= JModuleHelper::getModules('yoorecipe-shoppinglist-top');
$bottom_modules = JModuleHelper::getModules('yoorecipe-shoppinglist-bottom');

$document = JFactory::getDocument();
$document->addStyleSheet('media/com_yoorecipe/styles/yoorecipe.css');
$document->addScript('media/com_yoorecipe/js/generic.js');
$document->addScript('media/com_yoorecipe/js/shoppinglists.js');
?>
<div class="huge-ajax-loading"></div>
<?php
	if (count($top_modules) > 0) {
		echo '<div>';
		foreach($top_modules as $module) {
			echo JModuleHelper::renderModule($module);
		}
		echo '</div>';
	}
?>
<div class="item-page<?php echo $this->pageclass_sfx; ?>" itemscope itemtype="http://schema.org/Article">
	<?php if ($this->menuParams->get('show_page_heading', 1)) : ?>
	<div class="page-header">
		<h1> <?php echo $this->escape($this->menuParams->get('page_heading')); ?> </h1>
	</div>
	<?php endif; ?>
	
	<h2><?php echo JText::_('COM_YOORECIPE_YOUR_SHOPPINGLISTS'); ?></h2>
	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th><?php echo JText::_('COM_YOORECIPE_TITLE'); ?></th>
				<th><?php echo JText::_('COM_YOORECIPE_SHOPPINGLIST_CREATION_DATE'); ?></th>
				<th></th>
			</tr>
		</thead>
		<tfoot></tfoot>
		<tbody id="shoppinglists-body">
		
	<?php 
		foreach ($this->items as $item) {
			echo JHtml::_('shoppinglistutils.generateShoppingListHTML', $item);
		} 
		?>
		</tbody>
	</table>
	<button type="button" class="btn btn-success" onclick="loadCreateShoppingListModal();"><i class="icon-plus" ></i> <?php echo JText::_('JNEW'); ?></button>
	<div id="modal-create-shopping-list" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3><?php echo JText::_('COM_YOORECIPE_NEW_SHOPPINGLIST'); ?></h3>
				</div>
				<div class="modal-body" id="modal-create-shopping-list-body">
				<div class="huge-ajax-loading"></div>
					<form id="AddListForm">
						<div class="control-group">
							<div class="control-label">
								<label><?php echo JText::_('COM_YOORECIPE_TITLE'); ?></label>
							</div>
							<div class="controls">
								<input type="text" name="shoppinglist_title" id="shoppinglist_title"/>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer" id="modal-create-shopping-list-footer">
					<button type="button" class="btn" data-dismiss="modal"><?php echo JText::_('JCANCEL'); ?></button>
					<button type="button" class="btn btn-primary" onclick="createShoppingList();"><?php echo JText::_('COM_YOORECIPE_CREATE'); ?></button>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
	if (count($bottom_modules) > 0) {
		echo '<div>';
		foreach($bottom_modules as $module) {
			echo JModuleHelper::renderModule($module);
		}
		echo '</div>';
	}
?>