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

// import the Joomla modellist library
jimport('joomla.application.component.modelitem');
/**
 * YooRecipeList Model
 */
class YooRecipeModelMaintenance extends JModelItem
{
	/**
	* deleteOldMealEntries
	*/
	public function deleteOldMealEntries($nb_days = 30)
	{
		// Create a new query object
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->delete('#__yoorecipe_meals');
		$query->where('meal_date < CURRENT_DATE - INTERVAL '.$nb_days.' DAY');		

		$db->setQuery((string)$query);
		return $db->execute();
	}
}