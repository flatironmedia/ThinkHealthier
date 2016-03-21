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

$document = JFactory::getDocument();
$document->addStyleSheet('media/com_yoorecipe/styles/yoorecipe.css');

$document->addScript('media/com_yoorecipe/js/generic.js');
$document->addScript('media/com_yoorecipe/js/shoppinglist.js');

$input				= JFactory::getApplication()->input;
$is_printing 		= $input->get('print', 0, 'INT');
$shoppinglist_id 	= $input->get('id', 0, 'INT');

if ($is_printing) {
	
	echo '<br/>';
	echo '<a href="'.JRoute::_(JHtml::_('YooRecipeHelperRoute.getshoppinglistroute', $shoppinglist_id)).'">'.JText::_('JPREV').'</a>';
	echo '<center><input type="button" class="btn btn-primary btn-large noPrint" onclick="javascript:window.print()" value="'.JText::_('JGLOBAL_PRINT').'"/></center>';
}
?>
<div class="huge-ajax-loading"></div>
<h2><?php echo $this->item->title ; ?></h2>

<form>
	
<?php
	echo '<div class="row-fluid">';
	
	$i = 0;
	
	foreach ($this->item->details as $item) {
		
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
		$i++;
		echo implode("\n",$html);
	}
?>
</form>