<?php
// ini_set('display_errors', 'On');
// error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('America/New_York');

$source = 'MDMHealth';
$campaign = $source;
$first = 'First';
$last = 'Last';
$email = (isset($_GET['em']) && !empty($_GET['em']) ? $_GET['em'] : '');
$zip = '';
$dob = '';
$gender = '';
$ip = $_SERVER['REMOTE_ADDR'];
$unID = '';
$nls  = '36';
$det = '';

$doi = 0;
$smMailingID = (isset($_GET['mid']) && !empty($_GET['mid']) ? $_GET['mid'] : 0);

if ($email != '' && $email != null) {
	// soi to doi swap
	$doi = 0; $ConfirmationType = 0; $nlID = $nls; // initial values
	$oemproDBConConfType = mysql_connect("206.188.9.102", "mailer", "jhaveri", false, 65536);
	mysql_select_db('oempro', $oemproDBConConfType);
	$smallDomainResults = mysql_query("call spGetConfirmationType(\"$email\")");
	if (mysql_num_rows($smallDomainResults) != 0) {
		$smallDomainRow = mysql_fetch_array($smallDomainResults);
		$ConfirmationType = $smallDomainRow['ConfirmationType'];
		$sdStatus = $smallDomainRow['Status'];
		$sdStatusDescription = $smallDomainRow['StatusDescription'];
	}
	mysql_close($oemproDBConConfType);

	if ($ConfirmationType == 3) { // if switching to DOI move SOI subs to DOI subs
		$doi = 3;
		$nlDOIAr = array( // for doi swap ids
			"10" => "17", // LR
			"36" => "37", // TH Healthy News
		);
	} else { // soi
		$nlDOIAr = array( // for soi do not change the ids
			"10" => "10", // LR
			"36" => "36", // TH Healthy News
		);
	}
	$nlID = $nlDOIAr["$nls"];
	// echo "nlID: $nlID" . PHP_EOL;
	// Call Nic's SP
	$leadAcqDBConAddLead = mysql_connect("206.188.9.102", "mailer", "jhaveri", false, 65536);
	mysql_select_db('lead', $leadAcqDBConAddLead);
	// $lsQuery = ("call spAddLead('', '$source', '$campaign', '$first', '$last', '$email', '$zip', '$dob', '$gender', '$ip', '$doi', '$nlID', 'Lead accepted from sooper.', '$unID', '$det')");
	$lsQuery = ("call spAddLead('', '$source', '$campaign', '$first', '$last', '$email', '$zip', '$dob', '$gender', '$ip', '$doi', '$nlID', 'Lead accepted from MDMHealth, smMailingID: $smMailingID.', '$det')");
	// echo "lsQuery: $lsQuery<br />\r\n";
	$lsResults = mysql_query("$lsQuery");
	if (mysql_num_rows($lsResults) != 0) {
		$lsRow = mysql_fetch_array($lsResults);
		$leadAcqID = $lsRow['LeadAcquisitionID'];
		$StatusID = $lsRow['StatusID'];
		$StatusDescription = $lsRow['StatusDescription'];
	} else {
		$StatusID = 3; $StatusDescription = "Query Failed"; $leadAcqID = 0;
	}
	mysql_close($leadAcqDBConAddLead);
	// echo '{"StatusID":"'.$StatusID.'","StatusDescription":"'.$StatusDescription.',"leadAcqID":"'.$leadAcqID.'"}' . PHP_EOL;
} else { // no json data sent
	// echo '{"StatusID":"2","StatusDescription":"No json data sent.","leacdAcqID":"0"}' . PHP_EOL;
	$StatusID = 2; $StatusDescription = "No data sent."; $leadAcqID = 0;
}

if ($StatusID == 0) { 
	$lnkTH = "http://www.thinkhealthier.com/unsubscribe?getSubs=1&getSubsLA=".$leadAcqID."&keyed=". mt_rand(1000000, 999999999);
	$lnkMDM = "http://www.mydailymoment.com/unsubscribe.php?getSubs=1&getSubsEmail=".$email."&keyed=". mt_rand(1000000, 999999999);
?>
	<h3>Thank you for expressing interest in the ThinkHealthier newsletter.</h3>
	<p>You (<?php echo $email; ?>) should start receiving your first edition in the next 10-15 minutes.  If you do not find it in your inbox please check your spam/junk folder.</p>
	<hr />	
	<p style="margin-bottom:80px;">To manage your <strong>ThinkHealthier</strong> subscriptions <a href="<?php echo $lnkTH; ?>">click here</a>, it may take up to 2 hours for your new subscription to show.</p>
	
<?php } else { ?>

	<p style="margin-bottom:200px;">There was a problem adding your <strong>ThinkHealthier</strong> subscription.  Please <a href="/subscribe">go here</a> to subscribe.<p>

<?php
}

echo '<!-- {"StatusID":"'.$StatusID.'","StatusDescription":"'.$StatusDescription.',"leadAcqID":"'.$leadAcqID.'"} -->' . PHP_EOL;

$myFile = "../thaddLeadAnt.txt";
$fh = fopen($myFile, 'a') or die("can't open file");
$stringData = '"Date":"' . date('Y-m-d H:i:s') . '", "source":"' . $source . '", "campaign":"' . $campaign . '", "first":"' . $first . '", "last":"' . $last . '", "email":"' . $email . '", "zip":"' . $zip . '", "dob":"' . $dob . '", "gender":"' . $gender . '", "ip":"' . $ip . '", "doi":"' . $doi . '", "unID":"' . $unID . '", "nls":"' . $nls  . '", "tID":"' . $tID . '", "smMailingID":"' . $smMailingID . '", "StatusID":"' . $StatusID . '", "StatusDescription":"' . $StatusDescription . '", "leadAcqID":"' . $leadAcqID . '"' . PHP_EOL;
fwrite($fh, $stringData);
fclose($fh);

?>
