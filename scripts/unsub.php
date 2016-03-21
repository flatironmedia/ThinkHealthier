<?php
// ini_set('display_errors', 'On');
// error_reporting(E_ALL | E_STRICT);
// function to scrub the bad chars
include_once($_SERVER["DOCUMENT_ROOT"] . "/scripts/convertChars.php");
// which subscription db should we connect to?
include_once($_SERVER["DOCUMENT_ROOT"] . "/scripts/whichSubDB.php");

if ((count($_POST) == 0) && (count($_GET) > 0))
{
	foreach ($_GET as $Key=>$Val)
	{
		$_POST[$Key] = $Val;
	}
}

$resultMessage = '';
$unsubEmail = '';
$signInEmail = 'your email address...';
$signInBoxDisplay = '';
$signInBoxMessage = 'Just enter your <strong>email address*</strong> and we\'ll help you subscribe or unsubscribe your email subscriptions.';
$removeSubsBoxDisplay = '';
$addSubsBoxDisplay = '';
$resultMessageDisplay = '';
$title = 'Manage My E-Mail Newsletter Subscriptions';
$nlTotal = 0; $subBounce = 'Failed'; $signInBoxInnerDisplay = ''; $curSubs = '';

$nl10 = 0;
$nl17 = 0;
$nl36 = 0;
$nl37 = 0;

