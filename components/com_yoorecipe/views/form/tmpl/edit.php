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

// Factories
$document 	= JFactory::getDocument();
$lang 		= JFactory::getLanguage();
$user		= JFactory::getUser();
$version	= new JVersion;

JHtml::_('formbehavior.chosen', 'select');

JHtml::_('bootstrap.framework');
JHTML::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('jquery.ui', array('core', 'sortable'));
JHtml::_('behavior.calendar');

jimport('joomla.environment.uri' );

// Load content language file
$lang->load('com_categories', JPATH_ADMINISTRATOR, $lang->getTag(), true);

// Get config parameters
$params 				= JComponentHelper::getParams('com_yoorecipe');
$use_recipe_settings 	= $params->get('use_recipe_settings', 1);
$use_nutrition_facts	= $params->get('use_nutrition_facts', 1);
$show_language 			= $params->get('show_language', 0);

// Make upload work for ipads
$show_description		= $use_recipe_settings ? $params->get('show_description', 1) : $params->get('show_description_fe', 1);
$show_preparation_time	= $use_recipe_settings ? $params->get('show_preparation_time', 1) : $params->get('show_preparation_time_fe', 1);
$show_cook_time			= $use_recipe_settings ? $params->get('show_cook_time', 1) : $params->get('show_cook_time_fe', 1);
$show_wait_time 		= $use_recipe_settings ? $params->get('show_wait_time', 1) : $params->get('show_wait_time_fe', 1);
$show_difficulty		= $use_recipe_settings ? $params->get('show_difficulty', 1) : $params->get('show_difficulty_fe', 1);
$show_cost				= $use_recipe_settings ? $params->get('show_cost', 1) : $params->get('show_cost_fe', 1);

$use_nutrition_facts	= $params->get('use_nutrition_facts', 1);

// Add style and JS
$document->addStyleSheet('media/com_yoorecipe/styles/yoorecipe.css');
$document->addStyleSheet('media/com_yoorecipe/styles/ajax-upload.css');
$document->addScript('administrator/components/com_yoorecipe/views/yoorecipe/submitbutton.js');
$document->addScript('media/com_yoorecipe/js/jquery-ui.custom-draggable.min.js');
$document->addScript('media/com_yoorecipe/js/jquery.ui.touch-punch.js');
$document->addScript('media/com_yoorecipe/js/generic.js');
$document->addScript('media/com_yoorecipe/js/ingredient.js');

$script = "window.addEvent('domready', function() {
	
	showLoading = function() {
		jQuery('div.huge-ajax-loading').css('display', 'block');
	}

	hideLoading = function() {
		jQuery('div.huge-ajax-loading').css('display', 'none');
	}
	
	Locale.use('".$lang->getTag()."');
	adminFormValidator = new Form.Validator.Inline('adminForm', {
		stopOnFailure: false,
		useTitles: false,
		errorPrefix: '',
		ignoreHidden:false,
		onFormValidate: function(passed, form, event) {
			if (passed) {
				jQuery('#alert-recipe-container').fadeOut();
				showLoading();
				return true;
			} else {
				jQuery('#alert-recipe-container').fadeIn();
				return false;
			}
		}
	});

	adminFormValidator.add('validate-ingredients', {
		test: function(field){
			if ($('nb_ingredients').value > 0) {
				jQuery('#alert-ingr-container').fadeOut();
				return true;
			} else {
				jQuery('#alert-ingr-container').fadeIn();
				return false;
			}
		}
	});
	adminFormValidator.add('validate-fraction', {
		errorMsg: Joomla.JText._('COM_YOORECIPE_FRACTION', true),
		test: function(field){
			decimal_pattern = new RegExp(/^\d*[,\.]?\d*$/g);
			fraction_pattern = new RegExp(/^(\d*)[\s]?\d*(\/)?\d*$/g);
			if (decimal_pattern.test(field.get('value')) || fraction_pattern.test(field.get('value'))) {
				return true;
			}else {
				return false;
			}
		}
	});
});";
$document->addScriptDeclaration($script);

$input	= JFactory::getApplication()->input;
$tab	= $input->get('tab', '', 'STRING');

$is_admin	= $user->authorise('core.admin', 'com_yoorecipe');
$is_new		= $this->item->id == 0 ? true : false;

$can_edit_state	= $user->authorise('core.admin', 'com_yoorecipe') || $user->authorise('core.edit.state', 'com_yoorecipe');

$sections = array();
$sections['ingredients'] 	= 'COM_YOORECIPE_YOORECIPE_INGREDIENTS';
if ($use_nutrition_facts) {
	$sections['nutritionfacts'] = 'COM_YOORECIPE_YOORECIPE_NUTRITION_FACTS';
}
$sections['details'] 		= 'COM_YOORECIPE_YOORECIPE_DETAILS';

if ($is_admin || $can_edit_state) {
	$sections['moderation'] 	= 'COM_YOORECIPE_YOORECIPE_MODERATION';
}
?>
<div class="huge-ajax-loading"></div>

<div class="yoorecipe-top-editrecipe">
<?php
	$modules = JModuleHelper::getModules('yoorecipe-top-editrecipe');
	foreach($modules as $module) {
		echo JModuleHelper::renderModule($module);
	}
