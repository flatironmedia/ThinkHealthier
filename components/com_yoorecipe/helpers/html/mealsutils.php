<?php
/*------------------------------------------------------------------------
# com_yoorecipe -  YooRecipe! Joomla 2.5 & 3.x recipe component
# ------------------------------------------------------------------------
# author    YooRock!
# copyright Copyright (C) 2012 yoorock.fr. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.yoorecipe.com
# Technical Support:  Forum - http://www.yoorecipe.com/
-------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

abstract class JHtmlMealsUtils
{
	/**
		* generateRecipeQueueItem
	*/
	private static function generateRecipeQueueItem($recipe) {
			
		$html = array();
		
		// Take care of picture
		$picture_path = JHtml::_('imageutils.getPicturePath', $recipe->picture);
		$html[] = '<div class="draggable recipe-queue" id="recipe_queue_'.$recipe->id.'" data-type="recipebox_item" data-recipe_id="'.$recipe->id.'" data-nb_servings="'.$recipe->nb_persons.'">';
		$html[] = '<input type="hidden" id="queue_servings_'.$recipe->id.'" value="'.$recipe->nb_persons.'"/>';

		if ($picture_path !== false) {
			$html[] = '<div class="recipe-img">';
			$html[] = '<img src="'.JURI::base(true).'/'.$picture_path.'" alt="'.htmlspecialchars($recipe->title).'" class="nomove"/>';
			$html[] = '<a href="#" onclick="removeRecipeFromQueue('.$recipe->id.');return false;"><span class="delete-recipe" >&times;</span></a>';
			$html[] = '</div>';
		}
		
		$html[] = '<div class="recipe-details">';
		$html[] = '<strong>'.$recipe->title.'</strong>';
		$html[] = '</div>';
		
		$html[] = '</div>';		
		return implode("\n", $html);
	}
	
	/**
	* generateRecipeQueueItems
	*/
	public static function generateRecipeQueueItems($recipes) {
	
		$html = array();
		$html[] = JText::sprintf('COM_YOORECIPE_NB_RESULTS',count($recipes));
		$html[] = '<div class="clearfix"></div>';
		
		foreach($recipes as $i => $recipe){
			$html[] = self::generateRecipeQueueItem($recipe);
		}

		return implode("\n", $html);
	}
	
	/**
	 * generateMealEntryHTML
	 */
	public static function generateMealEntryHTML($meal_id, $recipe_id, $recipe_title, $recipe_alias, $servings_type_code, $nb_servings, $date, $picture) {
		
		$html = array();

		$picture_path = JHtml::_('imageutils.getPicturePath', $picture);
		$html[] = '<div id="mp_id_'.$meal_id.'" 
			class="planned-meal mp-'.$date.' draggable"
			data-meal_id="'.$meal_id.'"
			data-recipe_id="'.$recipe_id.'"
			data-date="'.$date.'"
			>';
		$html[] = '<div class="recipe-img">';
		$html[] = '<img src="'.JURI::base(true).'/'.$picture_path.'" alt="'.htmlspecialchars($recipe_title).'" class="nomove"/>';
		$html[] = '<a href="#" onclick="deleteMeal('.$meal_id.');return false;"><span class="delete-recipe" >&times;</span></a>';
		$html[] = '</div>';
		$html[] = '<div class="recipe-details">';
		$html[] = '<strong>'.$recipe_title.'</strong>';
		$html[] = '<br/>';
		$html[] = '<a href="#" onclick="updateMealServings('.$meal_id.',-1, this);return false;"><span class="btn-minus"><i class="icon-minus"></i></span></a>';
		$html[] = '<small><span id="mp_servings_'.$meal_id.'">'.$nb_servings.'</span> '.JText::_('COM_YOORECIPE_SERVING_TYPE_'.$servings_type_code).'</small>';
		$html[] = '<a href="#" onclick="updateMealServings('.$meal_id.',1, this);return false;"><span class="btn-plus"><i class="icon-plus"></i></span></a>';
		$html[] = '</div>';
		$html[] = '<div class="clearfix"></div>';
		$html[] = '<br>';
		$html[] = '</div>';

		return implode("\n",$html);
	}
	
	/**
	* generateMealEntryPrintoutHTML
	*/
	public static function generateMealEntryPrintoutHTML($meal_id, $recipe_id, $recipe_title, $recipe_alias, $servings_type_code, $nb_servings, $dow, $picture) {
		
		$html = array();
		
		$picture_path = JHtml::_('imageutils.getPicturePath', $picture);
		$html[] = '<div class="span3">';
		$html[] = '<div class="thumbnail"><img src="'.$picture_path.'" alt="'.htmlspecialchars($recipe_title).'" class="nomove"/>';
		$html[] = '<h4><a href="'.JRoute::_(JHtml::_('YooRecipeHelperRoute.getreciperoute', $recipe_id.':'.$recipe_alias), false).'" target="_blank">'.$recipe_title.'</a></h4>';
		$html[] = '<h4><span id="mp_servings_'.$meal_id.'">'.$nb_servings.'</span> '.JText::_('COM_YOORECIPE_SERVING_TYPE_'.$servings_type_code).'</h4>';
		$html[] = '</div>';
		$html[] = '</div>';
		
		return implode("\n",$html);
	}
	
	/**
	* buildMealsObject
	*/
	public static function buildMealsObject($start_date_str, $nb_days, $meals) {
	
		$days_of_week = array();
		
		$start_date_obj = JFactory::getDate($start_date_str);
		for ($i = 0; $i < $nb_days ; $i++) {
			$day_of_week = new stdclass;
			$day_of_week->dow 		= strtolower($start_date_obj->format('D'));
			$day_of_week->label 	= $start_date_obj->format('D d');
			$day_of_week->meals 	= array();
			
			$days_of_week[$start_date_obj->format('Y-m-d')] = $day_of_week;
			$start_date_obj->add(new DateInterval('P1D')); // +1 day
		}
		
		foreach ($meals as $meal) {
			$meal_date = JFactory::getDate($meal->meal_date);
			$days_of_week[$meal_date->format('Y-m-d')]->meals[] = $meal;
		}
		
		return $days_of_week;
	}
}