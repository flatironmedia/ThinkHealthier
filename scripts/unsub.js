function compareSubs() {
	var c = document.getElementById('curSubs');
	var curAr = c.value.split(",");
	var subsAr = new Array();
	if (document.getElementById('thhnNL1').checked == true) {
		subsAr.push(document.getElementById('thhnNL1').value);
	}
	if (document.getElementById('partner1').checked == true) {
		subsAr.push(document.getElementById('partner1').value);
	}
	// alert("curAr: " + curAr);
	// alert("subsAr: " + subsAr);
	if (curAr.compare(subsAr)) {
		alert('You have not made any changes to your current subscriptions that require an update.');
		return false;
	} else {
		return true;
	}
}
Array.prototype.compare = function (array) {
    // if the other array is a falsy value, return
    if (!array)
        return false;

    // compare lengths - can save a lot of time
    if (this.length != array.length)
        return false;

    this.sort();
    array.sort();
    for (var i = 0; i < this.length; i++) {
        // Check if we have nested arrays
        if (this[i] instanceof Array && array[i] instanceof Array) {
            // recurse into the nested arrays
            if (!this[i].compare(array[i]))
                return false;
        }
        else if (this[i] != array[i]) {
            // Warning - two different object instances will never be equal: {x:20} != {x:20}
            return false;
        }
    }
    return true;
}
function addAllNls() {
	if (document.getElementById) {
		document.getElementById('thhnNL1').checked = true;
		document.getElementById('partner1').checked = true;
	}
}

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

var emReq;

function callEmailSP(curEm, newEm, ipAddr) {
	var emURL = "/scripts/chEm.php?curEm=" + curEm + "&newEm=" + newEm + "&ipAddr=" + ipAddr;
	// alert(emURL);
	if(window.XMLHttpRequest) {
		emReq = new XMLHttpRequest();
	} else if(window.ActiveXObject) {
		emReq = new ActiveXObject("Microsoft.XMLHTTP");
	} else {
		alert("Your browser does not support this process.  Please contact us to change your e-mail address.");
		return false;
	}
	emReq.open("GET", emURL, true);
	emReq.onreadystatechange = spCallBack;
	emReq.send(null);
}
function spCallBack() {
	obj = document.getElementById("emspPRes");
	
	// alert(emReq.readyState); alert(emReq.status); alert(emReq.responseText);
		if(emReq.readyState == 4) {
		if(emReq.status == 200) {
			response = emReq.responseText;
			obj.innerHTML = response;
		} else {
			alert("There was a problem retrieving the data:\n" + emReq.statusText);
		}
	}
}