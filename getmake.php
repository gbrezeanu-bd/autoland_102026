<?php
require_once('./includes/config.php');

$colname_Recordset1 = "-1";

if (isset($_GET['q'])) {
    $colname_Recordset1 = (get_magic_quotes_gpc()) ? $_GET['q'] : addslashes($_GET['q']);
}


$query_Recordset1 = sprintf("SELECT * FROM `search` WHERE categorie = '%s' ORDER BY marca ASC", $colname_Recordset1);
$Recordset1 = $connection->query($query_Recordset1) or die($connection->error);
$row_Recordset1 = mysqli_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysqli_num_rows($Recordset1);
?>
<select name="marcat" class="css-dropdowns" tabindex="1">
    <?php
    $_GET['marcat'] = "";
    do {
        ?>
        <option value="<?php echo $row_Recordset1['id'] ?>"<?php if (!(strcmp($row_Recordset1['id'], $_GET['marcat']))) {
        echo "selected=\"selected\"";
    } ?>><?php echo $row_Recordset1['marca'] ?></option>
        <?php
    } while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1));
    $rows = mysqli_num_rows($Recordset1);
    if ($rows > 0) {
        mysqli_data_seek($Recordset1, 0);
        $row_Recordset1 = mysqli_fetch_assoc($Recordset1);
    }
    ?>
</select>

<?php
// we must close the connection
require_once './includes/application_bottom.php';
?>
