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

$yooRecipeparams 				= JComponentHelper::getParams('com_yoorecipe');
$blog_description_max_length 	= $yooRecipeparams->get('blog_description_max_length', 200);

$recipe 					= $displayData['recipe'];
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
$blog_show_seasons 			= $displayData['blog_show_seasons'];
$blog_show_pending_recipes 	= $displayData['blog_show_pending_recipes'];

$cross_categories_layout = new JLayoutFile('cross_categories', $basePath = JPATH_ROOT .'/components/com_yoorecipe/layouts');

$user 			= JFactory::getUser();
$picture_path	= JHtml::_('imageutils.getPicturePath', $recipe->picture);
$url 			= JRoute::_(JHtml::_('YooRecipeHelperRoute.getreciperoute', $recipe->slug, $recipe->catslug) , false);

$html = array();

$html[] = '<div id="div_recipe_'.$recipe->id.'">';

if ($recipe->featured) {
	$html[] = '<div style="position:relative"><img class="featured-item" src="'.JURI::root().'media/com_yoorecipe/images/featured-item.png" alt="'.JText::_('JFEATURED').'"></div>';
}

if ($blog_is_picture_clickable) {
	$html[] = '<a href="'.$url.'"><img class="thumbnail" src="'.JURI::root().$picture_path.'" alt="'.htmlspecialchars($recipe->title).'" title="'.htmlspecialchars($recipe->title).'" /></a>';
} else {
	$html[] = '<img class="thumbnail" src="'.JURI::root().$picture_path.'" alt="'.htmlspecialchars($recipe->title).'" title="'.htmlspecialchars($recipe->title).'" />';
}

if ($blog_show_title) {
	$html[] = '<h3><a href="'.$url.'">'.$recipe->title.'</a></h3>';
}
	
if ($blog_show_rating) {
	
	$html[] = '<div class="recipe-rating">';
	if ($recipe->note == null) {
		$recipe->note = 0;
	}

	if ($blog_rating_style == 'grade') {
		$html[] = '<strong>'.JText::_('COM_YOORECIPE_RECIPE_NOTE').': </strong><span> '.$recipe->note.'/5</span>'; 
	}
	else if ($blog_rating_style == 'stars') {
	
		$width = ($recipe->note*113)/5;				
		$html[] = '<div class="rec-detail-wrapper span12">';
		$html[] = '<div class="rating-stars stars113x20 fl-left">';
		$html[] = '<div style="width:'.$width.'px;" class="rating-stars-grad"></div>';
		$html[] = '<div class="rating-stars-img">';
		$html[] = '<span class="rating hide">'.$recipe->note.'</span>';
		$html[] = '</div>';
		$html[] = '</div>';
		$html[] = '</div>';
		
	}
	$html[] = '</div>';
}

$html[] = '<div class="caption">';
if ($blog_show_description && !empty($recipe->description)) {

	$html[] = '<div class="recipe-desc">';
	if (($blog_description_max_length > 0 && strlen($recipe->description) > $blog_description_max_length)) {
		$html[] = JHTMLYooRecipeUtils::htmlCut($recipe->description, $blog_description_max_length);
	} else {
		$html[] = $recipe->description;
	}
	$html[] = '</div>';
}

if ($blog_show_pending_recipes) {

	if ($recipe->published_up != 1 || $recipe->published_down != 0) {
		$html[] = '<img src="media/com_yoorecipe/images/pending.png" alt="'.htmlspecialchars($recipe->title).'" title="'.JText::_('COM_YOORECIPE_EXPIRED').'"/>';
	} else if (!$recipe->validated) {
		$html[] = '<img src="media/com_yoorecipe/images/pending.png" alt="'.htmlspecialchars($recipe->title).'" title="'.JText::_('COM_YOORECIPE_PENDING_APPROVAL').'"/>';
	}
}

$html[] = '<div class="row-fluid">';
if (!$user->guest && $yooRecipeparams->get('use_favourites', 1) == 1 ) {
	$html[] = '<div class="span2" id="fav_'.$recipe->id.'">'.JHtml::_('yoorecipeicon.favourites',  $recipe, $yooRecipeparams).'</div>';
}	

if (!$user->guest && $yooRecipeparams->get('use_mealplanner', 1)) {
	$html[] = '<div id="mealplanner_'.$recipe->id.'">';
	$html[] = ($recipe->is_queued) ? '<span class="label label-success" onclick="removeRecipeFromQueue('.$recipe->id.');">'.JText::_('COM_YOORECIPE_ACTION_DEQUEUE').'</span>' : '<span class="label label-info" onclick="addRecipeToQueue('.$recipe->id.');">'.JText::_('COM_YOORECIPE_ACTION_QUEUE').'</span>';
	$html[] = '</div>';
}
$html[] = '</div>';

if ($blog_show_creation_date) {
	$html[] = '<div class="recipe-creation-date">';
	$html[] = JText::_('COM_YOORECIPE_RECIPES_ADDED_ON').' '.JHTML::_('date', $recipe->creation_date);
	$html[] = '</div>';
}

