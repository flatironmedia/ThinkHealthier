<?php
$whichSubDBSvr = "mailtools.thinkhealthier.com"; // svr that has the oempro/getleadsite scripts on them
$whichSubDBIP = "206.188.9.102"; // VIP that points to the db

// echo PHP_EOL . "<!-- whichSubDBSvr: '$whichSubDBSvr' | whichSubDBIP: '$whichSubDBIP' -->" . PHP_EOL;
// check the server IP
$goodIPs = array('206.188.9.76', '206.188.9.77', '10.10.10.187', '64.106.188.187'); $devCheckIP = false;
$ssa = strtolower($_SERVER['SERVER_ADDR']);
if ($ssa == '127.0.0.1' || $ssa == 'localhost') {
	$serv_addr = getHostByName(php_uname('n'));
} else {
	$serv_addr = $_SERVER['SERVER_ADDR'];
}
foreach ($goodIPs as $val) {
	if ($val == $serv_addr) {
		$devCheckIP = true;
		// echo "<-- true - -->";
	}
	// echo "<!-- serv_addr: '$serv_addr' - referServ: '$val'<br />\r\n -->";
}

// check the server domain
$goodName = 'dev.mydailymoment.com'; $devCheckName = false;
$serv_name = $_SERVER['SERVER_NAME'];
// echo "<!-- serv_name: $serv_name<br />\r\n -->";
$serv_nameAr = explode('.', $serv_name);
$serv_nameTLD = $serv_nameAr[sizeof($serv_nameAr)-2] . '.' . $serv_nameAr[sizeof($serv_nameAr)-1];
// echo "<!-- serv_nameTLD: $serv_nameTLD<br />\r\n -->";
if ($serv_nameTLD == $goodName) {
	$devCheckName = true;
}

if ($devCheckIP || $devCheckName) {
	// $whichSubDBSvr = "mlrdev.mydailymoment.com";
}

?>
