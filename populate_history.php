<?php
echo('<pre>'.print_r("...", true).'</pre>');

define('_JEXEC', 1);
define('JPATH_BASE', $_SERVER["DOCUMENT_ROOT"]);

require JPATH_BASE.'/includes/defines.php';
require JPATH_BASE.'/includes/framework.php';

// Initialiase variables.
$db    = JFactory::getDbo();
$query = $db->getQuery(true);


$query = "SELECT * FROM `ip8jd_newsletter_history` ";
$db->setQuery($query);
$records = $db->loadObjectList();
if (count($records) > 1) die('<pre>'.print_r("Already populated!", true).'</pre>'); 


$query = "SELECT * FROM `DailyHealthyNews` ";
$db->setQuery($query);
$records = $db->loadObjectList();

$id = 1;

foreach ($records as $key => $record) {

	$query = "INSERT INTO `#__newsletter_history` 
						(`id`, 
						`type`, 
						`article_recipe_id`, 
						`timestamp`, 
						`position`) 
					VALUES 
						(NULL, 
						1, 
						".$record->ftrdArticleID.", 
						'".$record->date." 12:00:00', 
						0);";
	$db->setQuery( $query );
	$db->execute();

	$query = "INSERT INTO `#__newsletter_history` 
						(`id`, 
						`type`, 
						`article_recipe_id`, 
						`timestamp`, 
						`position`) 
					VALUES 
						(NULL, 
						1, 
						".$record->article1ID.", 
						'".$record->date." 12:00:00', 
						1);";
	$db->setQuery( $query );
	$db->execute();

	$query = "INSERT INTO `#__newsletter_history` 
						(`id`, 
						`type`, 
						`article_recipe_id`, 
						`timestamp`, 
						`position`) 
					VALUES 
						(NULL, 
						1, 
						".$record->article2ID.", 
						'".$record->date." 12:00:00', 
						2);";
	$db->setQuery( $query );
	$db->execute();

	$query = "INSERT INTO `#__newsletter_history` 
						(`id`, 
						`type`, 
						`article_recipe_id`, 
						`timestamp`, 
						`position`) 
					VALUES 
						(NULL, 
						1, 
						".$record->article3ID.", 
						'".$record->date." 12:00:00', 
						3);";
	$db->setQuery( $query );
	$db->execute();

	$query = "INSERT INTO `#__newsletter_history` 
						(`id`, 
						`type`, 
						`article_recipe_id`, 
						`timestamp`, 
						`position`) 
					VALUES 
						(NULL, 
						2, 
						".$record->ftrdRecipeID.", 
						'".$record->date." 12:00:00', 
						0);";
	$db->setQuery( $query );
	$db->execute();



	//echo '<pre> '.$id++." ".print_r($record, true).'</pre>';	
}



echo "Finished!";



?>