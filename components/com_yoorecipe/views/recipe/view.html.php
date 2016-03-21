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
class YooRecipeViewRecipe extends JViewLegacy
{
	// Overwriting JViewLegacy display method
	function display($tpl = null) 
	{
		// Init variables
		$app		= JFactory::getApplication();
		$menu 		= $app->getMenu();
		$params 	= JComponentHelper::getParams('com_yoorecipe');	
		$active 	= $menu->getActive();
		$user 		= JFactory::getUser();
		
		$shoppingListsModel = JModelLegacy::getInstance('shoppinglists','YooRecipeModel');
		$shoppinglists		= $shoppingListsModel->getShoppingListsByUserId($user->id);
				
		// Assign data to view
		$this->shoppinglists = $shoppinglists;
		
		// Get models
		$mainModel 				= JModelLegacy::getInstance('yoorecipe','YooRecipeModel');
		$reviewModel 			= JModelLegacy::getInstance('review','YooRecipeModel');
		$categoriesModel		= JModelLegacy::getInstance('categories','YooRecipeModel');
		$mealPlannerQueueModel 	= JModelLegacy::getInstance('mealplannerqueue','YooRecipeModel');
		
		// Get recipe identifier to view
		$input 		= JFactory::getApplication()->input;
		$recipe_id 	= $input->get('id', 0, 'INT');
	
		// Get recipe
		$recipe  = $mainModel->getRecipeById($recipe_id, $config = array('ingredients' => 1, 'categories' => 1, 'seasons' => 1, 'ratings' => 1));

		//--- Xander@OGOSense Recipe image batch modification ---//
		if(!$recipe->picture){
			if(!JFile::exists('/images/com_yoorecipe/recipes/recipe'.$recipe->id.'.jpg')){
				$recipe->picture = 'images/com_yoorecipe/recipes/recipe'.$recipe->id.'.jpg';
			}
		}
		
		if (isset($recipe)) {
		
			if (!$recipe->validated) {
				JError::raiseNotice(100, JText::_('COM_YOORECIPE_AWAITING_VALIDATION'));
			}
		
			// Optionally prepare content using Joomla Content Plugins
			if ($params->def('prepare_content', 1) && isset($recipe))
			{
				JPluginHelper::importPlugin('content');
				
				$dispatcher		= JDispatcher::getInstance();
				$recipe->text 	= $recipe->preparation;
				
				$dispatcher->trigger('onContentPrepare', array ('com_yoorecipe.recipe', &$recipe, &$params, $page = 0));
				$recipe->preparation = $recipe->text;
			}
			
			// Increment counter of views
			if (isset($recipe->published) && isset($recipe->validated)) {
				$mainModel->incrementViewCountOfRecipe($recipe_id, $recipe->nb_views);
			}
			
			// Calculate user rights on edition
			$this->is_viewing_own_recipe = $recipe->created_by == $user->id;
			$this->canEdit = 	$user->authorise('core.admin', 'com_yoorecipe') || 
								$user->authorise('core.edit', 'com_yoorecipe') ||
								($user->authorise('core.edit.own', 'com_yoorecipe') && $this->is_viewing_own_recipe);
								
			$this->canShow 				= ($recipe->published && $recipe->validated) || $this->is_viewing_own_recipe || $user->authorise('core.admin', 'com_yoorecipe');
			$this->canManageComments 	= $user->authorise('core.admin', 'com_yoorecipe') || $user->authorise('recipe.comments.edit.own', 'com_yoorecipe');
			$this->hide_comment_form 	= ($params->get('limit_reviews', 1) && !$user->guest && $reviewModel->hasUserAlreadyCommentedRecipe($recipe->id, $user->id)) ? true : false;
			
			// Get recipe details
			$recipe->categories = $categoriesModel->getRecipeCategories($recipe_id);
			$recipe->quantities = JHtml::_('ingredientutils.getJsonQuantities', $recipe->nb_persons, $recipe);
			
			// Get tags
			$recipe->tags = new JHelperTags;
			$recipe->tags->getItemTags('com_yoorecipe.recipe' , $recipe->id);
			
			// Get recipe queue status if needed
			if ($params->get('use_mealplanner', 1)) {
				$recipe->is_queued	= $mealPlannerQueueModel->isRecipeQueued($recipe->id, $user->id);
			}
			
			// Get similar recipes if needed
			if ($params->get('use_similar_recipes', 1)) {
				$recipe->similar_recipes = $mainModel->getSimilarRecipes($recipe);
			}
			
			// Add breadcrumbs
			$app = JFactory::getApplication();
			$pathway = $app->getPathway();
			$pathway->addItem($recipe->title, JUri::current());
		}
		
		// In case recipe not found, get categories instead
		$this->categories 	= $categoriesModel->getAllPublishedCategories();
		$this->user			= $user;
		
		// Set Params defined in menu (if applicable)
		$this->menuParams = (isset($active)) ? $active->params : new JRegistry();
		
		// Assign data to view
		$this->recipe = $recipe;
		//die('<pre>'.print_r($this->recipe, true).'</pre>');
		
		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->menuParams->get('pageclass_sfx'));
		
		$this->_prepareDocument();
		
		// Display the view
		parent::display($tpl);
	}
	
	/**
	 * Prepare document
	 */
	protected function _prepareDocument() {
		
		$app = JFactory::getApplication();
		
		if (isset($this->recipe)) {
		
			$title = JText::_('COM_YOORECIPE_RECIPE').' '.$this->recipe->title;
			$this->document->setTitle($title);
			
			// Set meta description
			if ($this->recipe->description != '') {
				$this->document->setDescription(strip_tags($this->recipe->description));
			}
			
			// Set meta robots
			if ($this->recipe->metadata) {
				$this->document->setMetadata('robots',$this->recipe->metadata);
			} else {
			
				$menus	= $app->getMenu();
				$menu 	= $menus->getActive();
				if ($menu) {
					$menuParams = $menu->params;
					$this->document->setMetadata('keywords', $menuParams->get('menu-meta_keywords'));
				}
			}
			
			// Set meta keywords
			if ($this->recipe->metakey) {
				$this->document->setMetadata('keywords', $this->recipe->metakey);
			}
			
			// Set canonical url
			$uri = JURI::getInstance();
			$canonicalUrl = $uri->getScheme().'://'.$_SERVER['SERVER_NAME'].JRoute::_(JHtml::_('YooRecipeHelperRoute.getreciperoute', $this->recipe->slug, $this->recipe->catslug) , false);
			$this->document->addCustomTag('<link rel="canonical" href="'.$canonicalUrl.'"/>');
		
		} // End if (isset($this->recipe)) {

		JText::script('COM_YOORECIPE_ERROR_FORBIDDEN_DELETE_OPERATION');
		JText::script('COM_YOORECIPE_REVIEW_NOT_ADDED');
		JText::script('COM_YOORECIPE_ERROR_OCCURED');
	}
}