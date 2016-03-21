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

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
JHtmlBehavior::framework();
?>
<form action="<?php echo JRoute::_('index.php?option=com_yoorecipe&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
	
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
					<?php echo $this->form->renderField('recipe_id'); ?>
					<?php echo $this->form->renderField('title'); ?>
					<?php echo $this->form->renderField('note'); ?>
					<?php echo $this->form->renderField('comment'); ?>
					<?php echo $this->form->renderField('published'); ?>
					<?php echo $this->form->renderField('abuse'); ?>
					<?php echo $this->form->renderField('ip_address'); ?>
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