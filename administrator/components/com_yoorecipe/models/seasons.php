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
class YooRecipeModelSeasons extends JModelList
{	
	/**
	 * getRecipeSeasonsIds
	 */
	public function getRecipeSeasonsIds($recipe_id) {
	
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// From the recipe table
		$query->from('#__yoorecipe_seasons');
		$query->select('month_id');
		$query->where('recipe_id = '.$db->quote($recipe_id));
		
		$db->setQuery($query);
		return $db->loadColumn();
	}
	
	public function saveRecipeSeasons($recipe) {
	
		// Save multiple seasons
		$seasonsids = $recipe->seasons;
		$db 		= JFactory::getDBO();
		$query		= $db->getQuery(true);

		// Remove categories affectation
		$query->delete('#__yoorecipe_seasons');
		$query->where('recipe_id = '.$db->quote($recipe->id));
		$db->setQuery($query);
		$result[] = $db->execute();
			
		// Insert seasons
		if (count($seasonsids) > 0) {
			foreach($seasonsids as $seasonid) {
			
				$query->clear();
				$query->insert('#__yoorecipe_seasons');
				$query->set('recipe_id = '.$db->quote($recipe->id));
				$query->set('month_id = '.$db->quote($seasonid));
				
				$db->setQuery($query);
				$result[] = $db->execute();
			}
		}
		
		return !in_array(false, $result);
	}
	
	/**
	 * getAllRecipeSeasons
	 */
	public function	getAllRecipeSeasons() {	
	
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// From the recipe seasons table
		$query->select('*');
		$query->from('#__yoorecipe_seasons');
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	* deleteSeasonsByRecipeId
	*/
	public function deleteSeasonsByRecipeId($recipe_id) {
	
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->delete('#__yoorecipe_seasons');
		$query->where('recipe_id = '.$db->quote($recipe_id));
		$db->setQuery($query);
		return $db->execute();
	}
	
	
	/**
	* truncateYoorecipeSeasons
	*/
	public function truncateYoorecipeSeasons() {
	
		$db		= JFactory::getDBO();
		$query	= "TRUNCATE `#__yoorecipe_seasons`;";
		$db->setQuery($query);
		return $db->execute();
	}
}