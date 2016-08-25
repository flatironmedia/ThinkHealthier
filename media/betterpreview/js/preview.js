/**
 * @package         Better Preview
 * @version         4.1.3
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

(function($) {
	"use strict";

	$(document).ready(function() {
		$('div.betterpreview_message, div.betterpreview_error').click(function(e) {
			$(this).fadeOut();
			e.stopPropagation();
		});
		$('html').click(function() {
			$('div.betterpreview_message, div.betterpreview_error').fadeOut();
		});
	});
})(jQuery);
