<?php
// which subscription db should we connect to?
include_once($_SERVER["DOCUMENT_ROOT"] . "/scripts/whichSubDB.php");

if (isset($_GET['sub'])) {
  $sub = $_GET['sub'];
} else {
	$sub = 0;
}

if (isset($_GET['email'])) {
  $em = $_GET['email'];
} elseif (isset($_GET['FormValue_Email'])) {
  $em = $_GET['FormValue_Email'];
} else {
	$em = 'your email address...';
}

if (isset($_GET['channel'])) {
  $channel = strtolower($_GET['channel']);
} else {
	$channel = 'best';
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html lang="en" dir="ltr">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<?php include_once($_SERVER["DOCUMENT_ROOT"] . "/scripts/googleAnalytics.php"); ?>
<title>Got Mail Success?</title>
<script src="/javascript/mdm101022.js" type="text/javascript"></script>
<style type="text/css" media="all">
body, form {margin:0px; padding:0px; background-color:#FFF; color:#333;}
h2, h3 {margin:5px 5px 15px 5px;}
strong.youWill, p.youWill {display:block; text-align:center; margin:5px 5px 15px 5px; font-size:13px; font-family:arial,helvetica,verdana,sans-serif;}
#newslettersOuter{width:312px;}
#newslettersBdr1 {border:1px solid #d6d6d6;}
#newslettersBdr2 {border:1px solid #FFF;}
#newslettersInner {width:308px;}
#newslettersInner .clearRow {clear:both; height:5px;}
#newslettersInner .leftNewsletters, #newslettersInner .rightNewsletters {float:left;}
#newslettersInner input {float:left;}
#newslettersInner .bottomNewsletters input {float:none; clear:both;}
.newslettersImage {margin-bottom:10px;}
#newslettersInner label.newsletterDescription {float:left; width:127px; margin-left:3px; color:#333; font-size:10px; font-family:verdana,arial,helvetica,sans-serif;}
.leftNewsletters {margin-left:3px;}
.rightNewsletters {margin-left:10px;}
#newslettersInner .rightNewsletters label.newsletterDescription {width:120px;}
.dfbTitle, .rotdTitle, .horTitle, .gossipTitle, .quizTitle, .momTitle, .styleTitle, .loveTitle {font-size:12px;}
.dfbTitle {color:#8cb340;}
.rotdTitle {color:#69603a;}
.horTitle {color:#b7386b;}
.gossipTitle {color:#bd1800;}
.quizTitle {color:#6878a4;}
.momTitle {color:#47b0d7;}
.styleTitle, .bestTitle {color:#b32b81;}
.loveTitle {color:#6A338D;}
.newsletterDisclaimer  {margin:10px; color:#666; font-size:10px; font-family:verdana,arial,helvetica,sans-serif;}
.signupError {margin:-5px 20px 7px 10px; text-align:center; font-weight:bold; font-size:11px; color:#C00; font-family:verdana,arial,helvetica,sans-serif;}
</style>

<?php 

$nlSuccessURL = "";

$dietChannelCheck = '';
$gossipChannelCheck = '';
$recipeChannelCheck = '';
$horChannelCheck = '';
$quizChannelCheck = '';
$momsChannelCheck = '';
$styleChannelCheck = '';
$loveChannelCheck = '';
$bestChannelCheck = '';
if ($channel == 'diet') {
	$dietChannelCheck = 'checked="checked" ';
} elseif ($channel == 'recipe') {
	$recipeChannelCheck = 'checked="checked" ';
} elseif ($channel == 'horoscope') {
	$horChannelCheck = 'checked="checked" ';
} elseif ($channel == 'gossip') {
	// if gossip/quiz/moms/style/love, make it best of;
	$channel = 'best';
	$bestChannelCheck = 'checked="checked" ';
} elseif ($channel == 'quizzes') {
	$channel = 'best';
	$bestChannelCheck = 'checked="checked" ';
} elseif ($channel == 'moms' || $channel == 'mom') {
	$channel = 'best';
	$bestChannelCheck = 'checked="checked" ';
} elseif ($channel == 'style') {
	$channel = 'best';
	$bestChannelCheck = 'checked="checked" ';
} elseif ($channel == 'love') {
	$channel = 'best';
	$bestChannelCheck = 'checked="checked" ';
} elseif ($channel == 'best') {
	$channel = 'best';
	$bestChannelCheck = 'checked="checked" ';
}

if (isset($_GET['error'])) {
  $error = 1;
} else {
	$error = 0;
}
$hdrImage = '/images/nls/gotMail/gotmail' . $channel . 'hdr.png';

if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $hdrImage)) {
	$hdrImage = "/images/nls/gotMail/gotmaildiethdr.png";
}

$hdrImageFull = '<img src="http://cdn.mydailymoment.com' . $hdrImage . '" width="308" height="160" alt="" border="0" />';
?>
</head>
<body>
<?php
if ($sub == 1) { ?>
	<div id="newslettersOuter">
	<div id="newslettersBdr1">
	<div id="newslettersBdr2">
	<div id="newslettersInner">
		<div class="newslettersImage"><?php echo $hdrImageFull; ?></div>
		<strong class="youWill">Thank you for signing up for some of MyDailyMoment's newsletters.</strong>
		<p class="youWill">You will recieve a confirmation email shortly and start receiving your newsletters within 24 hrs.</p>
	</div>
	</div>
	</div>
	</div>

<?php } else { ?>
	<form name="oemProSubscription" method="post" action="http://<?php echo $whichSubDBSvr; ?>/getleadsite.php" onsubmit="return checkUnsubs(3);">
	<div id="newslettersOuter">
	<div id="newslettersBdr1">
	<div id="newslettersBdr2">
	<div id="newslettersInner">
		<div class="newslettersImage"><?php echo $hdrImageFull; ?></div>
		<?php if ($error == 1) { ?> <div class="signupError">There was a problem with your subscription.  Please try again.</div><?php } ?>
		<div>
			<div class="leftNewsletters">
				<input type="checkbox" name="FormValue_MailListIDs[]" value="30" id="healthNL" />
				<label class="newsletterDescription" for="gwNL"><span class="gossipTitle">Healthy News</span><br />From diabetes to digestive issues, get<br />the latest-breaking health news.</label>
				<div class="clearRow">&nbsp;</div>
				<input type="checkbox" name="FormValue_MailListIDs[]" value="33" id="bestNL" <?php echo $bestChannelCheck; ?>/>
				<label class="newsletterDescription" for="bestNL"><span class="bestTitle">Today on MDM</span><br />Get the latest bits of love, style &amp; beauty, quizzes, moms, diet, recipes, &amp; your daily horoscope all rolled<br />into one.</label>
			</div>
			<div class="rightNewsletters">
				<input type="checkbox" name="FormValue_MailListIDs[]" value="13" id="horNL" <?php echo $horChannelCheck; ?>/>
				<label class="newsletterDescription" for="horNL"><span class="horTitle">Daily Horoscope</span><br />Who needs a crystal ball when you've got us!</label>
				<div class="clearRow">&nbsp;</div>
				<input type="checkbox" name="FormValue_MailListIDs[]" value="11" id="dfbNL" <?php echo $dietChannelCheck; ?>/>
				<label class="newsletterDescription" for="dfbNL"><span class="dfbTitle">Diet &amp; Fitness Bytes</span><br />Get the latest tips, news and reviews!</label>
				<div class="clearRow">&nbsp;</div>
				<input type="checkbox" name="FormValue_MailListIDs[]" value="12" id="rotdNL" <?php echo $recipeChannelCheck; ?>/>
				<label class="newsletterDescription" for="rotdNL"><span class="rotdTitle">Recipe of the Day</span><br />Get today's scrumptious recipes!</label>
			</div>
		<div class="clearRow">&nbsp;</div>
		<div>
			<div class="bottomNewsletters">
				<input type="text" name="FormValue_Email" id="email" value="<?php echo $em; ?>" onblur="if(this.value=='') {this.value='your email address...';} else {this.value = trim(this.value);}" onfocus="if(this.value=='your email address...') this.value='';" class="searchBox" maxlength="50" style="border:1px solid #999;width:230px;padding:2px;margin:2px 8px 0 8px;" /> <input type="image" src="http://cdn.mydailymoment.com/images/searchGoBtn.png" title="go" alt="go" align="top" class="srchBtn" />
				<div class="newsletterDisclaimer">As part of this free service, you will also receive occasional special offers from MDM</div>
			</div>
		</div>
	</div>
	</div>
	</div>
	</div>
	
	<script type="text/javascript">
	/* <![CDATA[ */
		// write out soure and campaign for nl signups passing default values if no utm values are stored in cookies
		returnUTMSignupVars('GotMail', 'mdm');
	/* ]]> */
	</script>
	<input type="hidden" name="FormValue_CustomField14" value="<?php echo date('Y-m-d H:i:s'); ?>" />
	<input type="hidden" name="FormValue_CustomField15" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>" />
	<input type="hidden" name="FormValue_SuccessScreenID" value="0" />
	<input type="hidden" name="FormValue_FailureScreenID" value="0" />
	<input type="hidden" name="FormValue_CustomFieldIDs" value="" />
	<input type="hidden" name="FormValue_WhichNLs" id="FormValue_WhichNLs" value="" />
	<input type="hidden" name="campaign" id="campaign" value="<?php echo $campaign; ?>" />
	<input type="hidden" name="channel" value="<?php echo $channel; ?>" />
	<input type="hidden" name="FormValue_NLSuccessURL" id="FormValue_NLSuccessURL" value="http://www.mydailymoment.com/scripts/gotMailSuccess.php?sub=1&channel=<?php echo $channel; ?>" />
	</form>
<?php } ?>
</body>
</html>
