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
class YooRecipeModelMealPlannerQueue extends JModelAdmin
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
	public function getTable($type = 'MealPlannerQueue', $prefix = 'YooRecipeTable', $config = array()) 
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
		$form = $this->loadForm('com_yoorecipe.mealplannerqueue', 'MealPlannerQueue', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_yoorecipe.edit.yoorecipe_mealplanner.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
		}
		return $data;
	}	
	
	/**
	* getMealPlannerQueueById
	*/
	public function getMealPlannerQueueByUserId($user_id)
	{
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('id, user_id, recipe_id');
		$query->from('#__yoorecipe_mealplanners_queues');
		
		$query->where('user_id = '.$db->quote($user_id));
		$db->setQuery((string)$query);
		return $db->loadObjectList();
	}
	
	/**
	* getQueuedRecipesByUserId
	*/
	public function getQueuedRecipesByUserId($user_id) {
	
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('r.id, r.asset_id, r.access, r.category_id, r.created_by, r.user_id, r.title, r.alias, r.description, r.preparation');
		$query->select('r.nb_persons, r.difficulty, r.cost, r.sugar, r.carbs, r.fat, r.saturated_fat, r.cholesterol, r.proteins');
		$query->select('r.fibers, r.salt, r.kcal, r.kjoule, r.diet, r.veggie, r.gluten_free, r.lactose_free, r.creation_date, r.checked_out, r.checked_out_time');
		$query->select('r.publish_up, r.publish_down, r.preparation_time, r.serving_type_id, st.code as servings_type_code');
		$query->select('r.cook_time, r.wait_time, r.featured, r.picture, r.video, r.published, r.validated, r.nb_views, r.note, r.metakey, r.metadata, r.price, r.language');
		
		$query->from('#__yoorecipe r');
		
		// Join over recipe queues
		$query->join('LEFT', '#__yoorecipe_mealplanners_queues q on q.recipe_id = r.id');
		
		// Join over serving types
		$query->join('LEFT', '#__yoorecipe_serving_types st on st.id = r.serving_type_id');
		
		// Filter
		$query->where('q.user_id = '.$db->quote($user_id));
		
		$db->setQuery((string)$query);
		return $db->loadObjectList();
	}
	
	/**
	* getQueuedRecipesBySearchWord
	*/
	public function getQueuedRecipesBySearchWord($search_word, $user_id)
	{
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('r.id, r.asset_id, r.access, r.category_id, r.created_by, r.user_id, r.title, r.alias, r.description, r.preparation');
		$query->select('r.nb_persons, r.difficulty, r.cost, r.sugar, r.carbs, r.fat, r.saturated_fat, r.cholesterol, r.proteins');
		$query->select('r.fibers, r.salt, r.kcal, r.kjoule, r.diet, r.veggie, r.gluten_free, r.lactose_free, r.creation_date, r.checked_out, r.checked_out_time');
		$query->select('r.publish_up, r.publish_down, r.preparation_time, r.serving_type_id, st.code as servings_type_code');
		$query->select('r.cook_time, r.wait_time, r.featured, r.picture, r.video, r.published, r.validated, r.nb_views, r.note, r.metakey, r.metadata, r.price, r.language');
		
		$query->from('#__yoorecipe r');
		
		// Join over recipe queues
		$query->join('LEFT', '#__yoorecipe_mealplanners_queues q on q.recipe_id = r.id');
		
		// Join over serving types
		$query->join('LEFT', '#__yoorecipe_serving_types st on st.id = r.serving_type_id');
		
		// Filter
		$query->where('q.user_id = '.$db->quote($user_id));
		$query->where('r.title like '.$db->quote('%'.$search_word.'%'));
		
		$db->setQuery((string)$query);
		return $db->loadObjectList();
	}
	
	/**
	* deleteMealPlannerQueueById
	*/
	public function deleteMealPlannerQueueById($id)
	{
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->delete('#__yoorecipe_mealplanners_queues');
		$query->where('id = '.$db->quote($id));
		
		$db->setQuery((string)$query);
		return $db->execute();
	}	
	
	/**
	* deleteMealPlannerQueueByRecipeIdAndUserId
	*/
	public function deleteMealPlannerQueueByRecipeIdAndUserId($recipe_id, $user_id)
	{
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->delete('#__yoorecipe_mealplanners_queues');
		$query->where('recipe_id = '.$db->quote($recipe_id));
		$query->where('user_id = '.$db->quote($user_id));
		
		$db->setQuery((string)$query);
		return $db->execute();
	}

	/**
	* createMealPlannerQueue
	*/
	public function createMealPlannerQueue($user_id, $recipe_id)
	{
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->insert('#__yoorecipe_mealplanners_queues');
		
		$query->set('user_id = '.$db->quote($user_id));
		$query->set('recipe_id = '.$db->quote($recipe_id));
		
		$db->setQuery((string)$query);
		$result = $db->execute();
		if ($result !== false) {
			return $db->insertid();
		} else {
			return false;
		}
	}	

	/**
	* updateMealPlannerQueueObj
	*/
	public function updateMealPlannerQueueObj($mealplannerqueueObj)
	{
		// Create a new query object
		$db = JFactory::getDBO();
		return $db->updateObject('#__yoorecipe_mealplanners_queues', $mealplannerqueueObj, 'id', true);
	}
	
	/**
	* isRecipeQueued
	*/
	public function isRecipeQueued($recipe_id, $user_id) {
	
		// Create a new query object
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		// Select the required fields from the table.
		$query->select('count(id)');
		$query->from('#__yoorecipe_mealplanners_queues');
		
		$query->where('recipe_id = '.$db->quote($recipe_id));
		$query->where('user_id = '.$db->quote($user_id));
		
		$db->setQuery((string)$query);
		return $db->loadResult() > 0 ? true : false;
	}
	
	/**
	 * insertMealPlannerQueueObj
	 */
	public function insertMealplannerQueueObj($mealplannerQueueObj) {
	
		// Create a new query object
		$db = JFactory::getDBO();
		$result = $db->insertObject('#__yoorecipe_mealplanners_queues', $mealplannerQueueObj, 'id');
		
		if ($result === false) {
			return false;
		} else {
			return $db->insertid();
		}
	}

	/**
	 * getAllRecipeMealplannersQueues
	 */
	public function	getAllRecipeMealplannersQueues() {	
	
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// From the recipe mealplanners queues table
		$query->select('*');
		$query->from('#__yoorecipe_mealplanners_queues');
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	
	/**
	* 	truncateMealplannersQueues
	*/
	public function truncateMealplannersQueues() {
	
		$db		= JFactory::getDBO();
		$query	= "TRUNCATE `#__yoorecipe_mealplanners_queues`;";
		$db->setQuery($query);
		return $db->execute();
	}
}