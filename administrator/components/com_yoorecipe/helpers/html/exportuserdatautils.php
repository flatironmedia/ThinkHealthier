<?php
/*------------------------------------------------------------------------
# com_campingmanager
# ------------------------------------------------------------------------
# author    YooRock!
# copyright Copyright (C) 2012 yoorock.fr. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://extensions.yoorock.fr
# Technical Support:  Forum - http://extensions.yoorock.fr/
-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.folder');

/**
* JHtmlExportUserDataUtils
*/
abstract class JHtmlExportUserDataUtils {
	
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
		$zip_file_path 	= $folder.date("Y.m.d-H\hi").'.yoorecipe.zip';

		// Check if the zip file exists
		if (!$result = self::createZip($files_to_zip, $zip_file_path)) {
			
			//Delete the created zip file
			if (JFolder::exists($folder)) {
				JFolder::delete($folder);
			}
			
			return false;
		}
		
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename=".basename($zip_file_path));
		header("Content-Length: ".filesize($zip_file_path));
		
		readfile($zip_file_path);
		
		// Delete the created zip file
		if (JFolder::exists($folder)) {
			JFolder::delete($folder);
		}
		
		exit;
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
		$files_paths[] = self::assets2XML($folder);
		$files_paths[] = self::yoorecipeCategories2XML($folder);
		$files_paths[] = self::favourites2XML($folder);
		$files_paths[] = self::ingredients2XML($folder);
		$files_paths[] = self::ingredientsGroups2XML($folder);
		$files_paths[] = self::meals2XML($folder);
		$files_paths[] = self::mealPlannersQueues2XML($folder);
		$files_paths[] = self::seasons2XML($folder);
		$files_paths[] = self::shoppingLists2XML($folder);
		$files_paths[] = self::shoppingListsDetails2XML($folder);
		
