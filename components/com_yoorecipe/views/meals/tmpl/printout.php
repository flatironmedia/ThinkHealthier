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

// import library for calendar
jimport('joomla.html.html');

JHtml::_('bootstrap.framework');

$document = JFactory::getDocument();
$document->addStyleSheet('media/com_yoorecipe/styles/yoorecipe.css');

// Component Parameters
$yooRecipeparams 	= JComponentHelper::getParams('com_yoorecipe');
$use_fractions		= $yooRecipeparams->get('use_fractions', 0);

$document->addScript('media/com_yoorecipe/js/generic.js');

// Init variables
$input	 		= JFactory::getApplication()->input;
$is_printing 	= $input->get('print', 0, 'INT');

$print_start_date_obj 	= JFactory::getDate($this->print_start_date);
$print_end_date_obj 	= JFactory::getDate($this->print_end_date);
$start_date 			= $print_start_date_obj->format('d F Y');
$end_date 				= $print_end_date_obj->format('d F Y');

$print_url = JURI::getInstance().'&print=1';
$status = "status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=940,height=480,directories=no,location=no";
$script = "window.addEvent('domready', function() {
	printMeals = function() {
		window.open('".$print_url."', '".addslashes(JText::sprintf('COM_YOORECIPE_MEALPLANNER_FROM_TO', $start_date, $end_date))."', '".$status."');
	}
});";
	
$document->addScriptDeclaration($script);
?>
<div class="huge-ajax-loading"></div>
<?php

	echo '<h2>',JText::sprintf('COM_YOORECIPE_MEALPLANNER_FROM_TO', $start_date, $end_date),'</h2>';
	echo '<div class="row-fluid">';
	foreach ($this->days_of_week as $date => $day_of_week) {
	
		echo '<div id="meal_'.$date.'" class="mp-column drop-area" style="min-height:400px;">';
		echo '<h5>',JText::_($day_of_week->label),'</h5>';
		echo '<div id="meal_ctnr_'.$date.'">';
		echo '<hr/>';
		foreach ($day_of_week->meals as $meal) {
			echo JHtml::_('mealsutils.generateMealEntryHTML', $meal->meal_id, $meal->recipe_id, $meal->title, $meal->alias, $meal->servings_type_code, $meal->nb_servings, $date, $meal->picture);
		}
		echo '</div>';
		echo '</div>';
	}
	echo '</div>';
	
	echo '<h2>',JText::_('COM_YOORECIPE_SHOPPINGLIST'),'</h2>';
	echo '<div class="row-fluid">';
	
	$i = 0;
	foreach ($this->shopping_list->details as $item) {
		
		$rounded_quantity = round($item->quantity, 2);
		$quantity = ($use_fractions) ? JHtml::_('ingredientutils.decimalToFraction', $rounded_quantity) : $rounded_quantity;
		$quantity_string = ($quantity == 0) ? '' : $quantity.' ';
		
		$html = array();
		$html[] = '<div class="span6">';
		$html[] = '<label class="checkbox">';
		$html[] = '<input type="checkbox"/>'.$quantity_string.$item->description;
		$html[] = '</label>';
		$html[] = '</div>';
		
		if ($i%2 == 1) {
			$html[] = '</div>';
			$html[] = '<div class="row-fluid">';
		}
		$i++;
		echo implode("\n",$html);
	}
	echo '</div>';
		
	// Get module parameters
	$image_size 			= "150";
	$output_encoding 		= "UTF-8";
	$error_correction_level = "L";
	$margin 				= "4";

	// Build QR Code source
	$url			= JURI::root().'index.php?option=com_yoorecipe&view=shoppinglist&layout=smartphone&tmpl=component&id='.$this->shopping_list->id;
	$qrCodeSource 	= 'https://chart.googleapis.com/chart?chs=';
	$qrCodeSource	.= $image_size.'x'.$image_size.'&cht=qr';
	$qrCodeSource	.= '&chl='.rawurlencode ($url);
	$qrCodeSource	.= '&choe='.$output_encoding;
	$qrCodeSource	.= '&chld='.$error_correction_level.'|'.$margin;
?>
<div class="span4 pull-right hidden-phone">
	<p class="alert alert-warning"><?php echo JText::_('COM_YOORECIPE_SMARTPHONE_SHOPPINGLIST'); ?></p>
	<center>
		<img src="<?php echo $qrCodeSource; ?>" width="<?php echo $image_size; ?>" height="<?php echo $image_size; ?>"/>
	</center>
</div>
<?php 

if ($is_printing) {
	echo '<input type="button" class="btn" onclick="javascript:window.print()" value="'.JText::_('JGLOBAL_PRINT').'"/>';
} else {
	echo '<center><input type="button" class="btn btn-primary btn-large noPrint" onclick="printMeals()" value="'.JText::_('JGLOBAL_PRINT').'"/></center>';
}
?>