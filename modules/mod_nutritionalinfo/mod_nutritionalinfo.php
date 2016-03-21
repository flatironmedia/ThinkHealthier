<?php

/**
* @version 		1.0.0
* @copyright 	Copyright (C) 2014. All rights reserved.
* @license 		GNU General Public License version 2 or later; see LICENSE.txt
* @author 		Xander <avrhovac@ogosense.com> - http://www.ogosense.com
*/

// No direct access
defined('_JEXEC') or die;
// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';

$nutritions = modNutritionalInfoHelper::getNutritionalInfo();
$dish_size = $nutritions['nb_persons']['value'];
unset($nutritions['nb_persons']);
$nut_slider = $params->get('dynamic_nut');

// Render the module
require JModuleHelper::getLayoutPath('mod_nutritionalinfo', $params->get('layout', 'default'));