?>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_yoorecipe&view=form&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-validate form-horizontal">
	
	<input type="hidden" id="language_tag" value="<?php echo $lang->getTag(); ?>"/>
	<?php echo $this->form->getInput('id'); ?>
	<?php echo $this->form->getInput('asset_id'); ?>
	
	<input type="hidden" name="action" id="action" value="next" class="validate-ingredients"/>
	<input type="hidden" name="created_by" value="<?php echo $this->form->getValue('created_by'); ?>"/>
	
	<!-- Begin sections -->	
	<section id="general">
		<h2><?php echo JText::_('COM_YOORECIPE_RECIPE'); ?></h2>
		
		<?php echo $this->form->renderField('title'); ?>
	<?php if ($show_language) { ?>	
		<?php echo $this->form->renderField('language'); ?>
	<?php } ?>
		<?php echo $this->form->renderField('category_id'); ?>
		<br/>
		<?php echo $this->form->renderField('difficulty'); ?>
		<?php echo $this->form->renderField('cost'); ?>
		<br/>
		
	<?php if ($show_preparation_time) { ?>
		<?php echo $this->form->renderField('preparation_time'); ?>
	<?php } ?>
	
	<?php if ($show_cook_time) { ?>
		<?php echo $this->form->renderField('cook_time'); ?>
	<?php } ?>
	
	<?php if ($show_wait_time) { ?>
		<?php echo $this->form->renderField('wait_time'); ?>
	<?php } ?>
	
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('picture'); ?>
			</div>
			<div class="controls">
			<?php 
	
			$document->addScript('media/com_yoorecipe/js/bootstrap-fileupload.js');
			$document->addStyleSheet('media/com_yoorecipe/styles/bootstrap-fileupload.min.css');
		if ($this->form->getValue('picture') == '') {
			?>
			<div class="fileupload fileupload-new" data-provides="fileupload">
				<div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;"></div>
				<div>
					<span class="btn btn-file">
						<span class="fileupload-new"><?php echo JText::_('COM_YOORECIPE_UPLOAD'); ?></span>
						<span class="fileupload-exists"><?php echo JText::_('COM_YOORECIPE_INGREDIENTS_UPDATE'); ?></span>
						<input type="file" name="picture" value="<?php echo $this->form->getValue('picture'); ?>"/>
					</span>
					<a href="#" class="btn fileupload-exists" data-dismiss="fileupload"><?php echo JText::_('COM_YOORECIPE_INGREDIENTS_DELETE'); ?></a>
				</div>
			</div>
	<?php	} else { ?>
			<div class="fileupload fileupload-exists" data-provides="fileupload" data-name="picture">
				<input type="hidden" name="picture" value="1" />
				<div class="fileupload-new thumbnail" style="width: 200px; height: 150px;"><img src="https://www.placehold.it/200x150/EFEFEF/AAAAAA&text=no+image" /></div>
				<div class="fileupload-preview fileupload-exists thumbnail" style="width: 200px; height: 150px;">
					<img src="<?php echo $this->form->getValue('picture'); ?>">	
				</div>
				<div>
					<span class="btn btn-file">
						<span class="fileupload-new"><?php echo JText::_('COM_YOORECIPE_UPLOAD'); ?></span>
						<span class="fileupload-exists"><?php echo JText::_('COM_YOORECIPE_INGREDIENTS_UPDATE'); ?></span>
						<input type="file"/>
					</span>
					<a href="#" class="btn fileupload-exists" data-dismiss="fileupload"><?php echo JText::_('COM_YOORECIPE_INGREDIENTS_DELETE'); ?></a>
				</div>
			</div>
	<?php	} ?>
		</div>
	</div>
	</section>
	
	<?php echo $this->form->renderField('spacer1'); ?>
	
	<section id="description">
		<h2><?php echo JText::_('COM_YOORECIPE_YOORECIPE_DESCRIPTION_LABEL'); ?></h2>
	<?php if ($show_description) { ?>
			<?php echo $this->form->getInput('description'); ?>
	<?php } ?>
	</section>
	
	<?php echo $this->form->renderField('spacer1'); ?>
	
	<section id="preparation">
		<h2><?php echo JText::_('COM_YOORECIPE_YOORECIPE_PREPARATION_LABEL'); ?></h2>
		<?php echo $this->form->getInput('preparation'); ?>
	</section>
	
	<?php echo $this->form->renderField('spacer2'); ?>
	
	<section id="notes">
		<h2><?php echo JText::_('COM_YOORECIPE_YOORECIPE_NOTES_LABEL'); ?></h2>
		<?php echo $this->form->getInput('notes'); ?>
	</section>
	
	<?php echo $this->form->renderField('spacer2'); ?>
	
	<?php foreach ($sections as $key => $section) { ?>
	<section id="<?php echo $key; ?>">
		<?php echo $this->loadTemplate($key); ?>
	</section>
	<?php } ?>
	
	<div id="buttons_group">
		<button class="btn btn-primary" type="button" onclick="jQuery('#task').val('form.apply');Joomla.submitbutton('form.apply');"><i class="icon-ok"></i> <?php echo JText::_('JAPPLY') ?></button>
		<button class="btn btn-primary" type="button" onclick="jQuery('#task').val('form.save');Joomla.submitbutton('form.save');"><i class="icon-ok"></i> <?php echo JText::_('JSAVE') ?></button>
		<a href="<?php echo JRoute::_('index.php?option=com_yoorecipe&view=recipes&layout=myrecipes'); ?>" class="btn" type="button"><?php echo JText::_('JCANCEL') ?></a>
	</div>
	
	<div id="alert-recipe-container" class="row-fluid alert alert-error hide">
		<?php echo JText::_('COM_YOORECIPE_FORM_VALIDATION_FAILED'); ?>
	</div>
	
	<div id="alert-ingr-container" class="row-fluid alert alert-error hide">
		<?php echo JText::_('COM_YOORECIPE_NO_INGREDIENTS'); ?>
	</div>
	<!-- End Content -->
	
	<input type="hidden" name="task" id="task" value="form.apply" />
	<?php echo JHtml::_('form.token'); ?>
</form>