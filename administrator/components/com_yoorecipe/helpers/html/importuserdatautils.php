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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.log.log');

/**
* JHtmlImportUserDataUtils
*/
abstract class JHtmlImportUserDataUtils {
	
	/**
	 * importUserDataFromFile
	 * 
	 * @param	Array		$_FILES
	 * @param	Int			Value : 1 => 'UPDATE', 2 => 'INSERT', 3 => 'TRUNCATE & INSERT'
	 * @param	Int			The user id
	 * 
	 * @return	Boolean		True if success
	 */
	public static function importUserDataFromFile($fileInfo, $mode) {
		
		
		JLog::addLogger(array('text_file' => 'com_yoorecipe.errors.php'), JLog::ALL, array('com_yoorecipe'));
		
		// Validate params
		if (!in_array($mode, array(ImportModeEnum::UPDATE, ImportModeEnum::INSERT, ImportModeEnum::DELETE_INSERT))) {
			return false;
		}
		
		// Create folder used for files import
		$folder	= JPATH_ROOT.'/tmp/yoorecipe_import_user_data/';
		$folder	= JPath::clean($folder);
		
		// Clean and create the temporary folder
		if (JFolder::exists($folder)) {
			JFolder::delete($folder);
		}
		
		if (!JFolder::create($folder, 0755)) {
			return false;
		}
		
		// The name of the file in PHP's temp directory
		$tmp_file_path	= $fileInfo['tmp_name'];
		
		// Build file destination path
		$file_path	= $fileInfo['name'];
		$file_path	= $folder.$file_path;
		
		if (!JFile::upload($tmp_file_path, $file_path)) {
			echo JText::_('COM_YOORECIPE_ERROR_PARTIAL_UPLOAD');
			return false;
		}
		
		// Check if the file is valid
		$file_type 	= self::getFileType($file_path);
		
		if (empty($file_type)) {
			return false;
		}
		
		// Extract xml files from the zip file
		if ($file_type == 'zip') {
			
			$zip = JArchive::getAdapter('zip');
			if (!$zip->extract($file_path, $folder)) {
				return false;
			}
		}
		
		// Get the list of files names inside the specified path
		$files_paths_list = self::scanDirAndSubDir($folder);
		
		// Import data from XML files to the database
		$result = self::convertAndSave($files_paths_list, $mode);
		if ($result === false) {
			return false;
		} else {
			JError::raiseNotice(500, implode("<br/>", $result));
		}
		
		// Delete the temporary folder
		if (JFolder::exists($folder)) {
			JFolder::delete($folder);
		}
		
		return true;
	}
	
	/**
	 * getFileType
	 * @param	String		The file path
	 * @return	String		The file MIME type
	 */
	private static function getFileType($file) {
		
		// Is a zip ?
		if (is_resource($zip = zip_open($file))) {
			zip_close($zip);
			return 'zip';
		}
		
		// Is a XML ?
		if (simplexml_load_file($file)) {
			return 'xml';
		}
		
		return '';
	}
	
	/**
	 * scanDirAndSubDir
	 * 
	 * @param	String		The directory
	 *
	 * @return	Array		List of files paths
	 */
	private static function scanDirAndSubDir($directory) {
		
		// Scan directory
		$files_paths_list = array();
		
		$dir = new RecursiveDirectoryIterator($directory);
		
		foreach (new RecursiveIteratorIterator($dir) as $filename => $file) {
			$chunks = preg_split("/_/", $filename); // split file path to retrieve the table name: ex: path/19032013_082027_user42_accomodationprices.xml => accomodationprices.xml
			$files_paths_list[$chunks[sizeof($chunks)-1]] = $filename;
		}
		
		return $files_paths_list;
	}
	
