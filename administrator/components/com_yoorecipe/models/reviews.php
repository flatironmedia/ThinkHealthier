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
 
// import Joomla modelform library
jimport('joomla.application.component.modellist');
 
/**
 * YooRecipe Model
 */
class YooRecipeModelReviews extends JModelList
{
	/**
	 * getAllRecipeReviews
	 */
	public function	getAllRecipeReviews() {	
	
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// From the recipe rating table
		$query->select('*');
		$query->from('#__yoorecipe_reviews');
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	 * getReviewsByRecipeId
	 */
	public function getReviewsByRecipeId($recipe_id)
	{
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('r.id, r.recipe_id, r.note, r.author, r.user_id, r.comment, r.abuse, r.published, r.creation_date');

		// From the recipe rating table
		$query->from('#__yoorecipe_reviews r');
		
		// Join over the users for the author.
		$yooRecipeparams 	= JComponentHelper::getParams('com_yoorecipe');
		$showAuthorName 	= $yooRecipeparams->get('show_author_name', 'username');
		
		if ($showAuthorName == 'username') {
			$query->select('ua.username AS author_name');
		} else if ($showAuthorName == 'name') {
			$query->select('ua.name AS author_name');
		}
		$query->join('LEFT', '#__users ua ON ua.id = r.user_id');
		
		// Where
		$query->where('recipe_id = '.$db->quote($recipe_id));
		$query->order('creation_date desc');
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	 * getReviewsByRecipeIdOrderedByDateDesc
	 */
	public function getReviewsByRecipeIdOrderedByDateDesc($recipe_id, $published = null, $abuse = null) {
	
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		// Select some fields
		$query->select('r.id, r.recipe_id, r.note, r.author, r.user_id, r.comment, r.creation_date, r.published, r.abuse');

		// From the recipe rating table
		$query->from('#__yoorecipe_reviews r');
		
		if (!is_null($published)) {
			$query->where('r.published = '.$db->quote($published));
		}
		if (!is_null($abuse)) {
			$query->where('r.abuse = '.$db->quote($abuse));
		}
		
		// Join over the users for the author.
		$yooRecipeparams 	= JComponentHelper::getParams('com_yoorecipe');
		$showAuthorName 	= $yooRecipeparams->get('show_author_name', 'username');
		
		if ($showAuthorName == 'username') {
			$query->select('ua.username AS author_name');
		} else if ($showAuthorName == 'name') {
			$query->select('ua.name AS author_name');
		}
		$query->join('LEFT', '#__users ua ON ua.id = r.user_id');
		
		// Where
		$query->where('recipe_id = '.$db->quote($recipe_id));
		$query->order('creation_date desc');
		
		$db->setQuery((string) $query);
		return $db->loadObjectList();
	}
	
	/**
	* deleteReviewsByRecipeId
	*/
	public function deleteReviewsByRecipeId($recipe_id) {
		
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		// Delete cross categories
		$query->delete('#__yoorecipe_reviews');
		$query->where('recipe_id = '.$db->quote($recipe_id));
		$db->setQuery($query);
		return $db->execute();
	}
	
	
	/**
	* truncateReviews
	*/
	public function truncateReviews() {
	
		$db		= JFactory::getDBO();
		$query	= "TRUNCATE `#__yoorecipe_reviews`;";
		$db->setQuery($query);
		return $db->execute();
	}
}