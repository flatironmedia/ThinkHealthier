<?php
// function to scrub the bad chars
include_once($_SERVER["DOCUMENT_ROOT"] . "/scripts/convertChars.php");

if ((count($_POST) == 0) && (count($_GET) > 0))
{
	foreach ($_GET as $Key=>$Val)
	{
		$_POST[$Key] = $Val;
	}
}

$title = 'Change My E-Mail Address';
?>

<script type="text/javascript">
/* <![CDATA[ */

function sendSPRequest() {
	if (!document.getElementById) {
		alert("Your browser does not support this process.  Please contact us to change your e-mail address.");
	} else {
		curEmail = document.getElementById('curEmail');
		curEmailVal = curEmail.value;
		newEmail = document.getElementById('newEmail');
		newEmailVal = newEmail.value;
		newEmail2 = document.getElementById('newEmail2');
		newEmail2Val = newEmail2.value;
		ipAddress = document.getElementById('ipAddress');
		ipAddressVal = ipAddress.value;
		if (curEmailVal == '' || !validEmail(curEmail)) {
			alert("Please enter a valid current email.");
			curEmail.focus();
			return false;
		}
		if (newEmailVal == '' || !validEmail(newEmail)) {
			alert("Please enter a valid new email.");
			newEmail.focus();
			return false;
		}
		if (curEmailVal == newEmailVal) {
			alert("Your current email and new email should not match.  Please try again.");
			newEmail.focus();
			return false;
		}
		if (newEmail2Val == '' || !validEmail(newEmail2)) {
			alert("Please enter a valid confirm new email.");
			newEmail2.focus();
			return false;
		}
		if (newEmailVal != newEmail2Val) {
			alert("Your new email and confirm new email must match.  Please try again.");
			newEmail.focus();
			return false;
		}
		callEmailSP(curEmailVal, newEmailVal, ipAddressVal);
		return false;
	}
}


/* ]]> */
</script>


<div class="signupBoxNLsAbout clearFix">
	<div class="clearFix">
		<div>
				<div>
					Please enter your current email address, your new email address two times, then click <strong>update*</strong>
					and we will attempt to update your newsletter subscriptions.
					<hr>

					<form style="padding-left:10px;" action="<?php echo $_SERVER['PHP_SELF']; ?>
						" method="get" onsubmit="sendSPRequest(); return false;">
						<div class="clearFix">
							<label for="curEmail" class="emailLabel">Current email:</label>
							<input type="text" name="curEmail" id="curEmail" class="emailField" />
						</div>
						<div class="clearFix">
							<label for="newEmail" class="emailLabel">New email:</label>
							<input type="text" name="newEmail" id="newEmail" class="emailField" />
						</div>
						<div class="clearFix">
							<label for="newEmail2" class="emailLabel">Confirm new email:</label>
							<input type="text" name="newEmail2" id="newEmail2" class="emailField" />
						</div>
						<input type="hidden" name="ipAddress" id="ipAddress" value="<?php if($_GET['passIP'] != true) { echo $_SERVER['REMOTE_ADDR']; } ?>" />
						<p>
							<input src="scripts/btn-update.png" type="image" type="button" title="update" alt="update" value="update" class="srchBtn update-btn" />
						</p>
					</form>
					<hr>
					<div <?php echo $signInBoxInnerDisplay; ?>
						style="padding:5px 0 0 0;">After clicking update please wait for the system to respond with an answer to your request.  If you feel there is a problem you can
						<a href="#"> <strong>contact us</strong>
						</a>
						as well.
						<br>
						<br>
						*Note: If you are not currently a subscriber and wish to sign up for our newsletter(s), please
						<a href="#">
							<strong>click here</strong>
						</a>
						.  
		Alternatively you can
						<a href="#">
							<strong>unsubscribe</strong>
						</a>
						from any of your current subscriptions.
					</div>

				</div>
			</div>

	</div>

</div>

<br>
<br>