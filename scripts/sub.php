<?php
// which subscription db should we connect to?
include_once($_SERVER["DOCUMENT_ROOT"] . "/scripts/whichSubDB.php");

$ResultMessage = '';
$subEmail = '';
$signupBoxDisplay = '';
$resultMessageDisplay = '';

if (isset($_GET['ResultMessage'])) {
	if (isset($_GET['FormValue_Email'])) {
		if ($_GET['FormValue_Email'] != '') {
			$subEmail = "( " . $_GET['FormValue_Email'] . " )";
		}
		if ($subEmail == '' && isset($_GET['email'])) {
			$subEmail = "( " . $_GET['email'] . " )";
		}
	}
	$errorShowSignupBoxDisplay = false;
	if ($_GET['ResultMessage'] == ''  || $_GET['ResultMessage'] == '0' || $_GET['ResultMessage'] == 'Your subscription was successful.') {
		$ResultMessage = "<strong>You $subEmail have successfully subscribed.</strong><br /><br />You should receive your first newsletter within the next 24-48 hours.<br /><br />To manage your newsletter subscriptions <a href=\"/unsubscribe?getSubs=1&getSubsLA=" . $_GET['leadAcqID'] . "&keyed=" .  mt_rand(1000000, 999999999) . "\">click here</a>.<br /><br /><strong style=\"color:#c00;\">Please note it may take up to 1 hour for your new subscriptions to show up.</strong>\n";
		$title = 'Subscription Confirmation';
	} else {
		$tmpResultMessage = $_GET['ResultMessage'];
		$badResultMessage = "Your subscription failed. You are already a member of the mail list. To update your subscriptions and account information,";
		if (substr(trim($tmpResultMessage), 0, strlen($badResultMessage)) == $badResultMessage) {
			$newResultMessage = "Your subscription failed. You are already a member of the mail list(s).";
		} else {
			$newResultMessage = $tmpResultMessage;
		}
		if ($tmpResultMessage == '1') {
			$newResultMessage = "<a href=\"/unsubscribe?getSubs=1&getSubsEmail=" . $_GET['FormValue_Email'] . "&keyed=" .  mt_rand(1000000, 999999999) . "\">Please try again</a>.";
		}
		$ResultMessage = "<strong>There was a problem with your $subEmail subscription request.  </strong><br /><br />$newResultMessage\n";
		$title = 'Subscription Problem';
		$errorShowSignupBoxDisplay = true; // show signup box if there was a sub error
		$ResultMessage .= '  <a href="/subscribe">Please try again.</a>';
	}
	if (!$errorShowSignupBoxDisplay) {
		$signupBoxDisplay = ' style="display:none;"';
	}
} else {
	$title = 'Subscribe';
	$resultMessageDisplay = ' style="display:none;"';
}
?>

<script type="text/javascript">
/* <![CDATA[ */
function selectAllNls(el) {
	if (document.getElementById) {
		if(el.checked == true){
			document.getElementById('thhnNL').checked = true;
			document.getElementById('email').focus();
		}
		else
		{
			document.getElementById('thhnNL').checked = false;
			document.getElementById('bestNL').checked = false;
		}
	}
}
/* ]]> */
</script>
<script type="text/javascript" src="/scripts/email.js"></script>
<!-- <h1 class="contentheading" style="margin-left:15px"><?php echo $title; ?></h1> -->

<div class="signupBoxNLsAbout clearFix"<?php echo $resultMessageDisplay; ?>>
<?php echo $ResultMessage; ?>
</div>
<div class="signupBoxNLsAbout clearFix"<?php echo $signupBoxDisplay; ?>>
	<div class="clearFix">
	<form name="oemProSubscription" method="post" action="http://<?php echo $whichSubDBSvr; ?>/thgetleadsite.php" onsubmit="return validEmail(document.getElementById('email'));">
		<div class="clearFix">
			<div class="subscribe-wrapper">
				<div class="title">
					It's Free to Think<strong>Healthier</strong>
				</div>
				<div>
					Get the ultimate health roundup, delivered daily to your email. From cold sores to cancer, we'll keep you informed on all the health-related info you need to know and best of all its totally <strong>FREE</strong>.
				</div>
				<br>
				<div>
					<input name="FormValue_Email" id="email" placeholder="Your Email Address" maxlength="50" type="text">
				  <input title="submit" src="/images/unsub/subscribe-submit.png" alt="submit" class="submit" align="top" type="image">
				</div>
			</div>
			<div class="note-privacy">
				We value your privacy and ensure that every precaution is taking to secure and protect your personal information. View our <a href="privacy-policy" target="_blank" title="Privacy Policies">Privacy Policies</a>.
			</div>
		</div>
	<input type="hidden" name="FormValue_MailListIDs[]" id="thhn" value="36" />
	<input type="hidden" name="FormValue_MailListIDs[]" id="partner" value="10" />
	<script type="text/javascript">
	/* <![CDATA[ */
		// write out soure and campaign for nl signups passing default values if no utm values are stored in cookies
		returnUTMSignupVars('thros', '');
	/* ]]> */
	</script>
	<input type="hidden" name="FormValue_CustomField14" value="<?php echo date('Y-m-d H:i:s'); ?>" />
	<input type="hidden" name="FormValue_CustomField15" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>" />
	<input type="hidden" name="FormValue_SuccessScreenID" value="MjQ%3D" />
	<input type="hidden" name="FormValue_FailureScreenID" value="MjM%3D" />
	<input type="hidden" name="FormValue_CustomFieldIDs" value="" />
	<input type="hidden" name="FormValue_WhichNLs" id="FormValue_WhichNLs" value="" />
	</form>
	</div>
</div>

<br /><br /><br />

