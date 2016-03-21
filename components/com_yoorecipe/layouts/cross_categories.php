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

$user = JFactory::getUser();

$recipe			= $displayData['recipe'];
$do_row_fluid 	= $displayData['do_row_fluid'];

$html = array();
		
if ($do_row_fluid) {
	$html[] = '<div class="row-fluid">';
	$html[] = '<div class="span2">';
}
$html[] = '<strong>'.JText::_('COM_YOORECIPE_CATEGORY').':&nbsp;</strong>';

if ($do_row_fluid) {
	$html[] = '</div>';
	$html[] = '<div>';
}
$categories = array();
foreach ($recipe->categories as $i => $category) {
	$cat_url = JRoute::_(JHtml::_('YooRecipeHelperRoute.getcategoryroute', $category->id.":".$category->alias) , false);
	$categories[] = '<a href="'.$cat_url.'" title="'.htmlspecialchars($category->title).'">'.htmlspecialchars($category->title).'</a>';
}
$html[] = implode(", ",$categories);

if ($do_row_fluid) {
	$html[] = '</div>';
	$html[] = '</div>';
}

echo implode("\n", $html);
?>