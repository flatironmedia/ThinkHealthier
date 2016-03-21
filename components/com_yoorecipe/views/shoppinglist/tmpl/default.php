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

$lang 		= JFactory::getLanguage();

JHtml::_('bootstrap.framework');

// Component Parameters
$yooRecipeparams 	= JComponentHelper::getParams('com_yoorecipe');
$use_fractions		= $yooRecipeparams->get('use_fractions', 0);

$document 	= JFactory::getDocument();
$document->addStyleSheet('media/com_yoorecipe/styles/yoorecipe.css');

// Get module parameters
$image_size 			= "150";
$output_encoding 		= "UTF-8";
$error_correction_level = "L";
$margin 				= "4";

// Build QR Code source
$url	= JURI::root().'index.php?option=com_yoorecipe&view=shoppinglist&layout=smartphone&tmpl=component&id='.$this->item->id;
$qrCodeSource 	= 'https://chart.googleapis.com/chart?chs=';
$qrCodeSource	.= $image_size.'x'.$image_size.'&cht=qr';
$qrCodeSource	.= '&chl='.rawurlencode ($url);
$qrCodeSource	.= '&choe='.$output_encoding;
$qrCodeSource	.= '&chld='.$error_correction_level.'|'.$margin;

$document->addScript('media/com_yoorecipe/js/generic.js');
$document->addScript('media/com_yoorecipe/js/shoppinglist.js');

?>
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
	<a href="<?php echo JRoute::_(JHtml::_('YooRecipeHelperRoute.getshoppinglisteditroute',$this->item->id)); ?>" class="btn btn-primary"><?php echo JText::_('JGLOBAL_EDIT'); ?></a>
	<a href="<?php echo JRoute::_(JHtml::_('YooRecipeHelperRoute.getshoppinglistprintroute',$this->item->id).'&tmpl=component&print=1'); ?>" class="btn"><i class="icon-print"></i> <?php echo JText::_('JGLOBAL_PRINT');?></a>
	<a href="<?php echo JRoute::_('index.php?option=com_yoorecipe&view=shoppinglists'); ?>" class="btn"><?php echo JText::_('COM_YOORECIPE_YOUR_SHOPPINGLISTS');?></a>
</div>
<div class="row-fluid visible-phone">
	<button type="button" class="btn btn-large btn-block" onclick="loadCreateShoppingListDetailModal();"><i class="icon-plus"></i> <?php echo JText::_('COM_YOORECIPE_SHOPPINGLIST_ADD_INGREDIENT');?></button>
	<a href="<?php echo JRoute::_(JHtml::_('YooRecipeHelperRoute.getshoppinglistprintroute',$this->item->id).'&tmpl=component&print=1'); ?>" class="btn btn-large btn-block"><i class="icon-print"></i> <?php echo JText::_('JGLOBAL_PRINT');?></a>
	<a href="<?php echo JRoute::_('index.php?option=com_yoorecipe&view=shoppinglists'); ?>" class="btn btn-large btn-block"><?php echo JText::_('COM_YOORECIPE_YOUR_SHOPPINGLISTS');?></a>
</div>
<br/>
<div class="row-fluid">
	<div class="span8 pull-left">
		<ul class="unstyled" id="shoppinglist-ul">
<?php	
	echo '<div class="row-fluid">';
	foreach ($this->item->details as $i => $item) {
		
		$rounded_quantity = round($item->quantity, 2);
		$quantity = ($use_fractions) ? JHtml::_('ingredientutils.decimalToFraction', $rounded_quantity) : $rounded_quantity;
		$quantity_string = ($quantity == 0) ? '' : $quantity.' ';
		$checked = ($item->status==1) ? 'checked': '';
		
		$html = array();
		$html[] = '<div class="span6" id="ingr_'.$item->id.'">';
		$html[] = '<label class="checkbox">';
		$html[] = '<input type="checkbox" onclick="updateShoppingListDetailStatus('.$item->id.', this);"'.$checked.'/>'.$quantity_string.$item->description;
		$html[] = '</label>';
		$html[] = '</div>';
		
		if ($i%2 == 1) {
			$html[] = '</div>';
			$html[] = '<div class="row-fluid">';
		}
		echo implode("\n",$html);
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
	<div class="span4 pull-right visible-phone">
		<center>
			<a href="<?php echo JRoute::_($url);?>" class="btn btn-large btn-block btn-success"><?php echo JText::_('COM_YOORECIPE_SHOPPINGLIST_GO_TO_EDIT'); ?></a>
		</center>
	</div>
</div>