<?php
// ini_set('display_errors', 'On');
// error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('America/New_York');

$allGetDataQS =  http_build_query($_GET);
header('Location: http://' . $_SERVER['SERVER_NAME'] . '/unsubscribe?' . $allGetDataQS);
?>
