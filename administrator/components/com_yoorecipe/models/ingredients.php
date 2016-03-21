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
jimport('joomla.application.component.modellist');

/**
 * YooRecipeList Model
 */
class YooRecipeModelIngredients extends JModelList
{
	
	/**
	 * deleteIngredientsByRecipeId
	 */
	public function deleteIngredientsByRecipeId($recipe_id) {
	
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		// Delete recipe ingredients
		$query->delete('#__yoorecipe_ingredients');
		$query->where('recipe_id = '.$db->quote($recipe_id));
		
		$db->setQuery($query);
		return $db->execute();
	}
	
	/**
	 * deleteIngredientByRecipeAndIngredientId
	 */
	public function deleteIngredientByRecipeAndIngredientId($recipe_id, $ingredient_id)
	{
		// Prepare query
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->delete('#__yoorecipe_ingredients');
		$query->where('recipe_id = '.(int)$recipe_id);
		$query->where('id = '.(int)$ingredient_id);
		
		$db->setQuery((string)$query);
		return $db->execute();
	}
	
	/**
	 * Get ingregients of a given group
	 */
	public function getIngredientsByGroupId($group_id) {
	
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->select('i.id, i.recipe_id, i.ordering, i.quantity, i.unit, i.description, i.group_id, i.nutrition_id, i.standard_ingredient');
		$query->from('#__yoorecipe_ingredients i');
		$query->where('group_id = '.$group_id);
		$query->order('ordering asc');
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	 * getAllRecipeIngredients
	 */
	public function getAllRecipeIngredients() {
	
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// From the recipe ingredients table
		$query->select('*');
		$query->from('#__yoorecipe_ingredients');
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	* truncateYoorecipeIngredients
	*/
	public function truncateYoorecipeIngredients() {
	
		$db		= JFactory::getDBO();
		$query	= "TRUNCATE `#__yoorecipe_ingredients`;";
		
		$db->setQuery($query);
		return $db->execute();
	}


	//NUTRITIONIX API UPDATES BY XANDER
	/**
     * insertIngredientObj
     */
    public function insertIngredientObj($ingredientObj) {
        // Prepare query
        $db      = JFactory::getDBO();
        $session = JFactory::getSession();
        $nut_arr = $session->get('nutrition_values');
        $nut_val = $nut_arr[$ingredientObj->usda_ingredient_id];
        $this->updateRecipeNutrition($ingredientObj->quantity, $ingredientObj->recipe_id, $nut_val, $ingredientObj->unit, "insert");

        $result_insert                       = $db->insertObject('#__yoorecipe_ingredients', $ingredientObj, 'id');
        if($result_insert === false) {
            return false;
        }

        return $db->insertid();
    }

    public function updateRecipeNutrition($quantity, $recipe_id, $nutritions, $unit, $task = "insert") {
        $db       = JFactory::getDbo();
        $unit     = JText::_($unit);
        $task_fun = "+";

        if($task === "remove")
            $task_fun = "-";

        $query = "UPDATE `#__yoorecipe` SET " .
            "`kcal` = ROUND(`kcal`" . $task_fun . (($quantity * $nutritions['nf_calories'])) . ", 2)" .
            ", `fat` = ROUND(`fat`" . $task_fun . (($quantity * $nutritions['nf_total_fat'])) . ", 2)" .
            ", `saturated_fat` = ROUND(`saturated_fat`" . $task_fun . (($quantity * $nutritions['nf_saturated_fat'])) . ", 2)" .
            ", `proteins` = ROUND(`proteins`" . $task_fun . (($quantity * $nutritions['nf_protein'])) . ", 2)" .
            ", `carbs` = ROUND(`carbs`" . $task_fun . (($quantity * $nutritions['nf_total_carbohydrate'])) . ", 2)" .
            ", `sugar` = ROUND(`sugar`" . $task_fun . (($quantity * $nutritions['nf_sugars'])) . ", 2)" .
            ", `fibers` = ROUND(`fibers`" . $task_fun . (($quantity * $nutritions['nf_dietary_fiber'])) . ", 2)" .
            ", `cholesterol` = ROUND(`cholesterol`" . $task_fun . (($quantity * $nutritions['nf_cholesterol'])) . ", 2)" .
            ", `salt` = ROUND(`salt`" . $task_fun . (($quantity * $nutritions['nf_sodium'])) . ", 2)" .
            " WHERE `id` = " . $recipe_id;

        $db->setQuery($query);
        $db->execute();
    }

    public function addNutritions($recipe_id, $nutritions){
    	$db = JFactory::getDbo();

    	$query = $db->getQuery(true);

    	$query->select('serving_size, kcal, fat, saturated_fat, proteins, carbs, sugar, fibers, cholesterol, salt, kjoule')
    		->from($db->quoteName('#__yoorecipe'))
    		->where($db->quoteName('id').' = '.$recipe_id);
	    $db->setQuery($query);
	    $temp = $db->loadObject();
	    foreach ($temp as $key => $value) {
	    	$nutritions[$key] += $value;
	    }
	    return $nutritions;
    }
}