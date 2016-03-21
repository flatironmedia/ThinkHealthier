<?php
date_default_timezone_set('America/New_York');

// which subscription db should we connect to?
include_once($_SERVER["DOCUMENT_ROOT"] . "/scripts/whichSubDB.php");

function isEmailPHP($emTest) {
	if (preg_match("/^(\w+((-\w+)|(\w.\w+))*)\@(\w+((\.|-)\w+)*\.\w+$)/",$emTest)) {
		return true;
	} else {
		return false;
	}
}
// check the server IP
$goodIPs = array('64.106.188.181', '10.10.10.181', '64.106.188.182', '10.10.10.182', '64.106.188.183', '10.10.10.183', '64.106.188.178', '10.10.10.178', '64.106.188.179', '10.10.10.179', '64.106.188.184', '10.10.10.184', '64.106.188.186', '10.10.10.186', '64.106.188.187', '10.10.10.187', '206.188.9.76', '206.188.9.77', '127.0.0.1');
// $goodScript = 'change.php'; // old MDM URL
$goodScript = 'change-my-e-mail-address'; // new TH URL
$checkIP = false;
$serv_addr = $_SERVER['SERVER_ADDR'];
foreach ($goodIPs as $val) {
	if ($val == $serv_addr) {
		$checkIP = true;
		// echo "<-- true -->";
	}
	// echo "<!-- serv_addr: '$serv_addr' - referServ: '$val'<br />\r\n -->";
}

// check the server domain
$goodName = 'thinkhealthier.com'; $checkName = false;
$serv_name = $_SERVER['SERVER_NAME'];
// echo "<!-- serv_name: $serv_name<br />\r\n -->";
$serv_nameAr = explode('.', $serv_name);
$serv_nameTLD = $serv_nameAr[sizeof($serv_nameAr)-2] . '.' . $serv_nameAr[sizeof($serv_nameAr)-1];
// echo "<!-- serv_nameTLD: $serv_nameTLD<br />\r\n -->";
if ($serv_nameTLD == $goodName) {
	$checkName = true;
}

// check the referer script
$checkScript = false;
$refer = $_SERVER['HTTP_REFERER'];
$referAr = explode('/', $refer);
$referScript = $referAr[sizeof($referAr) - 1];
if ($goodScript == $referScript) {
	$checkScript = true;
	// echo "<!-- referScript: '$referScript'<br /> -->\r\n";
}

if ($checkScript && ($checkIP || $checkName)) {
	if (isset($_GET['curEm'])) { $curEm = $_GET['curEm']; } else { $curEm = ''; }
	if (isset($_GET['newEm'])) { $newEm = $_GET['newEm']; } else { $newEm = ''; }
	if (isset($_GET['ipAddr'])) { $ipAddr = $_GET['ipAddr']; } else { $ipAddr = ''; }
	
	if (!isEmailPHP($curEm) || !isEmailPHP($newEm)) { echo '<div class="bad">You entered an invalid email address. Please try again.  <br /><br /><strong>Failure (1) ' . date('\o\n l, F j, Y \a\t h:i:s a') . '.</strong></div>'; return; }
	if ($curEm == $newEm) { echo '<div class="bad">Your current email and new email are the same. Please try again.  <br /><br /><strong>Failure (2) ' . date('\o\n l, F j, Y \a\t h:i:s a') . '.</strong></div>'; return; }
	
	if ($curEm != '' && $curEm != null && $newEm != '' && $newEm != null && $ipAddr != '' && $ipAddr != null) {
		$oemproDB = 'oempro';
		$oemproDBCon = mysql_connect($whichSubDBIP, "admin", "air35Q2", false, 65536);
		mysql_select_db($oemproDB, $oemproDBCon);
		$chEmResults = mysql_query("call spChangeEmail('$curEm', '$newEm', '$ipAddr');");
		if (mysql_num_rows($chEmResults) != 0) {
			$chEmRow = mysql_fetch_array($chEmResults);
			$chEmStatus = $chEmRow['status'];
			$chEmStatusDescription = ' because your ' . strtolower($chEmRow['statusDescription']);
		} else { $chEmStatus = 1; $chEmStatusDescription = ''; }
		if ($chEmStatus == 0) {
			echo '<div class="good">Your email has Successfully been changed.  <br /><br /><strong>Success ' . date('\o\n l, F j, Y \a\t h:i:s a') . '.</strong></div>';
		} else {
			echo '<div class="bad">Your information could not be processed at this time' . $chEmStatusDescription. ' Please correct your information and try again or <a href="/contact-us">contact us</a>.  <br /><br /><strong>Failure (3) ' . date('\o\n l, F j, Y \a\t h:i:s a') . '.</strong></div>';
		}
	} else {
		echo '<div class="bad">Your information could not be processed at this time. Please <a href="/contact-us">contact us</a>.  <br /><br /><strong>Failure (4) ' . date('\o\n l, F j, Y \a\t h:i:s a') . '.</strong></div>';
	}
}
else {
		echo '<div class="bad">Your information could not be processed at this time. Please <a href="/contact-us">contact us</a>.  <br /><br /><strong>Failure (5) ' . date('\o\n l, F j, Y \a\t h:i:s a') . '.</strong></div>';
	}
?>
