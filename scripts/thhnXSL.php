<?php
// ini_set('display_errors', 'On');
// error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('America/New_York');

/* date convert */
include_once($_SERVER["DOCUMENT_ROOT"] . "/scripts/convertChars.php");

// put full path to Smarty.class.php
require($_SERVER["DOCUMENT_ROOT"] . "/Smarty-2.6.12/libs/Smarty.class.php");

// create object
$smarty = new Smarty;

$smarty->template_dir = $_SERVER["DOCUMENT_ROOT"] . "/smarty/templates";
$smarty->compile_dir = $_SERVER["DOCUMENT_ROOT"] . "/smarty/templates_c";
$smarty->cache_dir = $_SERVER["DOCUMENT_ROOT"] . "/smarty/cache";
$smarty->config_dir = $_SERVER["DOCUMENT_ROOT"] . "/smarty/configs";

// grab the date requested, if none is passed use today
if (isset($_GET['date'])) {
  $date = $_GET['date'];
} else { $date = date('Y-m-d'); }

$articleDate = $date;

// if the date is messed up, fix it
if ($date == '0' || $date == '') { $date = date('Y-m-d'); }

// we need <strong>...</strong> around the day of the week
$articleDateFullStrong = '<strong>' . $dateConvertObj->convert($articleDate, 'Y-m-d', 'l') . '</strong>' . $dateConvertObj->convert($articleDate, 'Y-m-d', ', F j, Y');
$articleDateFull = $dateConvertObj->convert($articleDate, 'Y-m-d', 'F j, Y');

function recGlob($pattern, $flags = 0) {
    $files = glob($pattern, $flags); 
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
        $files = array_merge($files, recGlob($dir.'/'.basename($pattern), $flags));
    }
    return $files;
}
function getImage($srchType, $srchID) {
	$srchTypeAr = ['art', 'recipe'];
	$tmpIm = recGlob($_SERVER["DOCUMENT_ROOT"] . '/images/*/' . $srchTypeAr[$srchType] . $srchID . '.*');
	if (count($tmpIm) == 0 || count($tmpIm) == null) {
		$locIm = "default_image.jpg";
	} else { 
		$imAr = explode("/www.thinkhealthier.com/images/", $tmpIm[0]);
		$locIm = $imAr[1];
	}
	if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/images/" . $locIm)) { // if you can find image use default
		return "default_image.jpg";
	} else {
		return $locIm;
	}
}

// $imPrefix = "http://thdevpul.flatironmedia.netdna-cdn.com/images/";
// $imPrefix = "http://cdn.thinkhealthier.com/images/";
$imPrefix = "/images/"; // use ##domain_name[thinkhealthier.com]## in the XSL templates

// $artIm = $imPrefix . getImage(0,'18402');
// $recIm = $imPrefix . getImage(1, '12');
// echo "<br>" . $artIm . "<br>" . PHP_EOL;
// echo "<br>" . $recIm . "<br>" . PHP_EOL;

function convert_chars_dummy($str) { return $str; }

$con = mysqli_connect("10.10.10.186", "mdm3jomla", "horsetrainfRANCEqueen", "THCMS");

$thhnDailyQry = "select date, ftrdArticleID, ftrdRecipeID, Article1ID, Article2ID, Article3ID from DailyHealthyNews where date = '$articleDate';";
$thhnDailyRes = mysqli_query($con, "$thhnDailyQry");

if ( mysqli_num_rows($thhnDailyRes) == 0 ) { // if no data for this date grab oldest record
	$thhnDailyQry = "select date, ftrdArticleID, ftrdRecipeID, Article1ID, Article2ID, Article3ID from DailyHealthyNews order by id limit 1;";
	$thhnDailyRes = mysqli_query($con, "$thhnDailyQry");
}
$thhnDailyRow = mysqli_fetch_array($thhnDailyRes);
$thhnDate = $thhnDailyRow['date'];
$thhnFtrdArticleID = $thhnDailyRow['ftrdArticleID'];
$thhnFtrdRecipeID = $thhnDailyRow['ftrdRecipeID'];
$thhnArticle1ID = $thhnDailyRow['Article1ID'];
$thhnArticle2ID = $thhnDailyRow['Article2ID'];
$thhnArticle3ID = $thhnDailyRow['Article3ID'];

$artIDAr = [$thhnFtrdArticleID, $thhnArticle1ID, $thhnArticle2ID, $thhnArticle3ID];

$thhnContentQry = "select id, title, introtext from ip8jd_content where id in($thhnFtrdArticleID, $thhnArticle1ID, $thhnArticle2ID, $thhnArticle3ID);";
$thhnContentRes = mysqli_query($con, "$thhnContentQry");