if (isset($_GET['thunsub']) && $_GET['thunsub'] == 1) { // success
	$updateType = $_GET['updateType'];
	$thunsub = $_GET['thunsub'];
	if (isset($_GET['getSubsMem']) && $_GET['getSubsMem'] != '') {
		$signInMemId = $_POST['getSubsMem'];
		if ($signInMemId != 0) { // grab email by member id
			$getMemEmailDBCon = mysql_connect('206.188.9.102', "mailer", "jhaveri", false, 65536); // read from the master, staiano 2011-03-03
			mysql_select_db('oempro', $getMemEmailDBCon) or die("Database maintenance is being performed.  Please try again later.");
			$getMemEmSP = "call spGetMemberEmail('$signInMemId');";
			$getMemEmResults = mysql_query("$getMemEmSP", $getMemEmailDBCon);
			if (mysql_num_rows($getMemEmResults) != 0) {
				$getMemEmRow = mysql_fetch_array($getMemEmResults);
				$signInEmail = $getMemEmRow['Email'];
			}
			mysql_close($getMemEmailDBCon);
		}
	} elseif (isset($_GET['FormValue_Email']) && $_GET['FormValue_Email'] != '') {
		$signInEmail = $_GET['FormValue_Email'];
	} elseif (isset($_GET['Email']) && $_GET['Email'] != '') {
		$signInEmail = $_GET['Email'];
	}

	$resultMessage .= "<div class=\"errorBox\"><div class=\"errorMessage\"><strong>You have ";
	if ($updateType == 1) { // sub
		$resultMessage .= " added those additional newsletters subscriptions for <span style=\"color:#333;\">$signInEmail</span>.</strong></div><br />\n";
	}
	if ($updateType == 0) { // unsub
		$resultMessage .= " updated the newsletter subscriptions for <span style=\"color:#333;\">$signInEmail</span>.</strong></div>\n";
	}
	$resultMessage .= "<hr size=\"1\" noshade=\"noshade\" /><br />These changes should take effect within 72 hours.  Click the button below to make additional changes.<br /><br />\n";
	if ($updateType == 1) { // sub
		$resultMessage .= "<strong style=\"color:#c00;\">Please note it may take a few hours for your additional newsletters subscriptions to show up.</strong><br /><br />\n";
	}
	$resultMessage .= "<form name=\"thGetSubsBack\" method=\"get\" action=\"/unsubscribe\">\n";
	$resultMessage .= "<input type=\"hidden\" value=\"1\" name=\"getSubs\" />\n";
	$resultMessage .= "<input type=\"hidden\" name=\"getSubsMem\" value=\"$signInMemId\" />\n";
	// $resultMessage .= "<input type=\"hidden\" name=\"getSubsEmail\" value=\"$signInEmail\" />\n";
	$resultMessage .= "<input type=\"hidden\" value=\"" . mt_rand(1000000, 999999999) ."\" name=\"keyed\" />\n";
	$resultMessage .= "<input type=\"image\" src=\"http://cdn.thinkhealthier.com/images/unsub/btnBack.gif\" alt=\"back to you subscriptions.\" title=\"back\" value=\"back to you subscriptions.\" />\n";
	$resultMessage .= "</form></div>\n";
	
	$signInBoxDisplay = ' style="display:none;"';
	$removeSubsBoxDisplay = ' style="display:none;"';
	$addSubsBoxDisplay = ' style="display:none;"';
} else {
	if (isset($_GET['thunsub']) && $_GET['thunsub'] == 0) { // failure
		$resultMessage = "<div class=\"errorBox\"><span class=\"errorMessage\"><strong>There was a problem with your request.</strong></span>";
		if ( ($signInEmail != 'your email address...') && ($signInEmail != '') && ($signInEmail != '0') ) {
			$resultMessage .= "<br /><br />Please check your subscriptions below and try again.";
		}
		$resultMessage .= "</div>\n";
	}
	if (isset($_POST['getSubs'])) {	
		$signInEmail = $_POST['getSubsEmail'];
	} elseif (isset($_GET['FormValue_Email']) && $_GET['FormValue_Email'] != '') {
		$signInEmail = $_GET['FormValue_Email'];
	} elseif (isset($_GET['Email']) && $_GET['Email'] != '') {
		$signInEmail = $_GET['Email'];
	} elseif (isset($_GET['email']) && $_GET['email'] != '') {
		$signInEmail = $_GET['email'];
	} elseif (isset($_GET['tommy']) && $_GET['tommy'] != '') {
		$signInEmail = $_GET['tommy'];
	} elseif (isset($_GET['fe']) && $_GET['fe'] != '') {
		$signInEmail = $_GET['fe'];
	}
	if ( ($signInEmail == 'your email address...') || ($signInEmail != '') || ($signInEmail != '0') ) {
		// if no email passed, look for member ID
		$signInMemId = 0;
		if (isset($_POST['getSubsMem'])) {	
			$signInMemId = $_POST['getSubsMem'];
		} elseif (isset($_POST['getSubs'])) {	
			$signInMemId = $_POST['getSubsEmail'];
		}
		if ($signInMemId != 0) { // grab email by member id
			$getEmailDBCon = mysql_connect($whichSubDBIP, "mailer", "jhaveri", false, 65536); // read from the master, staiano 2011-03-03
			mysql_select_db('oempro', $getEmailDBCon) or die("Database maintenance is being performed.  Please try again later.");
			// echo "signInMemId: " . $signInMemId . "<br>" . PHP_EOL;
			$getEmSP = "call spGetMemberEmail('$signInMemId');";
			$getEmResults = mysql_query("$getEmSP", $getEmailDBCon);
			// echo "lalalala:" . $getEmResults . "<br>" . PHP_EOL;
			if (mysql_num_rows($getEmResults) != 0) {
				$getEmRow = mysql_fetch_array($getEmResults);
				$signInEmail = $getEmRow['Email'];
			}
			mysql_close($getEmailDBCon);
		} else {
			// if no email or member id passed, look for LeadAcq ID
			$signInLAId = 0;
			if (isset($_POST['getSubsLA'])) {	
				$signInLAId = $_POST['getSubsLA'];
			}
			if ($signInLAId != 0) { // grab email by LeadAcq id
				$getEmailLADBCon = mysql_connect($whichSubDBIP, "mailer", "jhaveri", false, 65536); // read from the master, staiano 2011-03-03
				mysql_select_db('lead', $getEmailLADBCon) or die("Database maintenance is being performed.  Please try again later.");
				$getEmSP = "call spGetLeadInfo('$signInLAId');";
				$getEmResults = mysql_query("$getEmSP", $getEmailLADBCon);
				if (mysql_num_rows($getEmResults) != 0) {
					$getEmRow = mysql_fetch_array($getEmResults);
					$signInEmail = $getEmRow['Email'];
				}
				mysql_close($getEmailLADBCon);
			}
		}
	}
	if ( ($signInEmail != 'your email address...') && ($signInEmail != '') && ($signInEmail != '0') ) {
		$oemProDB = 'oempro';
		$oemProDBCon = mysql_connect($whichSubDBIP, "mailer", "jhaveri", false, 65536); // read from the master, staiano 2011-03-03
		mysql_select_db($oemProDB, $oemProDBCon) or die("Database maintenance is being performed.  Please try again later.");
		
		// $subResults = mysql_query("select RelMailListID, SubscriptionStatus, SubscriptionDate, Email, CustomField12, CustomField19 from oemp_maillist_members LEFT JOIN (oemp_members) ON (oemp_members.MemberID=oemp_maillist_members.RelMemberID) where oemp_members.Email = '$signInEmail' and SMBounce_MS4 = 'Valid' and (oemp_maillist_members.RelMailListID = '2' or oemp_maillist_members.RelMailListID = '4' or oemp_maillist_members.RelMailListID >= 10);");
		// $qSP = select RelMailListID, SubscriptionStatus, SubscriptionDate, Email, CustomField12, CustomField19, SMBounce_MS4, CustomField20, MemberID from oemp_maillist_members LEFT JOIN (oemp_members) ON (oemp_members.MemberID=oemp_maillist_members.RelMemberID) where oemp_members.Email = '$signInEmail'";
		$qSP = "call spGetMemberSubscriptions('$signInEmail');";
		$subResults = mysql_query("$qSP", $oemProDBCon);
		
		if (mysql_num_rows($subResults) != 0) {
			$s = 0;
			while ( $subRow = mysql_fetch_array($subResults) ) {
				if (trim($subRow[6]) != 'Valid ') { // use this to find users that have not bounced
				    $subRelMailListID[$s]['RelMailListID'] = $subRow[0];
				    $subStatus[$s]['SubscriptionStatus'] = $subRow[1];
				    $subDate[$s]['SubscriptionDate'] = $subRow[2];
				    $subEmail[$s]['Email'] = $subRow[3];
				    $subNode = $subRow[4]; // array/$s doesn't matter since it will always be the same node, campagin, bounce and source
				    $subCampaign = $subRow[5];
					$subBounce = trim($subRow[6]);
				    $subSource = $subRow[7];
				    $subMemID = $subRow[8]; // array/$s doesn't matter since it will always be the same MemberID
				    $s++;
				} else { $subBounce = 'Failed'; break; }
			}
			if ($subBounce == 'Valid') {
				// echo "s: " . $s . " - num_rows: " . mysql_num_rows($subResults) . "\n<br />";
				// echo "Before test: nl10: $nl10 - nl17: $nl17- nl36: $nl36 - nl37: $nl37\n<br />";
				for ($t = 0; $t < $s; $t++) {
					// echo $t . ": " . $subRelMailListID[$t]['RelMailListID'] . ": " . $subStatus[$t]['SubscriptionStatus'] ."\n<br />";
					if ( ($subRelMailListID[$t]['RelMailListID'] == '10' || $subRelMailListID[$t]['RelMailListID'] == '4') && ($subStatus[$t]['SubscriptionStatus'] == 'Subscribed') ) {
						$nl10 = 1; $curSubs .= "10,";
						$nl10SubDate = $dateConvertObj->convert($subDate[$t]['SubscriptionDate'], 'Y-m-d H:i:s', 'g:ia \o\n m/d/Y');
					}
					if ( ($subRelMailListID[$t]['RelMailListID'] == '36') && ($subStatus[$t]['SubscriptionStatus'] == 'Subscribed') ) {
						$nl36 = 1; $curSubs .= "36,";
						$nl36SubDate = $dateConvertObj->convert($subDate[$t]['SubscriptionDate'], 'Y-m-d H:i:s', 'g:ia \o\n m/d/Y');
					}
				}
				
				// echo "After test: nl10: $nl10 - nl11: $nl11 - nl12: $nl12 - nl13: $nl13 - nl14: $nl14 - nl15: $nl15 - nl16: $nl16 - nl17: $nl17 - nl18: $nl18 - nl19: $nl19 - nl20: $nl20 - nl21: $nl21 - nl22: $nl22 - nl23: $nl23 - nl24: $nl24 - nl25: $nl25 - nl36: $nl36 - nl37: $nl37 - nl28: $nl28 - nl29: $nl29 - nl30: $nl30 - nl31: $nl31 - nl32: $nl32 - nl33: $nl33 - nl35: $nl35\n<br />";
				// total nls subscribed too
				$nlLR = $nl10 + $nl17;
				$nlTHHN = $nl36 + $nl37;
				
				$nlTotal = $nlLR + $nlTHHN;
				$resultMessage = "<div class=\"errorMessage\"><strong>You are signed in as $signInEmail. <!-- n:'$subNode' -  s:'$subSource' - c:'$subCampaign' --></strong><hr size=\"1\" noshade=\"noshade\" /></div>\n";
				
				$signInBoxDisplay = ' style="display:none;"';
			} else { 
				$resultMessage = "<div class=\"errorMessage\"><strong>You are signed in as $signInEmail. <!-- n:'$subNode' -  s:'$subSource' - c:'$subCampaign' --></strong><hr size=\"1\" noshade=\"noshade\" /></div>\n";
				$signInBoxMessage = "Unfortunately your email address bounced back when we tried to email you. Please <a href=\"/contact-us\">contact us</a> about this problem.\n";
				$removeSubsBoxDisplay = ' style="display:none;"';
				$addSubsBoxDisplay = ' style="display:none;"';
				$signInBoxInnerDisplay = ' style="display:none;"';
			}
		} else { // email is not in the db
			$signInBoxMessage = "<div class=\"signInBoxMessage\"><span class=\"errorMessage\">Your email address ($signInEmail) was not found in our database.</span>  Please verify that you have typed it in correctly.  You can try to login with a different email address or <a href=\"/subscribe\" class=\"changeLnk\">click here</a> to sign up for our newsletter(s).  If you are positive you have typed in your email address correct please <a href=\"/contact-us\">contact us</a> about your problem.</div>\n";
			$resultMessageDisplay = ' style="display:none;"';
			$removeSubsBoxDisplay = ' style="display:none;"';
			$addSubsBoxDisplay = ' style="display:none;"';
		}
		// free the results from memory
		mysql_free_result($subResults);
	} else {
		if ($signInEmail == '0') { // email is equal to 0 which means nothing was originally passed to oempro
			$signInEmail = 'your email address...';
			$signInBoxMessage = "<div class=\"signInBoxMessage\"><span class=\"errorMessage\">Your email address ($signInEmail) was not found in our database.</span>  Please verify that you have typed it in correctly.  You can try to login with a different email address or <a href=\"/change-my-e-mail-addres\" class=\"changeLnk\">click here</a> to sign up for our newsletter(s).  If you are positive you have typed in your email address correct please <a href=\"/contact-us\">contact us</a> about your problem.</div>\n";
			$resultMessageDisplay = ' style="display:none;"';
			$removeSubsBoxDisplay = ' style="display:none;"';
			$addSubsBoxDisplay = ' style="display:none;"';
		}
		$removeSubsBoxDisplay = ' style="display:none;"';
		$addSubsBoxDisplay = ' style="display:none;"';
	}
}
?>

