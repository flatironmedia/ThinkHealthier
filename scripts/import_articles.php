<?php

// Setting error reporting... Uncomment the block below for displaying errors
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

define('_JEXEC', 1);
define('JPATH_BASE', $_SERVER["DOCUMENT_ROOT"]);

require JPATH_BASE.'/includes/defines.php';
require JPATH_BASE.'/includes/framework.php';

// Downloading the file 
echo('<pre>'.print_r("Downloading the file...", true).'</pre>');
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://www.healthday.com/wwwroot/flatiron/newsfeed_daily_t.dat');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "flatiron:jacquot1218");
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
$file = curl_exec($ch);
curl_close($ch);

file_put_contents('newsfeed_daily_t.dat', $file);

// Reading the XML file and converting it to object structure
$file = "newsfeed_daily_t.dat";
$feed = file_get_contents($file);
$xml = simplexml_load_string($feed, null, LIBXML_NOCDATA) or die("Error: Cannot create object");

$db = JFactory::getDbo();

// Proccessing articles one by one
foreach ($xml->ARTICLE as $key => $article) {
	echo('<pre>'.print_r("#############################################################################################################################################################################################", true).'</pre>');
	echo('<pre>'.print_r($article->HEADLINE->__toString(), true).'</pre>');

	// Getting tag list(aliases) from XML (and converting aliases)
	$tags = array();
	foreach ($article->TOPICS->TOPIC as $key => $topic) {
		$topicArray = (array)$topic; // had to cast it to array to access properly
		$temp = $topicArray['@attributes']['ID'];

		$temp = str_replace("=", "eee", $temp);
		$temp = str_replace("-", "ddd", $temp);
		$temp = strtolower($temp);
		$temp = "\"h-news-tag-".$temp."\"";

		$tags[] = $temp;

		echo('<pre>'.print_r($topicArray['@attributes']['ID'], true).'</pre>');
	}
	echo('<pre>'.print_r(implode(", ", $tags), true).'</pre>');

	// Getting matching tag ids from database based on alias
	$query = "SELECT id FROM `#__tags` WHERE alias IN (".implode(", ", $tags).")";
	$db->setQuery($query);
	$tagIDs = $db->loadObjectList();
	echo('<pre>'.print_r($tagIDs, true).'</pre>');

	// Setting and saving article data
	$jarticle 					= new stdClass();
	$jarticle->title			= $article->HEADLINE->__toString(); //had to convert it to String to access properly
	$jarticle->alias			= JFilterOutput::stringURLSafe( $article->HEADLINE->__toString() );

	$jarticle->introtext		= $article->BLURB->__toString();
	$jarticle->fulltext			= $article->BODY->__toString();
	$jarticle->fulltext			.= '<div class="healthday-copyright">'.$article->COPYRIGHT->__toString().'</div>';


	$images = array(
					'image_intro' 	=> strtok($article->FEATURE_IMAGE, '?')."?resize=245:165", 
					'float_intro'	=> "",
					'image_intro_alt'	=> "",
					'image_intro_caption'	=> "",
					'image_fulltext'=> strtok($article->FEATURE_IMAGE, '?')."?resize=600:400",
					'float_fulltext'	=> "",
					'image_fulltext_alt'	=> "",
					'image_fulltext_caption'	=> "" 
					);

	$jarticle->images 			= json_encode($images);

	$jarticle->state			= 1;
	$jarticle->catid			= 230;

	$jarticle->created = $jarticle->publish_up = JFactory::getDate()->toSQL();
	//$jarticle->created_by			= JFactory::getUser()->id;

	$jarticle->access			= 1;
	//$jarticle->metadata			= '{"page_title":"New article added programmatically","author":"","robots":""}';
	$jarticle->language			= '*';

	$table = JTable::getInstance('content', 'JTable');
	$data = (array)$jarticle;

	// Bind data
	if (!$table->bind($data))
	{
	    echo('<pre>'.print_r("bind failed", true).'</pre>');
	    //return false;
	    continue;
	}

	// Check the data.
	if (!$table->check())
	{
	    echo('<pre>'.print_r("check failed", true).'</pre>');
	    //return false;
	    continue;
	}

	// Store the data.
	if (!$table->store())
	{
	    echo('<pre>'.print_r("storing failed, article is probaly in the database already", true).'</pre>');
	    //return false;
	    continue;
	}
	else // if article storing was successful, assign tags to article
	{
		echo('<pre>'.print_r($table->id, true).'</pre>'); // id of stored article
		foreach ($tagIDs as $key => $tagID) {
			$query = "INSERT INTO `#__contentitem_tag_map` 
								(`type_alias`,
								 `core_content_id`,
								 `content_item_id`,
								 `tag_id`,
								 `tag_date`,
								 `type_id`) 
						 VALUES ('com_content.article',
								 '0',
								 ".$table->id.",
								 ".$tagID->id.",
								 CURRENT_TIMESTAMP,
								 '1');";
			$db->setQuery($query);
			echo('<pre>'.print_r($db->execute(), true).'</pre>');
		}

	}

	

}


?>