		$files_paths[] = self::reviews2XML($folder);
		$files_paths[] = self::servingTypes2XML($folder);
		$files_paths[] = self::cuisines2XML($folder);
		
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
		$xml[] = '&lt;yoorecipe xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"&gt;'."\r\n";
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
		$xml[] = '&lt;/yoorecipe&gt;';
		
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
		$xml[] = '&lt;categories xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"&gt;'."\r\n";
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
	 * assets2XML
	 * @param	String	The folder were the XML files will be saved
	 * @return	String	The XML file path
	 */
	private static function assets2XML($folder) {
		
		// Get assets
		$yoorecipeModel	= JModelLegacy::getInstance('yoorecipe', 'YooRecipeModel');
		$assets = $yoorecipeModel->getYooRecipeAssets();
		
		// Checks if there aren't assets
		if (count($assets)<=0) {
			return '';
		}
		
		// Prepare XML file header
		$xml	= array();
		$xml[]	= '&lt;?xml version="1.0" encoding="utf-8"?&gt;'."\r\n";
		
		// Prepare XML file content
		$xml[] = '&lt;assets xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"&gt;'."\r\n";
		if (isset($assets)) {
			
			// Loop by assets
			foreach ($assets as $asset) {
				$xml[] = "\t".'&lt;asset&gt;'."\r\n";
				foreach ($asset as $key=>$value) {
					$xml[] = ($value == null) ? "\t\t".'&lt;'.$key.' xsi:nil="true"&gt;&lt;/'.$key.'&gt;'."\r\n" : "\t\t".'&lt;'.$key.'&gt;<![CDATA['.$value.']]>&lt;/'.$key.'&gt;'."\r\n";
				}
				$xml[] = "\t".'&lt;/asset&gt;'."\r\n";
			}
		}
		$xml[] = '&lt;/assets&gt;';
		
		// Convert the xml array content to string
		$string = htmlspecialchars_decode(implode("", $xml));
		
		// Set the file path
		$path	= $folder.date("dmY_His").'_assets.xml';
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
		$xml[] = '&lt;yoorecipecategories xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"&gt;'."\r\n";
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
		$xml[] = '&lt;/yoorecipecategories&gt;';
		
		// Convert the xml array content to string
		$string = htmlspecialchars_decode(implode("", $xml));
		
		// Set the file path
		$path	= $folder.date("dmY_His").'_yoorecipecategories.xml';
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
		
		// Get favourites
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
		$xml[] = '&lt;yoorecipefavourites xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"&gt;'."\r\n";
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
		$xml[] = '&lt;/yoorecipefavourites&gt;';
		
		// Convert the xml array content to string
		$string = htmlspecialchars_decode(implode("", $xml));
		
		// Set the file path
		$path	= $folder.date("dmY_His").'_yoorecipefavourites.xml';
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
		$xml[] = '&lt;yoorecipeingredients xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"&gt;'."\r\n";
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
		$xml[] = '&lt;/yoorecipeingredients&gt;';
		
		// Convert the xml array content to string
		$string = htmlspecialchars_decode(implode("", $xml));
		
		// Set the file path
		$path	= $folder.date("dmY_His").'_yoorecipeingredients.xml';
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
		$xml[] = '&lt;yoorecipeingredientsgroups xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"&gt;'."\r\n";
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
		$xml[] = '&lt;/yoorecipeingredientsgroups&gt;';
		
		// Convert the xml array content to string
		$string = htmlspecialchars_decode(implode("", $xml));
		
		// Set the file path
		$path	= $folder.date("dmY_His").'_yoorecipeingredientsgroups.xml';
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
		$mealsModel				= JModelLegacy::getInstance('meals', 'YooRecipeModel');
		$yoorecipe_meals = $mealsModel->getAllMeals();
		
		// Checks if there aren't meals
		if (count($yoorecipe_meals)<=0) {
			return '';
		}
		
		// Prepare XML file header
		$xml	= array();
		$xml[]	= '&lt;?xml version="1.0" encoding="utf-8"?&gt;'."\r\n";
		
		// Prepare XML file content
		$xml[] = '&lt;yoorecipemeals xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"&gt;'."\r\n";
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
		$xml[] = '&lt;/yoorecipemeals&gt;';
		
		// Convert the xml array content to string
		$string = htmlspecialchars_decode(implode("", $xml));
		
		// Set the file path
		$path	= $folder.date("dmY_His").'_yoorecipemeals.xml';
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
		$xml[] = '&lt;yoorecipemealplannersqueues xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"&gt;'."\r\n";
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
		$xml[] = '&lt;/yoorecipemealplannersqueues&gt;';
		
		// Convert the xml array content to string
		$string = htmlspecialchars_decode(implode("", $xml));
		
		// Set the file path
		$path	= $folder.date("dmY_His").'_yoorecipemealplannersqueues.xml';
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
		$xml[] = '&lt;yoorecipeseasons xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"&gt;'."\r\n";
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
		$xml[] = '&lt;/yoorecipeseasons&gt;';
		
		// Convert the xml array content to string
		$string = htmlspecialchars_decode(implode("", $xml));
		
		// Set the file path
		$path	= $folder.date("dmY_His").'_yoorecipeseasons.xml';
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
		$shoppingListsModel			= JModelLegacy::getInstance('shoppinglists', 'YooRecipeModel');
		$yoorecipe_shoppinglists 	= $shoppingListsModel->getAllRecipeShoppinglists();
		
		// Checks if there aren't shoppinglists
		if (count($yoorecipe_shoppinglists)<=0) {
			return '';
		}
		
		// Prepare XML file header
		$xml	= array();
		$xml[]	= '&lt;?xml version="1.0" encoding="utf-8"?&gt;'."\r\n";
		
		// Prepare XML file content
		$xml[] = '&lt;yoorecipeshoppinglists xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"&gt;'."\r\n";
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
		$xml[] = '&lt;/yoorecipeshoppinglists&gt;';
		
		// Convert the xml array content to string
		$string = htmlspecialchars_decode(implode("", $xml));
		
		// Set the file path
		$path	= $folder.date("dmY_His").'_yoorecipeshoppinglists.xml';
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
		$xml[] = '&lt;yoorecipeshoppinglistdetails xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"&gt;'."\r\n";
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
		$xml[] = '&lt;/yoorecipeshoppinglistdetails&gt;';
		
		// Convert the xml array content to string
		$string = htmlspecialchars_decode(implode("", $xml));
		
		// Set the file path
		$path	= $folder.date("dmY_His").'_yoorecipeshoppinglistdetails.xml';
		$path	= JPath::clean($path);
		
		// Write file on disk
		JFile::write($path, $string, $use_streams=false);

		return $path;
	}
	