<div class="signupBoxNLsAbout2 clearFix" style="width:939px; margin:6px auto; padding:15px;">
<?php
$curSubs = substr($curSubs, 0, -1); // strip off the trailing ','

if ($subBounce == 'Valid') {
?>
<p class="unsub3Text">To <strong>unsubscribe</strong> from a specific mailing, remove the <strong>check</strong> next to the corresponding newsletter and click <strong>update</strong>. To add newsletters to your subscription, simply <strong>check</strong> the appropriate boxes [or <a href="javascript:addAllNls();">select all</a>] and click <strong>update</strong>. Click <strong>"unsubscribe me from everything"</strong> to be removed from all our newsletters.</p>
<hr size="1" noshade="noshade" />
<form name="oemProRemoveSubsTop" method="get" action="http://<?php echo $whichSubDBSvr; ?>/thspUnsub.php">
<input type="hidden" name="FormValue_MailListIDs[]" value="10,17,36,37" id="allNLsForUnsubTop" />
<input type="hidden" name="FormValue_Email" id="email1" value="<?php echo $signInEmail; ?>" />
<input type="hidden" name="FormValue_CustomField15" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>" />
<input type="hidden" name="curSvr" value="<?php echo $_SERVER['SERVER_NAME']; ?>" />
<input type="image" src="http://cdn.thinkhealthier.com/images/unsub/unsub3FromEverything.png" /><img src="http://cdn.thinkhealthier.com/images/unsub/unsub3ClickHere.png" width="342" height="65" alt="" border="0" />
</form>
<?php } ?>
<hr size="1" noshade="noshade" />
<table width="939" border="0" cellpadding="0" cellspacing="0">
<tr>
    <td valign="top" align="left" width="550">
		<div class="signInBoxOuter clearFix"<?php echo $resultMessageDisplay; ?> style="margin:5px 0 9px 2px;">
			<?php echo $resultMessage; ?>
		</div>
		<div class="signInBoxOuter signInBoxOuterUnsub3"<?php echo $signInBoxDisplay; ?>><?php echo $signInBoxMessage; ?>
			<div class="signInBoxInner" <?php echo $signInBoxInnerDisplay; ?>>
				<form name="thGetSubs" method="get" action="/unsubscribe" <?php echo $signInBoxDisplay; ?>>
				<input type="hidden" value="1" name="getSubs" />
				<input type="hidden" value="<?php echo mt_rand(1000000, 999999999); ?>" name="key" />
				<label for="getSubsEmail">Email Address: </label><input type="text" name="getSubsEmail" id="getSubsEmail" value="<?php echo $signInEmail; ?>" onblur="if(this.value=='') { this.value='your email address...'; } else { this.value = trim(this.value); }" onfocus="if(this.value=='your email address...') this.value='';" maxlength="50" /> <input type="image" src="http://cdn.thinkhealthier.com/images/unsub/btnSignIn.gif" alt="sign In" title="Sign In" value="Sign In" align="absmiddle" />
				</form>
			</div>
			<p>Alternatively if you want to <a href="/change-my-e-mail-address" class="changeLnk"><strong>update/change your email address</strong></a> go to our <a href="/change-my-e-mail-address" class="changeLnk">Change My E-Mail Address</a> page.</p>
			<div class="signupBoxNoteAbout"<?php echo $signInBoxInnerDisplay; ?>>*Note: If you are not currently a subscriber and wish to sign up for our newsletter(s), please <a href="/subscribe" class="changeLnk">click here</a>.</div>
		</div>
		
		<div class="signInBoxOuter clearFix"<?php echo $removeSubsBoxDisplay; ?>>
		<?php
		if ($subBounce == 'Valid') {
			// LRs
			$nl1017Chk = ''; $nl1017Date = ''; $nl1017val = 10;
			if ($nl17 == 1) { $nl1017val = 17; }
			if ($nl10 == 1 || $nl17 == 1) {
				$nl1017Chk = 'checked="checked" ';
				$nl1017Date = "<br /><span class=\"signupBoxNLsItemSubDate\">You signed up at $nl10SubDate.</span>";
			}
			// th healthy news 
			$nl3637Chk = ''; $nl3637Date = ''; $nl3637val = 36;
			if ($nl37 == 1) { $nl3637val = 37; }
			if ($nl36 == 1 || $nl37 == 1) {
				$nl3637Chk = 'checked="checked" ';
				$nl3637Date = "<br /><span class=\"signupBoxNLsItemSubDate\">You signed up at $nl36SubDate.</span>";
			}
		?>
		
		<form name="oemProRemoveSubs" method="get" action="http://<?php echo $whichSubDBSvr; ?>/thintermed.php" onsubmit="return compareSubs();">
			<table width="100%" border="0" cellpadding="0" cellspacing="10" style=" border:1px solid #999;">
			<tr>
				<td valign="middle" align="left"><input type="checkbox" name="FormValue_MailListIDs[]" value="<?php echo $nl3637val; ?>" id="thhnNL1" <?php echo $nl3637Chk; ?>/></td>
				<td valign="middle" align="left"><div class="signupBoxNLsItemAbout"><label for="thhnNL1" class="unsub3Label"><img src="http://cdn.thinkhealthier.com/images/unsub/unsub3HealthIcon.png" width="30" height="33" alt="" border="0" title="Daily Horoscope" hspace="5" vspace="5" align="left" />Healthy News</label> <?php echo $nl3637Date; ?></div></td>
				<td valign="middle" align="right" width="200"><?php if ($nl36 == 1 || $nl37 == 1) { echo '<div class="curSub">Currently Subscribed</div>'; } else { echo '<img src="http://cdn.thinkhealthier.com/images/s.gif" style="height:23px" width="177" height="23" />&nbsp;&nbsp;'; } ?></td>
			</tr>
			<tr>
				<td valign="middle" align="left"><input type="checkbox" name="FormValue_MailListIDs[]" value="<?php echo $nl1017val; ?>" id="partner1" <?php echo $nl1017Chk; ?>/></td>
				<td valign="middle" align="left"><div class="signupBoxNLsItemAbout"><label for="partner1" class="unsub3Label"><img src="http://cdn.thinkhealthier.com/images/unsub/unsub3LRIcon.png" width="30" height="33" alt="" border="0" title="Special Offers" hspace="5" vspace="5" align="left" />Special Offers</label> <?php echo $nl1017Date; ?></div></td>
				<td valign="middle" align="right"><?php if ($nl10 == 1 || $nl17 == 1) { echo '<div class="curSub">Currently Subscribed</div>'; } else { echo '<img src="http://cdn.thinkhealthier.com/images/s.gif" style="height:23px" width="177" height="23" />&nbsp;&nbsp;'; } ?></td>
			</tr>
			</table>
			<input type="hidden" name="curSubs" id="curSubs" value="<?php echo $curSubs; ?>" />
			<input type="hidden" name="FormValue_Email" id="email1" value="<?php echo $signInEmail; ?>" />
			<input type="hidden" name="FormValue_MemID" value="<?php echo $subMemID; ?>" />
			<input type="hidden" name="FormValue_CustomField15" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>" />
			<input type="hidden" name="FormValue_CustomField20" value="<?php echo $subSource; ?>" />
			<input type="hidden" name="curSvr" value="<?php echo $_SERVER['SERVER_NAME']; ?>" />
			<input type="image" src="http://cdn.thinkhealthier.com/images/unsub/unsub3Update.png" title="uodate" alt="update" value="update" align="top" class="srchBtn" />
			</form>
			<form name="oemProRemoveSubsBottom" method="get" action="http://<?php echo $whichSubDBSvr; ?>/thspUnsub.php">
			<input type="hidden" name="FormValue_MailListIDs[]" value="10,17,36,37" id="allNLsForUnsubBot" />
			<input type="hidden" name="FormValue_Email" id="email1" value="<?php echo $signInEmail; ?>" />
			<input type="hidden" name="FormValue_CustomField15" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>" />
			<input type="hidden" name="curSvr" value="<?php echo $_SERVER['SERVER_NAME']; ?>" />
			<input type="image" src="http://cdn.thinkhealthier.com/images/unsub/unsub3FromEverything.png" />
			</form>
		<?php
		} else { } // else user has bounced do nothing.
		?>	
		<img src="http://cdn.thinkhealthier.com/images/s.gif" width="550" height="1" alt="" border="0" /></td>
    <td valign="top" align="middle" width="209"><br /><br /><img src="http://cdn.thinkhealthier.com/images/unsub/SecuredSiteLogo.jpg" width="153" height="124" alt="" border="0" /></td>
	<td valign="top" align="left" width="180"><br /><br /><div class="signupBoxNoteAbout signupBoxNoteAboutPriv">We value your privacy and ensure that every precaution is taking to secure and protect your personal
	information.  View our <a href="/privacy-policy" class="changeLnk">Privacy Policies</a>.</div>
	</td>
</tr>
</table>
</div>
