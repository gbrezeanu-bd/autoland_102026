<?php require_once('../includes/config.php'); ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "../index.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "../tehnic/login.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
  $MM_referrer .= "?" . $QUERY_STRING;
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php require_once('../includes/config.php'); ?>
<?php
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE rent SET cat=%s, marca=%s, tip=%s, combust=%s, detalii=%s, pret=%s, pret1=%s, pret2=%s, pret3=%s, pret4=%s, garantie=%s WHERE id=%s",
                       GetSQLValueString($_POST['cat'], "text"),
                       GetSQLValueString($_POST['marca'], "text"),
                       GetSQLValueString($_POST['tip'], "text"),
                       GetSQLValueString($_POST['combust'], "text"),
                       GetSQLValueString($_POST['detalii'], "text"),
                       GetSQLValueString($_POST['pret'], "int"),
                       GetSQLValueString($_POST['pret1'], "int"),
                       GetSQLValueString($_POST['pret2'], "int"),
                       GetSQLValueString($_POST['pret3'], "int"),
                       GetSQLValueString($_POST['pret4'], "int"),
                       GetSQLValueString($_POST['garantie'], "text"),
                       GetSQLValueString($_POST['id'], "int"));
                                                      
  $Result1 = $connection->query($updateSQL) or die($connection->error);

  $updateGoTo = "view_rent.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_GET['delfotoid'])) && ($_GET['delfotoid'] != "")) {
  $deleteSQL = sprintf("DELETE FROM fotor WHERE oferta=%s",
                       GetSQLValueString($_GET['delfotoid'], "int"));
                                                   
  $Result1 = $connection->query($deleteSQL) or die($connection->error);

  $deleteGoTo = "view_rent.php";
  header(sprintf("Location: %s", $deleteGoTo));
}

if ((isset($_GET['del'])) && ($_GET['del'] != "")) {
  $deleteSQL = sprintf("DELETE FROM rent WHERE id=%s",
                       GetSQLValueString($_GET['del'], "int"));
                                                  
  $Result1 = $connection->query($deleteSQL) or die($connection->error);

  $deleteGoTo = "view_rent.php";
  header(sprintf("Location: %s", $deleteGoTo));
}
                                                
$query_marca = "SELECT * FROM marci ORDER BY den ASC";
$marca = $connection->query($query_marca) or die($connection->error);
$row_marca = mysqli_fetch_assoc($marca);
$totalRows_marca = mysqli_num_rows($marca);
                                                 
$query_rent = "SELECT * FROM rent ORDER BY id ASC";
$rent = $connection->query($query_rent) or die($connection->error);
$row_rent = mysqli_fetch_assoc($rent);
$totalRows_rent = mysqli_num_rows($rent);

$headerTitle = 'Baza de date rent';
$headerScripts = '<script type="text/javascript" src="../tinymce/jscripts/tiny_mce/tiny_mce_gzip.js"></script>
<script type="text/javascript">
tinyMCE_GZ.init({
	plugins : "layer,table,nonbreaking,xhtmlxtras",
	themes : "simple,advanced",
	languages : "en",
	disk_cache : true,
	debug : false
});
</script>
<script language="javascript" type="text/javascript">
tinyMCE.init({
	mode : "textareas",
	theme : "simple"
});
</script>
';
include 'header.php';
?><!-- InstanceBeginEditable name="main" -->
      <td><table width="98%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <?php do { ?>
        <tr>
            <td>
              <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
                <table align="center" bgcolor="#eeeeee">
                  <tr valign="baseline">
                    <td nowrap align="right">&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr valign="baseline">
                    <td colspan="2" align="right" nowrap><table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td width="80" align="right">Masina : </td>
                          <td><select name="marca" class="vanzari_box" id="marca">
                            <?php
do {  
?>
                            <option value="<?php echo $row_marca['den']?>"<?php if (!(strcmp($row_marca['den'], $row_rent['marca']))) {echo "selected=\"selected\"";} ?>><?php echo $row_marca['den']?></option>
                            <?php
} while ($row_marca = mysqli_fetch_assoc($marca));
  $rows = mysqli_num_rows($marca);
  if($rows > 0) {
      mysqli_data_seek($marca, 0);
	  $row_marca = mysqli_fetch_assoc($marca);
  }
