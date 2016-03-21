<?php
/*------------------------------------------------------------------------
# com_yoorecipe - YooRecipe! Joomla 2.5 & 3.x recipe component
# ------------------------------------------------------------------------
# author    YooRock!
# copyright Copyright (C) 2011 yoorock.fr. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.yoorecipe.com
# Technical Support:  Forum - http://www.yoorecipe.com/
-------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class com_YooRecipeInstallerScript
{
	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent) 
	{
		// $parent is the class calling this method
		$parent->getParent()->setRedirectURL('index.php?option=com_yoorecipe');
	}

	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent) {}

	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent) {}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent) 
	{
		// Installing component manifest file version
        $this->release = $parent->get( "manifest" )->version;
		
		$old_release = $this->getParam('version');
		if (version_compare( $this->release, '5.0.0', '<' )) {
			if (JFolder::exists(JPATH_ADMINISTRATOR.'/components/com_yoorecipe')) {
				JFolder::delete(JPATH_ADMINISTRATOR.'/components/com_yoorecipe');
			}
			if (JFolder::exists(JPATH_SITE.'/components/com_yoorecipe')) {
				JFolder::delete(JPATH_SITE.'/components/com_yoorecipe');
			}
		}
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight($type, $parent) 
	{
		// $type is the type of change (install, update or discover_install)
		if ( $type == 'update' ) {
			
			$old_release = $this->getParam('version');
			
			if (version_compare( $this->release, '5.2.0', '>=' )) {
				$folders_to_delete = array();
				$folders_to_delete[] = JPATH_ADMINISTRATOR.'/components/com_yoorecipe/views/ingredientgroup';
				$folders_to_delete[] = JPATH_ADMINISTRATOR.'/components/com_yoorecipe/views/ingredientgroups';
				$folders_to_delete[] = JPATH_ADMINISTRATOR.'/components/com_yoorecipe/views/units';
				$folders_to_delete[] = JPATH_ADMINISTRATOR.'/components/com_yoorecipe/views/unit';
				
				foreach ($folders_to_delete as $folder) {
					if (JFolder::exists($folder)) {
						JFolder::delete($folder);
					}
				}
				
				$files_to_delete = array();
				$files_to_delete[] = JPATH_SITE.'/media/com_yoorecipe/js/form-edit-ingredient.js';
				$files_to_delete[] = JPATH_SITE.'/media/com_yoorecipe/js/form-edit.js';
				$files_to_delete[] = JPATH_ADMINISTRATOR.'/components/com_yoorecipe/controllers/unit.php';
				$files_to_delete[] = JPATH_ADMINISTRATOR.'/components/com_yoorecipe/controllers/units.php';
				$files_to_delete[] = JPATH_ADMINISTRATOR.'/components/com_yoorecipe/models/tags.php';
				$files_to_delete[] = JPATH_ADMINISTRATOR.'/components/com_yoorecipe/models/unit.php';
				$files_to_delete[] = JPATH_ADMINISTRATOR.'/components/com_yoorecipe/tables/unit.php';
				$files_to_delete[] = JPATH_ADMINISTRATOR.'/components/com_yoorecipe/models/forms/ingredientgroup.xml';
				$files_to_delete[] = JPATH_ADMINISTRATOR.'/components/com_yoorecipe/models/forms/unit.xml';
				
				foreach ($files_to_delete as $file) {
					if (JFile::exists($file)) {
						JFile::delete($file);
					}
				}
				
				// Migrate ingredients
				require_once JPATH_ADMINISTRATOR.'/components/com_yoorecipe/helpers/html/ingredientutils.php';
				JHTMLIngredientUtils::migrateIngredients();
			}
			
			if (version_compare( $this->release, '4.3.8', '==' )) {
				
				// Update landing page menu item
				$db = JFactory::getDbo();
				$db->setQuery("update `#__menu` set link = 'index.php?option=com_yoorecipe&view=landingpage' where link like '%com_yoorecipe%landingpage%'");
				$db->execute();
			}
			
			if (version_compare( $this->release, '3.8.0', '==' )) {

				// Give a language to all recipes without language
				$languagesParams = JComponentHelper::getParams('com_languages');
				$frontend_language = $languagesParams->get('site');
				$db = JFactory::getDBO();	
				$query = $db->getQuery(true);

				$query->update('#__yoorecipe');
				$query->set('language = '.$db->quote($frontend_language));
				$query->where('language = '.$db->quote('*'));
				$db->setQuery((string)$query);
				$db->execute();
			}
		}
	}
	
	/*
	 * get a variable from the manifest file (actually, from the manifest cache).
	 */
	function getParam( $name ) {
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = '.$db->quote('com_yoorecipe'));
		$manifest = json_decode($db->loadResult(), true );
		return $manifest[ $name ];
	}
}