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

JHtml::_('behavior.formvalidation');

$document 	= JFactory::getDocument();
$user 		= JFactory::getUser();
$lang 		= JFactory::getLanguage();

JHtml::_('bootstrap.framework');

// Component Parameters
$yooRecipeparams 			= JComponentHelper::getParams('com_yoorecipe');
$register_to_review			= $yooRecipeparams->get('register_to_review', 0);
$show_recaptch				= $yooRecipeparams->get('show_recaptch', 'std');
$publickey 					= $yooRecipeparams->get('recaptcha_public_key');
$show_email					= $yooRecipeparams->get('show_email', 1) && $user->guest;
$nb_reviews_to_fetch 		= $yooRecipeparams->get('nb_reviews_to_fetch', 9);
$can_review_own 			= $yooRecipeparams->get('can_review_own', 1);
$recaptcha_version 			= $yooRecipeparams->get('recaptcha_version', 'v1');

$recipe 	= $this->recipe;

if ($show_recaptch == 'recaptcha') {
	require_once JPATH_COMPONENT.'/lib/recaptchalib.php';
}
// Anti spam code generation
$int1 = rand(0, 5);
$int2 = rand(0, 4);

$document->addScript('media/com_yoorecipe/js/generic.js');
$document->addScript('media/com_yoorecipe/js/recipe-reviews.js');
// $document->addScript('https://www.google.com/recaptcha/api.js');

$can_report_comments = JHtml::_('yoorecipeutils.canreportReviews', $user);
$nb_reviews = count($recipe->ratings);

$html = array();
$html[] = '<a id="reviews" name="reviews"></a>';
if ($nb_reviews > 0) {

	$html[] = '<div class="review-section row-fluid">';
	$html[] = '<h3>'.JText::_('COM_YOORECIPE_REVIEWS').'</h3>';
	if ($nb_reviews_to_fetch < $nb_reviews) {
		$html[] = '<div class="fixedVersion">';
		$html[] = '<div class="modal fade" id="reviewsModal" aria-hidden="true" style="display: none;">';
		$html[] = '<div class="modal-dialog">';
		$html[] = '<div class="modal-content">';
		$html[] = '<div class="modal-header">';
		$html[] = '<button type="button" class="close" data-dismiss="modal">&times;</button>';
		$html[] = '<h3>'.JText::_('COM_YOORECIPE_ALL_REVIEWS').'</h3>';
		$html[] = '</div>';
		$html[] = '<div class="modal-body">';
		$html[] = '<div class="row-fluid">';
		$html[] = JHtml::_('yoorecipeutils.generateReviews', $recipe->ratings, $can_report_comments, $nb_reviews);
		$html[] = '</div>';
		$html[] = '</div>';
		$html[] = '</div>';
		$html[] = '</div>';
		$html[] = '</div>';
		$html[] = '</div>';
	}
	$html[] = '<div class="row-fluid">';
	$html[] = JHtml::_('yoorecipeutils.generateReviews', $recipe->ratings, $can_report_comments, $nb_reviews_to_fetch);
	$html[] = '</div>';
	if ($nb_reviews_to_fetch < $nb_reviews) {
		$html[] = '<a href="#reviewsModal" data-toggle="modal" class="btn">'.JText::_('COM_YOORECIPE_MORE_REVIEWS').'</a>';
	}
	$html[] = '</div>';
	
}
echo implode("\n", $html);
?>

<input type="hidden" id="int1" value="<?php echo $int1; ?>"/>
<input type="hidden" id="int2" value="<?php echo $int2; ?>"/>
<input type="hidden" id="language_tag" value="<?php echo $lang->getTag(); ?>"/>
<input type="hidden" id="juri_base" value="<?php echo JURI::base( true ); ?>"/>

<div id="commentModal" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="myModalLabel"><?php echo JText::_('COM_YOORECIPE_EDIT_REVIEW'); ?></h3>
			</div>
			<div class="modal-body">
				<form>
					<input type="hidden" id="edit_rating_id" value=""/>
					<textarea id="edit_rating_text" rows="3"></textarea>
				</form>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal"><?php echo JText::_('COM_YOORECIPE_CLOSE'); ?></button>
				<button class="btn btn-primary" onclick="updateReview();return false;"><?php echo JText::_('COM_YOORECIPE_SAVE_CHANGES'); ?></button>
			</div>
		</div>
	</div>
</div>

<div class="row-fluid">
	<h3><?php echo ($nb_reviews == 0) ? JText::_('COM_YOORECIPE_POST_FIRST_REVIEW') : JText::_('COM_YOORECIPE_ADD_REVIEW'); ?></h3>
	
