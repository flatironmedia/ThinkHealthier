<?php

function rsearch($folder, $pattern) {
    $dir = new RecursiveDirectoryIterator($folder);
    $ite = new RecursiveIteratorIterator($dir);
    $files = new RegexIterator($ite, $pattern, RegexIterator::GET_MATCH);
    $fileList = array();
    foreach($files as $file) {
        $fileList = array_merge($fileList, $file);
    }
    return $fileList;
}


define('_JEXEC', 1);
define('JPATH_BASE', $_SERVER["DOCUMENT_ROOT"]);

require JPATH_BASE.'/includes/defines.php';
require JPATH_BASE.'/includes/framework.php';

echo "<META http-equiv='Content-Type' content='text/html; charset=UTF-8'>";
echo('<pre>'.print_r("ADAM test", true).'</pre>');
 
//$xml = simplexml_load_string($myXMLData, 'SimpleXMLElement', LIBXML_PARSEHUGE) or die("Error: Cannot create object");

//$files = glob("*.htm");

$files = rsearch("./","*.+.htm*");

//die('<pre>'.print_r($files, true).'</pre>');

$openTags = array();

$catNameIdList = array("Drug Category" => 229,
						"Supplement" => 228,
						"Treatment" => 227,
						"Herb Warning Links" => 226,
						"Condition" => 225,
						"Depletion" => 224,
						"Herb Side Effect Links" => 223,
						"Supplement Use Links" => 222,
						"Supplement Side Effect Links" => 221,
						"Supplement Interaction" => 220,
						"Supplement Depletion Links" => 219,
						"Supplement Warning Links" => 218,
						"Condition Symptom Links" => 217,
						"Herb" => 216,
						"Herb Interaction" => 215,
						"Lookup" => 214,
						"Herb Use Links" => 213,
						"Injury" => 212,
						"Nutrition" => 211,
						"Test" => 210,
						"Poison" => 209,
						"Surgery" => 208,
						"Symptoms" => 207,
						"Disease" => 206,
						"Vascular" => 205,
						"Ophthalmology" => 204,
						"Neurology" => 203,
						"Urology" => 202,
						"Hematology" => 201,
						"Pediatrics" => 200,
						"Thoracic Surgery" => 199,
						"Orthopedics" => 198,
						"Plastic Surgery" => 197,
						"First Aid" => 196,
						"Birth Control" => 195,
						"General Surgery" => 194,
						"Discharge Instructions" => 193,
						"SpecialTopic" => 192,
						"Questions To Ask Your Doctor" => 191,
						"Special Topics" => 190);

//die('<pre>'.print_r($files, true).'</pre>');

