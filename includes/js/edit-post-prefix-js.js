
//デフォルトの言語HRを追加する（post_title）
jQuery(function($){
	$("#titlewrap").before("<h5 class='postbox_hr post page' id='postbox_hr_title'>Information setting (default)</h5>")
});

//国旗を表示できるように
jQuery(document).ready(function(){
	var default_language = jQuery("#default_language").val();
	var default_language_flag = '&nbsp; <img src="' + default_language + '">';
	jQuery("#postbox_hr_title").append(default_language_flag);
});


