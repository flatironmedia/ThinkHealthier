<?php
/*------------------------------------------------------------------------
# com_yoorecipe -  YooRecipe! Joomla 2.5 & 3.x recipe component
# ------------------------------------------------------------------------
# author    YooRock!
# copyright Copyright (C) 2011 yoorock.fr. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.yoorecipe.com
# Technical Support:  Forum - http://www.yoorecipe.com/
-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtmlBehavior::framework();
?>
<div class="alert alert-info"><?php echo JText::_('COM_YOORECIPE_CUISINE_TYPE_HINT'); ?></div>

<form action="<?php echo JRoute::_('index.php?option=com_yoorecipe&view=servingtypelayout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm">
<div class="row-fluid">
	<!-- Begin Content -->
	<div class="span10 form-horizontal">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#general" data-toggle="tab"><?php echo JText::_('COM_YOORECIPE_YOORECIPE_DETAILS');?></a></li>
		</ul>

		<div class="tab-content">
			<!-- Begin Tabs -->
			<div class="tab-pane active" id="general">
				<?php echo $this->form->renderField('id'); ?>
				<?php echo $this->form->renderField('lang'); ?>
				<?php echo $this->form->renderField('code'); ?>
				<?php echo $this->form->renderField('published'); ?>
				<?php echo $this->form->renderField('creation_date'); ?>
			</div>
			<!-- End tab general -->

		
			<!-- End Tabs -->
		</div>
			<input type="hidden" name="task" value="" />
			<?php echo JHtml::_('form.token'); ?>
	</div>
		<!-- End Content -->
</div>
</form>