foreach ($files as $key => $file) {

	$feed = file_get_contents($file);

	$feed = preg_replace('/<script\b[^>]*>.*var URL(.*?)<\/script>/is', '<script>var SSL = "http"</script> <script src="http://default.adam.com/urac/urac.js" type="text/javascript"></script>', $feed); 


	$p = xml_parser_create();
	xml_parse_into_struct($p, $feed, $vals, $index);
	xml_parser_free($p);


	$html = "";
	$keywords = array();
	$title = "";
	$projectTypeID;
	$genContentID;

	//echo('<pre>'.print_r($vals, true).'</pre>');

	foreach ($vals as $key => $val) {
		switch ($val['tag']) {
			case 'ADAMCONTENT':
				if ($val['type'] == 'open') {
					$title = $val['attributes']['TITLE'];
					$html .= '<div class="adamcontent">';
					array_push($openTags, $val['level'].$val['tag']);
					$projectTypeID = $val['attributes']['PROJECTTYPEID'];
					$genContentID = $val['attributes']['GENCONTENTID'];
					$subContent = $val['attributes']['SUBCONTENT'];
					if (empty($catNameIdList[$subContent])) continue 3;
					//if (empty($projectTypeID)) continue 3;
					//if (empty($genContentID)) continue 3;
					echo('<pre>'.print_r("##".$catNameIdList[$subContent], true).'</pre>');

				}
				if ($val['type'] == 'close') 
					if (end($openTags) == $val['level'].$val['tag']) {
						$html .= '</div>';
						array_pop($openTags);
					}
				break;

			case 'VERSIONINFO':
				if ($val['type'] == 'complete') $versionInfo = '<div class="versioninfo">
					<h4>Review date: '.$val['attributes']['REVIEWDATE'].'</h4>
					<p>Reviewed by: '.$val['attributes']['REVIEWEDBY'].'</p>
					</div>';
				//if ($val['type'] == 'close') $html .= '</div>';
				break;

			case 'TEXTCONTENT':
				if ($val['attributes']['TITLE'] == "visHeader") break;
				if ($val['attributes']['TITLE'] == "ADAMDisclaimer") break;
				if ($val['type'] == 'open') {
					$html .= '<div class="textcontent textcontent-'.$val['attributes']['TITLE'].'">';
					array_push($openTags, $val['level'].$val['tag']);
					$html .= '<h3>'.$val['attributes']['TITLE'].'</h3>';
					$html .= $val['value'];
				}
				if ($val['type'] == 'cdata') $html .= $val['value'];
				if ($val['type'] == 'close') 
					if (end($openTags) == $val['level'].$val['tag']) {
						$html .= '</div>';
						array_pop($openTags);
					}
				break;

			case 'SCRIPT':
				/*$html .= '<script ';
				if (isset($val['attributes']['SRC'])) $html .= ' src="'.$val['attributes']['SRC'].'" ';
				if (isset($val['attributes']['TYPE'])) $html .= ' type="'.$val['attributes']['TYPE'].'" ';
				$html .= ' >'.$val['value'].'</script>';*/
				break;

			case 'TEXTLINK':
				if ($val['type'] == 'complete') 
					if ($val['attributes']['LINKTYPE']=="int") $html .= '<a adam_id="'.$val['attributes']['PROJECTTYPEID'].','.$val['attributes']['GENCONTENTID'].'" >'.$val['value'].'</a>';
				break;

			case 'VISUALCONTENT':
				/*
				if ($val['type'] == 'open') {
					if ($val['attributes']['GRAPHICTYPE']=='image_parent') $html .= '<img src="./../adam_import/graphics/images/en/'.$val['attributes']['GENCONTENTID'].'.'.$val['attributes']['MEDIATYPE'].'" alt="'.$val['attributes']['ALT'].'">';
					if ($val['attributes']['GRAPHICTYPE']=='thumb_globals') $html .= '<img src="../adam_import/graphics/tnail/'.$val['attributes']['GENCONTENTID'].'t.'.$val['attributes']['MEDIATYPE'].'" alt="'.$val['attributes']['ALT'].'">';
				}
				if ($val['type'] == 'complete') {
					echo('<pre>'.print_r("OGO SENSE TEST1", true).'</pre>');
					if ($val['attributes']['GRAPHICTYPE']=='image_parent') $html .= '<img src="./../adam_import/graphics/images/en/'.$val['attributes']['GENCONTENTID'].'.'.$val['attributes']['MEDIATYPE'].'" alt="'.$val['attributes']['ALT'].'">';
					if ($val['attributes']['GRAPHICTYPE']=='thumb_globals') $html .= '<img src="../adam_import/graphics/tnail/'.$val['attributes']['GENCONTENTID'].'t.'.$val['attributes']['MEDIATYPE'].'" alt="'.$val['attributes']['ALT'].'">';
				}
				*/
				//if ($val['type'] == 'close') $html .= '</'.strtolower($val['tag']).'>';
				//if ($val['type'] == 'complete') $html .= '<'.strtolower($val['tag']).'>'.$val['value'].'</'.strtolower($val['tag']).'>';
				break;

			case 'WORD':
				if ($val['type'] == 'complete') $keywords[] = $val['value'];
				break;

			// tags to dismiss
			case 'METADATA': break;
			case 'KEYWORDLIST': break;
			case 'KEYWORD': break;
			case 'RELEVANCY': break;
			case 'TAXONOMY': break;
			case 'CODE': break;
			case 'SPECIAL': break;
			case 'TYPE': break;
			case 'VALUE': break;

			default:
				if (strpos($val['value'],'The information provided herein should not') !== false) break;
				if (strpos($val['value'],'A.D.A.M., Inc.  Any duplicat') !== false) break;
				if ($val['attributes']['CLASS'] == "ADAMDisclaimer") break;
				if ($val['attributes']['CLASS'] == "ADAMURAC") break;
				if ($val['type'] == 'open') {
					$html .= '<'.strtolower($val['tag']);
					if ( isset($val['attributes']['CLASS']) ) $html .= ' class='.$val['attributes']['CLASS'];
					$html .='>'.$val['value'];
					array_push($openTags, $val['level'].$val['tag']);

				}
				if ($val['type'] == 'close') 
					if (end($openTags) == $val['level'].$val['tag']) {
						$html .= '</'.strtolower($val['tag']).'>';
						array_pop($openTags);
					}
				if ($val['type'] == 'complete') $html .= '<'.strtolower($val['tag']).'>'.$val['value'].'</'.strtolower($val['tag']).'>';
				if ($val['type'] == 'cdata') $html .= $val['value'];
				break;
		}
	}

	$html .= $versionInfo;

	$db    = JFactory::getDbo();
	//$query = $db->getQuery(true);
	$query = "SELECT * FROM `#__adam_mapping` 
		WHERE projectTypeID=".$projectTypeID." AND genContentID=".$genContentID;
	$db->setQuery($query);
	unset($map_result);
	$map_result = $db->loadObjectList();

	echo('<pre>'.print_r($map_result, true).'</pre>');

	//$result = 1;

	if (empty($map_result)) {
		$query = "INSERT INTO `ip8jd_content` 
					(`id`, 
					`asset_id`, 
					`title`, 
					`alias`, 
					`introtext`, 
					`fulltext`, 
					`state`, 
					`catid`, 
					`created`, 
					`created_by`, 
					`created_by_alias`, 
					`modified`, 
					`modified_by`, 
					`checked_out`, 
					`checked_out_time`, 
					`publish_up`, 
					`publish_down`, 
					`images`, 
					`urls`, 
					`attribs`, 
					`version`, 
					`ordering`, 
					`metakey`, 
					`metadesc`, 
					`access`, 
					`hits`, 
					`metadata`, 
					`featured`, 
					`language`, 
					`xreference`)
				VALUES (NULL, 
					'0', 
					".$db->quote($title).", 
					'', 
					".$db->quote($html).", 
					'', 
					'0', 
					".$db->quote($catNameIdList[$subContent]).", 
					".$db->quote(date("Y-m-d H:i:s")).", 
					'0', 
					'', 
					'0000-00-00 00:00:00.000000', 
					'0', 
					'0', 
					'0000-00-00 00:00:00.000000', 
					'0000-00-00 00:00:00.000000', 
					'0000-00-00 00:00:00.000000', 
					'', 
					'', 
					'', 
					'1', 
					'0', 
					".$db->quote(implode($keywords,", ")).", 
					'', 
					'1', 
					'0', 
					'', 
					'0', 
					'*', 
					'')";
		$db->setQuery($query);
		$result = $db->loadObjectList();
		$last_id = $db->insertid();

		$query = "INSERT INTO `ip8jd_adam_mapping` 
						(`map_id`, `projectTypeID`, `genContentID`, `content_id`) 
				VALUES (NULL, '".$projectTypeID."', '".$genContentID."', '".$last_id."')";
		$db->setQuery($query);
		$result = $db->execute();
	}
	else {
		//die('<pre>'.print_r($map_result, true).'</pre>');
		$query = "UPDATE `#__content` 
				  SET title=".$db->quote($title).", introtext=".$db->quote($html).", metakey=".$db->quote(implode($keywords,", "))."
				  WHERE id=".$map_result[0]->content_id;
		$db->setQuery($query);
		$result = $db->execute();
		echo('<pre>'.print_r($result, true).'</pre>');
	}


}



?>