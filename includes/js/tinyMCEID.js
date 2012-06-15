// <![CDATA[
//アップロード/挿入
jQuery(document).ready(function() {
	jQuery('#cftloading_img').ajaxStart(function() { jQuery(this).show();});
	jQuery('#cftloading_img').ajaxStop(function() { jQuery(this).hide();});
});
var tinyMCEID = new Array();
// ]]>

/*jQuery(function(){
	jQuery('h5.postbox_hr').each(function(){
        var postboxh5_num = jQuery('h5.postbox_hr').index(this);
        //$(this).addClass('postbox_hr');
        jQuery(this).attr('id', 'postbox_hr' + postboxh5_num);
    }); 
});*/

// <![CDATA[
//jQuery(".wp-editor-container").width.(100%);
function cft_use_this(file_id) {
	var win = window.dialogArguments || opener || parent || top;
	win.jQuery("#"+win.jQuery("#cft_clicked_id").val()+"_hide").val(file_id);
	var fields = win.jQuery("#cft :input").fieldSerialize();

}

function qt_set(new_id) { 
	eval("qt_"+new_id+" = new QTags('qt_"+new_id+"', '"+new_id+"', 'editorcontainer_"+new_id+"', 'more');");		
}

function _edInsertContent(myField, myValue) {
	var sel, startPos, endPos, scrollTop;

	//IE support
	if (document.selection) {
		myField.focus();
		sel = document.selection.createRange();
		sel.text = myValue;
		myField.focus();
	}
	//MOZILLA/NETSCAPE support
	else if (myField.selectionStart || myField.selectionStart == "0") {
		startPos = myField.selectionStart;
		endPos = myField.selectionEnd;
		scrollTop = myField.scrollTop;
		myField.value = myField.value.substring(0, startPos)
		              + myValue
                      + myField.value.substring(endPos, myField.value.length);
		myField.focus();
		myField.selectionStart = startPos + myValue.length;
		myField.selectionEnd = startPos + myValue.length;
		myField.scrollTop = scrollTop;
	} else {
		myField.value += myValue;
		myField.focus();
	}
}
function send_to_custom_field(h) {
	if ( tmpFocus ) ed = tmpFocus;
	else if ( typeof tinyMCE == "undefined" ) ed = document.getElementById("content");
	else { ed = tinyMCE.get("content"); if(ed) {if(!ed.isHidden()) isTinyMCE = true;}}
	if ( typeof tinyMCE != "undefined" && isTinyMCE && !ed.isHidden() ) {
		ed.focus();
		if ( tinymce.isIE && ed.windowManager.insertimagebookmark )
			ed.selection.moveToBookmark(ed.windowManager.insertimagebookmark);
		if ( h.indexOf("[caption") === 0 ) {
			if ( ed.plugins.wpeditimage )
				h = ed.plugins.wpeditimage._do_shcode(h);
		} else if ( h.indexOf("[gallery") === 0 ) {
			if ( ed.plugins.wpgallery )
				h = ed.plugins.wpgallery._do_gallery(h);
		} else if ( h.indexOf("[embed") === 0 ) {
			if ( ed.plugins.wordpress )
				h = ed.plugins.wordpress._setEmbed(h);
		}
		ed.execCommand("mceInsertContent", false, h);
	} else {
		if ( tmpFocus ) _edInsertContent(tmpFocus, h);
		else edInsertContent(edCanvas, h);
	}
	tb_remove();
	tmpFocus = undefined;
	isTinyMCE = false;
}
jQuery(".thickbox").bind("click", function (e) {
	tmpFocus = undefined;
	isTinyMCE = false;
});

var isTinyMCE;
var tmpFocus;
function focusTextArea(id) {
	jQuery(document).ready(function() {
		if ( typeof tinyMCE != "undefined" ) {
			var elm = tinyMCE.get(id);
		}
		if ( ! elm || elm.isHidden() ) {
			elm = document.getElementById(id);
			isTinyMCE = false;
		}else isTinyMCE = true;
		tmpFocus = elm
		elm.focus();
		if (elm.createTextRange) {
			var range = elm.createTextRange();
			range.move("character", elm.value.length);
			range.select();
		} else if (elm.setSelectionRange) {
			elm.setSelectionRange(elm.value.length, elm.value.length);
		}
	});
}

function switchMode(id,type) {
	var ed = tinyMCE.get(id);
	if ( ! ed || ed.isHidden() && type == 'visual') {
		document.getElementById(id).value = switchEditors.wpautop(document.getElementById(id).value);
		if ( ed ) { jQuery('#qt_'+id+'_toolbar').hide(); ed.show(); }
		else {var ed = new tinyMCE.Editor(id, tinyMCEPreInit.mceInit['content']); ed.render();}
		//ビジュアルモード状態
		jQuery('#'+id+'_div_visual').removeClass('html-active');
		jQuery('#'+id+'_div_visual').addClass('tmce-active');
	} else {
		if (  type == 'html' ){
			ed.hide(); jQuery('#qt_'+id+'_toolbar').show(); document.getElementById(id).style.color="#000000";
			document.getElementById(id).style.width="100%";
			//HTMLモード状態
			jQuery('#'+id+'_div_visual').addClass('html-active');
			jQuery('#'+id+'_div_visual').removeClass('tmce-active');
		}
	}
}

function thickbox(link) {
	var t = link.title || link.name || null;
	var a = link.href || link.alt;
	var g = link.rel || false;
	tb_show(t,a,g);
	link.blur();
	return false;
}
//-->

