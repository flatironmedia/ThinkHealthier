jQuery( document ).ready(function() {

	editComment = function(rating_id) {
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?option=com_yoorecipe&task=getReview&format=raw&rating_id='+rating_id,
		}).done(function(json) {
			jQuery('#edit_rating_id').val(json.id);
				jQuery('#edit_rating_text').val(json.comment);
				jQuery('#commentModal').modal('show');
		}).error(function(json) {
		});
	}
	
	updateReview = function() {
		var rating_id = jQuery('#edit_rating_id').val();
		
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?option=com_yoorecipe&task=updateReview&format=raw',
			data: {
				'edit_rating_id':rating_id,
				'comment' : jQuery('#edit_rating_text').val()
			}
		}).done(function(json) {
			if (json.status) {
				jQuery('#comment_'+rating_id).html(jQuery('#edit_rating_text').val());
			} else {
				alert(Joomla.JText._('COM_YOORECIPE_ERROR_OCCURED', true));
			}
			if (json.published == 0) {
				jQuery('#yoorecipe_comment_'+rating_id).addClass('greyedout');
			}
			jQuery('#commentModal').modal('hide');
		}).error(function(json) {
		});
	}
	
	setRatingValue = function(ratingValue) {
		for (i = 1 ; i <= 5; i++) {
			if (i <= ratingValue) {
				jQuery('#star-icon-'+i).attr('src', jQuery('#juri_base').val() +'/media/com_yoorecipe/images/star-icon.png');
			}
			else {
				jQuery('#star-icon-'+i).attr('src', jQuery('#juri_base').val() +'/media/com_yoorecipe/images/star-icon-empty.png');
			}
		}
		jQuery('#span-rating').html(ratingValue);
		jQuery('#rating').val(ratingValue);
	}
	
	addRecipeReview = function() {
	
		showLoading();

		var recipeId = jQuery('#yoorecipe-rating-form-recipe-id').val();
		var userId = jQuery('#yoorecipe-rating-form-user-id').val();
		var rating = jQuery('#rating').val();
		var comment = jQuery('#yoorecipe-rating-form-comment').val();
		var author = jQuery('#yoorecipe-rating-form-author').val();
		var recaptcha_response = jQuery('#g-recaptcha-response').val();
		var email;
		if (jQuery('#yoorecipe-rating-form-email') != undefined) {
			email = jQuery('#yoorecipe-rating-form-email').val();
		}
		var recaptcha_challenge_field;
		if (jQuery('#recaptcha_challenge_field') != undefined) {
			recaptcha_challenge_field = jQuery('#recaptcha_challenge_field').val();
		}
		var recaptcha_response_field;
		if (jQuery('#recaptcha_response_field') != undefined) {
			recaptcha_response_field = jQuery('#recaptcha_response_field').val();
		}
		
		var x = new Request({
			url: 'index.php?option=com_yoorecipe&task=addRecipeReview&format=raw', 
			method: 'post',
			data: {
				'recipeId':recipeId,
				'userId':userId,
				'rating':rating,
				'comment':comment,
				'author':author,
				'email':email,
				'recaptcha_challenge_field':recaptcha_challenge_field,
				'recaptcha_response_field':recaptcha_response_field,
				'g-recaptcha-response':recaptcha_response
			},
			onSuccess: function(response){
				json = JSON.decode(response);
				if (json.status) {
					jQuery('#yoorecipe-rating-form-comment').val('');
					window.location.reload();
				}
				else {
					hideLoading();
					alert(Joomla.JText._('COM_YOORECIPE_REVIEW_NOT_ADDED', true));
				}
			},
			onFailure: function(response){
				hideLoading();
				alert(Joomla.JText._('COM_YOORECIPE_ERROR_OCCURED', true));
			}                
		}).send();
	}
	
	seeFullReview = function(review_id) {
		jQuery.noConflict();
		jQuery('span.comment_'+review_id).fadeOut();
		jQuery('span.full_review_'+review_id).fadeIn();
	}
	
	if ($('yoorecipe-rating-form')) {
		Locale.use(jQuery('#language_tag').val());
		addReviewFormValidator = new Form.Validator.Inline('yoorecipe-rating-form', {
			stopOnFailure: true,
			useTitles: false,
			errorPrefix: '',
			ignoreHidden:false,
			onFormValidate: function(passed, form, event) {
				if (passed) {
					addRecipeReview();
				}
			}
		});
	
		addReviewFormValidator.add('validate-simple-captcha', {
			test: function(field){
				return (field.value == parseInt(jQuery('#int1').val())+parseInt(jQuery('#int2').val()));
			}
		});
	}
});