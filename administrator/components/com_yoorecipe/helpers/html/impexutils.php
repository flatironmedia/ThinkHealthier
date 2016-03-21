<?php
/*------------------------------------------------------------------------
# com_yoorecipe -  YooRecipe! Joomla 2.5 & 3.x recipe component
# ------------------------------------------------------------------------
# author    YooRock!
# copyright Copyright (C) 2012 yoorock.fr. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.yoorecipe.com
# Technical Support:  Forum - http://www.yoorecipe.com/
-------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.folder');

abstract class JHtmlImpexUtils
{
	/**
	 * exportUserData2XML
	 * @return	Boolean		True => The zip file is created
	 */
	public static function exportUserData2XML() {
		
		// Create the folder in the directory path used for temporary files
		$folder	= JPATH_ROOT.'/tmp/yoorecipe_export_user_data/';
		$folder	= JPath::clean($folder);
		
		// Clean the folder
		if (JFolder::exists($folder)) {
			JFolder::delete($folder);
		}
		
		if (!JFolder::create($folder, 0755)) {
			return false;
		}
		
		// Get the list of XML files to zip
		$files_to_zip = self::getXMLFilesPaths($folder);
		
		// Create the zip file
		$zip_file_path 	= $folder.date("Y.m.d-H\hi").'.zip';
		
		// Check if the zip file exists
		if (!$result = self::createZip($files_to_zip, $zip_file_path)) {
			
			// Delete the created zip file
			if (JFolder::exists($folder)) {
				JFolder::delete($folder);
			}
			
			return false;
		}
		
		//TODO resoudre bug
		// Download the zip file from the disk
		// header('Content-Description: File Transfer');
		// header('Content-Type: application/octet-stream');
		// header('Content-Disposition: attachment; filename='.basename($zip_file_path));
		// header('Content-Transfer-Encoding: binary');
		// header('Expires: 0');
		// header('Cache-Control: must-revalidate');
		// header('Pragma: public');
		// header('Content-Length: '.filesize($zip_file_path));
		// ob_clean();
		// flush();
		
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename=".basename($zip_file_path));
		header("Content-Length: " . filesize($zip_file_path));
		
		readfile($zip_file_path);
		
		//TODO uncomment after tests
		// Delete the created zip file
		// if (JFolder::exists($folder)) {
			// JFolder::delete($folder);
		// }
		return $result;
	}
	
	/**
	 * getXMLFilesPaths
	 * 
	 * @param	int		The user id
	 * @param	String	The folder were the XML files will be saved
	 *
	 * @return	Array	List of XML files paths
	 */
	private static function getXMLFilesPaths($folder) {
		
		// Get XML files paths
		$files_paths = array();
			
		// Get parameters XML data
		$files_paths[] = self::yoorecipes2XML($folder);
		$files_paths[] = self::categories2XML($folder);
		$files_paths[] = self::yoorecipeCategories2XML($folder);
		$files_paths[] = self::favourites2XML($folder);
		$files_paths[] = self::ingredients2XML($folder);
		$files_paths[] = self::ingredientsGroups2XML($folder);
		$files_paths[] = self::meals2XML($folder);
		$files_paths[] = self::mealPlannersQueues2XML($folder);
		$files_paths[] = self::ratings2XML($folder);
		$files_paths[] = self::seasons2XML($folder);
		$files_paths[] = self::shoppingLists2XML($folder);
		$files_paths[] = self::shoppingListsDetails2XML($folder);
		
		// Set the warning message
		if (implode('', $files_paths) == '') {
			$files_paths[]	= self::warningMessage($folder);
		}
		
		return $files_paths;
	}
	
	/**
	 * createZip : Creates a compressed zip file
	 * 
	 * @param	Array		The array of files paths
	 * @param	String		The destination folder, where the zip will be saved
	 * @param	Boolean		True => To overwrite the zip file, if it already exists
	 *
	 * @return	Boolean		True => If the zip is created
	 */
	private static function createZip($files = array(), $destination = '', $overwrite = true) {
		
		// Validate params
		if ((JFile::exists($destination) && !$overwrite) || !is_array($files) || count($files) <= 0) {
			return false;
		}
		
		// Create the ZIP archive
		$zip = new ZipArchive();
		
		if(!$zip->open($destination, ZIPARCHIVE::OVERWRITE | ZIPARCHIVE::CREATE)) {
			return false;
		}
		
		// Add the files
		foreach($files as $file) {
			
			if (JFile::exists($file)) {
				$zip->addFile($file, basename($file));
			}
		}
		// Close the zip
		$zip->close();
		
		// Check to make sure the file exists
		return file_exists($destination);
	}
	
	/**
	 * warningMessage
	 *
	 * @return	String	The XML file path
	 */
	private static function warningMessage($folder) {
		
		// Set warning messsge
		$msg = JText::_('COM_YOORECIPE_WARNING_NO_XML_FILES');
		
		// Set the file path
		$path	= $folder.date("dmY_His").'_warning_message.txt';
		$path	= JPath::clean($path);
		
		// Write file on disk
		JFile::write($path, $msg, $use_streams=false);
		
		return $path;
	}
	
	/**
	 * yoorecipes2XML
	 * @param	String	The folder were the XML files will be saved
	 * @return	String	The XML file path
	 */
	private static function yoorecipes2XML($folder) {
		
		// Get campingmanager
		$yoorecipesModel	= JModelLegacy::getInstance('yoorecipes', 'YooRecipeModel');
		$recipes			= $yoorecipesModel->getAllRecipes();
		
		// Checks if there isn't any recipes
		if (count($recipes)<=0) {
			return '';
		}
		
		// Prepare XML file header
		$xml	= array();
		$xml[]	= '&lt;?xml version="1.0" encoding="utf-8"?&gt;'."\r\n";
		
		// Prepare XML file content
		$xml[] = '&lt;yoorecipes&gt;'."\r\n";
		if (isset($recipes)) {
			
			// Loop over recipe objects
			foreach ($recipes as $yoorecipe) {
				
				$xml[] = "\t".'&lt;yoorecipe&gt;'."\r\n";
				foreach ($yoorecipe as $key=>$value) {
					$xml[] = ($value == null) ? "\t\t".'&lt;'.$key.' xsi:nil="true"&gt;&lt;/'.$key.'&gt;'."\r\n" : "\t\t".'&lt;'.$key.'&gt;<![CDATA['.$value.']]>&lt;/'.$key.'&gt;'."\r\n";
				}
				$xml[] = "\t".'&lt;/yoorecipe&gt;'."\r\n";
			}
		}
		$xml[] = '&lt;/yoorecipes&gt;';
		
		// Convert the xml array content to string
		$string = htmlspecialchars_decode(implode("", $xml));
		
		// Set the file path
		$path	= $folder.date("dmY_His").'_yoorecipe.xml';
		$path	= JPath::clean($path);
		
		// Write file on disk
		JFile::write($path, $string, $use_streams=false);
		
		return $path;
	}

	/**
	 * categories2XML
	 * @param	String	The folder were the XML files will be saved
	 * @return	String	The XML file path
	 */
	private static function categories2XML($folder) {
		
		// Get categories
		$yoorecipeModel	= JModelLegacy::getInstance('yoorecipe', 'YooRecipeModel');
		$categories = $yoorecipeModel->getAllCategories();
		
		// Checks if there aren't categories
		if (count($categories)<=0) {
			return '';
		}
		
		// Prepare XML file header
		$xml	= array();
		$xml[]	= '&lt;?xml version="1.0" encoding="utf-8"?&gt;'."\r\n";
		
		// Prepare XML file content
		$xml[] = '&lt;categories&gt;'."\r\n";
		if (isset($categories)) {
			
			// Loop by categories
			foreach ($categories as $category) {
				$xml[] = "\t".'&lt;category&gt;'."\r\n";
				foreach ($category as $key=>$value) {
					$xml[] = ($value == null) ? "\t\t".'&lt;'.$key.' xsi:nil="true"&gt;&lt;/'.$key.'&gt;'."\r\n" : "\t\t".'&lt;'.$key.'&gt;<![CDATA['.$value.']]>&lt;/'.$key.'&gt;'."\r\n";
				}
				$xml[] = "\t".'&lt;/category&gt;'."\r\n";
			}
		}
		$xml[] = '&lt;/categories&gt;';
		
		// Convert the xml array content to string
		$string = htmlspecialchars_decode(implode("", $xml));
		
		// Set the file path
		$path	= $folder.date("dmY_His").'_categories.xml';
		$path	= JPath::clean($path);
		
		// Write file on disk
		JFile::write($path, $string, $use_streams=false);
		
		return $path;
	}
	
	/**
	 * yoorecipeCategories2XML
	 * @param	String	The folder were the XML files will be saved
	 * @return	String	The XML file path
	 */
	private static function yoorecipeCategories2XML($folder) {
		
		// Get categories
		$crossCategoryModel		= JModelLegacy::getInstance('crossCategory', 'YooRecipeModel');
		$yoorecipe_categories 	= $crossCategoryModel->getAllRecipeCategories();
		
		// Checks if there aren't categories
		if (count($yoorecipe_categories)<=0) {
			return '';
		}
		
		// Prepare XML file header
		$xml	= array();
		$xml[]	= '&lt;?xml version="1.0" encoding="utf-8"?&gt;'."\r\n";
		
		// Prepare XML file content
		$xml[] = '&lt;yoorecipe_categories&gt;'."\r\n";
		if (isset($yoorecipe_categories)) {
			
			// Loop by categories
			foreach ($yoorecipe_categories as $yoorecipe_category) {
				$xml[] = "\t".'&lt;yoorecipe_category&gt;'."\r\n";
				foreach ($yoorecipe_category as $key=>$value) {
					$xml[] = ($value == null) ? "\t\t".'&lt;'.$key.' xsi:nil="true"&gt;&lt;/'.$key.'&gt;'."\r\n" : "\t\t".'&lt;'.$key.'&gt;<![CDATA['.$value.']]>&lt;/'.$key.'&gt;'."\r\n";
				}
				$xml[] = "\t".'&lt;/yoorecipe_category&gt;'."\r\n";
			}
		}
		$xml[] = '&lt;/yoorecipe_categories&gt;';
		
		// Convert the xml array content to string
		$string = htmlspecialchars_decode(implode("", $xml));
		
		// Set the file path
		$path	= $folder.date("dmY_His").'_yoorecipe_categories.xml';
		$path	= JPath::clean($path);
		
		// Write file on disk
		JFile::write($path, $string, $use_streams=false);
		
		return $path;
	}
	
	/**
	 * favourites2XML
	 * @param	String	The folder were the XML files will be saved
	 * @return	String	The XML file path
	 */
		private static function favourites2XML($folder) {
		
		// Get favouries
		$favouritesModel		= JModelLegacy::getInstance('favourites', 'YooRecipeModel');
		$yoorecipe_favourites 	= $favouritesModel->getAllRecipeFavourites();
		
		// Checks if there aren't favourites
		if (count($yoorecipe_favourites)<=0) {
			return '';
		}
		
		// Prepare XML file header
		$xml	= array();
		$xml[]	= '&lt;?xml version="1.0" encoding="utf-8"?&gt;'."\r\n";
		
		// Prepare XML file content
		$xml[] = '&lt;yoorecipe_favourites&gt;'."\r\n";
		if (isset($yoorecipe_favourites)) {
			
			// Loop by favourites
			foreach ($yoorecipe_favourites as $yoorecipe_favourite) {
				$xml[] = "\t".'&lt;yoorecipe_favourite&gt;'."\r\n";
				foreach ($yoorecipe_favourite as $key=>$value) {
					$xml[] = ($value == null) ? "\t\t".'&lt;'.$key.' xsi:nil="true"&gt;&lt;/'.$key.'&gt;'."\r\n" : "\t\t".'&lt;'.$key.'&gt;<![CDATA['.$value.']]>&lt;/'.$key.'&gt;'."\r\n";
				}
				$xml[] = "\t".'&lt;/yoorecipe_favourite&gt;'."\r\n";
			}
		}
		$xml[] = '&lt;/yoorecipe_favourites&gt;';
		
		// Convert the xml array content to string
		$string = htmlspecialchars_decode(implode("", $xml));
		
		// Set the file path
		$path	= $folder.date("dmY_His").'_yoorecipe_favourites.xml';
		$path	= JPath::clean($path);
		
		// Write file on disk
		JFile::write($path, $string, $use_streams=false);
		
		return $path;
	}
	
	/**
	 * ingredients2XML
	 * @param	String	The folder were the XML files will be saved
	 * @return	String	The XML file path
	 */
		private static function ingredients2XML($folder) {
		
		// Get categories
		$ingredientsModel	= JModelLegacy::getInstance('ingredients ', 'YooRecipeModel');
		$yoorecipe_ingredients = $ingredientsModel->getAllRecipeIngredients();
		
		// Checks if there aren't ingredients
		if (count($yoorecipe_ingredients)<=0) {
			return '';
		}
		
		// Prepare XML file header
		$xml	= array();
		$xml[]	= '&lt;?xml version="1.0" encoding="utf-8"?&gt;'."\r\n";
		
		// Prepare XML file content
		$xml[] = '&lt;yoorecipe_ingredients&gt;'."\r\n";
		if (isset($yoorecipe_ingredients)) {
			
			// Loop by ingredients
			foreach ($yoorecipe_ingredients as $yoorecipe_ingredient) {
				$xml[] = "\t".'&lt;yoorecipe_ingredient&gt;'."\r\n";
				foreach ($yoorecipe_ingredient as $key=>$value) {
					$xml[] = ($value == null) ? "\t\t".'&lt;'.$key.' xsi:nil="true"&gt;&lt;/'.$key.'&gt;'."\r\n" : "\t\t".'&lt;'.$key.'&gt;<![CDATA['.$value.']]>&lt;/'.$key.'&gt;'."\r\n";
				}
				$xml[] = "\t".'&lt;/yoorecipe_ingredient&gt;'."\r\n";
			}
		}
		$xml[] = '&lt;/yoorecipe_ingredients&gt;';
		
		// Convert the xml array content to string
		$string = htmlspecialchars_decode(implode("", $xml));
		
		// Set the file path
		$path	= $folder.date("dmY_His").'_yoorecipe_ingredients.xml';
		$path	= JPath::clean($path);
		
		// Write file on disk
		JFile::write($path, $string, $use_streams=false);
		
		return $path;
	}
	
	/**
	 * ingredientsGroups2XML
	 * @param	String	The folder were the XML files will be saved
	 * @return	String	The XML file path
	 */
		private static function ingredientsGroups2XML($folder) {
		
		// Get ingredients groups
		$ingredientsGroupsModel	= JModelLegacy::getInstance('ingredientsgroups', 'YooRecipeModel');
		$yoorecipe_ingredients_groups = $ingredientsGroupsModel->getAllRecipeIngredientsGroups();
		
		// Checks if there aren't ingredients groups
		if (count($yoorecipe_ingredients_groups)<=0) {
			return '';
		}
		
		// Prepare XML file header
		$xml	= array();
		$xml[]	= '&lt;?xml version="1.0" encoding="utf-8"?&gt;'."\r\n";
		
		// Prepare XML file content
		$xml[] = '&lt;yoorecipe_ingredients_groups&gt;'."\r\n";
		if (isset($yoorecipe_ingredients_groups)) {
			
			// Loop by ingredients groups
			foreach ($yoorecipe_ingredients_groups as $yoorecipe_ingredient_group) {
				$xml[] = "\t".'&lt;yoorecipe_ingredients_group&gt;'."\r\n";
				foreach ($yoorecipe_ingredient_group as $key=>$value) {
					$xml[] = ($value == null) ? "\t\t".'&lt;'.$key.' xsi:nil="true"&gt;&lt;/'.$key.'&gt;'."\r\n" : "\t\t".'&lt;'.$key.'&gt;<![CDATA['.$value.']]>&lt;/'.$key.'&gt;'."\r\n";
				}
				$xml[] = "\t".'&lt;/yoorecipe_ingredients_group&gt;'."\r\n";
			}
		}
		$xml[] = '&lt;/yoorecipe_ingredients_groups&gt;';
		
		// Convert the xml array content to string
		$string = htmlspecialchars_decode(implode("", $xml));
		
		// Set the file path
		$path	= $folder.date("dmY_His").'_yoorecipe_ingredients_groups.xml';
		$path	= JPath::clean($path);
		
		// Write file on disk
		JFile::write($path, $string, $use_streams=false);
		
		return $path;
	}
	
	/**
	 * meals2XML
	 * @param	String	The folder were the XML files will be saved
	 * @return	String	The XML file path
	 */
		private static function meals2XML($folder) {
		
		// Get meals
		$mealsModel			= JModelLegacy::getInstance('meals', 'YooRecipeModel');
		$yoorecipe_meals	= $mealsModel->getAllMeals();
		
		// Checks if there aren't meals
		if (count($yoorecipe_meals)<=0) {
			return '';
		}
		
		// Prepare XML file header
		$xml	= array();
		$xml[]	= '&lt;?xml version="1.0" encoding="utf-8"?&gt;'."\r\n";
		
		// Prepare XML file content
		$xml[] = '&lt;yoorecipe_meals&gt;'."\r\n";
		if (isset($yoorecipe_meals)) {
			
			// Loop by meals
			foreach ($yoorecipe_meals as $yoorecipe_meal) {
				$xml[] = "\t".'&lt;yoorecipe_meal&gt;'."\r\n";
				foreach ($yoorecipe_meal as $key=>$value) {
					$xml[] = ($value == null) ? "\t\t".'&lt;'.$key.' xsi:nil="true"&gt;&lt;/'.$key.'&gt;'."\r\n" : "\t\t".'&lt;'.$key.'&gt;<![CDATA['.$value.']]>&lt;/'.$key.'&gt;'."\r\n";
				}
				$xml[] = "\t".'&lt;/yoorecipe_meal&gt;'."\r\n";
			}
		}
		$xml[] = '&lt;/yoorecipe_meals&gt;';
		
		// Convert the xml array content to string
		$string = htmlspecialchars_decode(implode("", $xml));
		
		// Set the file path
		$path	= $folder.date("dmY_His").'_yoorecipe_meals.xml';
		$path	= JPath::clean($path);
		
		// Write file on disk
		JFile::write($path, $string, $use_streams=false);
		
		return $path;
	}
	
	/**
	 * mealPlannersQueues2XML
	 * @param	String	The folder were the XML files will be saved
	 * @return	String	The XML file path
	 */
		private static function mealPlannersQueues2XML($folder) {
		
		// Get mealplanners queues
		$mealPlannerQueueModel			= JModelLegacy::getInstance('mealplannerqueue', 'YooRecipeModel');
		$yoorecipe_mealplanners_queues 	= $mealPlannerQueueModel->getAllRecipeMealplannersQueues();
		
		// Checks if there aren't mealplanners queues
		if (count($yoorecipe_mealplanners_queues)<=0) {
			return '';
		}
		
		// Prepare XML file header
		$xml	= array();
		$xml[]	= '&lt;?xml version="1.0" encoding="utf-8"?&gt;'."\r\n";
		
		// Prepare XML file content
		$xml[] = '&lt;yoorecipe_mealplanners_queues&gt;'."\r\n";
		if (isset($yoorecipe_mealplanners_queues)) {
			
			// Loop by mealplanners queues
			foreach ($yoorecipe_mealplanners_queues as $yoorecipe_mealplanner_queue) {
				$xml[] = "\t".'&lt;yoorecipe_mealplanner_queue&gt;'."\r\n";
				foreach ($yoorecipe_mealplanner_queue as $key=>$value) {
					$xml[] = ($value == null) ? "\t\t".'&lt;'.$key.' xsi:nil="true"&gt;&lt;/'.$key.'&gt;'."\r\n" : "\t\t".'&lt;'.$key.'&gt;<![CDATA['.$value.']]>&lt;/'.$key.'&gt;'."\r\n";
				}
				$xml[] = "\t".'&lt;/yoorecipe_mealplanner_queue&gt;'."\r\n";
			}
		}
		$xml[] = '&lt;/yoorecipe_mealplanners_queues&gt;';
		
		// Convert the xml array content to string
		$string = htmlspecialchars_decode(implode("", $xml));
		
		// Set the file path
		$path	= $folder.date("dmY_His").'yoorecipe_mealplanners_queues.xml';
		$path	= JPath::clean($path);
		
		// Write file on disk
		JFile::write($path, $string, $use_streams=false);
		
		return $path;
	}
	
	/**
	 * ratings2XML
	 * @param	String	The folder were the XML files will be saved
	 * @return	String	The XML file path
	 */
		private static function ratings2XML($folder) {
		
		// Get reviews
		$reviewsModel		= JModelLegacy::getInstance('reviews', 'YooRecipeModel');
		$yoorecipe_ratings = $reviewsModel->getAllRecipeReviews();
		
		// Checks if there aren't ratings
		if (count($yoorecipe_ratings)<=0) {
			return '';
		}
		
		// Prepare XML file header
		$xml	= array();
		$xml[]	= '&lt;?xml version="1.0" encoding="utf-8"?&gt;'."\r\n";
		
		// Prepare XML file content
		$xml[] = '&lt;yoorecipe_ratings&gt;'."\r\n";
		if (isset($yoorecipe_ratings)) {
			
			// Loop by ratings
			foreach ($yoorecipe_ratings as $yoorecipe_rating) {
				$xml[] = "\t".'&lt;yoorecipe_rating&gt;'."\r\n";
				foreach ($yoorecipe_rating as $key=>$value) {
					$xml[] = ($value == null) ? "\t\t".'&lt;'.$key.' xsi:nil="true"&gt;&lt;/'.$key.'&gt;'."\r\n" : "\t\t".'&lt;'.$key.'&gt;<![CDATA['.$value.']]>&lt;/'.$key.'&gt;'."\r\n";
				}
				$xml[] = "\t".'&lt;/yoorecipe_rating&gt;'."\r\n";
			}
		}
		$xml[] = '&lt;/yoorecipe_ratings&gt;';
		
		// Convert the xml array content to string
		$string = htmlspecialchars_decode(implode("", $xml));
		
		// Set the file path
		$path	= $folder.date("dmY_His").'yoorecipe_ratings.xml';
		$path	= JPath::clean($path);
		
		// Write file on disk
		JFile::write($path, $string, $use_streams=false);
		
		return $path;
	}
	/**
	 * seasons2XML
	 * @param	String	The folder were the XML files will be saved
	 * @return	String	The XML file path
	 */
		private static function seasons2XML($folder) {
		
		// Get seasons
		$seasonsModel		= JModelLegacy::getInstance('seasons', 'YooRecipeModel');
		$yoorecipe_seasons 	= $seasonsModel->getAllRecipeSeasons();
		
		// Checks if there aren't seasons
		if (count($yoorecipe_seasons)<=0) {
			return '';
		}
		
		// Prepare XML file header
		$xml	= array();
		$xml[]	= '&lt;?xml version="1.0" encoding="utf-8"?&gt;'."\r\n";
		
		// Prepare XML file content
		$xml[] = '&lt;yoorecipe_seasons&gt;'."\r\n";
		if (isset($yoorecipe_seasons)) {
			
			// Loop by seasons
			foreach ($yoorecipe_seasons as $yoorecipe_season) {
				$xml[] = "\t".'&lt;yoorecipe_season&gt;'."\r\n";
				foreach ($yoorecipe_season as $key=>$value) {
					$xml[] = ($value == null) ? "\t\t".'&lt;'.$key.' xsi:nil="true"&gt;&lt;/'.$key.'&gt;'."\r\n" : "\t\t".'&lt;'.$key.'&gt;<![CDATA['.$value.']]>&lt;/'.$key.'&gt;'."\r\n";
				}
				$xml[] = "\t".'&lt;/yoorecipe_season&gt;'."\r\n";
			}
		}
		$xml[] = '&lt;/yoorecipe_seasons&gt;';
		
		// Convert the xml array content to string
		$string = htmlspecialchars_decode(implode("", $xml));
		
		// Set the file path
		$path	= $folder.date("dmY_His").'yoorecipe_seasons.xml';
		$path	= JPath::clean($path);
		
		// Write file on disk
		JFile::write($path, $string, $use_streams=false);
		
		return $path;
	}
	
	/**
	 * shoppingLists2XML
	 * @param	String	The folder were the XML files will be saved
	 * @return	String	The XML file path
	 */
		private static function shoppingLists2XML($folder) {
		
		// Get shoppinglists
		$yoorecipeModel	= JModelLegacy::getInstance('yoorecipe', 'YooRecipeModel');
		$yoorecipe_shoppinglists = $yoorecipeModel->getAllRecipeShoppinglists();
		
		// Checks if there aren't shoppinglists
		if (count($yoorecipe_shoppinglists)<=0) {
			return '';
		}
		
		// Prepare XML file header
		$xml	= array();
		$xml[]	= '&lt;?xml version="1.0" encoding="utf-8"?&gt;'."\r\n";
		
		// Prepare XML file content
		$xml[] = '&lt;yoorecipe_shoppinglists&gt;'."\r\n";
		if (isset($yoorecipe_shoppinglists)) {
			
			// Loop by shoppinglists
			foreach ($yoorecipe_shoppinglists as $yoorecipe_shoppinglist) {
				$xml[] = "\t".'&lt;yoorecipe_shoppinglist&gt;'."\r\n";
				foreach ($yoorecipe_shoppinglist as $key=>$value) {
					$xml[] = ($value == null) ? "\t\t".'&lt;'.$key.' xsi:nil="true"&gt;&lt;/'.$key.'&gt;'."\r\n" : "\t\t".'&lt;'.$key.'&gt;<![CDATA['.$value.']]>&lt;/'.$key.'&gt;'."\r\n";
				}
				$xml[] = "\t".'&lt;/yoorecipe_shoppinglist&gt;'."\r\n";
			}
		}
		$xml[] = '&lt;/yoorecipe_shoppinglists&gt;';
		
		// Convert the xml array content to string
		$string = htmlspecialchars_decode(implode("", $xml));
		
		// Set the file path
		$path	= $folder.date("dmY_His").'yoorecipe_shoppinglists.xml';
		$path	= JPath::clean($path);
		
		// Write file on disk
		JFile::write($path, $string, $use_streams=false);

		return $path;
	}
	
	/**
	 * shoppinglistsdetails2XML
	 * @param	String	The folder were the XML files will be saved
	 * @return	String	The XML file path
	 */
	private static function shoppingListsDetails2XML($folder) {
		
		// Get shoppinglists details
		$shoppingListDetailsModel	= JModelLegacy::getInstance('shoppinglistdetails', 'YooRecipeModel');
		$shoppinglists_details 		= $shoppingListDetailsModel->getAllRecipeShoppinglistsDetails();
		
		// Checks if there aren't shoppinglists details
		if (count($shoppinglists_details)<=0) {
			return '';
		}
		
		// Prepare XML file header
		$xml	= array();
		$xml[]	= '&lt;?xml version="1.0" encoding="utf-8"?&gt;'."\r\n";
		
		// Prepare XML file content
		$xml[] = '&lt;yoorecipe_shoppinglists_details&gt;'."\r\n";
		if (isset($shoppinglists_details)) {
			
			// Loop by shoppinglists
			foreach ($shoppinglists_details as $shoppinglists_detail) {
				$xml[] = "\t".'&lt;yoorecipe_shoppinglists_detail&gt;'."\r\n";
				foreach ($shoppinglists_detail as $key=>$value) {
					$xml[] = ($value == null) ? "\t\t".'&lt;'.$key.' xsi:nil="true"&gt;&lt;/'.$key.'&gt;'."\r\n" : "\t\t".'&lt;'.$key.'&gt;<![CDATA['.$value.']]>&lt;/'.$key.'&gt;'."\r\n";
				}
				$xml[] = "\t".'&lt;/yoorecipe_shoppinglists_detail&gt;'."\r\n";
			}
		}
		$xml[] = '&lt;/yoorecipe_shoppinglists_details&gt;';
		
		// Convert the xml array content to string
		$string = htmlspecialchars_decode(implode("", $xml));
		
		// Set the file path
		$path	= $folder.date("dmY_His").'yoorecipe_shoppinglists_details.xml';
		$path	= JPath::clean($path);
		
		// Write file on disk
		JFile::write($path, $string, $use_streams=false);

		return $path;
	}
}