	/**
	 * convertAndSave
	 *
	 * @param	Array		List of files paths
	 * @param	Int			Value : 1 => 'UPDATE', 2 => 'INSERT', 3 => 'TRUNCATE & INSERT', 4 => INSERT WITH A SPECIFIED ID
	 * @param	Int			The user id
	 *
	 * @return	Boolean		False if files have not been found
	 */
	private static function convertAndSave($files_paths_list = array(), $mode) {
		
		// Validate params
		if (!is_array($files_paths_list) || count($files_paths_list) <= 0 || !in_array($mode, array(ImportModeEnum::UPDATE, ImportModeEnum::INSERT, ImportModeEnum::DELETE_INSERT))) {
			return false;
		}
		
		// Define the execution tables order
		$execute_order = array( 
			'yoorecipe', 'categories', 'yoorecipecategories', 'yoorecipefavourites', 'yoorecipeingredients', 'yoorecipeingredientsgroups', 'yoorecipemeals', 'yoorecipemealplannersqueues', 
			'yoorecipereviews', 'yoorecipeseasons', 'yoorecipeshoppinglists', 'yoorecipeservingtypes', 'yoorecipecuisines', 'yoorecipeshoppinglistdetails', 'yoorecipeunits', 'assets'
		);
		
		// Loop by files paths
		$statistics = array();
		foreach ($execute_order as $key) {
			
			// Get the file path
			if (!array_key_exists($key.'.xml', $files_paths_list)) {
				continue;
			}
			
			$file_name = $files_paths_list[$key.'.xml'];
			
			// Convert XML to array
			if(!JFile::exists($file_name) || !($xml = simplexml_load_file($file_name, null, LIBXML_NOCDATA))) {
				continue;
			}
			
			// Simplification of the array stucture
			$root_tag				= $xml->getName();
			$valid_object_structure	= self::XML2Array($xml);
			
			unset($file_name, $xml);
			
			// Send data to database
			self::sendDataToDatabase($root_tag, $mode, $valid_object_structure);
			
			$statistics[] = JText::sprintf('COM_YOORECIPE_NB_ARRAY_ELEMENTS', sizeof($valid_object_structure), $key);
		}
		
		return $statistics;
	}
	
	/**
	 * XML2Array
	 * @param	SimpleXMLElement	An object of SimpleXMLElement class with properties containing the data held within the XML document
	 * @return	Array				The list of array
	 */
	private static function XML2Array($xml) {
		
		$elements 				= array_values(json_decode(json_encode($xml), TRUE));
		$valid_object_structure	= @is_array($elements[0][0]) ? $elements[0] : $elements;
		
		unset($xml);
		
		// Null values management
		$valid_object_structure_length = count($valid_object_structure);
		
		for($i = 0; $i < $valid_object_structure_length; $i++) {
			
			$array_values 			= array_values($valid_object_structure[$i]);
			$array_keys				= array_keys($valid_object_structure[$i]);
			$array_values_length	= count($array_values);
			
			for ($j = 0; $j < $array_values_length; $j++) {
				
				if (is_array($array_values[$j])) { // nil attribute value found
					$valid_object_structure[$i][$array_keys[$j]] = null;
				}
			}
		}
		
		return $valid_object_structure;
	}
	
