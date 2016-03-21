<?

/*
MyDailyMoment
App ID:	155501944517197
App Secret:	80e55be560a7cf35bb9b0675deb93c5c

Get temp app token by calling:
https://www.facebook.com/dialog/oauth?client_id=155501944517197&redirect_uri=http://www.mydailymoment.com/&scope=manage_pages,offline_access,publish_stream&response_type=token

Returns this:
http://www.mydailymoment.com/#access_token=AAACNbZA4qMk0BAPhG7XZB6eKbZCVnV4nDizREjwzZByJ3EqZCYW7BAixYsEHpg90yDm0AcujFjZAgZC6ztKsJXsbpJS4wYN8XUZD&expires_in=0

Request Page temp tokens:
https://graph.facebook.com/me/accounts?access_token=AAACNbZA4qMk0BAPhG7XZB6eKbZCVnV4nDizREjwzZByJ3EqZCYW7BAixYsEHpg90yDm0AcujFjZAgZC6ztKsJXsbpJS4wYN8XUZD 
 
Returns this:
      {
         "name": "MyDailyMoment Diet & Fitness",
         "access_token": "AAACNbZA4qMk0BAN8lhjzyO9vkemPfBg7R5ldASYcfuBj4Gv5ceZCK4oXYxypnd9D2PiWY1qlmYZA3VboUPFuKdDxYZBaZBClvoUO67l29fAZDZD",
         "category": "Health/beauty",
         "id": "328400570543630"
      },
      {
         "name": "MyDailyMoment Moms",
         "access_token": "AAACNbZA4qMk0BAPeRDfsUDiNdyM5M0Rajqj1Y7VErfa5nX32srj25SoJlrOrgB8IPGPq3gikoBlbVH30AjyjzMoGa2E3l5EpClmqLPgZDZD",
         "category": "Website",
         "id": "339993962710621"
      },
      etc.

Extend token duration to 2 months:   
https://graph.facebook.com/oauth/access_token?client_id=385542631458731&client_secret=13978a44310ba88b24228a8f6c11c3e8&grant_type=fb_exchange_token&fb_exchange_token=AAAFepiOYB6sBAE0OgChWj7eji64RXqYo1xnh7ZC5SFOaECD2e4KInZAENnuMvB7utywKucR2HpTcVZAzLC3xgDzYM52joxsmqTDn0AsDwZDZD
*/

error_reporting(E_ERROR);
ini_set('display_errors', 1);
$id = 201;

// Connect to DB 
$conn = mysqli_connect("localhost", "admin", "air35Q2", "MDMCMS3" );

// Get socail page parameters
$res = mysqli_query($conn, "SELECT * from SocialConnect WHERE id=".$id);
$row = mysqli_fetch_array($res);
//print_r($row);
$token = $row["token"]; 
$pageID = $row["pageID"];
$pageName = $row["pageName"];
$serviceName = $row["serviceName"];
$lastPostDate = $row["lastPostDate"];
$lastPostID = $row["lastPostID"];
$postCounter = $row["postCounter"];
$maxPosts = $row["maxPosts"];
$today = $articleDate = date('Y-m-d');
$tomorrow = date('Y-m-d',mktime(0,0,0,date('m'), date('d')+1, date('y'))); // for gossip
$thisyear = date('Y'); 
$lastyear = date('Y',mktime(0,0,0,date('m'), date('d'), date('y')-1)); 

// For pages where more than one post per day are allowed like Gossip
if ($maxPosts > 1) 
{
	if (($postCounter==$maxPosts) and ($lastPostDate==$today))
	{
		$maxPostsReached = true;
		$postCounter = 0;
	}
	else
	{
		$postCounter = $postCounter + 1;
		$maxPostsReached = false;
	}
}
// For pages where only one post per day is allowed, check if we already posted today
else if ($lastPostDate == $today)
		$maxPostsReached = true;
//otherwise, go ahead and post		
else
	$maxPostsReached = false;	

// Have we already posted for that day? 
if ($maxPostsReached)
{
	mysqli_close($conn);
	echo "<p>".date('Y-m-d G:i')." => Do nothing. <i>".$serviceName." - ".$pageName."</i> has already been updated today</p?";
	return false;
}

