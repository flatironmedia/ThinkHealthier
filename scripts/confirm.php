<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('America/New_York');

if (isset($_GET['em']) && $_GET['em'] != 'false') { 
    $email = $_GET['em']; $email2 = $_GET['em'];
} else { $email = 'noEmail'; $email2 = ''; }
if (isset($email2) && trim($email2) != '') {
	$email3 = " (" . trim($email2) . ") ";
} else { $email3 = ""; }
if (isset($_GET['fn'])) { $fn = $_GET['fn']; } else { $fn = ''; }
if (isset($_GET['ln'])) { $ln = $_GET['ln']; } else { $ln = ''; }
if (isset($_GET['c'])) { $c = $_GET['c']; } else { $c = ''; }
if (isset($_GET['st'])) { $st = $_GET['st']; } else { $st = ''; }
if (isset($_GET['z'])) { $z = $_GET['z']; } else { $z = ''; }
if (isset($_GET['g'])) { $g = $_GET['g']; } else { $g = ''; }
// for dob add in the variabla name so you don't see 'blank' dob error
if (isset($_GET['dob']) && $_GET['dob'] != '') { $dob = "%26dob=" . $_GET['dob']; } else { $dob = ''; }
$allVars = "em=$email2%26fn=$fn%26ln=$ln%26c=$c%26st=$st%26z=$z%26g=$g$dob";
echo "<!-- allVars: $allVars -->\r\n";
$dOptInPageTitle = "Confirming your ThinkHealthier Subscriptions";
// see if they are from a double opt in
if (isset($_GET['dOptIn'])) { 
    $dOptIn = $_GET['dOptIn'];
} else { $dOptIn = 3; }
if (isset($_GET['rmlID'])) { 
    $rmlID = $_GET['rmlID'];
} else { $rmlID = 0; }
if ($dOptIn == '1') { 
    $dOptInText = '<div class="dOptIn"><div class="dOptInTitle">Thank you for confirming your subscription[s].</div><div class="dOptInText">You\'ll start receiving your email newsletters within the next 48 hours. In the interim, please feel free to start <br />exploring <a href="http://www.ThinkHealthier.com/">ThinkHealthier.com</a>. <strong>Thank you!</strong></div></div>';
	
} elseif ($dOptIn == '0') { 
    $dOptInText = '<div class="dOptIn"><div class="dOptInTitle">We are sorry but your confirmation failed.</div><div class="dOptInText">Please <a href="/contact-us">contact us</a> about confirming your subscription. Make sure to include the email address ' . $email3 . ' you signed up with.</div></div>';
} elseif ($dOptIn == '2') { 
    $dOptInText = '<div class="dOptIn"><div class="dOptInTitle">You have already confirmed your subscriptions.</div><div class="dOptInText">Please <a href="/unsubscribe?getSubs=1&key=' . mt_rand(1000000, 999999999) . '&getSubsEmail=' . $email2 . '">login</a> to manage your subscriptions and make changes.</div></div>';
} else { 
    $dOptInText = '';
}

echo $dOptInText;

?>