<?php
	if ($register_to_review && $user->guest) {
	
		$returnUrl		= base64_encode(JRoute::_(JHtml::_('YooRecipeHelperRoute.getreciperoute', $recipe->slug, $recipe->catslug) , false).'#reviews'); 
		$redirectUrl 	= JRoute::_('index.php?option=com_users&view=login&return='.$returnUrl);
?>
		<a href="<?php echo $redirectUrl; ?>" rel="nofollow"><?php echo JText::_('COM_YOORECIPE_REGISTER_TO_REVIEW'); ?></a>
<?php	
	}
	else if ($this->hide_comment_form) {
		echo '<span class="alert alert-info">'.JText::_('COM_YOORECIPE_YOU_ALREADY_REVIEWED_THIS_RECIPE').'</span>';
	}
	else if (!$can_review_own && $this->is_viewing_own_recipe) {
		echo '<span class="alert alert-info">'.JText::_('COM_YOORECIPE_YOU_CANNOT_REVIEW_YOUR_OWN_RECIPE').'</span>';
	}
	else if ($show_recaptch == 'recaptcha' && $publickey == '') {
		echo recaptcha_get_html($publickey, $recaptcha_version);
	}
	else {
?>
	<div class="add-comment-container">
		<form action="<?php echo 'index.php?option=com_yoorecipe&task=addRecipeReview'; ?>" method="post" id="yoorecipe-rating-form" class="form-validate form-horizontal">
			<div>
				<input type="hidden" name="recipeId" class="required" id="yoorecipe-rating-form-recipe-id" value="<?php echo $recipe->id; ?>"/>
				<input type="hidden" name="userId" id="yoorecipe-rating-form-user-id" value="<?php if (!$user->guest) { echo $user->id; } ?>"/>
				<input type="hidden" name="currentUrl" value="<?php echo JRoute::_(JHtml::_('YooRecipeHelperRoute.getreciperoute', $recipe->slug, $recipe->catslug).'#reviews' , false); ?>"/>
				<input type="hidden" value="<?php echo JText::_('COM_YOORECIPE_REVIEW_ERROR'); ?>"/>
				<input type="hidden" value="<?php echo JText::_('COM_YOORECIPE_NAME_ERROR'); ?>"/>
				<input type="hidden" value="<?php echo JText::_('COM_YOORECIPE_EMAIL_ERROR'); ?>"/>
				<input type="hidden" value="<?php echo JText::_('COM_YOORECIPE_ENIGMA_ERROR'); ?>"/>
			</div>
			
			<div class="control-group">
				<img id="star-icon-1" src="media/com_yoorecipe/images/star-icon.png" onmouseover="setRatingValue(1);" onclick="setRatingValue(1);" alt=""/>
				<img id="star-icon-2" src="media/com_yoorecipe/images/star-icon.png" onmouseover="setRatingValue(2);" onclick="setRatingValue(2);" alt=""/>
				<img id="star-icon-3" src="media/com_yoorecipe/images/star-icon.png" onmouseover="setRatingValue(3);" onclick="setRatingValue(3);" alt=""/>
				<img id="star-icon-4" src="media/com_yoorecipe/images/star-icon.png" onmouseover="setRatingValue(4);" onclick="setRatingValue(4);" alt=""/>
				<img id="star-icon-5" src="media/com_yoorecipe/images/star-icon.png" onmouseover="setRatingValue(5);" onclick="setRatingValue(5);" alt=""/>
				(<span id="span-rating">5</span>/<span>5</span>)
			</div>

			<input type="hidden" id="rating" name="rating" value="5"/>
			
			<div class="control-group">
				<textarea onfocus=""
					name="comment" 
					id="yoorecipe-rating-form-comment" 
					rows="2" cols="20"
					placeholder="<?php echo JText::_('COM_YOORECIPE_ADD_REVIEW'); ?>"
					title="<?php echo JText::_('COM_YOORECIPE_ADD_REVIEW'); ?>" 
					class="required"
					style="resize:none;width:100%"
				></textarea>
			</div>
<?php
		if ($user->guest) {
		
			echo '<div class="control-group">';
			echo '<label class="control-label" for="yoorecipe-rating-form-author">'.JText::_('COM_YOORECIPE_NAME').'</label>';
			echo '<div class="controls">';
			echo '<input type="text" name="author" id="yoorecipe-rating-form-author" class="required" placeholder="'.JText::_('COM_YOORECIPE_NAME').'"/>';
			echo '</div>';
			echo '</div>';
		} 
		else {
			echo '<input type="hidden" name="author" id="yoorecipe-rating-form-author" class="required" value="'.$user->name.'"/>';
		}
			
		if ($show_email) {
			echo '<div class="control-group">';
			echo '<label class="control-label" for="yoorecipe-rating-form-email">'.JText::_('COM_YOORECIPE_EMAIL').'</label>';
			echo '<div class="controls">';
			echo '<input type="text" name="email" id="yoorecipe-rating-form-email" class="required validate-email" placeholder="'.JText::_('COM_YOORECIPE_EMAIL').'"/>';
			echo '</div>';
			echo '<div><small>'.JText::_('COM_YOORECIPE_EMAIL_NOT_USED').'</small></div>';
			echo '</div>';
			echo '<br/>';
		}
		else if (!$user->guest) {
			echo '<input type="hidden" name="email" id="yoorecipe-rating-form-email" class="hide required validate-email" value="'.$user->email.'"/>';
		}
		
		if ($show_recaptch == 'std') {
			
			echo '<div class="control-group">';
			echo '<label class="control-label" for="yoorecipe-rating-form-enigma">';
			echo '<span>'.JText::_('COM_YOORECIPE_HOW_MANY_ARE').' '.$int1.' + '.$int2.'? '.'</span>';
			echo '</label>';
			echo '<div class="controls">';
			echo '<input type="text" name="enigma" id="yoorecipe-rating-form-enigma" class="required validate-simple-captcha" />';
			echo '</div>';
			echo '</div>';
			
		}
		else if ($show_recaptch == 'recaptcha') {
			echo recaptcha_get_html($publickey, $recaptcha_version);			
		}
?>
			<input type="button" class="btn pull-right" value="<?php echo JText::_('JSUBMIT'); ?>" onclick="addReviewFormValidator.validate();"/>
		</form>
	</div>
<?php	} ?>
</div>

<div class="yoorecipe-after-reviews">
<?php
	$modules = JModuleHelper::getModules('yoorecipe-after-reviews');
	foreach($modules as $module) {
		echo JModuleHelper::renderModule($module);
	}
?>
</div>