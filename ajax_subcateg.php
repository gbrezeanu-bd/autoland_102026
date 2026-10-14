<?php

require_once('./includes/config.php');

$colname_Recordset1 = "-1";
if (isset($_GET['q'])) {
  $colname_Recordset1 = $connection->real_escape_string($_GET['q']);
}

$query_Recordset1 = sprintf("SELECT * FROM `search_subcateg` WHERE categorie = '%s' ORDER BY subcateg ASC", $colname_Recordset1);
$Recordset1 = $connection->query($query_Recordset1) or db_fail($connection);
$row_Recordset1 = mysqli_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysqli_num_rows($Recordset1);

?>
<select name="subcateg" class="css-dropdowns" tabindex="1">
  <?php
$_REQUEST['subcateg']="";
do {
?>
  <option value="<?php echo $row_Recordset1['id']?>"<?php if (!(strcmp($row_Recordset1['id'], $_REQUEST['subcateg']))) {echo "selected=\"selected\"";} ?>><?php echo $row_Recordset1['subcateg']?></option>
  <?php
} while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1));
  $rows = mysqli_num_rows($Recordset1);
  if($rows > 0) {
      mysqli_data_seek($Recordset1, 0);
	  $row_Recordset1 = mysqli_fetch_assoc($Recordset1);
  }

  // we must close the connection
  include_once './includes/application_bottom.php';
?>
</select>