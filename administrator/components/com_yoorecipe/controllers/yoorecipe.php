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
 
// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * YooRecipe Controller
 */
class YooRecipeControllerYooRecipe extends JControllerForm
{
	/**
	* Cancel recipe edition
	*/
	function cancel ($cachable = false)
	{
		// set default view if not set
		$app = JFactory::getApplication();
		$app->redirect('index.php?option=com_yoorecipe&view=yoorecipes');
	}
	
	/**
	 * Function that allows child controller access to model data after the data has been saved.
	 *
	 * @param   JModelLegacy  $model      The data model object.
	 * @param   array         $validData  The validated data.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function postSaveHook(JModelLegacy $model, $validData = array())
	{
		$recordId = $model->getState($this->context . '.id');
		
		$is_new = ($validData['id'] == 0);
		
		// Initialise variables.
        $task = $this->getTask();
		
		$input 	= JFactory::getApplication()->input;
		$data 	= $input->get('jform', array(), 'ARRAY');
		$table 	= $model->getTable();
		
		// Get models
		$crossCategoryModel 	= JModelLegacy::getInstance('crossCategory', 'YooRecipeModel');
		$ingredientsGroupModel 	= JModelLegacy::getInstance('ingredientsgroup', 'YooRecipeModel');
		$ingredientsModel 		= JModelLegacy::getInstance('ingredients', 'YooRecipeModel');
		$ingredientModel 		= JModelLegacy::getInstance('ingredient', 'YooRecipeModel');
		$yoorecipeModel 		= JModelLegacy::getInstance('yoorecipe', 'YooRecipeModel');
		$seasonsModel			= JModelLegacy::getInstance('seasons', 'YooRecipeModel');
		$seasonModel			= JModelLegacy::getInstance('season', 'YooRecipeModel');
		
		// Save ingredient groups and ingredients
		$ingredient_groups	= JHTMLIngredientUtils::buildIngredientGroupsFromRequest($input, $recordId);
		$ingredients		= JHTMLIngredientUtils::buildIngredientsFromRequest($input, $recordId);
		
		$delete_groups = array();
		
		// Take care of ingredient groups
		$ingredient_groups_map = array();
		foreach ($ingredient_groups as $ingredient_group) {
		
			$ingredient_groups_map[$ingredient_group->index] = $ingredient_group;
			$action = ($task == 'save2copy') ? 'I' : $ingredient_group->action;

			unset($ingredient_group->action);
			unset($ingredient_group->index);
			
			switch ($action) {
				case 'I':
					unset($ingredient_group->id);
					$ingredient_group->id = $ingredientsGroupModel->insertYoorecipeIngredientsGroupsObj($ingredient_group);
				break;
				
				case 'U':
					$ingredientsGroupModel->updateYoorecipeIngredientsGroupsObj($ingredient_group);
				break;
				
				case 'D':
					$ingredientsGroupModel->deleteIngredientsGroup($ingredient_group->id);
					$delete_groups[] = $ingredient_group->id;
				break;
			}
		}
		
		// Take care of ingredients
		foreach ($ingredients as $ingredient) {
		
			$ingredient->group_id = $ingredient_groups_map[$ingredient->group_index]->id;
			unset($ingredient->group_index);
			
			$action = ($task == 'save2copy') ? 'I' : $ingredient->action;
			unset($ingredient->action);
			
			if (in_array($ingredient->group_id, $delete_groups)) {
				$action = 'D';
			}
			switch ($action) {
				case 'I':
					unset($ingredient->id);
					$ingredient->id = $ingredientModel->insertIngredientObj($ingredient);
				break;
				
				case 'U':
					$ingredientModel->updateIngredientObj($ingredient);
				break;
				
				case 'D':
					$ingredientsModel->deleteIngredientByRecipeAndIngredientId($recordId, $ingredient->id);
				break;
			}
		}
		
		// Save categories
		$crossCategoryModel->saveRecipeCategories($data['category_id'], $recordId);

		// Save multiple seasons
		$seasonsModel->deleteSeasonsByRecipeId($recordId);
		if (isset($data['seasons'])) {
			
			$seasonids = $data['seasons'];
			foreach($seasonids as $seasonid) {
				$seasonModel->insertSeason($recordId, $seasonid);
			}
		}
		
		return;
	}
}