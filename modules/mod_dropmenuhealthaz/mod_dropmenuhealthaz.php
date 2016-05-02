<?php

/**
* @version 		1.0.0
* @package 		mod_dropmenuhealthaz
* @copyright 	Copyright (C) 2014. All rights reserved.
* @license 		GNU General Public License version 2 or later; see LICENSE.txt
* @author 		Xander <avrhovac@ogosense.com> - http://www.ogosense.com
*/

// No direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';

$result = ModDropMenuHealthAZHelper::getHealthAZCategories($params);

// Render the module
require JModuleHelper::getLayoutPath('mod_dropmenuhealthaz', $params->get('layout', 'default'));