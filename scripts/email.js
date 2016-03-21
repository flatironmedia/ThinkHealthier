function lTrim(str) {
    var whitespace = new String(" \t\n\r");
    var s = new String(str);
    if (whitespace.indexOf(s.charAt(0)) != -1) {
        var j=0, i = s.length;
        while (j < i && whitespace.indexOf(s.charAt(j)) != -1)
        j++;
        s = s.substring(j, i);
    }
    return s;
}
function rTrim(str) {
    var whitespace = new String(" \t\n\r");
    var s = new String(str);
    if (whitespace.indexOf(s.charAt(s.length-1)) != -1) {
        var i = s.length - 1;
        while (i >= 0 && whitespace.indexOf(s.charAt(i)) != -1)
        i--;
        s = s.substring(0, i+1);
    }
    return s;
}
function trim(str) {
    return rTrim(lTrim(str));
}
function validEmail(em) {
	var isEmail;
	var objRegExp = /^([a-zA-Z0-9_\-\.\+]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
	if(objRegExp.test(trim(em.value))) { isEmail = true; }
	else { isEmail = false; }
	if (em.value.indexOf(".@", 0) != -1) { isEmail = false; } // -1 means not found and we don't want .@ 
	if(!(em.value.indexOf("dodgeit.com",0)==-1) || !(em.value.indexOf("mailinator.com",0)==-1)){ isEmail = false; }
	return isEmail;
}
function validEmail2(str) {
  var re1 = /^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?)$/
  var re2 = /(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)/
  if (str.search(re1) != 0) { return false; }
  else if (str.search(re2) > -1) { return false; }
  else { return true; }
}

function viewPic(img) {
	picfile = new Image(); 
	picfile.src =(img); 
	fileCheck(img); 
}
function fileCheck(img){
	if( (picfile.width!=0) && (picfile.height!=0) ) {
		makeWindow(img); 
	} else {
		funzione="fileCheck('"+img+"')"; 
		intervallo=setTimeout(funzione,50); 
	}
}

function makeWindow(img) {
	ht = picfile.height + 20;
	wd = picfile.width + 20; 

	var args= "height=" + ht + ",innerHeight=" + ht;
	args += ",width=" + wd + ",innerWidth=" + wd;
	if (window.screen) {
		var avht = screen.availHeight; 
		var avwd = screen.availWidth;
		var xcen = (avwd - wd) / 2; 
		var ycen = (avht - ht) / 2;
		args += ",left=" + xcen + ",screenX=" + xcen;
		args += ",top=" + ycen + ",screenY=" + ycen + ",resizable=yes"; 	
    }
	// return window.open(img, '', args);
	imagePopup=window.open(img, '', args);
	imagePopup.focus();
} 

function getCookie(name) {
	var arg=name+"="; var alen=arg.length; var clen=document.cookie.length;
	if((document.cookie==null)||(document.cookie.length==null)) { return null; }
	var i=0;
	while(i < clen) {
		var j=i+alen;
		if(document.cookie.substring(i,j)==arg) { return getCookieVal(j); }
		i=document.cookie.indexOf(" ",i)+1;
		if(i==0) break;
	}
	return null;
}
function getCookieVal(offset) {
	var endstr=document.cookie.indexOf(";",offset);
	if(endstr==-1) { endstr=document.cookie.length; }
	return unescape(document.cookie.substring(offset,endstr));
}
function getCookieKey(name,key) {
	var result=""; var tcookie=getCookie2(name);
	if(tcookie!=null){temp=tcookie.split("&");
		for(var i=0; i<temp.length; i++) {
			if(temp[i].indexOf(key,0)>=0) {
				args=temp[i].split("="); result=args[1]; break;
			}
		}
	}
	return result;
}
function setCookie(name,value,expires,path,domain,secure){
	// set time, it's in milliseconds
	var today = new Date();
	today.setTime(today.getTime());
	// pass expires as a number of days
	if (expires) { expires = expires * 1000 * 60 * 60 * 24; }
	var expires_date = new Date( today.getTime() + (expires) );
	
	document.cookie=name+"="+escape(value) +
	((expires) ? "; expires="+expires_date.toGMTString() : "") +
	((path) ? "; path="+path : "") +
	((domain) ? "; domain="+domain : "") +
	((secure) ? "; secure" : "");
}
function deleteCookie(name,path,domain ) {
	if(getCookie(name)) document.cookie = name + "=" +
	((path) ? ";path=" + path : "") +
	((domain) ? ";domain=" + domain : "" ) +
	";expires=Thu, 01-Jan-1970 00:00:01 GMT";
}
function exemptURL() {
	return false;
}
/* Client-side access to querystring name=value pairs | Version 1.3 | 28 May 2008
License (Simplified BSD): http://adamv.com/dev/javascript/qslicense.txt */
function Querystring(qs) { // optionally pass a querystring to parse
	this.params = {};
	
	if (qs == null) qs = location.search.substring(1, location.search.length);
	if (qs.length == 0) return;

// Turn <plus> back to <space>
// See: http://www.w3.org/TR/REC-html40/interact/forms.html#h-17.13.4.1
	qs = qs.replace(/\+/g, ' ');
	var args = qs.split('&'); // parse out name/value pairs separated via &
	
// split out each name=value pair
	for (var i = 0; i < args.length; i++) {
		var pair = args[i].split('=');
		var name = decodeURIComponent(pair[0]);
		
		var value = (pair.length==2)
			? decodeURIComponent(pair[1])
			: name;
		
		this.params[name] = value;
	}
}

