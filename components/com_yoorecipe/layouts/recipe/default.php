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

$recipe 					= $displayData['recipe'];
$blog_is_picture_clickable 	= $displayData['blog_is_picture_clickable'];
$blog_show_rating 			= $displayData['blog_show_rating'];
$blog_show_cuisine	 		= $displayData['blog_show_cuisine'];
$blog_rating_style 			= $displayData['blog_rating_style'];
$blog_show_category_title 	= $displayData['blog_show_category_title'];

$user 			= JFactory::getUser();
$picture_path 	= JHtml::_('imageutils.getPicturePath', $recipe->picture);
$url 			= JRoute::_(JHtml::_('YooRecipeHelperRoute.getreciperoute', $recipe->slug, $recipe->catslug) , false);

$html = array();
$html[] = '<div class="recipe-img">';

if ($recipe->featured) {
	// $html[] = '<img class="featured-item" src="media/com_yoorecipe/images/featured-item.png" alt="'.JText::_('JFEATURED').'">';
	$html[] = '<span class="recipe-featured"><i class="icon-star"></i></span>';
}

$image_html = '<img src="'.JURI::root().'slir/w303-h202/'.$picture_path.'" alt="'.htmlspecialchars($recipe->title).'" title="'.htmlspecialchars($recipe->title).'" />';
if ($blog_is_picture_clickable) {
	$image_html = '<a href="'.$url.'">'.$image_html.'</a>';
}

$html[] = $image_html;
		
$html[] = '</div>';
$html[] = '<div class="recipe-details">';
$html[] = '<a href="'.$url.'">';
$html[] = '<h4>'.$recipe->title.'</h4>';
$html[] = '</a>';

if ($blog_show_rating) {
	
	$html[] = '<div class="row-fluid">';
	$html[] = '<div class="recipe-rating">';
	if ($recipe->note == null) {
		$recipe->note = 0;
	}
	
	if ($blog_rating_style == 'grade') {
		$html[] = '<strong>'.JText::_('COM_YOORECIPE_RECIPE_NOTE').': </strong><span> '.$recipe->note.'/5</span>'; 
	}
	else if ($blog_rating_style == 'stars') {
	
		$width = ($recipe->note*113)/5;				
		$html[] = '<center>';
		$html[] = '<div class="rec-detail-wrapper">';
		$html[] = '<div class="rating-stars stars113x20">';
		$html[] = '<div style="width:'.$width.'px;" class="rating-stars-grad"></div>';
		$html[] = '<div class="rating-stars-img">';
		$html[] = '<span class="rating hide">'.$recipe->note.'</span>';
		$html[] = '</div>';
		$html[] = '</div>';
		$html[] = '</div>';
		$html[] = '</center>';
		
	}
	$html[] = '</div>';
	$html[] = '</div>';
}

$html[] = '<div class="row-fluid recipe-labels">';

if ($blog_show_cuisine && !empty($recipe->cuisine)) {
	$html[] = '<a href="'.JRoute::_('index.php?option=com_yoorecipe&view=recipes&layout=cuisine&cuisine='.$recipe->cuisine).'" title="'.htmlspecialchars($recipe->cuisine).'"><span class="label label-info">'.JText::_('COM_YOORECIPE_CUISINE_'.$recipe->cuisine).'</span></a>';
}

if (isset($recipe->categories)) {
	if ($blog_show_category_title) {
		$categories = array();
		foreach ($recipe->categories as $i => $category) {
			$cat_url = JRoute::_(JHtml::_('YooRecipeHelperRoute.getcategoryroute', $category->id.":".$category->alias) , false);
			$categories[] = '<a href="'.$cat_url.'" title="'.htmlspecialchars($category->title).'"><span class="label label-success">'.htmlspecialchars($category->title).'</span></a>';
		}
		$html[] = implode(" ",$categories);
	}
}
	$html[] = '</div>';	

$html[] = '<br/>';
$html[] = '</div>';

echo implode("\n", $html);
?>