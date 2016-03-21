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
class YooRecipeModelFavourites extends JModelList
{
	/**
	* deleteFavoritesByRecipeId
	*/
	public function deleteFavoritesByRecipeId($recipe_id, $user = null) {
	
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->delete('#__yoorecipe_favourites');
		$query->where('recipe_id = '.$db->quote($recipe_id));
		if ($user != null) {
			$query->where('user_id = '.$db->quote($user->id));
		}
		$db->setQuery($query);
		return $db->execute();
	}
	
	/**
	* deleteFromFavourites
	*/
	public function deleteFromFavourites($recipe_id, $user = null) {
	
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->delete('#__yoorecipe_favourites');
		$query->where('recipe_id = '.$db->quote($recipe_id));
		if ($user != null) {
			$query->where('user_id = '.$db->quote($user->id));
		}
		$db->setQuery($query);
		return $db->execute();
	}
	
	/**
	 * getAllRecipeFavourites
	 */
	public function getAllRecipeFavourites() {
	
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// From the recipe favourites table
		$query->select('*');
		$query->from('#__yoorecipe_favourites');
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	* 	truncateFavourites
	*/
	public function truncateFavourites() {
	
		$db		= JFactory::getDBO();
		$query	= "TRUNCATE `#__yoorecipe_favourites`;";
		$db->setQuery($query);
		return $db->execute();
	}

}