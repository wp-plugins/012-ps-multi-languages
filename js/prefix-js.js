jQuery(document).ready(function(){
	var default_language = jQuery("#default_language").val();
	var default_language_flag = '&nbsp; <img src="' + default_language + '">';
	jQuery("label[for='tag-name']").append(default_language_flag);
	jQuery("label[for='name']").append(default_language_flag);
	jQuery("th#name a span:first-child").append(default_language_flag);
});
