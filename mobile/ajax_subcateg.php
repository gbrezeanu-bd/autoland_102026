<?php require_once('Connections/autoland.php'); 
$colname_Recordset1 = "-1";
if (isset($_GET['q'])) {
  $colname_Recordset1 = (get_magic_quotes_gpc()) ? $_GET['q'] : addslashes($_GET['q']);
}                                                 
$query_Recordset1 = sprintf("SELECT * FROM `search_subcateg` WHERE categorie = '%s' ORDER BY subcateg ASC", $colname_Recordset1);
$Recordset1 = mysql_query($query_Recordset1, $autoland) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
</head>

<body>
<select name="subcateg" class="vanzari_box">
  <?php
$_REQUEST['subcateg']="";
do {  
?>
  <option value="<?php echo $row_Recordset1['id']?>"<?php if (!(strcmp($row_Recordset1['id'], $_REQUEST['subcateg']))) {echo "selected=\"selected\"";} ?>><?php echo $row_Recordset1['subcateg']?></option>
  <?php
} while ($row_Recordset1 = mysql_fetch_assoc($Recordset1));
  $rows = mysql_num_rows($Recordset1);
  if($rows > 0) {
      mysql_data_seek($Recordset1, 0);
	  $row_Recordset1 = mysql_fetch_assoc($Recordset1);
  }
?>
</select>

</body>
</html>
<?php
mysql_free_result($Recordset1);
?>
