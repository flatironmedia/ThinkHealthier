<?php 

function processExtraText($myStr, $period = false) { // replace new lines and multiple <br />'s with a single <br />
	$myStr = nl2br($myStr, true);
	$myStr = str_replace("<br>", "<br />", $myStr);
	$myStr = str_replace("<br/>", "<br />", $myStr);
	if ($period) {
		$myStr = str_replace(".", ".<br />", $myStr);
	}
	$myStr = str_replace("\n", "", $myStr);
	$myStr = str_replace("\t", "", $myStr);
	$myStr = str_replace("\r", "", $myStr);
	$myStr = str_replace("\r\n", "", $myStr);
	for ($i=0; $i < 10; $i++) {
		$myStr = str_replace("<br /><br />", "<br />", $myStr);
		$myStr = str_replace("<br /> <br />", "<br />", $myStr);
	}
	$myStr = str_replace("<br />\n", "", $myStr);
	$myStr = str_replace("<br />\t", "", $myStr);
	$myStr = str_replace("<br />\r", "", $myStr);
	$myStr = str_replace("<br />\r\n", "", $myStr);
	$myStr = preg_replace('/\<br \/\>$/', '', $myStr);
	$myStr = preg_replace('/^\<br \/\>/', '', $myStr);
	return $myStr;
}

include_once("class.DateConvert.php");

function whichImage($img,$type,$ext) {
	$whichImage = $img . $type . $ext;
	if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $whichImage)) {
		$whichImage = $img . $ext;
	}
	return $whichImage;
}

function removeMosimage($str) {
	$mosStr =  substr($str, 0, 10);
	if ($mosStr == '{mosimage}') {
		$newStr = substr($str, 10);
	} elseif ($mosStr == '{mosimages}') {
		$newStr = substr($str, 11);
	} else {
		$newStr = $str;
	}
	return $newStr;
}
function removeRelated($str) {
	$relStr =  "{relatedarticles}";
	$relPos = strrpos($str, $relStr);
	if ($relPos === false) {
		$newStr = $str;
	} else {
		$relStrAr = explode($relStr, $str); $newStr = '';
		for ($i=0;$i<sizeof($relStrAr);$i++) {
			$newStr .= $relStrAr[$i];
		}
	}
	return $newStr;
}
function removeText($str,$txt) {
	$txt = strtolower($txt);
	if ($txt == '' || $txt == null) {
		$newTxtStr = $str;
	} else {
		$txtStr =  strtolower(substr($str, 0, strlen($txt)));
		if ($txtStr == $txt) {
			$newTxtStr = substr($str, strlen($txt));
		} else {
			$newTxtStr = $str;
		}
	}
	return $newTxtStr;
}

function cutText($str, $len) {
	if (!$str || strlen($str) == 0) {
		return;
	// edited by staiano 20121221 } elseif (!$len || strlen($len) == 0 || $len == 0 || $len >= strlen($str)) {
	} elseif (!$len || $len == 0 || $len >= strlen($str)) {
		return $str; // . " - " . strlen($str);
	} else {
		for ($i = $len + 2; $i >= $len - 25; $i--) {
			if (substr($str, $i, 1) == ' ' || substr($str, $i, 1) == ',') {
				$newLen = $i;
				$endText = '...';
				break;
			} elseif (substr($str, $i, 1) == '.' || substr($str, $i, 1) == '!' || substr($str, $i, 1) == '?') {
				$newLen = $i+1;
				$endText = '';
				break;
			} else { $newLen = $len; }
		}
		$newStr = substr($str, 0, $newLen) . $endText; // . " - " . $newLen;
		return $newStr;
	}
}

