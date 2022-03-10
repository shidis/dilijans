var TM={};

TM.global={
    language : 'ru',
    images_upload_url: '/cms/inc/tinymce/postAcceptor.php',
    relative_urls : true,
	content_css : "/app/css/custom.css",
	skin : "oxide",
	skin_variant : "silver",
    file_picker_callback : "tinyBrowser",
	width:'100%',
	script_url : '/cms/inc/tinymce/tiny_mce.js'

}
	
TM.cfg1=$.extend(TM.global,{
		mode:'exact',
		theme: 'silver',
		plugins : "advlist, anchor, autolink, autoresize, autosave, charmap, code, codesample, colorpicker, contextmenu, directionality, emoticons, fullpage, fullscreen, help, hr, image, imagetools, importcss, insertdatetime, legacyoutput, link, lists, media, nonbreaking, noneditable, pagebreak, paste, preview, print, quickbars, save, searchreplace, spellchecker, tabfocus, table, template, textcolor, textpattern, toc, visualblocks, visualchars, wordcount",

		// Theme options
		theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,fullscreen",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,pagebreak",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true

});

$().ready(function(){
	
	$('.TM_sw').html('переключить');
	
	$('textarea.TM').each(function(){
		var n=$(this).attr('name');
		var n1=n.replace(/\[([^\]]+)\]/,'[tmh_$1]');
		if(n1==n) n1='tmh_'+n;
		$(this).before('<textarea name="'+n1+'" class="hide"></textarea>');
	});

	$('textarea.TM').blur(function() {
		var n=$(this).attr('name');
		if(!$(this).hasClass('hhh')){
			var n1=n.replace(/\[([^\]]+)\]/,'[tmh_$1]');
			if(n1==n) n1='tmh_'+n;
			$('[name="'+n1+'"]').val($(this).val());
		}
	});	
	
	$('.TM_sw').click(function(e){
		e.preventDefault();
		var n=$(e.target).attr('forel');
		if($(this).hasClass('hhh')){
			$('[name="'+n+'"]').tinymce().show();
			//tinyMCE.execCommand('mceRemoveControl', false, id);
			//tinyMCE.execCommand('mceFocus', false, id);
			$(this).removeClass('hhh');
			var n1=n.replace(/\[([^\]]+)\]/,'[tmh_$1]');
			if(n1==n) n1='tmh_'+n;
			$('[name="'+n1+'"]').val('');
		}else{
			$('[name="'+n+'"]').tinymce().hide();
			//tinyMCE.execCommand('mceAddControl', false, id);
			//tinyMCE.execCommand('mceFocus', false, id);
			$(this).addClass('hhh');
			$('[name="'+n+'"]').focus();
		}
	});
	
});


// не используется
TM.init=function(fn){
	
	
	var js = document.createElement('script'); 
	js.type = 'text/javascript';
	js.async = false;
    js.src = '/cms/inc/tiny_mce/tiny_mce.js';
    var s = document.getElementsByTagName('head')[0]; s.appendChild(js);

	var js = document.createElement('script'); 
	js.type = 'text/javascript';
	js.async = false;
    js.src = '/cms/inc/tiny_mce/jquery.tinymce.js';
    var s = document.getElementsByTagName('head')[0]; s.appendChild(js);
	

	var js = document.createElement('script'); 
	js.type = 'text/javascript';
	js.async = false;
    js.src = '/cms/inc/tinybrowser/tb_tinymce.js.php';
    var s = document.getElementsByTagName('head')[0]; s.appendChild(js);
	
	
}

