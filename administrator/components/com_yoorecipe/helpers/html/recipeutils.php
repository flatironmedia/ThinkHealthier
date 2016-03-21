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

abstract class JHtmlRecipeUtils
{
	/**
	 * Build a recipe object from data contained in post
	 */
	public static function buildRecipeFromRequest($params) {
	
		jimport( 'joomla.error.error' );
		
		// Get input
		$input 	= JFactory::getApplication()->input;
		$jform 	= $input->get('jform', array(), 'ARRAY');
		
		// FIRST TAB - DETAILS
		$prep_days 		= $input->get('prep_days', 0, 'INT');
		$prep_hours   	= $input->get('prep_hours', 0, 'INT');
		$prep_minutes 	= $input->get('prep_minutes', 0, 'INT');
		
		$cook_days		= $input->get('cook_days', 0, 'INT');
		$cook_hours		= $input->get('cook_hours', 0, 'INT');
		$cook_minutes	= $input->get('cook_minutes', 0, 'INT');
		
		$wait_days		= $input->get('wait_days', 0, 'INT');
		$wait_hours		= $input->get('wait_hours', 0, 'INT');
		$wait_minutes	= $input->get('wait_minutes', 0, 'INT');
		
		$created_by		= $input->get('created_by', 0, 'INT');
		
		// Init variables
		$recipe = new stdclass;
		$user = JFactory::getUser();
		
		$recipe->id					= $jform['id'];
		$recipe->title				= $jform['title'];
		$recipe->description		= $jform['description'];
		$recipe->alias 				= JFilterOutput::stringURLSafe($recipe->title);
		$recipe->category_id 		= $jform['category_id'];
		$recipe->preparation 		= $jform['preparation'];
		$recipe->nb_persons 		= isset($jform['nb_persons']) ? $jform['nb_persons'] : 4; // 4 is default value when creating new recipe
		$recipe->serving_type_id 	= isset($jform['serving_type_id']) ? $jform['serving_type_id'] : null;
		
		// Recipe fields
		$recipe->use_slider		= isset($jform['use_slider']) ? $jform['use_slider'] : 1;
		$recipe->price 			= isset($jform['price']) ? str_replace(",", ".", $jform['price']) : null;
		$recipe->difficulty 	= isset($jform['difficulty']) ? $jform['difficulty'] : null;
		$recipe->cost 			= isset($jform['cost']) ? $jform['cost'] : null;
		$recipe->seasons 		= isset($jform['seasons']) ? $jform['seasons'] : null;
		
		if (isset($prep_days) || isset($prep_hours) || isset($prep_minutes)) {
			$recipe->preparation_time = $prep_days * 1440 + $prep_hours * 60 + $prep_minutes; 
		}
		if (isset($cook_days) || isset($cook_hours) || isset($cook_minutes)) {
			$recipe->cook_time = $cook_days * 1440 + $cook_hours * 60 + $cook_minutes; 
		}
		if (isset($wait_days) || isset($wait_hours) || isset($wait_minutes)) {
			$recipe->wait_time = $wait_days * 1440 + $wait_hours * 60 + $wait_minutes; 
		}
		
		// 3rd TAB: PUBLISHING OPTIONS
		if (!empty($created_by)) {
			$recipe->created_by	= $created_by;
		} else {
			$recipe->created_by	= $user->id;
		}
		
		$recipe->access 		= isset($jform['access']) 		? $jform['access'] : null;
		$recipe->publish_up		= isset($jform['publish_up']) 	? $jform['publish_up'] : null;
		$recipe->publish_down 	= isset($jform['publish_down']) ? $jform['publish_down'] : null;
		$recipe->nb_views 		= isset($jform['nb_views']) 	? $jform['nb_views'] : null;
		
		// 4TH TAB: NUTRITION FACTS
		$recipe->serving_size	= isset($jform['serving_size']) ? $jform['serving_size'] : null;
		$recipe->diet 			= isset($jform['diet']) 		? $jform['diet'] : null;
		$recipe->gluten_free 	= isset($jform['gluten_free']) 	? $jform['gluten_free'] : null;
		$recipe->veggie			= isset($jform['veggie']) 		? $jform['veggie'] : null;
		$recipe->lactose_free 	= isset($jform['lactose_free']) ? $jform['lactose_free'] : null;

		$recipe->kcal 			= isset($jform['kcal']) 		? $jform['kcal'] : null;
		$recipe->kjoule 		= isset($jform['kjoule']) 		? $jform['kjoule'] : null;
		$recipe->sugar 			= isset($jform['sugar']) 		? str_replace(",", ".", $jform['sugar']) : null;
		$recipe->carbs 			= isset($jform['carbs']) 		? str_replace(",", ".", $jform['carbs']) : null;
		$recipe->fat 			= isset($jform['fat']) 			? str_replace(",", ".", $jform['fat']) : null;
		$recipe->saturated_fat 	= isset($jform['saturated_fat']) ? str_replace(",", ".", $jform['saturated_fat']) : null;
		$recipe->cholesterol 	= isset($jform['cholesterol']) ? str_replace(",", ".", $jform['cholesterol']) : null;
		$recipe->proteins 		= isset($jform['proteins']) 	? str_replace(",", ".", $jform['proteins']) : null;
		$recipe->fibers 		= isset($jform['fibers']) 		? str_replace(",", ".", $jform['fibers']) : null;
		$recipe->salt 			= isset($jform['salt']) 		? str_replace(",", ".", $jform['salt']) : null;

		// 5th TAB: SEO
		if (isset($jform['metakey'])) {
			$recipe->metakey =  $jform['metakey'];
		}
		if (isset($jform['metadata'])) {
			$recipe->metadata =  $jform['metadata'];
		}
		
		// Status parameters
		if ($params->get('auto_publish', 1)) {
			$recipe->published = 1;
		} else if (isset($jform['published'])) {
			$recipe->published = $jform['published'];
		}
		
		if ($params->get('auto_validate', 0)) {
			$recipe->validated = 1;
		} else if (isset($jform['validated'])) {
			$recipe->validated = $jform['validated'];
		} else {
			$recipe->validated = 0;
		}
		
		if (isset($jform['featured'])) {
			$recipe->featured = $jform['featured'];
		}
		
		if (isset($jform['language'])) {
			$recipe->language = $jform['language'];
		} else {
			$recipe->language = JFactory::getLanguage()->getTag();
		}
		
		// Retrieve tags
		// $recipe->tags = isset($jform['tags']) ? $jform['tags'] : null;
		
		// Check uploaded picture and video
		$picture = $input->get('picture', '', 'STRING');
		if ($input->get('picture', '', 'STRING') !== '1') {
			$recipe->picture = JHtml::_('imageutils.uploadRecipePicture','picture');
		}
		$recipe->video = isset($jform['video']) ? $jform['video'] : null;
		
		return $recipe;
	}
}