<?php
class CTinyMCETextarea extends CTextarea 
{
	public $script_path = TINYMCE_SCRIPT_PATH;
	
	protected $template = "
	<script language=\"javascript\" type=\"text/javascript\" src=\"{script_path}\"></script>
<script language=\"javascript\" type=\"text/javascript\">
	tinyMCE.init({
		mode : \"exact\",
		elements : \"{id}\",
		theme : \"advanced\",
		plugins : \"style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template\",
		theme_advanced_buttons1_add_before : \"save,newdocument,separator\",
		theme_advanced_buttons1_add : \"fontselect,fontsizeselect\",
		theme_advanced_buttons2_add : \"separator,insertdate,inserttime,preview,separator,forecolor,backcolor\",
		theme_advanced_buttons2_add_before: \"cut,copy,paste,pastetext,pasteword,separator,search,replace,separator\",
		theme_advanced_buttons3_add_before : \"tablecontrols,separator\",
		theme_advanced_buttons3_add : \"emotions,iespell,media,advhr,separator,print,separator,ltr,rtl,separator,fullscreen\",
		theme_advanced_buttons4 : \"insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,|,code\",
		theme_advanced_toolbar_location : \"top\",
		theme_advanced_toolbar_align : \"left\",
		theme_advanced_path_location : \"bottom\",
	    plugin_insertdate_dateFormat : \"%Y-%m-%d\",
	    plugin_insertdate_timeFormat : \"%H:%M:%S\",
		extended_valid_elements : \"hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]\",
		external_link_list_url : \"example_link_list.js\",
		external_image_list_url : \"example_image_list.js\",
		flash_external_list_url : \"example_flash_list.js\",
		media_external_list_url : \"example_media_list.js\",
		template_external_list_url : \"example_template_list.js\",
		theme_advanced_resize_horizontal : false,
		theme_advanced_resizing : true,
		nonbreaking_force_tab : true,
		apply_source_formatting : true,
		relat2ive_urls : false,
		rem2ove_script_host : false
	});
</script>
	<textarea id=\"{id}\" rows=\"{rows}\" cols=\"{cols}\" name=\"{name}\" class=\"{class}\" {style} {htmlEvents}>{innerHTML}</textarea>";
	
	
	public function CTinyMCETextarea()
	{
		$this->CTextarea();
	}
}
?>