	/**
	 * sendDataToDatabase
	 * 
	 * @param	String		The table name
	 * @param	Int			Value : 1 => 'UPDATE', 2 => 'INSERT', 3 => 'TRUNCATE & INSERT'
	 * @param	Array		List of generic table data object
	 *
	 * @return	Boolean		False params are not valid
	 */
	private static function sendDataToDatabase($table_name, $mode, $rows = array()) {
		
		// Validate params
		if (!is_string($table_name) || 
			$table_name == '' || 
			!in_array($mode, array(ImportModeEnum::UPDATE, ImportModeEnum::INSERT, ImportModeEnum::DELETE_INSERT)) || 
			!is_array($rows) || 
			count($rows) <= 0) {
			
			return false;
		}
		
		// Get models
		$yoorecipeModel				= JModelLegacy::getInstance('yoorecipe', 'YooRecipeModel');
		$crossCategoryModel			= JModelLegacy::getInstance('crosscategory', 'YooRecipeModel');
		$yoorecipesModel			= JModelLegacy::getInstance('yoorecipes', 'YooRecipeModel');
		$favouriteModel				= JModelLegacy::getInstance('favourite', 'YooRecipeModel');
		$favouritesModel			= JModelLegacy::getInstance('favourites', 'YooRecipeModel');
		$ingredientModel			= JModelLegacy::getInstance('ingredient', 'YooRecipeModel');
		$ingredientsModel			= JModelLegacy::getInstance('ingredients', 'YooRecipeModel');
		$ingredientsGroupModel		= JModelLegacy::getInstance('ingredientsgroup', 'YooRecipeModel');
		$ingredientsGroupsModel		= JModelLegacy::getInstance('ingredientsgroups', 'YooRecipeModel');
		$reviewsModel				= JModelLegacy::getInstance('reviews', 'YooRecipeModel');
		$reviewModel				= JModelLegacy::getInstance('review', 'YooRecipeModel');
		$unitModel					= JModelLegacy::getInstance('unit', 'YooRecipeModel');
		$unitsModel					= JModelLegacy::getInstance('units', 'YooRecipeModel');
		$seasonsModel				= JModelLegacy::getInstance('seasons', 'YooRecipeModel');
		$seasonModel				= JModelLegacy::getInstance('season', 'YooRecipeModel');
		$mealsModel					= JModelLegacy::getInstance('meals', 'YooRecipeModel');
		$mealModel					= JModelLegacy::getInstance('meal', 'YooRecipeModel');
		$mealPlannerQueueModel		= JModelLegacy::getInstance('mealplannerqueue', 'YooRecipeModel');
		$shoppingListDetailsModel	= JModelLegacy::getInstance('shoppinglistdetails', 'YooRecipeModel');
		$shoppingListDetailModel	= JModelLegacy::getInstance('shoppinglistdetail', 'YooRecipeModel');
		$shoppingListsModel			= JModelLegacy::getInstance('shoppinglists', 'YooRecipeModel');
		$shoppingListModel			= JModelLegacy::getInstance('shoppinglist', 'YooRecipeModel');
		$servingTypesModel			= JModelLegacy::getInstance('servingtypes', 'YooRecipeModel');
		$servingTypeModel			= JModelLegacy::getInstance('servingtype', 'YooRecipeModel');
		$cuisinesModel				= JModelLegacy::getInstance('cuisines', 'YooRecipeModel');
		$cuisineModel				= JModelLegacy::getInstance('cuisine', 'YooRecipeModel');

		//Switch table name
		switch ($table_name) {
			
			case 'yoorecipe':
				if (in_array($mode, array(ImportModeEnum::DELETE_INSERT))) {
					$yoorecipesModel->truncateRecipes();
				}
				
				//Insert or update
				foreach ($rows as $row) {
					
					if (count($row) <= 0) {
						continue;
					}
					
					switch ($mode) {
						
						case ImportModeEnum::UPDATE:
							$yoorecipeModel->updateRecipeObj((object)$row);
						break;
						
						case ImportModeEnum::INSERT:
						case ImportModeEnum::DELETE_INSERT:
							$yoorecipeModel->insertRecipeObj((object)$row);
						break;
						
						default:
						break;
					}
				}
			break;
			
			case 'categories':
				if (in_array($mode, array(ImportModeEnum::DELETE_INSERT))) {
					$yoorecipesModel->deleteCategories();
				}
				
				//Insert or update
				foreach ($rows as $row) {
					
					if (count($row) <= 0) {
						continue;
					}
					
					switch ($mode) {
						
						case ImportModeEnum::UPDATE:
							$yoorecipeModel->updateCategoriesObj((object)$row);
						break;
						
						case ImportModeEnum::INSERT:
						case ImportModeEnum::DELETE_INSERT:
							$yoorecipeModel->insertCategoriesObj((object)$row);
						break;
						
						default:
						break;
					}
				}
			break;
			
			case 'yoorecipecategories':
				
				if (in_array($mode, array(ImportModeEnum::DELETE_INSERT))) {
					$yoorecipesModel->truncateYoorecipeCategories();
				}
				
				//Insert or update
				foreach ($rows as $row) {
					
					if (count($row) <= 0) {
						continue;
					}
					
					switch ($mode) {
						
						case ImportModeEnum::UPDATE:
							$crossCategoryModel->updateCrossCategoryObj((object)$row);
						break;
						
						case ImportModeEnum::INSERT:
						case ImportModeEnum::DELETE_INSERT:
							$crossCategoryModel->insertCrossCategoryObj((object)$row);
						break;
						
						default:
						break;
					}
				}
			break;
			
			case 'yoorecipefavourites':
				// truncate  table
				if (in_array($mode, array(ImportModeEnum::DELETE_INSERT))) {
					$favouritesModel->truncateFavourites();
				}
				
				// Insert or update
				foreach ($rows as $row) {
					
					if (count($row) <= 0) {
						continue;
					}
					
					switch ($mode) {
						
						case ImportModeEnum::UPDATE:
							$favouriteModel->updateFavouritesObj((object)$row);
						break;
						
						case ImportModeEnum::INSERT:
						case ImportModeEnum::DELETE_INSERT:
							$favouriteModel->insertFavouritesObj((object)$row);
						break;
						
						default:
						break;
					}
				}
			break;
			
			case 'yoorecipeingredients':
				
				if (in_array($mode, array(ImportModeEnum::DELETE_INSERT))) {
					$ingredientsModel->truncateYoorecipeIngredients();
				}
				
				// Insert or update
				foreach ($rows as $row) {
					
					if (count($row) <= 0) {
						continue;
					}
					
					switch ($mode) {
						
						case ImportModeEnum::UPDATE:
							$ingredientModel->updateIngredientObj((object)$row);
						break;
						
						case ImportModeEnum::INSERT:
						case ImportModeEnum::DELETE_INSERT:
							$ingredientModel->insertIngredientObj((object)$row);
						break;
						
						default:
						break;
					}
				}
			break;
			case 'yoorecipeingredientsgroups':
				
				if (in_array($mode, array(ImportModeEnum::DELETE_INSERT))) {
					$ingredientsGroupsModel->truncateYoorecipeIngredientsGroups();
				}
				// Insert or update
				foreach ($rows as $row) {
					
					if (count($row) <= 0) {
						continue;
					}
					switch ($mode) {
						
						case ImportModeEnum::UPDATE:
							$ingredientsGroupModel->updateYoorecipeIngredientsGroupsObj((object)$row);
						break;
						
						case ImportModeEnum::INSERT:
						case ImportModeEnum::DELETE_INSERT:
							$ingredientsGroupModel->insertYoorecipeIngredientsGroupsObj((object)$row);
						break;
						
						default:
						break;
					}
				}
			break;
			
			case 'yoorecipemeals':

				if (in_array($mode, array(ImportModeEnum::DELETE_INSERT))) {
					$mealsModel->truncateMeals();
				}
				
				//Insert or update
				foreach ($rows as $row) {
					
					if (count($row) <= 0) {
						continue;
					}
					
					switch ($mode) {
						
						case ImportModeEnum::UPDATE:
							$mealModel->updateMealObj((object)$row);
						break;
						
						case ImportModeEnum::INSERT:
						case ImportModeEnum::DELETE_INSERT:
							$mealModel->insertMealObj((object)$row);
						break;
						
						default:
						break;
					}
				}
			break;
			
			case 'yoorecipemealplannersqueues':

				if (in_array($mode, array(ImportModeEnum::DELETE_INSERT))) {
					$mealPlannerQueueModel->truncateMealplannersQueues();
				}
				//Insert or update
				foreach ($rows as $row) {
					
					if (count($row) <= 0) {
						continue;
					}
					switch ($mode) {
						
						case ImportModeEnum::UPDATE:
							$mealPlannerQueueModel->updateMealPlannerQueueObj((object)$row);
						break;
						
						case ImportModeEnum::INSERT:
						case ImportModeEnum::DELETE_INSERT:
							$mealPlannerQueueModel->insertMealPlannerQueueObj((object)$row);
						break;
						
						default:
						break;
					}
				}
			break;
				case 'yoorecipereviews':

					if (in_array($mode, array(ImportModeEnum::DELETE_INSERT))) {
						$reviewsModel->truncateReviews();
					}
					
					// Insert or update
					foreach ($rows as $row) {
						
						if (count($row) <= 0) {
							continue;
						}
						
						switch ($mode) {
							
							case ImportModeEnum::UPDATE:
								$reviewModel->updateReviewObj((object)$row);
							break;
							
							case ImportModeEnum::INSERT:
							case ImportModeEnum::DELETE_INSERT:
								$reviewModel->insertReviewObj((object)$row);
							break;
							
							default:
							break;
						}
					}
			break;
			case 'yoorecipeseasons':
				
				if (in_array($mode, array(ImportModeEnum::DELETE_INSERT))) {
					$seasonsModel->truncateYoorecipeSeasons();
				}
				
				// Insert or update
				foreach ($rows as $row) {
					
					if (count($row) <= 0) {
						continue;
					}
					
					switch ($mode) {
						
						case ImportModeEnum::UPDATE:
							$seasonModel->updateSeasonObj((object)$row);
						break;
						
						case ImportModeEnum::INSERT:
						case ImportModeEnum::DELETE_INSERT:
							$seasonModel->insertSeasonObj((object)$row);
						break;
						
						default:
						break;
					}
				}
			break;
			
			case 'yoorecipeshoppinglists':
				
				if (in_array($mode, array(ImportModeEnum::DELETE_INSERT))) {
					$shoppingListsModel->truncateYoorecipeShoppingLists();
				}
				
				// Insert or update
				foreach ($rows as $row) {
					
					if (count($row) <= 0) {
						continue;
					}
					
					switch ($mode) {
						
						case ImportModeEnum::UPDATE:
							$shoppingListModel->updateShoppingListObj((object)$row);
						break;
						
						case ImportModeEnum::INSERT:
						case ImportModeEnum::DELETE_INSERT:
							$shoppingListModel->insertShoppingListObj((object)$row);
						break;
						
						default:
						break;
					}
				}
			break;
			
			case 'yoorecipeshoppinglistdetails':
				
				if (in_array($mode, array(ImportModeEnum::DELETE_INSERT))) {
					$shoppingListDetailsModel->truncateYoorecipeShoppingListDetails();
				}
				
				// Insert or update
				foreach ($rows as $row) {
					
					if (count($row) <= 0) {
						continue;
					}
					
					switch ($mode) {
						
						case ImportModeEnum::UPDATE:
							$shoppingListDetailModel->updateShoppingListDetailObj((object)$row);
						break;
						
						case ImportModeEnum::INSERT:
						case ImportModeEnum::DELETE_INSERT:
							$shoppingListDetailModel->insertShoppingListDetailObj((object)$row);
						break;
						
						default:
						break;
					}
				}
			break;
			
			case 'yoorecipeservingtypes':
				if (in_array($mode, array(ImportModeEnum::DELETE_INSERT))) {
					$servingTypesModel->truncateServingTypes();
				}
				
				//Insert or update
				foreach ($rows as $row) {
					
					if (count($row) <= 0) {
						continue;
					}
					
					switch ($mode) {
						
						case ImportModeEnum::UPDATE:
							$servingTypeModel->updateServingTypeObj((object)$row);
						break;
						
						case ImportModeEnum::INSERT:
						case ImportModeEnum::DELETE_INSERT:
							$servingTypeModel->insertServingTypeObj((object)$row);
						break;
						
						default:
						break;
					}
				}
			break;
			
			case 'yoorecipecuisines':
				if (in_array($mode, array(ImportModeEnum::DELETE_INSERT))) {
					$cuisinesModel->truncateCuisines();
				}
				
				//Insert or update
				foreach ($rows as $row) {
					
					if (count($row) <= 0) {
						continue;
					}
					
					switch ($mode) {
						
						case ImportModeEnum::UPDATE:
							$cuisineModel->updateCuisineObj((object)$row);
						break;
						
						case ImportModeEnum::INSERT:
						case ImportModeEnum::DELETE_INSERT:
							$cuisineModel->insertCuisineObj((object)$row);
						break;
						
						default:
						break;
					}
				}
			break;
			
			case 'assets':
				
				if (in_array($mode, array(ImportModeEnum::DELETE_INSERT))) {
					$yoorecipesModel->deleteAssets();
				}
				// Insert or update
				foreach ($rows as $row) {
					
					if (count($row) <= 0) {
						continue;
					}
					switch ($mode) {
						
						case ImportModeEnum::UPDATE:
							$yoorecipeModel->updateAssetsObj((object)$row);
						break;
						
						case ImportModeEnum::INSERT:
						case ImportModeEnum::DELETE_INSERT:
							$yoorecipeModel->insertAssetsObj((object)$row);
						break;
						
						default:
						break;
					}
				}
			break;
			
			default:
			break;
		}
		
		return true;
	}
}