?>
                          </select></td>
                          <td width="80" align="right">Tip:</td>
                          <td><input name="tip" type="text" class="vanzari_box" value="<?php echo $row_rent['tip']; ?>" size="32" /></td>
                          <td width="80" align="right">Combustibil:</td>
                          <td><select name="combust" class="vanzari_box" id="combust">
                            <option value="benzina" <?php if (!(strcmp("benzina", $row_rent['combust']))) {echo "selected=\"selected\"";} ?>>benzina</option>
                            <option value="motorina" <?php if (!(strcmp("motorina", $row_rent['combust']))) {echo "selected=\"selected\"";} ?>>motorina</option>
                            <option value="fara" <?php if (!(strcmp("fara", $row_rent['combust']))) {echo "selected=\"selected\"";} ?>>fara</option>
                          </select></td>
                        </tr>
                      </table></td>
                  </tr>

                  <tr valign="baseline">
                    <td colspan="2" align="right" valign="top" nowrap><textarea name="detalii" cols="50" rows="6" style="width: 100%;"><?php echo $row_rent['detalii']; ?></textarea></td>
                  </tr>
                  <tr valign="baseline">
                    <td colspan="2" align="right" valign="top" nowrap><table width="96%" border="0" align="right" cellpadding="0" cellspacing="0">
                        <tr>
                          <td align="right">[<a href="view_rent.php?delfotoid=<?php echo $row_rent['id']; ?>">sterge asocierile</a>] [<a href="asocr.php?ofid=<?php echo $row_rent['id']; ?>">asociaza foto</a>]&nbsp;&nbsp;&nbsp;&nbsp; </td>
                        </tr>
                        <tr>
                          <td style="border-top: 1px solid #CCCCCC;border-bottom: 1px solid #CCCCCC;"><?php include "showfoto1.php"; ?></td>
                        </tr>
                        <tr>
                          <td>&nbsp;</td>
                        </tr>
                      </table></td>
                  </tr>

                  <tr valign="baseline">
                    <td colspan="2" align="right" nowrap><table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#eeeeCC">
                        <tr>
                          <td colspan="6" align="center" bgcolor="#CCCCCC">Tarife inchiriere: </td>
                        </tr>
                        <tr>
                          <td align="center"><strong>1-3</strong></td>
                          <td align="center"><strong>4-7</strong></td>
                          <td align="center"><strong>8-14</strong></td>
                          <td align="center"><strong>15-20</strong></td>
                          <td align="center"><strong>&gt;20</strong></td>
                          <td align="center"><strong>Garantie</strong></td>
                        </tr>
                        <tr>
                          <td align="center"><input name="pret" type="text" id="pret" style="width:50px;" value="<?php echo $row_rent['pret']; ?>" size="32" /></td>
                          <td align="center"><input name="pret1" type="text" id="pret1" style="width:50px;" value="<?php echo $row_rent['pret1']; ?>" size="32" /></td>
                          <td align="center"><input name="pret2" type="text" id="pret2" style="width:50px;" value="<?php echo $row_rent['pret2']; ?>" size="32" /></td>
                          <td align="center"><input name="pret3" type="text" id="pret3" style="width:50px;" value="<?php echo $row_rent['pret3']; ?>" size="32" /></td>
                          <td align="center"><input name="pret4" type="text" id="pret4" style="width:50px;" value="<?php echo $row_rent['pret4']; ?>" size="32" /></td>
                          <td align="center"><input name="garantie" type="text" id="garantie" style="width:50px;" value="<?php echo $row_rent['garantie']; ?>" size="32" /></td>
                        </tr>
                      </table></td>
                  </tr>
                  <tr valign="baseline">
                    <td nowrap align="right">&nbsp;</td>
                    <td><input type="submit" value="Salveaza modificarile">
                    [<a href="view_rent.php?del=<?php echo $row_rent['id']; ?>">sterge complet oferta</a>]</td>
                  </tr>
                  <tr valign="baseline">
                    <td colspan="2" align="right" nowrap><hr /></td>
                  </tr>
                  </table>
                <input type="hidden" name="id" value="<?php echo $row_rent['id']; ?>">
                <input type="hidden" name="cat" value="<?php echo $row_rent['cat']; ?>">
                <input type="hidden" name="MM_update" value="form1">
                <input type="hidden" name="id" value="<?php echo $row_rent['id']; ?>">
                        </form></td>
        </tr>
        <?php } while ($row_rent = mysqli_fetch_assoc($rent)); ?>
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table></td><!-- InstanceEndEditable -->
  </tr>
</table>
</body>
<!-- InstanceEnd --></html>
<?php
mysqli_free_result($marca);

mysqli_free_result($rent);
?>

