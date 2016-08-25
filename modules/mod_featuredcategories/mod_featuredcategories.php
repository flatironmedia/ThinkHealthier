<?php 

/**
 * Hello World! Module Entry Point
 * 
 * @author     Ace Udovicic | OGOSense
 * @link       https://www.ogosense.com/
 */

// No direct access
defined('_JEXEC') or die;
// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';


$categories = ModFeaturedCategoriesHelper::getFeaturedCategories($params);
require JModuleHelper::getLayoutPath('mod_featuredcategories');

//echo('<pre>'.print_r($articles, true).'</pre>');


