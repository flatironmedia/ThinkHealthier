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
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

JHtml::_('jquery.ui', array('core', 'sortable'));

JHtmlBehavior::framework();

$document = JFactory::getDocument();
 
// Load content language file
$lang 	= JFactory::getLanguage();
$lang->load('com_categories', JPATH_ADMINISTRATOR, $lang->getTag(), true);

// Get config parameters
$params 	= JComponentHelper::getParams('com_yoorecipe');
$currency	= $params->get('currency', '&euro;');

$document->addStyleSheet('../media/com_yoorecipe/styles/yoorecipe.css');
$document->addScript('../media/com_yoorecipe/js/generic.js');
$document->addScript('../media/com_yoorecipe/js/ingredient.js');

$input 	= JFactory::getApplication()->input;
$is_new = $this->item->id == 0 ? true : false;

$editor = JFactory::getEditor();
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
?>
<div class="huge-ajax-loading"></div>
<div id="loading" class="huge-ajax-loading" style="display: none;"></div>
<div id="alert-recipe-container" class="alert alert-error hide">
<?php echo JText::_('COM_YOORECIPE_FORM_VALIDATION_FAILED'); ?>
</div>
<div id="alert-ingr-container" class="row-fluid alert alert-error hide">
	<?php echo JText::_('COM_YOORECIPE_INGREDIENTS_MANDATORY'); ?>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_yoorecipe&view=yoorecipe&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">

	<input type="hidden" id="language_tag" value="<?php echo $lang->getTag(); ?>"/>
	
	<?php echo $this->form->getInput('id'); ?>
	<?php echo $this->form->getInput('asset_id'); ?>
	
	<div class="row-fluid">
		<!-- Begin Content -->
		<div class="span10 form-horizontal">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#general" data-toggle="tab"><?php echo JText::_('COM_YOORECIPE_YOORECIPE_DETAILS');?></a></li>
				<li><a href="#ingredients" data-toggle="tab"><?php echo JText::_('COM_YOORECIPE_YOORECIPE_INGREDIENTS');?></a></li>
				<li><a href="#publication" data-toggle="tab"><?php echo JText::_('COM_YOORECIPE_YOORECIPE_PUBLICATION');?></a></li>
				<li><a href="#nutritionfacts" data-toggle="tab"><?php echo JText::_('COM_YOORECIPE_YOORECIPE_NUTRITION_FACTS');?></a></li>
				<li><a href="#seo" data-toggle="tab"><?php echo JText::_('COM_YOORECIPE_YOORECIPE_SEO');?></a></li>
			</ul>

			<div class="tab-content">
				<!-- Begin Tabs -->
				<div class="tab-pane active" id="general">
					<div class="row-fluid">
						<div class="span6">
							
							<?php echo $this->form->renderField('title'); ?>
							
							<?php echo $this->form->renderField('alias'); ?>
							<?php echo $this->form->renderField('language'); ?>
							<?php echo $this->form->renderField('category_id'); ?>
							<?php echo $this->form->renderField('cuisine'); ?>
							<?php echo $this->form->renderField('spacer1'); ?>
							
							<div class="control-group">
								<div class="control-label"><?php echo $this->form->getLabel('description'); ?></div>
							</div>
							<div class="control-label"><?php echo $this->form->getInput('description'); ?></div>
							
							<?php echo $this->form->renderField('spacer2'); ?>
							
							<div class="control-group">
								<div class="control-label"><?php echo $this->form->getLabel('preparation'); ?></div>
							</div>
							<div class="control-label"><?php echo $this->form->getInput('preparation'); ?></div>

							<?php echo $this->form->renderField('spacer2'); ?>
							
							<div class="control-group">
								<div class="control-label"><?php echo $this->form->getLabel('notes'); ?></div>
							</div>
							<div class="control-label"><?php echo $this->form->getInput('notes'); ?></div>


						</div>
						<div class="span6" id="details">
							<?php echo $this->loadTemplate('details'); ?>
						</div>
					</div>
				</div>
				<!-- End tab general -->
				<div class="tab-pane" id="ingredients">
					<fieldset>
						<?php echo $this->loadTemplate('ingredients'); ?>
					</fieldset>
				</div>
				<div class="tab-pane" id="publication">
					<fieldset>
						<?php echo $this->loadTemplate('publication'); ?>
					</fieldset>
				</div>
				<div class="tab-pane" id="nutritionfacts">
					<fieldset>
						<?php echo $this->loadTemplate('nutritionfacts'); ?>
					</fieldset>
				</div>
				<div class="tab-pane" id="seo">
					<fieldset>
						<?php echo $this->loadTemplate('seo'); ?>
					</fieldset>
				</div>
				<!-- End Tabs -->
			</div>
			
			<input type="hidden" id="task" name="task" value="yoorecipe.edit" />
			<?php echo JHtml::_('form.token'); ?>
	
		</div>
		<!-- End Content -->
	</div>
</form>