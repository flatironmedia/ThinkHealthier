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
 
// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the YooRecipe Component
 */
class YooRecipeViewMeals extends JViewLegacy
{
	function display($tpl = null) 
	{
		// Check session
		$user 	= JFactory::getUser();
		if ($user->guest == 1) {
			$app = JFactory::getApplication();
			$app->redirect(JRoute::_('index.php?option=com_users&view=login&return='.base64_encode(JUri::getInstance()),false));
			return;
		}
		
		$input		= JFactory::getApplication()->input;
		$layout 	= $input->get('layout', '', 'STRING');
		
		if ($layout == 'printout') {
			//TODO ajouter form validation empecher impression avec rien
			$this->managePrintOutLayout();
		} else {
			$this->manageDefaultLayout();
		}
		
		// Breadcrumbs
		$app = JFactory::getApplication();
		$pathway = $app->getPathway();
		$pathway->addItem(JText::_('COM_YOORECIPE_MEALPLANNER'), JUri::current());
		
		// Prepare document
		$this->_prepareDocument();
		
		// Display the view
		parent::display($tpl);
	}
	
	/**
	* manageDefaultLayout
	*/
	private function manageDefaultLayout() {
	
		// Check session
		$user 			= JFactory::getUser();
		$state 			= $this->get('State');
		
		// Get models
		$mealPlannerQueueModel 	= JModelLegacy::getInstance('mealplannerqueue','YooRecipeModel');
		$mealsModel				= JModelLegacy::getInstance('meals', 'YooRecipeModel');
		
		$app	= JFactory::getApplication();
		$input 	= $app->input;
		$menus	= $app->getMenu();
		$title	= null;
		
		$task		= $input->get('task', '', 'STRING');
		$startdate	= $input->get('startdate', '', 'STRING');

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu 		= $menus->getActive();
		$nb_days	= 7;

		$start_date_obj = JFactory::getDate($startdate);
		$start_date_obj = JHtmlDateTimeUtils::getFirstDayOfWeek($start_date_obj);
		$end_date_obj	= JHtmlDateTimeUtils::getFirstDayOfWeek(JFactory::getDate($startdate))->add(new DateInterval('P'.($nb_days-1).'D'));
		
		switch ($task) {
			
			case 'previous_week':
				$start_date_obj->sub(new DateInterval('P'.$nb_days.'D'));
				$end_date_obj->sub(new DateInterval('P'.$nb_days.'D'));
			break;
			
			case 'next_week':
				$start_date_obj->add(new DateInterval('P'.$nb_days.'D'));
				$end_date_obj->add(new DateInterval('P'.$nb_days.'D'));
			break;
		}
		
		// Initialise variables.
		$start_date_str = $start_date_obj->format('Y-m-d');
		$end_date_str	= $end_date_obj->format('Y-m-d');		

		// Get data
		$queued_recipes 		= $mealPlannerQueueModel->getQueuedRecipesByUserId($user->id);
		$meals					= $mealsModel->getMealsByUserIdAndPeriod($user->id, JHtmlDateTimeUtils::getDate00h00m00s($start_date_obj), JHtmlDateTimeUtils::getDate23h59m59s($end_date_obj), $get_details = true);
		$days_of_week			= JHtml::_('mealsutils.buildMealsObject', $start_date_str, $nb_days, $meals);
		
		// Assign the Data
		$this->state			= $state;
		$this->nb_days 			= $nb_days;
		$this->queued_recipes 	= $queued_recipes;
		$this->days_of_week		= $days_of_week;
		$this->start_date_obj	= $start_date_obj;
		$this->end_date_obj		= $end_date_obj;
	}
	
	/**
	* managePrintOutLayout
	*/
	private function managePrintOutLayout() {
	
		// Check session
		$user 	= JFactory::getUser();
		
		// Get input variables
		$input		= JFactory::getApplication()->input;
		$layout 	= $input->get('layout', '', 'STRING');
		
		// Get models
		$mealsModel			= JModelLegacy::getInstance('meals', 'YooRecipeModel');
		$shoppingListModel 	= JModelLegacy::getInstance('shoppinglist','YooRecipeModel');
		
		// Get variables
		$print_start_date 	= $input->get('print_start_date', '', 'STRING');
		$print_end_date 	= $input->get('print_end_date', '', 'STRING');
		$shoppinglist_id	= $input->get('shoppinglist_id', 0, 'INT');
		$post 				= $input->get('post', array(), 'ARRAY');
		
		// Initialise variables.
		$start_date_obj = JFactory::getDate($print_start_date);
		$start_date_str = $start_date_obj->format('Y-m-d');
		$end_date_obj 	= JFactory::getDate($print_end_date);
		$nb_days		= JHtmlDateTimeUtils::getTimeIntervalInDaysBetween($start_date_obj, $end_date_obj);

		// Get data
		$meals = $mealsModel->getMealsByUserIdAndPeriod($user->id, JHtmlDateTimeUtils::getDate00h00m00s($start_date_obj), JHtmlDateTimeUtils::getDate23h59m59s($end_date_obj), $get_details = true);

		$this->days_of_week = JHtml::_('mealsutils.buildMealsObject', $start_date_str, $nb_days, $meals);
		
		$this->shopping_list = $shoppingListModel->getShoppingListById($shoppinglist_id, $get_details = true);
		
		// Assign the Data
		$this->print_start_date		= $start_date_str;
		$this->print_end_date		= $end_date_obj->format('Y-m-d');
	}
	
	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$title	= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu 		= $menus->getActive();
		if ($menu)
		{
			$menuParams = $menu->params;
			$menuParams->def('page_heading', $menuParams->get('page_title', $menu->title));
			$title = $menuParams->get('page_title', '');
			if (!empty($title)) {
				$this->document->setTitle($title);
			}
			
			if ($menuParams->get('menu-meta_description')) {
				$this->document->setDescription($menuParams->get('menu-meta_description'));
			}

			if ($menuParams->get('menu-meta_keywords')) {
				$this->document->setMetadata('keywords', $menuParams->get('menu-meta_keywords'));
			}
			
			if ($menuParams->get('robots')) {
				$this->document->setMetadata('robots', $menuParams->get('robots'));
			}
		} else {
			$this->document->setTitle(JText::_('COM_YOORECIPE_MEALPLANNER_TITLE'));
		}
		JText::script('COM_YOORECIPE_ERROR_FORBIDDEN_DELETE_OPERATION');
	}
}