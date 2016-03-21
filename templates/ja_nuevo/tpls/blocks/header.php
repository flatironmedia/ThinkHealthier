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


// get params
$sitename  = $this->params->get('sitename');
$slogan    = $this->params->get('slogan', '');
$logotype  = $this->params->get('logotype', 'text');
$logoimage = $logotype == 'image' ? $this->params->get('logoimage', T3Path::getUrl('images/logo.png', '', true)) : '';
$logoimgsm = ($logotype == 'image' && $this->params->get('enable_logoimage_sm', 0)) ? $this->params->get('logoimage_sm', T3Path::getUrl('images/logo-sm.png', '', true)) : false;

if (!$sitename) {
	$sitename = JFactory::getConfig()->get('sitename');
}

$logosize = 'col-lg-2 col-xs-4';

$mainnavsize = 'col-lg-10 col-xs-8';
if ($headright = $this->countModules('head-search or languageswitcherload or off-canvas')) {
	$mainnavsize = 'col-lg-10 col-xs-8';
}

?>

<!-- HEADER -->
<header id="t3-header" class="wrap t3-header">
	<div class="container">
		<div class="row">
			<!-- LOGO -->
			<div class="col-lg-6 col-md-6 col-xs-6 logo">
				<div class="logo-<?php echo $logotype, ($logoimgsm ? ' logo-control' : '') ?>">
					<a href="/" title="">
						<?php if($logotype == 'image'): ?>
							<img class="logo-img" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/images/logo.png" alt="<?php echo strip_tags($sitename) ?>" />
							<img class="logo-img logo-m" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/images/logo-m.png" alt="<?php echo strip_tags($sitename) ?>" />
						<?php endif ?>
						<?php if($logoimgsm) : ?>
							<img class="logo-img-sm" src="<?php echo JURI::base(true) . '/' . $logoimgsm ?>" alt="<?php echo strip_tags($sitename) ?>" />
						<?php endif ?>
						<span><?php echo $sitename ?></span>
					</a>
					<small class="site-slogan"><?php echo $slogan ?></small>
				</div>
			</div>
			<!-- //LOGO -->


				<!-- OFFCANVAS -->
				<?php if ($this->countModules('off-canvas')) : ?>
					<div class="pull-right">
					<?php if ($this->getParam('addon_offcanvas_enable')) : ?>
						<?php $this->loadBlock ('off-canvas') ?>
					<?php endif ?>
					</div>
				<?php endif ?>
				<!-- //OFFCANVAS -->


			<!-- RIGHT MODS -->
			<div id="right-mods" class="col-lg-6 col-md-6 col-xs-6">
				<?php if ($headright): ?>
					<div class="t3-nav-btn pull-right">



						<!-- OFF SIDEBAR WAS HERE -->



						<!-- Brand and toggle get grouped for better mobile display -->
						<div class="navbar-header pull-right">

							<?php if ($this->getParam('navigation_collapse_enable', 1) && $this->getParam('responsive', 1)) : ?>
								<?php $this->addScript(T3_URL.'/js/nav-collapse.js'); ?>
								<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".t3-navbar-collapse">
									<i class="fa fa-bars"></i>
								</button>
							<?php endif ?>
						</div>

						<?php if ($this->countModules('languageswitcherload')) : ?>
							<!-- LANGUAGE SWITCHER -->
							<div class="languageswitcherload<?php $this->_c('languageswitcherload') ?>">
								<jdoc:include type="modules" name="<?php $this->_p('languageswitcherload') ?>" style="raw" />
							</div>
							<!-- //LANGUAGE SWITCHER -->
						<?php endif ?>

						<!-- SEARCH -->
						<?php if ($this->countModules('head-search')) : ?>
							<div class="-search">
										<jdoc:include type="modules" name="<?php $this->_p('head-search') ?>" style="Xhtml" />
							</div>
						<?php endif ?>
						<!-- //HEAD SEARCH -->
					</div>
				<?php endif ?>

				<div class="navbar navbar-default t3-mainnav pull-right">

					<?php if ($this->getParam('navigation_collapse_enable')) : ?>
						<div class="t3-navbar-collapse navbar-collapse collapse"></div>
					<?php endif ?>


				</div>

			</nav>
			<!-- //RIGHT MODS -->

		</div>
	</div>
</header>
<!-- //HEADER -->

<!-- MAIN NAVIGATION -->
<nav id="t3-mainnav" class="wrap" style="width:100%;">


	<div class="navbar navbar-default t3-mainnav container">

		<div class="t3-navbar navbar-collapse collapse row">
			<jdoc:include type="<?php echo $this->getParam('navigation_type', 'megamenu') ?>" name="<?php echo $this->getParam('mm_type', 'mainmenu') ?>" />
		</div>

	</div>

</nav>
<!-- //MAIN NAVIGATION -->
