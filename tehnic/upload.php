<html>

<head><title>File Upload</title></head>
<table border="1" cellpadding="5" width="100%" align="center">
<tr><td colspan="2" bgcolor="#0066cc"><font color="#FFFFCC" size="+1" align="center">Files Uploaded</font></td></tr>
<tr  bgcolor="#ffff00"><td><nobr>File Name</nobr></td>
	<td align="right"><nobr>File size</nobr></td></tr>
<?php

/*
 * SET THE SAVE PATH by editing the line below. Make sure that the path
 * name ends with the correct file system path separator ('/' in linux and
 * '\\' in windows servers (eg "c:\\temp\\uploads\\" )
 */

$save_path="../foto/";    


$file = $_FILES['userfile'];
$k = count($file['name']);


for($i=0 ; $i < $k ; $i++)
{
	if($i %2)
	{
		echo '<tr bgcolor="#FFFF99"> ';
	}
	else
	{	
		echo '<tr>';
	}
	
	echo '<td align="left">' . $file['name'][$i] ."</td>\n";
	echo '<td align="right">' . $file['size'][$i] ."</td></tr>\n";

	if(isset($save_path) && $save_path!="")
	{
		$name = split('/',$file['name'][$i]);
		$filename = split('\.',$name[count($name)-1]);

		$ftext = join('.',array_slice($filename, 0, count($filename)-1)).'-'.time().'.'.$filename[count($filename)-1];		

		move_uploaded_file($file['tmp_name'][$i], $save_path . $ftext);
	}
	
}

echo "<tr style='color: #0066cc'><td>SSL</td><td>". (($_SERVER[HTTPS] != 'on') ? 'Off' : 'On') ."</td></tr>";
if(! isset($save_path) || $save_path =="")
{
	echo '<tr style="color: #0066cc"><td colspan=2 align="left">Files have not been saved, please edit upload.php to match your configuration</td></tr>';
}



?>	
	
</table>
</html>