function convert_chars($string) { /**
 *  ‘  8216  curly left single quote
 *  ’  8217  apostrophe, curly right single quote
 *  “  8220  curly left double quote
 *  ”  8221  curly right double quote
 *  —  8212  em dash
 *  –  8211  en dash
 *  …  8230  ellipsis
 */
$search = array(
//                '&',
//                '<',
//                '>',
//                '"',
				'â€™', /* bad char for astrocenter */
                chr(212),
                chr(213),
                chr(210),
                chr(211),
                chr(209),
                chr(208),
                chr(201),
                chr(145),
                chr(146),
                chr(147),
                chr(148),
                chr(151),
                chr(150),
                chr(133), /*horizontal ellipsis*/
                chr(224), /* à */
                chr(225), /* á */
                chr(226), /* â */
                chr(227), /* ã */
                chr(228), /* ä */
                chr(229), /* å */
                chr(230), /* æ */
                chr(231), /* ç */
                chr(232), /* è */
                chr(233), /* é */
                chr(234), /* ê */
                chr(235), /* ë */
                chr(236), /* ì */
                chr(237), /* í */
                chr(238), /* î */
                chr(239), /* ï */
                chr(240), /* ð */
                chr(241), /* ñ */
                chr(242), /* ò */
                chr(243), /* ó */
                chr(244), /* ô */
                chr(245), /* õ */
                chr(246), /* ö */
				chr(188), /* 1/4 */
				chr(189), /* 1/2 */
				chr(190), /* 3/4 */
				'&nbsp;', /* space */
				'Ê', /* space */
				'â€™', /* screwy â€™ */
				'–',
				'®'
                );
$replace = array(
//                '&amp;',
//                '&lt;',
//                '&gt;',
//                '&quot;',
				'&#8217;', /* bad char for astrocenter */
                '&#8216;',
                '&#8217;',
                '&#8220;',
                '&#8221;',
                '&#8211;',
                '&#8212;',
                '&#8230;',
                '&#8216;',
                '&#8217;',
                '&#8220;',
                '&#8221;',
                '&#8211;',
                '&#8212;',
                '&#8230;', /*horizontal ellipsis*/
				'&#224;', /* à */
				'&#225;', /* á */
				'&#226;', /* â */
				'&#227;', /* ã */
				'&#228;', /* ä */
				'&#229;', /* å */
				'&#230;', /* æ */
				'&#231;', /* ç */
				'&#232;', /* è */
				'&#233;', /* é */
				'&#234;', /* ê */
				'&#235;', /* ë */
				'&#236;', /* ì */
				'&#237;', /* í */
				'&#238;', /* î */
				'&#239;', /* ï */
				'&#240;', /* ð */
				'&#241;', /* ñ */
				'&#242;', /* ò */
				'&#243;', /* ó */
				'&#244;', /* ô */
				'&#245;', /* õ */
				'&#246;', /* ö */
				'&#188;', /* 1/4 */
				'&#189;', /* 1/2 */
				'&#190;', /* 3/4 */
				' ', /* space */
				' ', /* space */
				'&quot;', /* screwy â€™ */
				'&#8212;',
				'&reg;'
                );
    // return str_replace($search, $replace, $string); 
    // return normalize_special_characters(str_replace($search, $replace, $string));
    return normalize_special_characters($string);
}
function normalize_special_characters( $str ) 
{ 
 // Quotes cleanup 
    $str = ereg_replace( chr(ord("`")), "'", $str ); // `
    $str = ereg_replace( chr(ord("´")), "'", $str ); // ´
    $str = ereg_replace( chr(ord("„")), ",", $str ); // „
    $str = ereg_replace( chr(ord("`")), "'", $str ); // `
    $str = ereg_replace( chr(ord("´")), "'", $str ); // ´
    $str = ereg_replace( chr(ord("“")), "\"", $str ); // “
    $str = ereg_replace( chr(ord("”")), "\"", $str ); // ”
    $str = ereg_replace( chr(ord("´")), "'", $str ); // ´


    $unwanted_array2 = array( 'Š'=>htmlentities('Š'), 'š'=>htmlentities('š'), 'Ž'=>htmlentities('Ž'), 'ž'=>htmlentities('ž'), 'À'=>htmlentities('À'), 
								'Á'=>htmlentities('Á'), 'Â'=>htmlentities('Â'), 'Ã'=>htmlentities('Ã'), 'Ä'=>htmlentities('Ä'), 'Å'=>htmlentities('Å'), 
								'Æ'=>htmlentities('Æ'), 'Ç'=>htmlentities('Ç'), 'È'=>htmlentities('È'), 'É'=>htmlentities('É'), 'Ê'=>htmlentities('Ê'), 
								'Ë'=>htmlentities('Ë'), 'Ì'=>htmlentities('Ì'), 'Í'=>htmlentities('Í'), 'Î'=>htmlentities('Î'), 'Ï'=>htmlentities('Ï'), 
								'Ñ'=>htmlentities('Ñ'), 'Ò'=>htmlentities('Ò'), 'Ó'=>htmlentities('Ó'), 'Ô'=>htmlentities('Ô'), 'Õ'=>htmlentities('Õ'), 
								'Ö'=>htmlentities('Ö'), 'Ø'=>htmlentities('Ø'), 'Ù'=>htmlentities('Ù'), 'Ú'=>htmlentities('Ú'), 'Û'=>htmlentities('Û'), 
								'Ü'=>htmlentities('Ü'), 'Ý'=>htmlentities('Ý'), 'Þ'=>htmlentities('Þ'), 'ß'=>htmlentities('ß'), 'à'=>htmlentities('à'), 
								'á'=>htmlentities('á'), 'â'=>htmlentities('â'), 'ã'=>htmlentities('ã'), 'ä'=>htmlentities('ä'), 'å'=>htmlentities('å'), 
								'æ'=>htmlentities('æ'), 'ç'=>htmlentities('ç'), 'è'=>htmlentities('è'), 'é'=>htmlentities('é'), 'ê'=>htmlentities('ê'), 
								'ë'=>htmlentities('ë'), 'ì'=>htmlentities('ì'), 'í'=>htmlentities('í'), 'î'=>htmlentities('î'), 'ï'=>htmlentities('ï'), 
								'ð'=>htmlentities('ð'), 'ñ'=>htmlentities('ñ'), 'ò'=>htmlentities('ò'), 'ó'=>htmlentities('ó'), 'ô'=>htmlentities('ô'), 
								'õ'=>htmlentities('õ'), 'ö'=>htmlentities('ö'), 'ø'=>htmlentities('ø'), 'ù'=>htmlentities('ù'), 'ú'=>htmlentities('ú'), 
								'û'=>htmlentities('û'), 'ý'=>htmlentities('ý'), 'ý'=>htmlentities('ý'), 'þ'=>htmlentities('þ'), 'ÿ'=>htmlentities('ÿ'), ' '=>'  ' );
    $str = strtr( $str, $unwanted_array2 );

    $unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 
                                'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 
                                'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 
                                'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 
                                'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
    $str = strtr( $str, $unwanted_array );

 // Bullets, dashes, and trademarks 
    $str = ereg_replace( chr(149), "&#8226;", $str ); // bullet •
    $str = ereg_replace( chr(150), "&ndash;", $str ); // en dash
    $str = ereg_replace( chr(151), "&mdash;", $str ); // em dash
    $str = ereg_replace( chr(153), "&#8482;", $str ); // trademark
    $str = ereg_replace( chr(169), "&copy;", $str ); // copyright mark
    $str = ereg_replace( chr(174), "&reg;", $str ); // registration mark
    $str = ereg_replace( "â€™", "&#8217;", $str ); // bad char for astrocenter
    $str = ereg_replace( chr(212), "&#8216;", $str );
    $str = ereg_replace( chr(213), "&#8217;", $str );
    $str = ereg_replace( chr(210), "&#8220;", $str );
    $str = ereg_replace( chr(211), "&#8221;", $str );
    $str = ereg_replace( chr(209), "&#8211;", $str );
    $str = ereg_replace( chr(208), "&#8212;", $str );
    $str = ereg_replace( chr(201), "&#8230;", $str );
    $str = ereg_replace( chr(145), "&#8216;", $str );
    $str = ereg_replace( chr(146), "&#8217;", $str );
    $str = ereg_replace( chr(147), "&#8220;", $str );
    $str = ereg_replace( chr(148), "&#8221;", $str );
    $str = ereg_replace( chr(151), "&#8211;", $str );
    $str = ereg_replace( chr(150), "&#8212;", $str );
    $str = ereg_replace( chr(133), "&#8230;", $str ); // horizontal ellipsis
	$str = ereg_replace( "â€™", "&quot;", $str ); // screwy â€™
    $str = ereg_replace( chr(188), "&#188;", $str ); // 1/4
    $str = ereg_replace( chr(189), "&#189;", $str ); // 1/2
    $str = ereg_replace( chr(190), "&#190;", $str ); // 3/4

    return $str; 
} 
?>