	/**
	 * reviews2XML
	 * @param	String	The folder were the XML files will be saved
	 * @return	String	The XML file path
	 */
	private static function reviews2XML($folder) {
		
		// Get reviews details
		$reviewsModel	= JModelLegacy::getInstance('reviews', 'YooRecipeModel');
		$reviews 		= $reviewsModel->getAllRecipeReviews();
		
		// Checks if there aren't reviews
		if (count($reviews)<=0) {
			return '';
		}
		
		// Prepare XML file header
		$xml	= array();
		$xml[]	= '&lt;?xml version="1.0" encoding="utf-8"?&gt;'."\r\n";
		
		// Prepare XML file content
		$xml[] = '&lt;reviews xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"&gt;'."\r\n";
		if (isset($reviews)) {
			
			// Loop by shoppinglists
			foreach ($reviews as $review) {
				$xml[] = "\t".'&lt;review&gt;'."\r\n";
				foreach ($review as $key=>$value) {
					$xml[] = ($value == null) ? "\t\t".'&lt;'.$key.' xsi:nil="true"&gt;&lt;/'.$key.'&gt;'."\r\n" : "\t\t".'&lt;'.$key.'&gt;<![CDATA['.$value.']]>&lt;/'.$key.'&gt;'."\r\n";
				}
				$xml[] = "\t".'&lt;/review&gt;'."\r\n";
			}
		}
		$xml[] = '&lt;/reviews&gt;';
		
		// Convert the xml array content to string
		$string = htmlspecialchars_decode(implode("", $xml));
		
		// Set the file path
		$path	= $folder.date("dmY_His").'_yoorecipereviews.xml';
		$path	= JPath::clean($path);
		
		// Write file on disk
		JFile::write($path, $string, $use_streams=false);

		return $path;
	}

	/**
	 * servingTypes2XML
	 * @param	String	The folder were the XML files will be saved
	 * @return	String	The XML file path
	 */
	private static function servingTypes2XML($folder) {
		
		// Get servingtypes details
		$servingtypesModel	= JModelLegacy::getInstance('servingtypes', 'YooRecipeModel');
		$servingtypes 		= $servingtypesModel->getAllServingTypes();
		
		// Checks if there aren't servingtypes
		if (count($servingtypes)<=0) {
			return '';
		}
		
		// Prepare XML file header
		$xml	= array();
		$xml[]	= '&lt;?xml version="1.0" encoding="utf-8"?&gt;'."\r\n";
		
		// Prepare XML file content
		$xml[] = '&lt;servingtypes xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"&gt;'."\r\n";
		if (isset($servingtypes)) {
			
			// Loop by shoppinglists
			foreach ($servingtypes as $servingtype) {
				$xml[] = "\t".'&lt;servingtype&gt;'."\r\n";
				foreach ($servingtype as $key=>$value) {
					$xml[] = ($value == null) ? "\t\t".'&lt;'.$key.' xsi:nil="true"&gt;&lt;/'.$key.'&gt;'."\r\n" : "\t\t".'&lt;'.$key.'&gt;<![CDATA['.$value.']]>&lt;/'.$key.'&gt;'."\r\n";
				}
				$xml[] = "\t".'&lt;/servingtype&gt;'."\r\n";
			}
		}
		$xml[] = '&lt;/servingtypes&gt;';
		
		// Convert the xml array content to string
		$string = htmlspecialchars_decode(implode("", $xml));
		
		// Set the file path
		$path	= $folder.date("dmY_His").'_yoorecipeservingtypes.xml';
		$path	= JPath::clean($path);
		
		// Write file on disk
		JFile::write($path, $string, $use_streams=false);

		return $path;
	}

	/**
	 * cuisines2XML
	 * @param	String	The folder were the XML files will be saved
	 * @return	String	The XML file path
	 */
	private static function cuisines2XML($folder) {
		
		// Get cuisines details
		$cuisinesModel	= JModelLegacy::getInstance('cuisines', 'YooRecipeModel');
		$cuisines 		= $cuisinesModel->getAllCuisines();
		
		// Checks if there aren't cuisines
		if (count($cuisines)<=0) {
			return '';
		}
		
		// Prepare XML file header
		$xml	= array();
		$xml[]	= '&lt;?xml version="1.0" encoding="utf-8"?&gt;'."\r\n";
		
		// Prepare XML file content
		$xml[] = '&lt;cuisines xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"&gt;'."\r\n";
		if (isset($cuisines)) {
			
			// Loop by shoppinglists
			foreach ($cuisines as $cuisine) {
				$xml[] = "\t".'&lt;cuisine&gt;'."\r\n";
				foreach ($cuisine as $key=>$value) {
					$xml[] = ($value == null) ? "\t\t".'&lt;'.$key.' xsi:nil="true"&gt;&lt;/'.$key.'&gt;'."\r\n" : "\t\t".'&lt;'.$key.'&gt;<![CDATA['.$value.']]>&lt;/'.$key.'&gt;'."\r\n";
				}
				$xml[] = "\t".'&lt;/cuisine&gt;'."\r\n";
			}
		}
		$xml[] = '&lt;/cuisines&gt;';
		
		// Convert the xml array content to string
		$string = htmlspecialchars_decode(implode("", $xml));
		
		// Set the file path
		$path	= $folder.date("dmY_His").'_yoorecipecuisines.xml';
		$path	= JPath::clean($path);
		
		// Write file on disk
		JFile::write($path, $string, $use_streams=false);

		return $path;
	}
}