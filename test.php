<?php
// $ip = getHostByName(php_uname('n'));
// echo $ip;
print_r($_SERVER);
$headers = apache_request_headers(); $real_client_ip = $headers["X-Forwarded-For"];
print_r($headers);
echo 'real_client_ip: ' . $real_client_ip . PHP_EOL;
echo phpinfo();
?>
