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

JHtml::_('behavior.formvalidation');
JHtml::_('bootstrap.framework');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

// Component Parameters
$yooRecipeparams 	= JComponentHelper::getParams('com_yoorecipe');
$use_fractions		= $yooRecipeparams->get('use_fractions', 0);

$lang		= JFactory::getLanguage();
$document 	= JFactory::getDocument();
$document->addStyleSheet('media/com_yoorecipe/styles/yoorecipe.css');

$document->addScript('media/com_yoorecipe/js/generic.js');
$document->addScript('media/com_yoorecipe/js/shoppinglist-edit.js');

// Get module parameters
$image_size 			= "150";
$output_encoding 		= "UTF-8";
$error_correction_level = "L";
$margin 				= "4";

// Build QR Code source
$url	= JURI::root().'index.php?option=com_yoorecipe&view=shoppinglist&layout=smartphone&id='.$this->item->id.'&tmpl=component';
$qrCodeSource 	= 'https://chart.googleapis.com/chart?chs=';
$qrCodeSource	.= $image_size.'x'.$image_size.'&cht=qr';
$qrCodeSource	.= '&chl='.rawurlencode ($url);
$qrCodeSource	.= '&choe='.$output_encoding;
$qrCodeSource	.= '&chld='.$error_correction_level.'|'.$margin;

$status = "status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=940,height=480,directories=no,location=no";
$script = "window.addEvent('domready', function() {
	printShoppingList = function() {
		window.open('" .JUri::current().'?option=com_yoorecipe&view=shoppinglist&layout=print&id='.$this->item->id."&tmpl=component&print=1', '".addslashes($this->item->title)."', '".$status."');
	}
});";
	
$document->addScriptDeclaration($script);
?>
<input type="hidden" name="sl_id" id="sl_id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" id="language_tag" value="<?php echo $lang->getTag(); ?>"/>

<div class="huge-ajax-loading"></div>
<h2><?php echo JText::sprintf('COM_YOORECIPE_SHOPPINGLIST_TITLE', $this->item->title); ?></h2>
<p><?php echo JText::_('COM_YOORECIPE_RECIPES_FOR_SHOPPINGLIST'); ?></p>
<div>
	<?php
		$infos = (array) json_decode($this->item->infos, true);
		$html = array();
		foreach ($infos as $info) {
			$html[] = '<a href="'.$info['link'].'" target="_blank">'.$info['title'].'</a>';
		}
		echo implode(", ",$html);
	?>
</div>
<hr/>
<div class="row-fluid hidden-phone">
	<button type="button" class="btn" onclick="loadCreateShoppingListDetailModal();"><i class="icon-plus"></i> <?php echo JText::_('COM_YOORECIPE_SHOPPINGLIST_ADD_INGREDIENT');?></button>
	<button type="button" class="btn" onclick="printShoppingList();"><i class="icon-print"></i> <?php echo JText::_('JGLOBAL_PRINT');?></button>
	<a href="<?php echo JRoute::_(JHtml::_('YooRecipeHelperRoute.getshoppinglistroute',$this->item->id), false); ?>" class="btn"><?php echo JText::_('JPREV');?></a>
</div>
<div class="row-fluid visible-phone">
	<a href="<?php echo JRoute::_($url);?>" class="btn btn-large btn-block btn-success"><?php echo JText::_('COM_YOORECIPE_SHOPPINGLIST_GO_TO_EDIT'); ?></a>
	<button type="button" class="btn btn-large btn-block" onclick="loadCreateShoppingListDetailModal();"><i class="icon-plus"></i> <?php echo JText::_('COM_YOORECIPE_SHOPPINGLIST_ADD_INGREDIENT');?></button>
	<button type="button" class="btn btn-large btn-block" onclick="printShoppingList();"><i class="icon-print"></i> <?php echo JText::_('JGLOBAL_PRINT');?></button>
	<a href="<?php echo JRoute::_(JHtml::_('YooRecipeHelperRoute.getshoppinglistroute',$this->item->id), false); ?>" class="btn btn-large btn-block"><?php echo JText::_('JPREV');?></a>
</div>
<br/>
<div class="row-fluid">
	<div class="span8 pull-left">
		<ul class="unstyled" id="shoppinglist-ul">
<?php	foreach ($this->item->details as $item) {	
			echo JHtml::_('shoppinglistutils.generateShoppingListDetailForEditHTML', $item, $use_fractions);
		}
?>
		</ul>
	</div>
	<div class="span4 pull-right hidden-phone">
		<p class="alert alert-warning"><?php echo JText::_('COM_YOORECIPE_SMARTPHONE_SHOPPINGLIST'); ?></p>
		<center>
			<img src="<?php echo $qrCodeSource; ?>" width="<?php echo $image_size; ?>" height="<?php echo $image_size; ?>"/>
		</center>
	</div>
</div>

<div id="modal-edit-shoppinglist-detail" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h3><?php echo JText::_('JGLOBAL_EDIT'); ?></h3>
			</div>
			<div class="modal-body">
				<div class="huge-ajax-loading"></div>
				<form id="edit-shoppinglist-detail-form" class="form-horizontal validate">
					<div class="control-group">
						<label class="control-label"><?php echo JText::_('COM_YOORECIPE_YOORECIPE_QUANTITIES_LABEL');?></label>
						<div class="controls">
							<input type="text" name="sld_quantity" id="sld_quantity" class="required validate-fraction"/>
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label"><?php echo JText::_('COM_YOORECIPE_INGREDIENTS_DESCRIPTION');?></label>
						<div class="controls">
							<input type="text" name="sld_description" id="sld_description" class="required"/>
						</div>
					</div>
					
					<input type="hidden" name="sld_id" id="sld_id"/>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn" data-dismiss="modal"><?php echo JText::_('JCANCEL'); ?></button>
				<button type="button" class="btn btn-primary" onclick="editShoppingListDetailFormValidator.validate();"><?php echo JText::_('JSAVE'); ?></button>
			</div>
		</div>
	</div>
</div>