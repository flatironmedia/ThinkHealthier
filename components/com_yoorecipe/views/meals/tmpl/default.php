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

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import library for calendar
jimport('joomla.html.html');

JHtml::_('bootstrap.framework');
JHtml::_('jquery.ui');

$document = JFactory::getDocument();
$document->addStyleSheet('media/com_yoorecipe/styles/yoorecipe.css');

$document->addScript('media/com_yoorecipe/js/jquery-ui.custom-draggable.min.js');
$document->addScript('media/com_yoorecipe/js/jquery.ui.touch-punch.min.js');
$document->addScript('media/com_yoorecipe/js/generic.js');
$document->addScript('media/com_yoorecipe/js/meals.js');

$top_modules 	= JModuleHelper::getModules('yoorecipe-mealplanner-top');
$bottom_modules = JModuleHelper::getModules('yoorecipe-mealplanner-bottom');

// Component Parameters
$yooRecipeparams 	= JComponentHelper::getParams('com_yoorecipe');

$start_date_obj = $this->start_date_obj;
$end_date_obj 	= $this->end_date_obj;

$start_date = $start_date_obj->format('Y-m-d');
$end_date 	= $end_date_obj->format('Y-m-d');
?>
<div class="huge-ajax-loading"></div>
<?php
	if (count($top_modules) > 0) {
		echo '<div>';
		foreach($top_modules as $module) {
			echo JModuleHelper::renderModule($module);
		}
		echo '</div>';
	}
?>
<div class="row-fluid">
	<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm" class="form-inline" onsubmit="showLoading();">
		<div class="row-fluid meals-header">
			<a href="#" class="span1" onclick="$('task').value='previous_week';submitForm();"><img src="media/com_yoorecipe/images/prev.png" alt="previous" name="previous"/></a>
			<center>
				<h1 class="span10">
				<?php echo JText::sprintf('COM_YOORECIPE_MY_MEALPLANNER_FROM_TO', $start_date_obj->format('d F'), $end_date_obj->format('d F Y')).'&nbsp;';?>
				<button onclick="printMeals();" type="button" class="btn visible-desktop"><i class="icon-print"></i></button>
				</h1>
			</center>
			<a href="#" class="span1" onclick="$('task').value='next_week';submitForm();"><img src="media/com_yoorecipe/images/next.png" alt="next" name="next"/></a>
		</div>
		<input type="hidden" id="task" name="task" value=""/>
		<input type="hidden" name="startdate" id="startdate" value="<?php echo $start_date; ?>"/> 
		<input type="hidden" name="enddate" id="enddate" value="<?php echo $end_date; ?>"/> 
	</form>
	<div id="mealplanner-container">
	<?php
		$i=0;
		foreach ($this->days_of_week as $date => $day_of_week) {
			
			$style = 'min-height:400px;';
			($i == 0)? $style .='margin-left:50px;' : '';
			
			echo '<div class="droppable mp-column" style="'.$style.'" data-date="'.$date.'">';
			echo '<center><h5>',JText::_($day_of_week->label),'</h5></center>';
			echo '<div id="meal_ctnr_'.$date.'">';
			echo '<hr/>';
			foreach ($day_of_week->meals as $meal) {
				echo JHtml::_('mealsutils.generateMealEntryHTML', $meal->meal_id, $meal->recipe_id, $meal->title, $meal->alias, $meal->servings_type_code, $meal->nb_servings, $date, $meal->picture);
			}
			echo '</div>';
			echo '</div>';
			
			$i++;
		}
	?>
	</div>
</div>

<hr/>

<div class="row-fluid">
<?php
	if (count($this->queued_recipes) == 0) {
		echo '<div class="alert alert-warning">',JText::_('COM_YOORECIPE_MEALPLANNER_EMPTY_QUEUE'),'</div>';
	} else {
	
		echo '<p class="lead">',JText::_('COM_YOORECIPE_MEALPLANNER_YOUR_QUEUED_RECIPES'),'</p>';
		echo '<input type="text" id="search_word" class="input-medium search-query"/>';
		echo '<button class="btn" onclick="searchQueuedRecipes();">'.JText::_('JSEARCH_FILTER_SUBMIT').'</button>';
		echo '<div id="queue-container">';
		echo JHtml::_('mealsutils.generateRecipeQueueItems', $this->queued_recipes);
		echo '</div>';
	}
?>
</div>

<?php
	if (count($bottom_modules) > 0) {
		echo '<div class="row-fluid">';
		foreach($bottom_modules as $module) {
			echo JModuleHelper::renderModule($module);
		}
		echo '</div>';
	}
?>
<div class="row-fluid">
	<div class="alert alert-success">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<?php echo JText::_('COM_YOORECIPE_MEALPLANNER_INFO'); ?>
	</div>
</div>