Querystring.prototype.get = function(key, default_) {
	var value = this.params[key];
	return (value != null) ? value : default_;
}
Querystring.prototype.contains = function(key) {
	var value = this.params[key];
	return (value != null);
}
// Parse the current page's querystring
var qs = new Querystring();
// utm parameters
utmSource = qs.get("utm_source","");
utmSourceOrig = qs.get("utm_source","");
utmCampaign = qs.get("utm_campaign","");
utmMedium = qs.get("utm_medium","");
function checkSource(thsource,thcampaign) {
	// alert ('thsource : ' + thsource);
	badSourceArray = ['THHNNL', 'HNNL']; /* array of bad/nl sources */
	for (var j = 0; j < 10; j++) { /* run this a few times to get them all */
		for (var i = 0; i < badSourceArray.length; i++) { /* check each against the passed source */
		    if (thsource.indexOf(badSourceArray[i]) != -1) {
				utmSource = thsource.substring(badSourceArray[i].length, thsource.length); /* strip out the bad stuff */
				setCookie('thIsFromNLWhichPageJS', 'firstPage', 0, '/');
				setCookie('thIsFromNLJS', badSourceArray[i], 0, '/');
				// alert('true: ' + thsource + ' ' + badSourceArray[i]);
				// return true; /* if found return true */
	    	}
		    if (thcampaign.indexOf(badSourceArray[i]) != -1) {
				utmCampaign = ''; /* blank out the campaign */
				setCookie('FMACampaign','',31,'/');
				// return true; /* if found return true */
	    	}
	  	}
	}
	// alert('false: ' + thsource);
	return false; /* else we're okay to read the source */
}
if (!checkSource(utmSource,utmCampaign)) { /* check to see if the source is from one of our NLS, if so don't store it */
	//FMASource
	// alert ('utmSource: ' + utmSource);
	if (utmSource != '') {
		setCookie('FMASource',utmSource,31,'/');
	}
	//FMACampaign
	// alert ('utmCampaign: ' + utmCampaign);
	if (utmCampaign != '') {
		setCookie('FMACampaign',utmCampaign,31,'/');
	}
	//FMAMedium
	// alert ('utmMedium: ' + utmMedium);
}
function queryStr(ji) {
	hu = window.location.search.substring(1);
	gy = hu.split("&");
	for (i=0;i<gy.length;i++) 
	{
		ft = gy[i].split("=");
		if (ft[0] == ji)
		{
			return ft[1];
		}
	}
}

function returnUTMSignupVars(thSource, thCampaign) {
	utmTmpSource = getCookie('FMASource');
	utmTmpCampaign = getCookie('FMACampaign');
	document.write("\n");
	if (utmTmpSource == '' || utmTmpSource == null) {
		document.write('<input type="hidden" name="FormValue_CustomField12" value="' + thSource + '" />');
		document.write("\n");
		document.write('<input type="hidden" name="FormValue_CustomField20" value="' + thSource + '" />');
	} else {
		document.write('<input type="hidden" name="FormValue_CustomField12" value="' + utmTmpSource + '" />');
		document.write("\n");
		document.write('<input type="hidden" name="FormValue_CustomField20" value="' + utmTmpSource + '" />');
	}
	document.write("\n");
	if (utmTmpCampaign == '' || utmTmpCampaign == null) {
		document.write('<input type="hidden" name="FormValue_CustomField19" value="' + thCampaign + '" />');
	} else {
		document.write('<input type="hidden" name="FormValue_CustomField19" value="' + utmTmpCampaign + '" />');
	}
}