// Include facebook class 
require_once("facebook.php");  
  
// function to scrub the bad chars 
include_once("convertChars.php");

// Create MDM App instance   
if ($id == 201){ //ThinkHealthier
	$facebook = new Facebook(array(  
	  'appId'  => '1681919848758845',  
	  'secret' => '022fbf29e5547f630862ef3106c74b62',  
	  'cookie' => true,  
	));  
} else { //MDM
	$facebook = new Facebook(array(  
	  'appId'  => '155501944517197',  
	  'secret' => '80e55be560a7cf35bb9b0675deb93c5c',  
	  'cookie' => true,  
	));  
}

// start ouput
echo date('Y-m-d G:i')." => ";

echo $id;

//switch based on social destination ID
switch ($id) {
	case 101: //MDM Horoscope
		// Get today's Horoscope
		$sql_query = "SELECT * FROM DailyHoroscope WHERE date = '".$today."'";
		$result =  mysqli_query($conn, $sql_query);
		$row = mysqli_fetch_array($result);
		$contentID = $row['id'];
		$postText = convert_chars(strip_tags($row['dailyOutlook'])); //no need to cut the text here
		$postTitle = "Daily Outlook: ".date('l, F j, Y');
		$postLink = "http://www.mydailymoment.com/horoscope/daily_readings/?date=".$articleDate."&utm_source=DHTMLface&utm_content&utm_campaign=HORface";
		$postMessage = "";
		$postImage = "http://cdn.mydailymoment.com/images/stories/hor/mfthIconStrategies.jpg";

		break;
	case 102: //MDM Style
		// Get today's Style tip
		$sql_query = "SELECT mdm_content.introtext, mdm_content.title, mdm_content.id FROM mdm_content, StyleBeautyArticles WHERE mdm_content.id = StyleBeautyArticles.beautyTipID and StyleBeautyArticles.date = '".$today."'";
		$result =  mysqli_query($conn, $sql_query);
		$row = mysqli_fetch_array($result);
		$contentID = $row['id'];
		$postText = cutText(convert_chars(strip_tags($row['introtext'])), 410);
		$postTitle = $row['title'];
		$postLink = "http://www.mydailymoment.com/style_and_beauty/beauty_tips/?date=".$today."&utm_source=DHTMLface&utm_content&utm_campaign=MULTIface";
		$postMessage = "Style Tip for ".date('l, F j, Y');
		// figure out the image
		$contentImage = "/images/stories/style/tips/tip".$contentID.".jpg";
		if (!file_exists("/usr/local/www/www.mydailymoment.com".$contentImage)) 
			$contentImage = "/images/content/style/".$thisyear."/tips/tip" .$contentID.".jpg";
		if (!file_exists("/usr/local/www/www.mydailymoment.com".$contentImage)) 
			$contentImage = "/images/content/style/".$lastyear."/tips/tip" .$contentID.".jpg";
		if (!file_exists("/usr/local/www/www.mydailymoment.com".$contentImage)) 
			$contentImage = "/images/mdmGeneric.jpg";
		$postImage = "http://cdn.mydailymoment.com/slir/w225-h225".$contentImage;
		break;
	case 103: //MDM Moms
		// Get today's Moms tip
		$sql_query = "SELECT mdm_content.introtext, mdm_content.title, mdm_content.id FROM mdm_content, DailyMom WHERE mdm_content.id = DailyMom.dailyMomTipsID and DailyMom.date = '".$today."'";
		$result =  mysqli_query($conn, $sql_query);
		$row = mysqli_fetch_array($result);
		$contentID = $row['id'];
		$postText = cutText(convert_chars(strip_tags($row['introtext'])), 410);
		$postTitle = $row['title'];
		$postLink = "http://www.mydailymoment.com/moms/daily_mom_tips/?date=".$today."&utm_source=DHTMLface&utm_content&utm_campaign=MULTIface";
		$postMessage = "Moms Tip for ".date('l, F j, Y');
		// figure out the image
		$contentImage = "/images/stories/moms/tips/tip".$contentID.".jpg";
		if (!file_exists("/usr/local/www/www.mydailymoment.com".$contentImage)) 
			$contentImage = "/images/content/moms/".$thisyear."/tips/tip" .$contentID.".jpg";
		if (!file_exists("/usr/local/www/www.mydailymoment.com".$contentImage)) 
			$contentImage = "/images/content/moms/".$lastyear."/tips/tip" .$contentID.".jpg";
		if (!file_exists("/usr/local/www/www.mydailymoment.com".$contentImage)) 
			$contentImage = "/images/mdmGeneric.jpg";
		$postImage = "http://cdn.mydailymoment.com/slir/w225-h225".$contentImage;
		break;
	case 104: //MDM Diet
		// Get today's Diet tip
		$sql_query = "SELECT mdm_content.id, mdm_content.introtext, mdm_content.title FROM mdm_content, DietFitnessBytesArticles WHERE DietFitnessBytesArticles.Date = '".$today."' and mdm_content.id = DietFitnessBytesArticles.DietTipArticleID;";
		$result =  mysqli_query($conn, $sql_query);
		$row = mysqli_fetch_array($result);
		$contentID = $row['id'];
		$postText = cutText(convert_chars(strip_tags($row['introtext'])), 410);
		$postTitle = $row['title'];
		$postLink = "http://www.mydailymoment.com/diet_and_fitness/diet_tips/?date=".$today."&utm_source=DHTMLface&utm_content&utm_campaign=DFBface";
		$postMessage = "Diet & Fitness Tip for ".date('l, F j, Y');
		// figure out the image
		$contentImage = "/images/stories/diet/tip/tip".$contentID.".jpg";
		if (!file_exists("/usr/local/www/www.mydailymoment.com".$contentImage)) 
			$contentImage = "/images/content/diet/".$thisyear."/tips/tip" .$contentID.".jpg";
		if (!file_exists("/usr/local/www/www.mydailymoment.com".$contentImage)) 
			$contentImage = "/images/content/diet/".$lastyear."/tips/tip" .$contentID.".jpg";
		if (!file_exists("/usr/local/www/www.mydailymoment.com".$contentImage)) 
			$contentImage = "/images/mdmGeneric.jpg";
		$postImage = "http://cdn.mydailymoment.com/slir/w225-h225".$contentImage;
		break;
	case 105: //MDM Food
		// Get today's Recipe
		$query = "SELECT mdm_rr_recipes.introtext, mdm_rr_recipes.title, mdm_rr_recipes.recipe_id as id FROM mdm_rr_recipes, DailyRecipe WHERE DailyRecipe.RecipeID = mdm_rr_recipes.recipe_id and DailyRecipe.date = '".$today."'";
		$result =  mysqli_query($conn, $query);
		$row = mysqli_fetch_array($result);
		$contentID = $row['id'];
		$postText = convert_chars(strip_tags($row['introtext'])); //no need to cut the text here
		$postTitle = $row['title'];
		$postLink = "http://www.mydailymoment.com/food_and_recipes/recipe_of_the_day/?date=".$today."&utm_source=DHTMLface&utm_medium=face&utm_campaign=ROTDface";
		$postMessage = "Recipe of the Day: ".date('l, F j, Y');
		// figure out the image
		$contentImage = "/images/stories/food/recipe/recipe".$contentID.".jpg";
		if (!file_exists("/usr/local/www/www.mydailymoment.com".$contentImage)) 
			$contentImage = "/images/content/food/".$thisyear."/recipe/recipe" .$contentID.".jpg";
		if (!file_exists("/usr/local/www/www.mydailymoment.com".$contentImage)) 
			$contentImage = "/images/content/food/".$lastyear."/recipe/recipe" .$contentID.".jpg";
		if (!file_exists("/usr/local/www/www.mydailymoment.com".$contentImage)) 
			$contentImage = "/images/mdmGeneric.jpg";
		$postImage = "http://cdn.mydailymoment.com/slir/w225-h225".$contentImage;
		break;
	case 106: //MDM Gossip
		// Get today's Gossip: get the oldest from today that was not yet posted
		$sql_query = "SELECT mdm_content.introtext, mdm_content.title, mdm_content.id, substring_index(substring_index(mdm_content.introtext,_latin1'\" />',1),_latin1'src=\"',-1) as imageSRC  
		                        FROM mdm_content WHERE created < '".$tomorrow."' AND id > ".$lastPostID." AND state = 1 and id != '301' and ( catid = '12' or  catid = '34' ) 
								ORDER BY id ASC LIMIT 1";
		$result =  mysqli_query($conn, $sql_query);
		$row = mysqli_fetch_array($result);
		$contentID = $row['id'];
		$postText = cutText(convert_chars(strip_tags($row['introtext'])), 410);
		$postTitle = $row['title'];
		$postLink = "http://www.mydailymoment.com/index.php?option=com_gossip&Itemid=320&id=".$contentID."&lang=en&utm_source=DHTMLface&utm_content&utm_campaign=MULTIface";
		$postMessage = "";
		$postImage = "http://cdn.mydailymoment.com/".strip_tags($row['imageSRC']); // "images/content/gossip/2012/03/kutcherspace.jpg"
		break;		
	case 107: //MDM Quizzes
		// Get today's Quiz
		$sql_query = "SELECT quizengine.quizzes.id, quizengine.quizzes.title, quizengine.quizzes.description FROM quizengine.quizzes, MDMCMS2.DailyQuizCorner, MDMCMS2.DailyQuizCornerArticles where MDMCMS2.DailyQuizCorner.QuizCornerID = MDMCMS2.DailyQuizCornerArticles.QuizCornerID AND quizengine.quizzes.id = MDMCMS2.DailyQuizCornerArticles.QuizID AND MDMCMS2.DailyQuizCorner.Date = '".$today."'";
		$result =  mysqli_query($conn, $sql_query);
		$row = mysqli_fetch_array($result);
		$contentID = $row['id'];
		$postText = convert_chars(strip_tags($row['description'])); //no need to cut the text here
		$postTitle = $row['title'];
		$postLink = "http://www.mydailymoment.com/app/quiz/userquiz/takequiz/".$contentID."&utm_source=DHTMLface&utm_content&utm_campaign=MULTIface";
		$postMessage = "Today's Quiz: ".date('l, F j, Y');
		// figure out the image
		$contentImage = "/images/stories/quiz/quiz".$contentID.".jpg";
		if (!file_exists("/usr/local/www/www.mydailymoment.com".$contentImage)) 
			$contentImage = "/images/mdmGeneric.jpg";		
		$postImage = "http://cdn.mydailymoment.com/slir/w225-h225".$contentImage;			
		break;
	case 108: //MDM Main - Diet Daily Featured Article
		// Get today's Diet Article
		$sql_query = "SELECT mdm_content.id, mdm_content.introtext, mdm_content.title, substring_index(substring_index(mdm_content.introtext,_latin1'\" />',1),_latin1'src=\"',-1) as imageSRC 
		                        FROM mdm_content, DietFitnessBytesArticles WHERE DietFitnessBytesArticles.Date = '".$today."' and mdm_content.id = DietFitnessBytesArticles.ftrdArticleID;";
		$result =  mysqli_query($conn, $sql_query);
		$row = mysqli_fetch_array($result);
		$contentID = $row['id'];
		$postText = cutText(convert_chars(strip_tags($row['introtext'])), 410);
		$postTitle = $row['title'];
		$postLink = "www.mydailymoment.com/index.php?option=com_article&Itemid=338&id=".$contentID."&lang=en&view=article&utm_source=DHTMLface&utm_content&utm_campaign=MULTIface";
		$postMessage = "";
		$postImage = "http://cdn.mydailymoment.com/".strip_tags($row['imageSRC']); 
		break;
	case 109: //MDM Main - Style Daily Featured Article
		// Get today's Style Article
		$sql_query = "SELECT mdm_content.id, mdm_content.introtext, mdm_content.title, substring_index(substring_index(mdm_content.introtext,_latin1'\" />',1),_latin1'src=\"',-1) as imageSRC 
	                            FROM mdm_content, StyleBeautyArticles WHERE mdm_content.id = StyleBeautyArticles.ftrdArticleID and StyleBeautyArticles.date = '".$today."'";
		$result =  mysqli_query($conn, $sql_query);
		$row = mysqli_fetch_array($result);
		$contentID = $row['id'];
		$postText = cutText(convert_chars(strip_tags($row['introtext'])), 410);
		$postTitle = $row['title'];
		$postLink = "www.mydailymoment.com/index.php?option=com_article&Itemid=338&id=".$contentID."&lang=en&view=article&utm_source=DHTMLface&utm_content&utm_campaign=MULTIface";
		$postMessage = "";
		$postImage = "http://cdn.mydailymoment.com/".strip_tags($row['imageSRC']); 
		break;
	case 110: //MDM Main - Moms Daily Featured Article
		// Get today's Moms Article
		$sql_query = "SELECT mdm_content.id, mdm_content.introtext, mdm_content.title, substring_index(substring_index(mdm_content.introtext,_latin1'\" />',1),_latin1'src=\"',-1) as imageSRC 
	                            FROM mdm_content, DailyMom WHERE mdm_content.id = DailyMom.dailyMomFtrdArticleID and DailyMom.date = '".$today."'";
		$result =  mysqli_query($conn, $sql_query);
		$row = mysqli_fetch_array($result);
		$contentID = $row['id'];
		$postText = cutText(convert_chars(strip_tags($row['introtext'])), 410);
		$postTitle = $row['title'];
		$postLink = "www.mydailymoment.com/index.php?option=com_article&Itemid=338&id=".$contentID."&lang=en&view=article&utm_source=DHTMLface&utm_content&utm_campaign=MULTIface";
		$postMessage = "";
		$postImage = "http://cdn.mydailymoment.com/".strip_tags($row['imageSRC']); 
		break;
	case 111: //MDM Main - Food Daily Featured Article
		// Get today's Food Article
		$sql_query = "SELECT mdm_content.id, mdm_content.introtext, mdm_content.title, substring_index(substring_index(mdm_content.introtext,_latin1'\" />',1),_latin1'src=\"',-1) as imageSRC 
		                        FROM mdm_content, DailyRecipe WHERE mdm_content.id = DailyRecipe.ftrdArticleID and DailyRecipe.date = '".$today."'";								
		$result =  mysqli_query($conn, $sql_query);
		$row = mysqli_fetch_array($result);
		$contentID = $row['id'];
		$postText = cutText(convert_chars(strip_tags($row['introtext'])), 410);
		$postTitle = $row['title'];
		$postLink = "www.mydailymoment.com/index.php?option=com_article&Itemid=338&id=".$contentID."&lang=en&view=article&utm_source=DHTMLface&utm_content&utm_campaign=MULTIface";
		$postMessage = "";
		$postImage = "http://cdn.mydailymoment.com/".strip_tags($row['imageSRC']); 
		break;
	case 112: //MDM Main - Love Daily Featured Article
		// Get today's Love Article
		$sql_query = "SELECT mdm_content.id, mdm_content.introtext, mdm_content.title, substring_index(substring_index(mdm_content.introtext,_latin1'\" />',1),_latin1'src=\"',-1) as imageSRC 
		                        FROM mdm_content, DailyLoveLetter WHERE mdm_content.id = DailyLoveLetter.ftrdArticleID and DailyLoveLetter.date = '".$today."'";								
		$result =  mysqli_query($conn, $sql_query);
		$row = mysqli_fetch_array($result);
		$contentID = $row['id'];
		$postText = cutText(convert_chars(strip_tags($row['introtext'])), 410);
		$postTitle = $row['title'];
		$postLink = "www.mydailymoment.com/index.php?option=com_article&Itemid=338&id=".$contentID."&lang=en&view=article&utm_source=DHTMLface&utm_content&utm_campaign=MULTIface";
		$postMessage = "";
		$postImage = "http://cdn.mydailymoment.com/".strip_tags($row['imageSRC']); 
		break;
	case 113: //MDM Love - Love Daily Tip
		// Get today's Love Tip
		$sql_query = "SELECT mdm_content.id, mdm_content.introtext, mdm_content.title, substring_index(substring_index(mdm_content.introtext,_latin1'\" />',1),_latin1'src=\"',-1) as imageSRC 
		                        FROM mdm_content, DailyLoveLetter WHERE DailyLoveLetter.Date = '".$today."' and mdm_content.id = DailyLoveLetter.tipID;";
		$result =  mysqli_query($conn, $sql_query);
		$row = mysqli_fetch_array($result);
		$contentID = $row['id'];
		$postText = cutText(convert_chars(strip_tags($row['introtext'])), 410);
		$postTitle = $row['title'];
		$postLink = "www.mydailymoment.com/index.php?option=com_article&Itemid=338&id=".$contentID."&lang=en&view=article&utm_source=DHTMLface&utm_content&utm_campaign=MULTIface";
		$postMessage = "Love Tip for ".date('l, F j, Y');
		
		// figure out the image
		$loveTipID = $contentID;

		//Generic image parsing code
		$loveTipImage = "/images/content/love/" . date('Y') . "/tips/tip" . $loveTipID . ".jpg";
		for ($i = 2010; $i <= date('Y'); $i++) {
			if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $loveTipImage)) {
				$loveTipImage = "/images/content/love/" . $i . "/tips/tip" . $loveTipID . ".jpg";
			} else {
				break;
			}
		}
		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $loveTipImage)) {
			$loveTipImage = "/images/content/love/loveGeneric.jpg";
		}
		//
		$contentImage = $loveTipImage;
		$postImage = "http://cdn.mydailymoment.com/slir/w225-h225".$contentImage;
		break;
	case 201: //ThinkHealthier - Daily health News
		// Connect to TH DB 
		$conn2 = mysqli_connect("localhost", "admin", "air35Q2", "THCMS" );	
		// Get today's Featured Health Article
		$sql_query = "SELECT ip8jd_content.id, ip8jd_content.introtext, ip8jd_content.title
		              FROM ip8jd_content, DailyHealthyNews 
		              WHERE DailyHealthyNews.Date = '".$today."' and ip8jd_content.id = DailyHealthyNews.ftrdArticleID";
		$result =  mysqli_query($conn2, $sql_query);
		$row = mysqli_fetch_array($result);
		$contentID = $row['id'];
		$postText = cutText(convert_chars(strip_tags($row['introtext'])), 410);
		$postTitle = $row['title'];
		$postLink = "http://www.thinkhealthier.com/index.php?option=com_article&Itemid=338&id=".$contentID."&lang=en&view=article&utm_source=DHTMLface&utm_content&utm_campaign=MULTIface";
		$postMessage = "Healthy News for ".date('l, F j, Y');
		// figure out the image
		$postImage = 'http://cdn.thinkhealthier.com/images/' . getImage(0,$contentID);
		mysqli_close($conn2);
		break;		
	default:
		return false;
		break;
}

