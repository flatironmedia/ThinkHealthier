<?php
$whichSubDBSvr = "mailtools.mydailymoment.com"; // svr that has the oempro/getleadsite scripts on them
$whichSubDBIP = "206.188.9.101"; // VIP that points to the db

// check the server IP
$goodIPs = array('206.188.9.77'); $devCheckIP = false;
$serv_addr = $_SERVER['SERVER_ADDR'];
foreach ($goodIPs as $val) {
	if ($val == $serv_addr) {
		$devCheckIP = true;
		// echo "<-- true - -->";
	}
	// echo "<!-- serv_addr: '$serv_addr' - referServ: '$val'<br />\r\n -->";
}

// check the server domain
$goodName = 'dev2.mydailymoment.com'; $devCheckName = false;
$serv_name = $_SERVER['SERVER_NAME'];
// echo "<!-- serv_name: $serv_name<br />\r\n -->";
$serv_nameAr = explode('.', $serv_name);
$serv_nameTLD = $serv_nameAr[sizeof($serv_nameAr)-2] . '.' . $serv_nameAr[sizeof($serv_nameAr)-1];
// echo "<!-- serv_nameTLD: $serv_nameTLD<br />\r\n -->";
if ($serv_nameTLD == $goodName) {
	$devCheckName = true;
}

if ($devCheckIP || $devCheckName) {
	$whichSubDBSvr = "mlrdev.mydailymoment.com";
}

?>
