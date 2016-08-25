<?php

/**
* @version 		1.0.0
* @package 		mod_dropmenurecipes
* @copyright 	Copyright (C) 2014. All rights reserved.
* @license 		GNU General Public License version 2 or later; see LICENSE.txt
* @author 		Xander <avrhovac@ogosense.com> - http://www.ogosense.com
*/

// No direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';
require_once JPATH_ROOT.'/components/com_yoorecipe/helpers/html/yoorecipehelperroute.php';

$result = ModDropMenuRecipesHelper::getFeaturedRecipes($params);

// Render the module
require JModuleHelper::getLayoutPath('mod_dropmenurecipes', $params->get('layout', 'default'));