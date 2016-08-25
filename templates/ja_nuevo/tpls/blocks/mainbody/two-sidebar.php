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
//die('<pre>'.print_r(JFactory::getApplication()->input->get('view'), true).'</pre>');
defined('_JEXEC') or die;

/**
 * Mainbody 3 columns, content in center: sidebar1 - content - sidebar2
 */
?>

<div id="t3-mainbody" class="container t3-mainbody">
	<div class="row">
		<?php if(JRequest::getVar( 'view' ) !== 'article' && JRequest::getVar( 'layout' ) !== 'blog' && JFactory::getApplication()->input->get('option', '', 'STRING') !== 'com_yoorecipe'): ?>
		<!-- MAIN CONTENT -->
		<div id="t3-content" class="t3-content col-xs-12">

			<!-- ARTICLE TOP -->
			<div class="article-top col-md-12">
				<jdoc:include type="modules" name="article-top" style="T3Xhtml" />
			</div>
			<!-- //ARTICLE TOP -->
			
			<?php if($this->hasMessage()) : ?>
				<jdoc:include type="message" />
			<?php endif ?>
				<jdoc:include type="component" />
		</div>
		<!-- //MAIN CONTENT -->
		<?php else: ?>

		<div class="main-content-sidebar-left col-xs-12 col-md-9">
			<!-- MAIN CONTENT -->
			<div id="t3-content" class="t3-content col-xs-12 col-md-8 col-md-push-4">
				<?php if($this->hasMessage()) : ?>
					<jdoc:include type="message" />
				<?php endif; ?>
					<jdoc:include type="component" />
			</div>
			<!-- //MAIN CONTENT -->
			
			<!-- SIDEBAR 1 -->
			<div class="t3-sidebar t3-sidebar-1 col-xs-6 col-md-4 col-md-pull-8 <?php $this->_c($vars['sidebar1']) ?>">
				<jdoc:include type="modules" name="<?php $this->_p($vars['sidebar1']) ?>" style="T3Xhtml" />
			</div>
			<!-- //SIDEBAR 1 -->

			<!-- ARTICLE BOTTOM -->
			<div class="article-bottom col-md-12">
				<jdoc:include type="modules" name="article-bottom" style="T3Xhtml" />
			</div>
			<!-- //ARTICLE BOTTOM -->
		</div>

		<!-- SIDEBAR 2 -->
		<div class="t3-sidebar t3-sidebar-2 col-xs-6  col-md-3 <?php $this->_c($vars['sidebar2']) ?>">
			<jdoc:include type="modules" name="<?php $this->_p($vars['sidebar2']) ?>" style="T3Xhtml" />
		</div>
		<!-- //SIDEBAR 2 -->


		<?php endif; ?>

	</div>
</div>
