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


$articles = ModDropMenuArticlesHelper::getArticles($params);
require JModuleHelper::getLayoutPath('mod_dropmenuarticles');

//echo('<pre>'.print_r($articles, true).'</pre>');