// If no content was returned, exit
if (!((int)$contentID>1))
{
	mysqli_close($conn);
	echo "<p>".date('Y-m-d G:i')." => Do nothing. <i>".$serviceName." - ".$pageName."</i> has no new content today</p>";
	return false;
}

//DEBUG
echo $token."\n";
echo $postMessage."\n";
echo $postLink."\n";
echo $postTitle."\n";
echo $postText."\n";
echo $postImage."\n";

// prepare post message
$post = array(	'access_token' => $token, 
				'message' => $postMessage,
				'link' => $postLink,
				'name' => $postTitle,
				'picture' => $postImage,
				'description' => $postText );

try{  
	// send to facebook
	$res = $facebook->api('/'.$pageID.'/feed','POST',$post);  
	
	// update tracking record
	$sql_query = "UPDATE SocialConnect SET lastPostDate='".$today."',lastPostID=".$contentID.",postCounter=".$postCounter." WHERE id=".$id;
	$res2 = mysqli_query($conn, $sql_query);
	echo "<p>".$sql_query."</p>";
	echo "<p>Published <b>".$postTitle."</b> to <i>".$serviceName." - ".$pageName."</i></p>";
  
} catch (Exception $e){  
  
  	//erro message
    echo $e->getMessage();  
}  

	mysqli_close($conn);




//returns a TH image based on type (0=article,1=recipe)
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
function recGlob($pattern, $flags = 0) {
    $files = glob($pattern, $flags); 
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
        $files = array_merge($files, recGlob($dir.'/'.basename($pattern), $flags));
    }
    return $files;
}
?>