while ( $thhnContentRow = mysqli_fetch_array($thhnContentRes) ) {
	$thhnContentIDTmp = $thhnContentRow['id'];
	
	for ($h = 0; $h < count($artIDAr); $h++) {
		if ($thhnContentIDTmp == $artIDAr[$h]) {
			$i = $h;
			// echo "$h ~ $artIDAr[$h]<br>" . PHP_EOL;
		}
	}

	$thhnContentID[$i] = $thhnContentRow['id'];
	// looks and see if there is teaser text else use begining of article text
	$thhnTeaserQry = "select id, intro from ip8jd_altarticledata_data where article_id = " . $thhnContentRow['id'] . ";";
	$thhnTeaserRes = mysqli_query($con, "$thhnTeaserQry");
	if (mysqli_num_rows($thhnTeaserRes) != 0) {
		$thhnTeaserRow = mysqli_fetch_array($thhnTeaserRes);
		if (strlen(trim($thhnTeaserRow['intro'])) < 10) { // if blank use article data
			$thhnContentText[$i] = convert_chars(strip_tags($thhnContentRow['introtext']));
		} else { // use teaser
			$thhnContentText[$i] = convert_chars(strip_tags($thhnTeaserRow['intro']));
		}
	} else { // default to article data
		$thhnContentText[$i] = convert_chars(strip_tags($thhnContentRow['introtext']));
	}
	$thhnContentTitle[$i] = convert_chars($thhnContentRow['title']);
	$thhnContentIm[$i] = $imPrefix . getImage(0,$thhnContentRow['id']);
	$thhnContentURL[$i] = "http://www.thinkhealthier.com/index.php?option=com_content&view=article&Itemid=752&id=" . $thhnContentRow['id'];
}

$thhnRecipeQry = "select id, title, description from ip8jd_yoorecipe where id = $thhnFtrdRecipeID;";
$thhnRecipeRes = mysqli_query($con, "$thhnRecipeQry");
$thhnRecipeRow = mysqli_fetch_array($thhnRecipeRes);
$thhnRecipeID = $thhnRecipeRow['id'];
$thhnRecipeTitle = convert_chars($thhnRecipeRow['title']);
$thhnRecipeText = convert_chars(strip_tags($thhnRecipeRow['description']));
$thhnRecipeIm = $imPrefix . getImage(1,$thhnRecipeRow['id']);
$thhnRecipeURL = "http://www.thinkhealthier.com/index.php?option=com_yoorecipe&view=recipe&Itemid=639&id=" . $thhnRecipeRow['id'];

mysqli_free_result($thhnDailyRes);
mysqli_free_result($thhnContentRes);
mysqli_free_result($thhnRecipeRes);
mysqli_close($con);

// random number is newsletter title number [201-299] + 6 digit date
//$adRandNum1 = "201" . date('ymd');
$adRandNum1 = "201" . $dateConvertObj->convert($date, 'Y-m-d', 'ymd');

// Mailing ID for Return Path: 1 + newsletter title number [201-299] + 6 digit date
//$mailingID = "1201" . date('Ymd');
$mailingID = "1201" . $dateConvertObj->convert($date, 'Y-m-d', 'Ymd');

$size1 = 180; // size for featured article and recipe text
$size2 = 90; // size for 3 additional articles text

$smarty->assign('mailingID', $mailingID);
$smarty->assign('articleDate', $articleDate);
$smarty->assign('articleDateFull', $articleDateFull);
$smarty->assign('adRandNum1', $adRandNum1);

$smarty->assign('ftrdArtTitle', $thhnContentTitle[0]);
$smarty->assign('ftrdArtText', cutText($thhnContentText[0], $size1));
$smarty->assign('ftrdArtIm', $thhnContentIm[0]);
$smarty->assign('ftrdArtURL', $thhnContentURL[0]);

$smarty->assign('ftrdRecipeTitle', $thhnRecipeTitle);
$smarty->assign('ftrdRecipeText', cutText($thhnRecipeText, $size2));
$smarty->assign('ftrdRecipeIm', $thhnRecipeIm);
$smarty->assign('ftrdRecipeURL', $thhnRecipeURL);

$smarty->assign('art1Title', $thhnContentTitle[1]);
$smarty->assign('art1Text', cutText($thhnContentText[1], $size2));
$smarty->assign('art1Im', $thhnContentIm[1]);
$smarty->assign('art1URL', $thhnContentURL[1]);
$smarty->assign('art2Title', $thhnContentTitle[2]);
$smarty->assign('art2Text', cutText($thhnContentText[2], $size2));
$smarty->assign('art2Im', $thhnContentIm[2]);
$smarty->assign('art2URL', $thhnContentURL[2]);
$smarty->assign('art3Title', $thhnContentTitle[3]);
$smarty->assign('art3Text', cutText($thhnContentText[3], $size2));
$smarty->assign('art3Im', $thhnContentIm[3]);
$smarty->assign('art3URL', $thhnContentURL[3]);

$xslHdr = ""
. "<xsl:stylesheet xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" version=\"1.0\">"
. "\n<xsl:output doctype-public=\"-//W3C//DTD XHTML 1.0 Strict//EN\" doctype-system=\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\" indent=\"yes\" encoding=\"UTF-8\" method=\"xml\" omit-xml-declaration=\"yes\" />\n";

$xslFtr = "\n</xsl:stylesheet>\n";

$smarty->assign('xslHdr', $xslHdr);
$smarty->assign('xslFtr', $xslFtr);

// display it
$smarty->display($_SERVER["DOCUMENT_ROOT"] . "/scripts/thhnXSL.tpl");
?>
