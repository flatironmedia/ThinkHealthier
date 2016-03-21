<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:import href="/data1/strongmail/data/messages/importFiles/thhn/thhnXSL##CUR_DATE[2016-01-26]##.php" />

<xsl:output method="html" version="1.0" encoding="iso-8859-1" indent="yes" 
	doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN"
	doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />

<xsl:template match="/">

<html lang="en-US" dir="ltr">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<title>ThinkHealthier: Healthier News</title>
<style type="text/css" media="all">
  @media only screen and (max-device-width:480px) {
    table[class=contenttable] {
	width:320px !important;
    }
  }
body {padding:0;margin:0;}
body, td, div, font, span {
	font-size:12px;
	font-family:verdana,arial,sans-serif;
	color:#63857d;
}
a {font-size:12px; font-family:verdana,arial,sans-serif; color:#63857d; text-decoration:none;}
a:hover {color:#454e4c; text-decoration:underline;}
a.nav {color:#ffffff; text-decoration:none;}
a.nav:hover {text-decoration:underline;}
li {color:#63857d;}
img { border:none; text-align:right;}
</style>
</head>
<body text="#666666" link="#63857d" vlink="#63857d" alink="#454e4c">

<xsl:variable name="myopentag"><![CDATA[##OPENTAG##]]></xsl:variable>
<xsl:value-of select="$myopentag" disable-output-escaping="yes" />

<div align="center">

<xsl:apply-imports />

<xsl:variable name="mySender"><![CDATA[##Sender[dm1]##]]></xsl:variable>
<xsl:variable name="mySubscriptionDate"><![CDATA[##SubscriptionDate##]]></xsl:variable>
<xsl:variable name="myCustomField15"><![CDATA[##CustomField15##]]></xsl:variable>
<xsl:variable name="myMemberID"><![CDATA[##MemberID##]]></xsl:variable>
<xsl:variable name="mySource"><![CDATA[##Source##]]></xsl:variable>
<xsl:variable name="myDomain"><![CDATA[##Domain##]]></xsl:variable>
<xsl:variable name="smMailingID"><![CDATA[##MailingID##]]></xsl:variable>
<xsl:variable name="smSerialNo"><![CDATA[##SERIAL_NUMBER##]]></xsl:variable>
<xsl:variable name="myNL" select="'HNNL'" />
<xsl:variable name="myGAVars" select="concat('utm_source=',$myNL,$mySource,'&amp;utm_campaign=',$myNL,$myarticleDate,'&amp;utm_medium=',$mySender,$myNL,$myDomain)"/>
<xsl:variable name="myGAVarsLinkMoms" select="concat('utm_source=DHTMLFrom',$myNL,$mySource,'&amp;utm_campaign=',$myNL,$myarticleDate,'LinkMOMS&amp;utm_medium=',$myNL,$myDomain,'&amp;sn=',$smSerialNo,'&amp;rn=',$myadRandNum1,$myMemberID)"/>

<xsl:text>&#xa;&#xa;</xsl:text>

<xsl:comment><xsl:text>&#x20;</xsl:text>article date:<xsl:value-of select="$myarticleDate" disable-output-escaping="yes" /> - mailing id:<xsl:value-of select="$mymailingID" disable-output-escaping="yes" /><xsl:text>&#x20;</xsl:text></xsl:comment>

<xsl:text>&#xa;&#xa;</xsl:text>


<xsl:variable name="myAdBnrTopStart"><![CDATA[<img src="http://ad.doubleclick.net/N4364/ad/thinkhealthier.nl;pos=top;tile=1;sz=750x250;dcove=r;]]></xsl:variable>
<xsl:variable name="myAdBnrEnd"><![CDATA[?" width="750" height="250" border="0" alt="" />]]></xsl:variable>
<xsl:variable name="myAdBnrTop" select="concat($myAdBnrTopStart,'ord=',$myadRandNum1,$myMemberID,$myAdBnrEnd)"/>

<xsl:variable name="myAdSmallSqStart"><![CDATA[<img src="http://ad.doubleclick.net/N4364/ad/thinkhealthier.nl;pos=right-top;tile=2;sz=300x600;dcove=r;]]></xsl:variable>
<xsl:variable name="myAdSmallSqEnd"><![CDATA[?" width="300" height="600" border="0" alt="" />]]></xsl:variable>
<xsl:variable name="myAdSmallSq" select="concat($myAdSmallSqStart,'ord=',$myadRandNum1,$myMemberID,$myAdSmallSqEnd)"/>

<xsl:variable name="myAdBigSqStart"><![CDATA[<img src="http://ad.doubleclick.net/N4364/ad/thinkhealthier.nl;pos=right-bottom;tile=3;sz=300x250;dcove=r;]]></xsl:variable>
<xsl:variable name="myAdBigSqEnd"><![CDATA[?" width="300" height="250" border="0" alt="" />]]></xsl:variable>
<xsl:variable name="myAdBigSq" select="concat($myAdBigSqStart,'rdord=',$myadRandNum1,$myMemberID,';ord=',$myadRandNum1,$myMemberID,$myAdBigSqEnd)"/>

<table width="770" border="0" cellpadding="0" cellspacing="0" align="center">
<tr>
	<td valign="top" align="left"><img src="http://cdn.##domain_name[thinkhealthier.com]##/s.gif" width="16" height="1" alt="" border="0" /></td>
	<td valign="middle" align="center" style="padding:1px 0;">
		<table border="0" cellpadding="0" cellspacing="0" align="center">
		<tr>
			<td valign="middle" align="left" style="border:1px solid #336699; background-color:#D9EEFF; padding:2px;"><font face="verdana,arial,sans-serif" color="#666666" size="1" style="font-size:11px;"><strong><a href="##OBUNSUBSCRIBETAG####ArgDelimiter##_sm_extra=##ArgDelimiter####ArgDelimiter####ArgDelimiter##http://www.##domain_name[thinkhealthier.com]##/newsletters.php?getSubs=1&amp;getSubsMem=##MemberID##&amp;mem=##MemberID##" class="unsubBtn" target="_blank" style="color:#666666;">Click here</a></strong> </font> </td>
			<td width="3"> </td>
			<td valign="middle" align="right"><font face="verdana,arial,sans-serif" color="#646464" size="1" style="font-size:11px;">if you no longer wish to receive this newsletter. </font></td>
		</tr>
		</table>
	</td>
	<td valign="top" align="left"><img src="http://cdn.##domain_name[thinkhealthier.com]##/s.gif" width="16" height="1" alt="" border="0" /></td>
</tr>
<tr>
	<td colspan="3"><img src="http://cdn.##domain_name[thinkhealthier.com]##/s.gif" width="760" height="6" alt="" border="0" /></td>
</tr>
<tr>
	<td colspan="3"><a href="##OBCLICKTAG##2001##ArgDelimiter##_sm_extra=##ArgDelimiter####ArgDelimiter####ArgDelimiter##http://www.##domain_name[thinkhealthier.com]##/?{$myGAVars}" target="_blank"><img src="http://cdn.##domain_name[thinkhealthier.com]##/images/nls/healthynews/thhnHdr.jpg" alt="ThinkHealthier" title="ThinkHealthier" width="770" height="90" usemap="#Map" style="display:block;" /></a>
        <map name="Map" id="Map">
		   <area shape="circle" coords="684,43,20" href="##OBCLICKTAG##2004##ArgDelimiter##_sm_extra=##ArgDelimiter####ArgDelimiter####ArgDelimiter##https://www.facebook.com/thinkhealthier/" title="Connect with us on Facebook" target="_blank" />
          <area shape="circle" coords="725,43,20" href="##OBCLICKTAG##2005##ArgDelimiter##_sm_extra=##ArgDelimiter####ArgDelimiter####ArgDelimiter##https://twitter.com/think_healthier" title="Connect with us on Twitter" target="_blank" />
        </map></td>
</tr>
<tr>
	<td height="31" colspan="3" align="center" valign="middle" style="text-align:center; padding-left:5px; color:#FFFFFF; background:#69beaa; background:#69beaa url(http://cdn.##domain_name[thinkhealthier.com]##/images/nls/healthynews/thhnGreenNav.png) repeat-x top left; font-size:10px;">
		<a style="color:#FFFFFF;" class="nav" href="##OBCLICKTAG##2006##ArgDelimiter##_sm_extra=##ArgDelimiter####ArgDelimiter####ArgDelimiter##http://www.##domain_name[thinkhealthier.com]##/your-health?{$myGAVars}" target="_blank">YOUR HEALTH</a>
<xsl:text>
</xsl:text>
<xsl:text>&#x20;</xsl:text><xsl:text>&#x20;</xsl:text>|
<xsl:text>&#x20;</xsl:text><xsl:text>&#x20;</xsl:text><a style="color:#FFFFFF;" class="nav" href="##OBCLICKTAG##2007##ArgDelimiter##_sm_extra=##ArgDelimiter####ArgDelimiter####ArgDelimiter##http://www.##domain_name[thinkhealthier.com]##/healthy-recipes?{$myGAVars}" target="_blank">HEALTHY RECIPES</a>
<xsl:text>
</xsl:text>
<xsl:text>&#x20;</xsl:text><xsl:text>&#x20;</xsl:text>|
<xsl:text>&#x20;</xsl:text><xsl:text>&#x20;</xsl:text><a style="color:#FFFFFF;" class="nav" href="##OBCLICKTAG##2008##ArgDelimiter##_sm_extra=##ArgDelimiter####ArgDelimiter####ArgDelimiter##http://www.##domain_name[thinkhealthier.com]##/health-az?{$myGAVars}" target="_blank">HEALTH A-Z</a>
	</td>
</tr>
</table>
<table width="770" border="0" cellpadding="0" cellspacing="0" align="center">
<tr>
	<td valign="top" align="center" width="10" rowspan="4"><img src="http://cdn.##domain_name[thinkhealthier.com]##/s.gif" width="10" height="12" alt="" border="0" /></td>
	<td valign="top" align="left" width="440"><img src="http://cdn.##domain_name[thinkhealthier.com]##/s.gif" width="440" height="12" alt="" border="0" /></td>
	<td valign="top" align="center" width="10" rowspan="4"><img src="http://cdn.##domain_name[thinkhealthier.com]##/s.gif" width="10" height="12" alt="" border="0" /></td>
	<td valign="top" align="left" width="300"><img src="http://cdn.##domain_name[thinkhealthier.com]##/s.gif" width="300" height="12" alt="" border="0" /></td>
	<td valign="top" align="center" width="10" rowspan="4"><img src="http://cdn.##domain_name[thinkhealthier.com]##/s.gif" width="10" height="12" alt="" border="0" /></td>
</tr>
<tr>
	<td align="center" colspan="3">
		<!-- begin 750x250 th healthy news nl Top ad tag (tile=1) -->
		<a href="http://ad.doubleclick.net/N4364/jump/thinkhealthier.nl;pos=top;tile=1;sz=750x250;dcove=r;ord={$myadRandNum1}{$myMemberID}?" target="_blank"><xsl:value-of select="$myAdBnrTop" disable-output-escaping="yes" /></a>
		<!-- End ad tag -->
	</td>
</tr>
<tr>
	<td valign="top" align="left" width="440">
		<img src="http://cdn.##domain_name[thinkhealthier.com]##/s.gif" width="400" height="12" alt="" border="0" />
		<table width="440" border="0" cellpadding="0" cellspacing="0" bgcolo="#f2f2f2" style="background-color:#f2f2f2;">
		<tr>
			<td valign="middle" align="left" width="285" style="background-color:#f2f2f2;"><img src="http://cdn.##domain_name[thinkhealthier.com]##/images/nls/healthynews/thhnTodaysFtrd.png" width="285" alt="Today's Featured" border="0" /></td>
			<td valign="middle" align="center" width="155" style="background-color:#f2f2f2;"><font face="verdana,arial,sans-serif" size="2" color="#333333" style="font-size:13px; color:#333333;"><xsl:value-of select="$myarticleDateFull" disable-output-escaping="yes" /> </font></td>
		</tr>
		</table>
		<div><a href="##OBCLICKTAG##2011##ArgDelimiter##_sm_extra=##ArgDelimiter####ArgDelimiter####ArgDelimiter##{$myftrdArtURL}&amp;{$myGAVars}"><img src="http://cdn.##domain_name[thinkhealthier.com]##{$myftrdArtIm}" width="439" vspace="10" alt="" border="0" /></a></div>
		<table width="440" border="0" align="center" cellpadding="0" cellspacing="O">
		<tr>
			<td align="left"><font face="verdana,arial,sans-serif" size="3"><strong><a href="##OBCLICKTAG##2012##ArgDelimiter##_sm_extra=##ArgDelimiter####ArgDelimiter####ArgDelimiter##{$myftrdArtURL}&amp;{$myGAVars}" style="font-size:18px;"><xsl:value-of select="$myftrdArtTitle" disable-output-escaping="yes" /></a></strong> </font><br />
				<font face="verdana,arial,sans-serif" size="2" color="#333333" style="font-size:13px; color:#333333;"><xsl:value-of select="$myftrdArtText" disable-output-escaping="yes" /><xsl:text>&#x20;</xsl:text><a href="##OBCLICKTAG##2013##ArgDelimiter##_sm_extra=##ArgDelimiter####ArgDelimiter####ArgDelimiter##{$myftrdArtURL}&amp;{$myGAVars}" style="font-size:13px;">Read more.</a> </font></td>
		</tr>
		</table><br />
		<table width="440" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td colspan="3"><img src="http://cdn.##domain_name[thinkhealthier.com]##/images/nls/healthynews/thhnMoreHealthyNews.jpg" width="439" alt="More Healthy News" title="More Healthy News" border="0" /></td>
		</tr>
		<tr>
			<td colspan="3"><img src="http://cdn.##domain_name[thinkhealthier.com]##/s.gif" width="10" height="8" alt="" border="0" /></td>
		</tr>
		<tr>
			<td width="124"><a href="##OBCLICKTAG##2014##ArgDelimiter##_sm_extra=##ArgDelimiter####ArgDelimiter####ArgDelimiter##{$myart1URL}&amp;{$myGAVars}"><img src="http://cdn.##domain_name[thinkhealthier.com]##{$myart1Im}" width="124" alt="{$myart1Title}" title="{$myart1Title}" border="0" /></a></td>
			<td><img src="http://cdn.##domain_name[thinkhealthier.com]##/s.gif" width="10" height="10" alt="" border="0" /></td>
			<td valign="top" align="left"><a href="##OBCLICKTAG##2015##ArgDelimiter##_sm_extra=##ArgDelimiter####ArgDelimiter####ArgDelimiter##{$myart1URL}&amp;{$myGAVars}"><strong style="font-size:14px;"><xsl:value-of select="$myart1Title" disable-output-escaping="yes" /></strong></a><br />
				<font face="verdana,arial,sans-serif" size="2" color="#333333" style="font-size:12px; color:#333333;"><xsl:value-of select="$myart1Text" disable-output-escaping="yes" /><xsl:text>&#x20;</xsl:text><a href="##OBCLICKTAG##2016##ArgDelimiter##_sm_extra=##ArgDelimiter####ArgDelimiter####ArgDelimiter##{$myart1URL}&amp;{$myGAVars}" style="font-size:12px;">Read more.</a> </font></td>
		</tr>
		<tr>
			<td colspan="3"><img src="http://cdn.##domain_name[thinkhealthier.com]##/s.gif" width="10" height="10" alt="" border="0" /></td>
		</tr>
		<tr>
			<td><a href="##OBCLICKTAG##2017##ArgDelimiter##_sm_extra=##ArgDelimiter####ArgDelimiter####ArgDelimiter##{$myart2URL}&amp;{$myGAVars}"><img src="http://cdn.##domain_name[thinkhealthier.com]##{$myart2Im}" width="124" alt="{$myart2Title}" title="{$myart2Title}" border="0" /></a></td>
			<td><img src="http://cdn.##domain_name[thinkhealthier.com]##/s.gif" width="10" height="10" alt="" border="0" /></td>
			<td valign="top" align="left"><a href="##OBCLICKTAG##2018##ArgDelimiter##_sm_extra=##ArgDelimiter####ArgDelimiter####ArgDelimiter##{$myart2URL}&amp;{$myGAVars}"><strong style="font-size:14px;"><xsl:value-of select="$myart2Title" disable-output-escaping="yes" /></strong></a><br />
				<font face="verdana,arial,sans-serif" size="2" color="#333333" style="font-size:12px; color:#333333;"><xsl:value-of select="$myart2Text" disable-output-escaping="yes" /><xsl:text>&#x20;</xsl:text><a href="##OBCLICKTAG##2019##ArgDelimiter##_sm_extra=##ArgDelimiter####ArgDelimiter####ArgDelimiter##{$myart2URL}&amp;{$myGAVars}" style="font-size:12px;">Read more.</a> </font></td>
		</tr>
		<tr>
			<td colspan="3"><img src="http://cdn.##domain_name[thinkhealthier.com]##/s.gif" width="10" height="10" alt="" border="0" /></td>
		</tr>
		<tr>
			<td><a href="##OBCLICKTAG##2020##ArgDelimiter##_sm_extra=##ArgDelimiter####ArgDelimiter####ArgDelimiter##{$myart3URL}&amp;{$myGAVars}"><img src="http://cdn.##domain_name[thinkhealthier.com]##{$myart3Im}" width="124" alt="{$myart3Title}" title="{$myart3Title}" border="0" /></a></td>
			<td><img src="http://cdn.##domain_name[thinkhealthier.com]##/s.gif" width="10" height="10" alt="" border="0" /></td>
			<td valign="top" align="left"><a href="##OBCLICKTAG##2021##ArgDelimiter##_sm_extra=##ArgDelimiter####ArgDelimiter####ArgDelimiter##{$myart3URL}&amp;{$myGAVars}"><strong style="font-size:14px;"><xsl:value-of select="$myart3Title" disable-output-escaping="yes" /></strong></a><br />
				<font face="verdana,arial,sans-serif" size="2" color="#333333" style="font-size:12px; color:#333333;"><xsl:value-of select="$myart3Text" disable-output-escaping="yes" /><xsl:text>&#x20;</xsl:text><a href="##OBCLICKTAG##2022##ArgDelimiter##_sm_extra=##ArgDelimiter####ArgDelimiter####ArgDelimiter##{$myart3URL}&amp;{$myGAVars}" style="font-size:12px;">Read more.</a> </font></td>
		</tr>
		</table>
		<br /><img src="http://cdn.##domain_name[thinkhealthier.com]##/images/nls/e3e3e3Dot.png" width="440" height="1" alt="" border="0" /><br />
<!--POWERINBOX-->
<div class="powerinbox">
	<!-- domain:rs-1031-a.com -->
	<table width="439" class="container" border="0" cellpadding="0" cellspacing="0">
	<tbody>
	<tr>
		<td align="left" valign="middle" style="color:#63857d; font-family:verdana, arial, sans-serif; font-size:18px; font-weight:bold; text-align:left;">You Might Like </td>
		<td align="right" style="text-align:right;">
			<a href="http://branding.rs-1031-a.com/?utm_source=contentstripe&amp;utm_medium=email&amp;utm_campaign=flatiron&amp;utm_content=animatedlogo" style="display:inline-block; border:0; outline:none; text-decoration:none;" target="_blank">
			<img src="http://branding.rs-1031-a.com/recommend/transparent.gif" style="width:143px; height:40px;" width="143" height="40" alt="Learn more about RevenueStripe..." border="0" /></a>
		</td>
	</tr>
	</tbody>
	</table>
	<table width="439" class="fallback" border="0" cellspacing="0" cellpadding="0">
	<tbody>
	<tr>
		<td width="139" style="border-collapse:collapse; padding-right:11px;">
			<a href="http://stripe.rs-1031-a.com/stripe/redirect?cs_email=##Em_MD5[8a91d72fe89003d2a7a81d5c1302c20d]##&amp;cs_esp=flatironesp&amp;cs_offset=0&amp;cs_stripeid=2650" style="border-style:none; outline:none; text-decoration:none;" target="_blank">
			<img alt="" height="198" src="http://stripe.rs-1031-a.com/stripe/image?cs_email=##Em_MD5[8a91d72fe89003d2a7a81d5c1302c20d]##&amp;cs_esp=flatironesp&amp;cs_offset=0&amp;cs_stripeid=2650" style="display:block; border:0; height:auto; line-height:100%; outline:none; text-decoration:none;" width="139" border="0" /></a>
		</td>
		<td width="139" style="border-collapse:collapse; padding-right:11px;">
			<a href="http://stripe.rs-1031-a.com/stripe/redirect?cs_email=##Em_MD5[8a91d72fe89003d2a7a81d5c1302c20d]##&amp;cs_esp=flatironesp&amp;cs_offset=1&amp;cs_stripeid=2650" style="border-style:none; outline:none; text-decoration:none;" target="_blank">
			<img alt="" height="198" src="http://stripe.rs-1031-a.com/stripe/image?cs_email=##Em_MD5[8a91d72fe89003d2a7a81d5c1302c20d]##&amp;cs_esp=flatironesp&amp;cs_offset=1&amp;cs_stripeid=2650" style="display:block; border:0; height:auto; line-height:100%; outline:none; text-decoration:none;" width="139" border="0" /></a>
		</td>
		<td width="139" style="border-collapse:collapse;">
			<a href="http://stripe.rs-1031-a.com/stripe/redirect?cs_email=##Em_MD5[8a91d72fe89003d2a7a81d5c1302c20d]##&amp;cs_esp=flatironesp&amp;cs_offset=2&amp;cs_stripeid=2650" style="border-style:none; outline:none; text-decoration:none;" target="_blank">
			<img alt="" height="198" src="http://stripe.rs-1031-a.com/stripe/image?cs_email=##Em_MD5[8a91d72fe89003d2a7a81d5c1302c20d]##&amp;cs_esp=flatironesp&amp;cs_offset=2&amp;cs_stripeid=2650" style="display:block; border:0; height:auto; line-height:100%; outline:none; text-decoration:none;" width="139" border="0" /></a>
		</td>
	</tr>
	</tbody>
	</table>
</div>
<!--POWERINBOX-->
	</td>
	<td valign="top" align="center" width="300">
		<img src="http://cdn.##domain_name[thinkhealthier.com]##/images/nls/adv-top.png" width="66" height="10" alt="" border="0" style="margin:0 0 2px 0;" />
		<div><!-- begin 300x600 th healthy news nl ad tag (tile=2) -->
			<a href="http://ad.doubleclick.net/N4364/jump/thinkhealthier.nl;pos=right-top;tile=2;sz=300x600;dcove=r;ord={$myadRandNum1}{$myMemberID}?" target="_blank"><xsl:value-of select="$myAdSmallSq" disable-output-escaping="yes" /></a>
			<!-- End ad tag -->
		</div><br />
		<table width="300" border="0" align="center" cellpadding="00" cellspacing="0" bgcolor="#f2f2f2">
		<tr>
			<td align="left" valign="top" colspan="2"><img src="http://cdn.##domain_name[thinkhealthier.com]##/images/nls/healthynews/thhnHealthyEats.png" width="300" alt="Healthy Eats" border="0" /></td>
		</tr>
		</table>
		<table width="300" border="0" align="center" cellpadding="0" cellspacing="12" bgcolor="#f2f2f2" style="background-color:#f2f2f2;">
		<tr>
			<td align="left" valign="middle" bgcolor="#f2f2f2"><a href="##OBCLICKTAG##2023##ArgDelimiter##_sm_extra=##ArgDelimiter####ArgDelimiter####ArgDelimiter##{$myftrdRecipeURL}&amp;{$myGAVars}"><img src="http://cdn.##domain_name[thinkhealthier.com]##{$myftrdRecipeIm}" width="150" alt="Healthy Eats" border="0" /></a></td>
			<td align="left" valign="left" width="130" bgcolor="#f2f2f2"><font face="verdana,arial,sans-serif" size="2" style="font-size:12px;"><strong><a href="##OBCLICKTAG##2024##ArgDelimiter##_sm_extra=##ArgDelimiter####ArgDelimiter####ArgDelimiter##{$myftrdRecipeURL}&amp;{$myGAVars}"><xsl:value-of select="$myftrdRecipeTitle" disable-output-escaping="yes" /></a></strong> </font></td>
		</tr>
		</table><br />
		<div><!-- begin 300x250 th healthy news nl ad tag (tile=3) -->
			<a href="http://ad.doubleclick.net/N4364/jump/thinkhealthier.nl;pos=right-bottom;tile=3;sz=300x250;dcove=r;rdord={$myadRandNum1}{$myMemberID};ord={$myadRandNum1}{$myMemberID}?" target="_blank"><xsl:value-of select="$myAdBigSq" disable-output-escaping="yes" /></a>
			<!-- End ad tag -->
		</div>
	</td>
</tr>
</table>
<br />
<!-- footer -->
<table cellspacing="0" cellpadding="0" width="610" align="center" border="0">
<tr>
	<td valign="top" align="center"><hr width="610" size="1" />
		<table cellspacing="0" cellpadding="0" width="554" border="0">
		<tr>
			<td valign="top" align="center"><font 
				face="verdana,arial,sans-serif" color="#666666" 
				size="1" style="font-size:11px;"><strong style="font-size:11px;">Contact us by mail: </strong>Feedback, 20 West 22nd Street, Suite 908, NY, NY 10010 </font></td>
		</tr>
		<tr>
			<td valign="top" align="center"><font face="verdana,arial,sans-serif" color="#666666" size="1" style="font-size:11px;"><strong style="font-size:11px;">Privacy statement: </strong><a href="##OBCLICKTAG##2009##ArgDelimiter##_sm_extra=##ArgDelimiter####ArgDelimiter####ArgDelimiter##http://www.##domain_name[thinkhealthier.com]##/privacy-policy" target="_blank" style="font-size:11px;">http://www.##domain_name[thinkhealthier.com]##/privacy-policy</a></font></td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td valign="top" align="left"><img src="http://cdn.##domain_name[thinkhealthier.com]##/s.gif" width="1" height="10" alt="" border="0" /></td>
</tr>
</table>

<xsl:text>&#xa;&#xa;</xsl:text>

<xsl:comment><xsl:text>&#x20;</xsl:text>MemberID: <xsl:value-of select="$myMemberID" disable-output-escaping="yes" /><xsl:text>&#x20;</xsl:text></xsl:comment>
<xsl:text>&#xa;</xsl:text>
<xsl:comment><xsl:text>&#x20;</xsl:text>MailingID: <xsl:value-of select="$smMailingID" disable-output-escaping="yes" /><xsl:text>&#x20;</xsl:text></xsl:comment>
<xsl:text>&#xa;</xsl:text>
</div>
<xsl:text>&#xa;</xsl:text>
</body>
</html>

</xsl:template>

</xsl:stylesheet>
