<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
<head>
<title>TinyMCE Test</title>
<script type="text/javascript" src="../tinymce/jscripts/tiny_mce/tiny_mce_gzip.js"></script>
<script type="text/javascript">
tinyMCE_GZ.init({
	plugins : 'style,layer,table,save,advhr,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras',
	themes : 'simple,advanced',
	languages : 'en',
	disk_cache : true,
	debug : false
});
</script>
<script language="javascript" type="text/javascript">
tinyMCE.init({
	mode : "textareas"
});
</script>
</head>
<body>
<!-- form sends content to moxiecode's demo page -->
<form method="post" action="https://tinymce.moxiecode.com/dump.php?example=true">
	<textarea name="content" cols="100" rows="15">This is some content that will be editable with TinyMCE.</textarea>
	<input type="submit" value="Save" />
</form>
</body>
</html>