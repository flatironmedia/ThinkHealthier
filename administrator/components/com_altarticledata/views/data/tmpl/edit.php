<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Altarticledata
 * @author     Ace | OGOSense <audovicic@ogosense.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

//echo('<pre>'.print_r($_GET, true).'</pre>');

if ($_GET['new']==1) {
	$db    = JFactory::getDbo();
	$query = "SELECT * FROM `#__altarticledata_data` 
		WHERE article_id=".$_GET['article_id'];
	$db->setQuery($query);
	$result = $db->loadObjectList();
	if (! empty($result)) {
		$app = JFactory::getApplication();
		$url = JRoute::_('index.php?option=com_altarticledata&view=data&layout=edit&id='.$result[0]->id.'&tmpl=component&article_id='.$_GET['article_id'].'&article_title='.$_GET['article_title'], false);
		//die('<pre>'.print_r($result, true).'</pre>');
		$app->redirect($url);
	}
}

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_altarticledata/assets/css/altarticledata.css');
?>
<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function () {
		
	});

	Joomla.submitbutton = function (task) {
		if (task == 'data.cancel') {
			Joomla.submitform(task, document.getElementById('data-form'));
		}
		else {
			
			if (task != 'data.cancel' && document.formvalidator.isValid(document.id('data-form'))) {
				
				Joomla.submitform(task, document.getElementById('data-form'));
			}
			else {
				alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_altarticledata&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="data-form" class="form-validate">

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_ALTARTICLEDATA_TITLE_DATA', true)); ?>
		<div class="row-fluid">
			<div class="span10 form-horizontal">
				<fieldset class="adminform">

									<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
				<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
				<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
				<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
				<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />

				<?php if(empty($this->item->created_by)){ ?>
					<input type="hidden" name="jform[created_by]" value="<?php echo JFactory::getUser()->id; ?>" />

				<?php } 
				else{ ?>
					<input type="hidden" name="jform[created_by]" value="<?php echo $this->item->created_by; ?>" />

				<?php } ?>			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('article_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('article_id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('article_title'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('article_title'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('headline'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('headline'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('intro'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('intro'); ?></div>
			</div>
			<!--
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('custom1'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('custom1'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('custom2'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('custom2'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('custom3'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('custom3'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('custom4'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('custom4'); ?></div>
			</div> 
			-->


				</fieldset>
			</div>
		</div>

		<button onclick="Joomla.submitbutton('data.apply');" class="btn btn-small btn-success">
		<span class="icon-apply icon-white"></span>Save</button>

		<?php 
		//echo('<pre>'.print_r($_GET, true).'</pre>');
		if (isset($_GET['article_id'])) echo 
			"<script>
				document.getElementById('jform_article_id').value =".$_GET['article_id'].";
			</script>";
		else echo "<script>
				window.parent.jModalClose();
			</script>";
		if (isset($_GET['article_title'])) echo 
			"<script>
				document.getElementById('jform_article_title').value ='".addslashes($_GET['article_title'])."';
			</script>";

		echo JHtml::_('bootstrap.endTab'); ?>

		

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

		<input type="hidden" name="task" value=""/>
		<?php echo JHtml::_('form.token'); ?>

	</div>
</form>
