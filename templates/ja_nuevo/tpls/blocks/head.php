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

<script>
 // Function to generate unique names for slots
 var nextSlotId = 1;
 var nextSlotId = <?php echo($_SESSION['item_counter']); ?>;
 function generateNextSlotName() {
   var id = nextSlotId++;
   return id;
 }

</script>


<script>
var adCounter = 0; 

function insertAd(adParameterX, adParameterY) {
    var $container = jQuery('#grid');
    var idCounter = generateNextSlotName();

    if (idCounter % adParameterX != 0) return 0;

    var slotName = 'adslot' + idCounter;
    var addSize = 250;
    adCounter++;
    if (idCounter % adParameterX == 0)
        if ((idCounter / adParameterX) % adParameterY == 0) {
            addSize = 600;
        }
    var OX_grid_ad = OX();
    OX_grid_ad.addAdUnit("538320228");
    OX_grid_ad.setAdUnitSlotId("538320228", slotName);
    OX_grid_ad.addVariable("pos", "infinite");
    OX_grid_ad.addVariable("cat", "your-health");
    OX_grid_ad.addVariable("scroll", adCounter);
    OX_grid_ad.load();
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
