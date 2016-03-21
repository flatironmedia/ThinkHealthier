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

// No direct access
defined('_JEXEC') or die;

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * YooRecipe Model
 *
 * @package		Joomla.Site
 * @subpackage	com_content
 * @since 1.5
 */
class YooRecipeModelMeal extends JModelAdmin
{
	
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Meal', $prefix = 'YooRecipeTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	/**
	* Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true) 
	{
		// Get the form.
		$form = $this->loadForm('com_yoorecipe.meal', 'Meal', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) 
		{
			return false;
		}
		return $form;
	}
	
	/**
	* Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData() 
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_yoorecipe.edit.yoorecipe_meal.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
		}
		return $data;
	}
	
	/**
	* deleteMeal
	*/
	public function deleteMeal($id, $user_id)
	{
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->delete('#__yoorecipe_meals');
		$query->where('id = '.$db->quote($id));
		$query->where('user_id = '.$db->quote($user_id));
		
		$db->setQuery((string)$query);
		return $db->execute();
	}

	/**
	* insertMeal
	*/
	public function insertMeal($user_id, $meal_date, $nb_servings, $recipe_id)
	{
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->insert('#__yoorecipe_meals');
		
		$query->set('user_id = '.$db->quote($user_id));
		$query->set('meal_date = '.$db->quote($meal_date));
		$query->set('nb_servings = '.$db->quote($nb_servings));
		$query->set('recipe_id = '.$db->quote($recipe_id));
		
		$db->setQuery((string)$query);
		$result = $db->execute();
		if ($result === false) {
			return false;
		} else {
			return $db->insertid();
		}
	}	
	
	/**
	* updateMealObj
	*/
	public function updateMealObj($mealObj)
	{
		// Create a new query object
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		return $db->updateObject('#__yoorecipe_meals', $mealObj, 'id', true);
	}
	
	/**
	 * insertMealObj
	 */
	public function insertMealObj($mealObj) {
	
		// Create a new query object
		$db = JFactory::getDBO();
		$result = $db->insertObject('#__yoorecipe_meals', $mealObj, 'id');
		
		if ($result === false) {
			return false;
		} else {
			return $db->insertid();
		}
	}
	
	/**
	* updateMealServings
	*/
	public function updateMealServings($meal_id, $user_id, $nb_servings)
	{
		// Create a new query object
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->update('#__yoorecipe_meals');
		$query->set('nb_servings = '.$db->quote($nb_servings));
		
		$query->where('id = '.$db->quote($meal_id));
		$query->where('user_id = '.$db->quote($user_id));
		
		$db->setQuery((string)$query);
		return $db->execute();
	}
	
	/**
	* updateMealDate
	*/
	public function updateMealDate($meal_id, $recipe_id, $user_id, $meal_date)
	{
		// Create a new query object
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->update('#__yoorecipe_meals');
		$query->set('meal_date = '.$db->quote($meal_date));		
		
		$query->where('id = '.$db->quote($meal_id));
		$query->where('user_id = '.$db->quote($user_id));
		$query->where('recipe_id = '.$db->quote($recipe_id));
		
		$db->setQuery((string)$query);
		return $db->execute();
	}
}