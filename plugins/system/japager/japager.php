<?php
/**
 * ------------------------------------------------------------------------
 * JA System Pager Plugin for J25 & J30
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Joomla! P3P Header Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	System.p3p
 */

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class plgSystemJAPager extends JPlugin
{
	function onAfterInitialise(){
		//include template tools
		if(is_file(JPATH_ROOT . '/templates/ja_wall/template_tools.php')){
			include_once JPATH_ROOT . '/templates/ja_wall/template_tools.php';
		}
	}

	function onAfterRoute()
	{
		$app = JFactory::getApplication('site');
		// don't use in admin
		if ($app->isAdmin() || !in_array(JRequest::getCmd('option'), array('com_content', 'com_k2'))){
			return;
		}

		$this->key_page = 'page';
		$this->key_limitstart = 'limitstart';

		$router = $app->getRouter();
		// attach build/parse rules for page SEF
		$router->attachBuildRule(array($this, 'buildRule'));
		// parse page rule
		$uri = JURI::getInstance ();
		$this->parseRule ($router, $uri);
	}

	function buildRule (&$router, &$uri) {
		$start = intval($uri->getVar($this->key_limitstart));
		
		//remove the page key - we should caculate it from limitstart
		$uri->delVar($this->key_page);

		//not necessary, remove
		if($start == 0){
			$uri->delVar($this->key_limitstart);
		}
		
		//no need to process
		if (!$start){
			return;
		}
		
		$limit = $this->getLimit($uri->getVar ('Itemid'));
		$page = round($start / $limit) + 1;
		if ($page > 1) {
			$uri->delVar($this->key_limitstart);
			$uri->setVar($this->key_page, $page);
		}
	}

	function parseRule (&$router, &$uri) {
		$page = intval($uri->getVar($this->key_page));
		if ($page < 2){
			return array();
		}

		$limit = $this->getLimit($uri->getVar ('Itemid'));
		$start = ($page - 1) * $limit;
		if ($start > 1) {
			// set direct to Request
			JRequest::setVar($this->key_limitstart, $start, 'get', true);
		}
		return array();
	}

	function getLimit ($mid = 0) {
		$app = JFactory::getApplication('site');

		// joomla standard content
		if(JRequest::getCmd('option') == 'com_content'){
			
			$params = new JRegistry;
			$menu = null;
			// get menu item or the current
			if ($mid) {
				$menu = $app->getMenu()->getItem($mid);
			} else {
				$menu = $app->getMenu()->getActive();
			}
			if ($menu) {
				$params->loadString($menu->params);
			}

			$limit = $params->get('num_leading_articles', 0) + $params->get('num_intro_articles', 0);

		} else { //k2 content
			JLoader::register('K2HelperUtilities', JPATH_SITE.'/components/com_k2/helpers/utilities.php');
			$params = K2HelperUtilities::getParams('com_k2');
			$task = JRequest::getWord('task');

			// Get data depending on task
			switch ($task) {
				case 'category':
					// Get category
					$id = JRequest::getInt('id');
					JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_k2/tables');
					$category = JTable::getInstance('K2Category', 'Table');
					$category->load($id);

					// State check
					if (!$category->published || $category->trash) {
						JError::raiseError(404, JText::_('K2_CATEGORY_NOT_FOUND'));
					}

					// Merge params
					$cparams = new JRegistry($category->params);

					// Get the meta information before merging params since we do not want them to be inherited
					if ($cparams->get('inheritFrom'))
					{
						$masterCategory = JTable::getInstance('K2Category', 'Table');
						$masterCategory->load($cparams->get('inheritFrom'));
						$cparams = new JRegistry($masterCategory->params);
					}

					$params->merge($cparams);
					
					$limit = $params->get('num_leading_items') + $params->get('num_primary_items') + $params->get('num_secondary_items');
					break;

				case 'user':
					// Set limit
					$limit = $params->get('userItemCount');
					break;

				case 'tag':
					// Set limit
					$limit = $params->get('tagItemCount');
					break;

				case 'search':
					// Set limit
					$limit = $params->get('genericItemCount');
					break;

				case 'date':
					// Set limit
					$limit = $params->get('genericItemCount');
					break;

				default:
					// Set limit
					$limit = $params->get('num_leading_items') + $params->get('num_primary_items') + $params->get('num_secondary_items');
					break;
			}
		}

		if ($limit){
			return $limit;
		} 

		// default list from configuration
		return $app->getCfg ('list_limit', 10);
	}

	/**
     * Add JA Extended menu parameter in administrator
     *
     * @param   JForm   $form   The form to be altered.
     * @param   array   $data   The associated data for the form
     *
     * @return  null
     */
	function onContentPrepareForm($form, $data)
	{
		// extra option for article in com_content
		if ($form->getName() == 'com_content.article') {
			$this->loadLanguage();
			JForm::addFormPath(dirname(__FILE__) . '/assets');
			$form->loadFile('content_params', false);
		}

		// extra option for menu item
		if ($form->getName() == 'com_menus.item') {
			$this->loadLanguage();
			JForm::addFormPath(dirname(__FILE__) . '/assets');
			$form->loadFile('menu_params', false);
		}
	}
}
