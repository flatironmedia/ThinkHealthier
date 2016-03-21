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

$yooRecipeparams 				= JComponentHelper::getParams('com_yoorecipe');
$blog_description_max_length 	= $yooRecipeparams->get('blog_description_max_length', 200);
$nb_cols					 	= $yooRecipeparams->get('nb_cols', 4);
$pagination_position			= $yooRecipeparams->get('pagination_position', 'bottom');

$document 	= JFactory::getDocument();
$document->addScript('media/com_yoorecipe/js/jquery.masonry.min.js');
$items = $displayData['items'];

$blog_is_picture_clickable 	= $displayData['blog_is_picture_clickable'];
$blog_show_title 			= $displayData['blog_show_title'];
$blog_show_creation_date 	= $displayData['blog_show_creation_date'];
$blog_show_ingredients 		= $displayData['blog_show_ingredients'];
$blog_show_readmore 		= $displayData['blog_show_readmore'];
$blog_show_description 		= $displayData['blog_show_description'];
$blog_show_cuisine	 		= $displayData['blog_show_cuisine'];
$blog_show_author 			= $displayData['blog_show_author'];
$blog_show_nb_views 		= $displayData['blog_show_nb_views'];
$blog_show_category_title 	= $displayData['blog_show_category_title'];
$blog_show_difficulty 		= $displayData['blog_show_difficulty'];
$blog_show_cost 			= $displayData['blog_show_cost'];
$blog_show_rating 			= $displayData['blog_show_rating'];
$blog_rating_style 			= $displayData['blog_rating_style'];
$blog_show_preparation_time = $displayData['blog_show_preparation_time'];
$blog_show_cook_time 		= $displayData['blog_show_cook_time'];
$blog_show_wait_time 		= $displayData['blog_show_wait_time'];
$blog_show_pending_recipes 	= $displayData['blog_show_pending_recipes'];

$recipe_layout 				= $yooRecipeparams->get('recipe_layout', 'default');
$cross_categories_layout 	= new JLayoutFile('cross_categories', $basePath = JPATH_ROOT .'/components/com_yoorecipe/layouts');
$recipe_layout 				= new JLayoutFile($recipe_layout, $basePath = JPATH_ROOT .'/components/com_yoorecipe/layouts/recipe');


$html = array();
if (count($items) > 0) {
		
	echo '<div id="masonry-container">';
	
	foreach ($items as $recipe) {
		
		$picture_path 	= JHtml::_('imageutils.getPicturePath', $recipe->picture);
		$url 			= JRoute::_(JHtml::_('YooRecipeHelperRoute.getreciperoute', $recipe->slug, $recipe->catslug) , false);
		
		$cssClass ='';
		
		if ($blog_show_pending_recipes) {
			$isNotDisplayable = $recipe->published_up != 1 || $recipe->published_down != 0 || $recipe->validated == 0;
			if ($isNotDisplayable) {
				$cssClass = " greyedout";
			}
		}
		
		$display_data['recipe'] 					= $recipe;
		$display_data = array_merge($display_data, $displayData);
		
		$html[] = '<div class="item-masonry'.(($recipe->featured) ? ' featured' : '').' '.$cssClass.'">';
		$html[] = $recipe_layout->render($display_data);
		$html[] = '</div>';
				
	}
	$html[] = '</div>';
	
	$html[] =  '<div class="clearfix"></div>';
	
} // End if (count($items) > 0) {

echo implode("\n", $html);
?>
<div class="huge-ajax-loading"></div>