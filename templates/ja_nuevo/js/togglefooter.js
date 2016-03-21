jQuery(document).ready(function(){
    jQuery("#toggle-footer").click(function(){
        jQuery(".t3-footer > .container").slideToggle();
        if(jQuery('#footer_show_hide').hasClass('fa-caret-up')){
   			jQuery('#footer_show_hide').removeClass("fa-caret-up fa-lg");
   			jQuery('#footer_show_hide').addClass("fa-caret-down fa-lg"); 
		} 
		else{ 
   			jQuery('#footer_show_hide').removeClass("fa-caret-down fa-lg");
   			jQuery('#footer_show_hide').addClass("fa-caret-up fa-lg"); 
   		}
    });
});