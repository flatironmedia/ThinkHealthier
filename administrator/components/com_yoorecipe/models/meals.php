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
jimport('joomla.application.component.modellist');

/**
 * YooRecipe Model
 *
 * @package		Joomla.Site
 * @subpackage	com_content
 * @since 1.5
 */
class YooRecipeModelMeals extends JModelList
{
	
	/**
	* Constructor.
	*
	* @param	array	An optional associative array of configuration settings.
	* @see		JController
	* @since	1.6
	*/
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	* Method to auto-populate the model state.
	*
	* Note. Calling getState in this method will result in recursion.
	*
	* @return	void
	* @since	1.6
	*/
	protected function populateState($ordering = null, $direction = null)
	{
		// Adjust the context to support modal layouts.
		$input = JFactory::getApplication()->input;
		if ($layout = $input->get('layout', '', 'STRING')) {
			$this->context .= '.'.$layout;
		}

		$search_startdate 	= $this->getUserStateFromRequest($this->context.'.filter.search_startdate', 'filter_search_startdate');
		$search_enddate 	= $this->getUserStateFromRequest($this->context.'.filter.search_enddate', 'filter_search_enddate');
		$get_details 		= $this->getUserStateFromRequest($this->context.'.filter.get_details', 'filter_get_details');
		
		// Init state variables
		if (empty($search_startdate) || empty($search_enddate)) {
		
			/*$start_date_obj	= JFactory::getDate();
			$start_date_obj = JHtmlDateTimeUtils::getFirstDayOfWeek($start_date_obj);
			$this->setState('filter.startdate', JHtmlDateTimeUtils::getDate00h00m00s($start_date_obj));
			
			$end_date_obj = $start_date_obj->add(new DateInterval('P7D'));
			$this->setState('filter.enddate', JHtmlDateTimeUtils::getDate23h59m59s($end_date_obj));*/
			
			$search_startdate 	= JFactory::getDate();
			$this->setState('filter.search_startdate', $search_startdate->format('Y-m-d'));
			
			$search_enddate 	= $search_startdate->add(new DateInterval('P7D'));
			$this->setState('filter.search_enddate', $search_enddate->format('Y-m-d'));
		} 
		else {
		
			$this->setState('filter.search_startdate', $search_startdate);
			$this->setState('filter.search_enddate', $search_enddate);
		}
		
		if (empty($get_details)) {
			$this->setState('filter.get_details', true);
		}
		
		// List state information.
		parent::populateState('id', 'asc');
		
		// Remove pagination
		$this->setState('list.limit', 0);
	}
	
	/**
	* getListQuery
	*/
	protected function getListQuery() {
		
		// Create a new query object.           
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		// Select the required fields from the table.
		$query->select('mp.id as meal_id, mp.user_id, mp.meal_date, mp.nb_servings, mp.recipe_id');
		$query->from('#__yoorecipe_meals as mp');
		
		// Join over recipes
		if ($this->getState('filter.get_details')) {
		
			$query->select('r.title, r.alias, r.serving_type_id, st.code as servings_type_code, r.picture');
			$query->join('LEFT', '#__yoorecipe r on r.id = mp.recipe_id');
			$query->join('LEFT', '#__yoorecipe_serving_types st on st.id = r.serving_type_id');
		}
		
		$user = JFactory::getUser();
		
		$query->where('mp.user_id = '.$db->quote($user->id));
		$query->where('mp.meal_date >= '.$db->quote($this->getState('filter.search_startdate')));
		$query->where('mp.meal_date <= '.$db->quote($this->getState('filter.search_enddate')));
		
		$query->order('meal_date asc');
		
		return $query;
	}
	
	/**
	* getMealsByUserIdAndPeriod
	*/
	public function getMealsByUserIdAndPeriod($user_id, $start_date, $end_date, $get_details = true) {
	
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('mp.id as meal_id, mp.user_id, mp.meal_date, mp.nb_servings, mp.recipe_id');
		$query->from('#__yoorecipe_meals as mp');
		
		// Join over recipes
		if ($get_details) {
			$query->select('r.title, r.alias, r.serving_type_id, st.code as servings_type_code, r.picture');
			$query->join('LEFT', '#__yoorecipe r on r.id = mp.recipe_id');
			$query->join('LEFT', '#__yoorecipe_serving_types st on st.id = r.serving_type_id');
		}
		
		$query->where('mp.user_id = '.$db->quote($user_id));
		$query->where('mp.meal_date >= '.$db->quote($start_date));
		$query->where('mp.meal_date <= '.$db->quote($end_date));
		
		$query->order('meal_date asc');
		
		$db->setQuery((string)$query);
		return $db->loadObjectList();
	}
	
	/**
	 * getAllMeals
	 */
	public function	getAllMeals() {
	
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// From the recipe meals table
		$query->select('*');
		$query->from('#__yoorecipe_meals');
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	* truncateMeals
	*/
	public function truncateMeals() {
	
		$db		= JFactory::getDBO();
		$query	= "TRUNCATE `#__yoorecipe_meals`;";
		$db->setQuery($query);
		return $db->execute();
	}
}