if ($blog_show_author) {
	$authorUrl = JRoute::_(JHtml::_('YooRecipeHelperRoute.getuserroute', $recipe->created_by) , false);
	$html[] = '<div class="recipe-author">';
	$html[] = JText::_('COM_YOORECIPE_BY').' '.'<a href="'.$authorUrl.'">'.$recipe->author_name.'</a>';
	$html[] = '</div>';
}

if ($blog_show_nb_views) {
	$html[] = '<div class="recipe-nbviews">';
	$html[] = $recipe->nb_views.' '.JText::_('COM_YOORECIPE_RECIPES_READ_TIMES');
	$html[] = '</div>';
}

if ($blog_show_category_title) {
	
	$display_data = array();
	$display_data['recipe'] 		= $recipe;
	$display_data['do_row_fluid'] 	= false;
	$html[] = $cross_categories_layout->render($display_data);
	
	$html[] = '<br/>';
}

if ($blog_show_cuisine && !empty($recipe->cuisine)) {
	$html[] = '<a href="'.JRoute::_('index.php?option=com_yoorecipe&view=recipes&layout=cuisine&cuisine='.$recipe->cuisine).'" title="'.htmlspecialchars($recipe->cuisine).'"><span class="label label-info">'.JText::_('COM_YOORECIPE_CUISINE_'.$recipe->cuisine).'</span></a>';
}

if ($blog_show_seasons) {
	$html[] = JHtml::_('yoorecipeutils.generateRecipeSeason', $recipe->seasons, $do_row_fluid = false);
}

if ($blog_show_ingredients) {
	$html[] = JHtml::_('yoorecipeutils.generateIngredientsList', $recipe);
}

if ($blog_show_preparation_time && $recipe->preparation_time != 0) {
	$html[] = '<div class="recipe-preptime">'.JText::_('COM_YOORECIPE_YOORECIPE_PREPARATION_TIME_LABEL').': '.JHtml::_('datetimeutils.formattime', $recipe->preparation_time).'</div>';
}

if ($blog_show_cook_time && $recipe->cook_time != 0) {
	$html[] = '<div class="recipe-cooktime">'.JText::_('COM_YOORECIPE_RECIPES_COOK_TIME').': '.JHtml::_('datetimeutils.formattime', $recipe->cook_time).'</div>';
}

if ($blog_show_wait_time && $recipe->wait_time != 0) {
	$html[] = '<div class="recipe-waittime">'.JText::_('COM_YOORECIPE_RECIPES_WAIT_TIME').': '.JHtml::_('datetimeutils.formattime', $recipe->wait_time).'</div>';		
}

if ($blog_show_difficulty) {
	
	$html[] = '<div class="recipe-difficulty">'.JText::_('COM_YOORECIPE_RECIPES_DIFFICULTY').': ';
	$html[] = '<span class="label label-warning">';
		switch($recipe->difficulty){
		case 1:
			$html[] = JText::_('COM_YOORECIPE_YOORECIPE_SUPER_EASY_LABEL');
			break;
		case 2:
			$html[] = JText::_('COM_YOORECIPE_YOORECIPE_EASY_LABEL');
			break;
		case 3:
			$html[] = JText::_('COM_YOORECIPE_YOORECIPE_MEDIUM_LABEL');
			break;
		case 4:
			$html[] = JText::_('COM_YOORECIPE_YOORECIPE_HARD_LABEL');
			break;
		}
	$html[] = '</span>';
	$html[] = '</div>';
}

if ($blog_show_cost) {
	$html[] = '<div class="recipe-cost">'.JText::_('COM_YOORECIPE_RECIPES_COST').': ';
	$html[] =  '<span class="label label-warning">';
	
	switch($recipe->cost){
	case 1:
		$html[] =  JText::_('COM_YOORECIPE_YOORECIPE_CHEAP_LABEL');
		break;
	case 2:
		$html[] =  JText::_('COM_YOORECIPE_YOORECIPE_INTERMEDIATE_LABEL');
		break;
	case 3:
		$html[] =  JText::_('COM_YOORECIPE_YOORECIPE_EXPENSIVE_LABEL');
		break;
	}
	$html[] =  '</span>';
	$html[] = '</div>';
}

$html[] = '<div class="recipe-btns">';
$html[] = JHtml::_('yoorecipeutils.generateManagementPanel', $recipe);
$html[] = '</div>';

if ($blog_show_readmore) {
	$html[] = '<div class="recipe-readmore">';
	$html[] = '<a href="'.$url.'" title="'.JText::_('COM_YOORECIPE_VIEW_DETAILS').'">'.JText::_('COM_YOORECIPE_VIEW_DETAILS').'</a>&nbsp;|&nbsp;';
	$html[] = '<span><a href="'.$url.'#reviews'.'" title="'.JText::_('COM_YOORECIPE_REVIEW_RECIPE').'">'.JText::_('COM_YOORECIPE_REVIEW_RECIPE').'</a></span>';
	$html[] = '</div>';
}
$html[] = '	</div>';
$html[] = '	</div><!--caption-->';
$html[] = '	<hr/>';

echo implode("\n", $html);
?>