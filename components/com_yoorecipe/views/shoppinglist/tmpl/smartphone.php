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
$document->setMetaData('viewport', 'width=device-width, initial-scale=1.0');
$document->addStyleSheet('media/com_yoorecipe/styles/yoorecipe.css');

$document->addScript('media/com_yoorecipe/js/generic.js');
$document->addScript('media/com_yoorecipe/js/shoppinglist.js');

$input			= JFactory::getApplication()->input;
$is_printing 	= $input->get('print', 0, 'INT');
if ($is_printing) {
	echo '<br/><center><input type="button" class="btn btn-primary btn-large noPrint" onclick="javascript:window.print()" value="'.JText::_('JGLOBAL_PRINT').'"/></center>';
}
?>
<div class="huge-ajax-loading"></div>
<h1><?php echo JText::sprintf('COM_YOORECIPE_SHOPPINGLIST_TITLE', $this->item->title); ?></h1>
<div class="pull-left"><?php echo JText::sprintf('COM_YOORECIPE_NB_ELEMENTS', count($this->item->details)); ?></div>
<div class="pull-right"><a href="<?php echo JRoute::_(JHtml::_('YooRecipeHelperRoute.getshoppinglisteditroute',$this->item->id).'&tmpl=component'); ?>"><?php echo JText::_('JGLOBAL_EDIT'); ?></a></div>
<div class="clearfix"></div>
<hr/>
<form>
<?php
	foreach ($this->item->details as $item) {
		
		$rounded_quantity = round($item->quantity, 2);
		$quantity = ($use_fractions) ? JHtml::_('ingredientutils.decimalToFraction', $rounded_quantity) : $rounded_quantity;
		$quantity_string = ($quantity == 0) ? '' : $quantity.' ';
		$checked = ($item->status==1) ? 'checked': '';
		
		$html = array();
		$html[] = '<div class="row-fluid " id="ingr_'.$item->id.'">';
		$html[] = '<div class="offset1">';
		$html[] = '<h1>';
		$html[] = '<input type="checkbox" onclick="updateShoppingListDetailStatus('.$item->id.', this);" '.$checked.'/>';
		// $html[] = '</h1>';
		// $html[] = '</div>';
		// $html[] = '<label class="checkbox">';
		// $html[] = '</label>';
		// $html[] = '<div class="span10">';
		// $html[] = '<h1>';
		$html[] = '&nbsp;'.$quantity_string.$item->description;
		$html[] = '</h1>';
		$html[] = '</div>';
		$html[] = '</div>';
		echo implode("\n",$html);
	}
?>
</form>

<div class="row-fluid">
	<a href="<?php echo JRoute::_('index.php?option=com_yoorecipe&view=shoppinglists'); ?>" class="btn btn-block">
		<h1><?php echo JText::_('COM_YOORECIPE_YOUR_SHOPPINGLISTS');?></h1>
	</a>
</div>