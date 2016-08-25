<?php
/**
 * ------------------------------------------------------------------------
 * JA Nuevo template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */

defined('_JEXEC') or die;

if (! isset($_SESSION['item_counter'])) $_SESSION['item_counter']=0;

?>

<!-- META FOR IOS & HANDHELD -->
<?php if ($this->getParam('responsive', 1)): ?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
	<style type="text/stylesheet">
		@-webkit-viewport   { width: device-width; }
		@-moz-viewport      { width: device-width; }
		@-ms-viewport       { width: device-width; }
		@-o-viewport        { width: device-width; }
		@viewport           { width: device-width; }
	</style>
	<script type="text/javascript">
		//<![CDATA[
		if (navigator.userAgent.match(/IEMobile\/10\.0/)) {
			var msViewportStyle = document.createElement("style");
			msViewportStyle.appendChild(
				document.createTextNode("@-ms-viewport{width:auto!important}")
			);
			document.getElementsByTagName("head")[0].appendChild(msViewportStyle);
		}
		//]]>
	</script>
<?php endif ?>
<meta name="HandheldFriendly" content="true"/>
<meta name="apple-mobile-web-app-capable" content="YES"/>
<!-- //META FOR IOS & HANDHELD -->

<?php
// SYSTEM CSS
$this->addStyleSheet(JURI::base(true) . '/templates/system/css/system.css');
?>

<?php
// T3 BASE HEAD
$this->addHead();
?>

<?php
// CUSTOM CSS
if (is_file(T3_TEMPLATE_PATH . '/css/custom.css')) {
	$this->addStyleSheet(T3_TEMPLATE_URL . '/css/custom.css');
}
?>

<?php
// PRINT PREVIEW CSS
if(is_file(T3_TEMPLATE_PATH . '/css/print.css')) {
	$this->addStyleSheet(T3_TEMPLATE_URL.'/css/print.css');
}
?>

<?php
// Article dropdown menu CSS & JS
$this->addStyleSheet(JURI::base(true) . '/modules/mod_xpertscroller/assets/css/xpertscroller.css');
$this->addScript(JURI::root(true).'/modules/mod_xpertscroller/assets/js/xpertscroller.js');
$this->addScript(JURI::root(true).'/modules/mod_xpertscroller/assets/js/script.js');
?>

<!-- Le HTML5 shim and media query for IE8 support -->
<!--[if lt IE 9]>
<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
<script type="text/javascript" src="<?php echo T3_URL ?>/js/respond.min.js"></script>
<![endif]-->

<!-- You can add Google Analytics here or use T3 Injection feature -->

<script type="text/javascript">var switchTo5x=true;</script>
<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
<script type="text/javascript">stLight.options({publisher: "8e278f8f-cab1-453b-ac33-51b7aec9cc64", doNotHash: false, doNotCopy: false, hashAddressBar: false});</script>

<script type="text/javascript" src="http://flatiron-d.openx.net/w/1.0/jstag"></script>

<script>
 // Load GPT
/* var googletag = googletag || {};
 googletag.cmd = googletag.cmd || [];
 (function(){
   var gads = document.createElement('script');
   gads.async = true;
   var useSSL = 'https:' == document.location.protocol;
   gads.src = (useSSL ? 'https:' : 'http:') +
       '//www.googletagservices.com/tag/js/gpt.js';
   var node = document.getElementsByTagName('script')[0];
   node.parentNode.insertBefore(gads, node);
   })();*/
</script>

<script>

/*var adslot0;

 googletag.cmd.push(function() {

   // Declare any slots initially present on the page
   // adslot0 = googletag.defineSlot('/6355419/Travel', [728, 90], 'leaderboard').
   //     setTargeting("test","infinitescroll").
   //     addService(googletag.pubads());

   // Infinite scroll requires SRA
   googletag.pubads().enableSingleRequest();

   // Disable initial load, we will use refresh() to fetch ads.
   // Calling this function means that display() calls just
   // register the slot as ready, but do not fetch ads for it.
   googletag.pubads().disableInitialLoad();

   // Enable services
   googletag.enableServices();
 });*/

 // Function to generate unique names for slots
 var nextSlotId = 1;
 var nextSlotId = <?php echo($_SESSION['item_counter']+1-20); ?>;
 function generateNextSlotName() {
   var id = nextSlotId++;
   return 'adslot' + id;
 }

 // Function to add content to page, mimics real infinite scroll
 // but keeps it much simpler from a code perspective.
 function moreContent() {

   // Generate next slot name
   var slotName = generateNextSlotName();

   // Create a div for the slot
   var slotDiv = document.createElement('div');
   slotDiv.className = 'grid-item item inf-item';
   slotDiv.id = slotName; // Id must be the same as slotName
   
         //slotDiv.style.max-width = '300px';
   document.getElementById('grid').appendChild(slotDiv);
   //document.body.appendChild(slotDiv);


   // Define the slot itself, call display() to 
   // register the div and refresh() to fetch ad.
   googletag.cmd.push(function() {
     var slot = googletag.defineSlot('/6355419/Travel', [728, 90], slotName).
         setTargeting("test","infinitescroll").
         addService(googletag.pubads());

     // Display has to be called before
     // refresh and after the slot div is in the page.
     googletag.display(slotName);
     googletag.pubads().refresh([slot]);
   });
 }
</script>

<script>

function insertAd() {
// Generate next slot name
              var $container = jQuery('#grid');
             var slotName = generateNextSlotName();
             //alert(slotName);

             // Create a div for the slot
/*             var slotDiv = document.createElement('div');
             slotDiv.className = 'grid-item item inf-item';
             slotDiv.id = slotName; // Id must be the same as slotName
             
                   //slotDiv.style.max-width = '300px';
             $container.masonry()
                  .append( slotDiv )
                  .masonry( 'appended', slotDiv )
                  // layout
                  .masonry();*/

             //document.body.appendChild(slotDiv);

             //alert(slotName);
             var OX_ads = OX();
             OX_ads.addAdUnit("538204016");
             OX_ads.setAdUnitSlotId("538204016",slotName);
             OX_ads.load();
             /* OX_ads.push({
                 slot_id: "'.$_SESSION['item_counter'].'",
                 auid: "538204016"
              });*/

             // Define the slot itself, call display() to 
             // register the div and refresh() to fetch ad.
             /*googletag.cmd.push(function() {
               var slot = googletag.defineSlot('/6355419/Travel', [728, 90], slotName).
                   setTargeting("test","infinitescroll").
                   addService(googletag.pubads());

               // Display has to be called before
               // refresh and after the slot div is in the page.
               googletag.display(slotName);
               googletag.pubads().refresh([slot]);
             });
*/
}

</script>



<script type="text/javascript">
  window._taboola = window._taboola || [];
  _taboola.push({article:'auto'});
  !function (e, f, u) {
    e.async = 1;
    e.src = u;
    f.parentNode.insertBefore(e, f);
  }(document.createElement('script'),
  document.getElementsByTagName('script')[0],
  '//cdn.taboola.com/libtrc/flatironmedia-thinkhealthier/loader.js');
</script>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
  ga('create', 'UA-947206-9', 'auto');
  ga('send', 'pageview');
</script> 
