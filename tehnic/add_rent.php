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

$MM_restrictGoTo = "../admin/login.php";
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

$colname_ptrinsert = "-1";
if (isset($_POST['marcat'])) {
  $colname_ptrinsert = (get_magic_quotes_gpc()) ? $_POST['marcat'] : addslashes($_POST['marcat']);
}                                                    
$query_ptrinsert = sprintf("SELECT * FROM `search` WHERE id = %s", $colname_ptrinsert);
$ptrinsert = $connection->query($query_ptrinsert) or die($connection->error);
$row_ptrinsert = mysqli_fetch_assoc($ptrinsert);
$totalRows_ptrinsert = mysqli_num_rows($ptrinsert);
$marca=substr($row_ptrinsert['marca'],18);
$categ=$row_ptrinsert['categorie'];

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO rent (id, cat, marca, tip, combust, detalii, pret, pret1, pret2, pret3, pret4, garantie) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['id'], "int"),
                       GetSQLValueString($categ, "text"),
                       GetSQLValueString($marca, "text"),
                       GetSQLValueString($_POST['tip'], "text"),
                       GetSQLValueString($_POST['combust'], "text"),
                       GetSQLValueString($_POST['detalii'], "text"),
                       GetSQLValueString($_POST['pret'], "int"),
                       GetSQLValueString($_POST['pret1'], "int"),
                       GetSQLValueString($_POST['pret2'], "int"),
                       GetSQLValueString($_POST['pret3'], "int"),
                       GetSQLValueString($_POST['pret4'], "int"),
                       GetSQLValueString($_POST['pret42'], "text"));
                                                   
  if ($row_ptrinsert['marca']{0}!=' ') {
  $Result1 = $connection->query($insertSQL) or die($connection->error);
  }

  $insertGoTo = "view_rent.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
                                                  
$query_Recordset1 = "SELECT * FROM `search` ORDER BY categorie, marca ASC";
$Recordset1 = $connection->query($query_Recordset1) or die($connection->querry);
$row_Recordset1 = mysqli_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysqli_num_rows($Recordset1);

$headerTitle = 'Vanzari :: adaugare vehicul';
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
?><!-- InstanceBeginEditable name="main" --><td>
      <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
        <table align="center" cellpadding="0" cellspacing="0">
          <tr valign="baseline">
            <td colspan="2" align="left" nowrap>&nbsp;</td>
          </tr>
          <tr valign="baseline">
            <td colspan="2" align="left" nowrap bgcolor="#CCCCCC"><strong>Adaugare vehicule in baza de date inchirieri </strong> : </td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">&nbsp;</td>
            <td bgcolor="#EEEEEE">&nbsp;</td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">Marca:</td>
            <td bgcolor="#EEEEEE"><select name="marcat" class="add_vanz" id="marcat">
              <?php
do {  
?>
              <option value="<?php echo $row_Recordset1['id']?>" <?php if ($row_Recordset1['marca']{0}==" ") {echo 'class="capat"';} ?>><?php echo $row_Recordset1['marca']?></option>
              <?php
} while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1));
  $rows = mysqli_num_rows($Recordset1);
  if($rows > 0) {
      mysqli_data_seek($Recordset1, 0);
	  $row_Recordset1 = mysqli_fetch_assoc($Recordset1);
  }
?>
            </select></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">Tip:</td>
            <td bgcolor="#EEEEEE"><input name="tip" type="text" class="add_vanz" value="" size="32"></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">Combust:</td>
            <td bgcolor="#EEEEEE"><select name="combust" class="add_vanz" id="combust">
              <option value="fara">fara</option>
              <option value="benzina">benzina</option>
              <option value="motorina">motorina</option>
            </select>            </td>
          </tr>
          <tr valign="baseline">
            <td align="right" valign="top" nowrap bgcolor="#EEEEEE">Detalii:</td>
            <td bgcolor="#EEEEEE"><textarea name="detalii" cols="50" rows="6"></textarea>            </td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">&nbsp;</td>
            <td bgcolor="#EEEEEE">&nbsp;</td>
          </tr>
          <tr valign="baseline">
            <td colspan="2" align="right" nowrap bgcolor="#EEEEEE"><table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#eeeeCC">
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
                <td align="center"><input name="pret" type="text" style="width:50px;" value="" size="32" /></td>
                <td align="center"><input name="pret1" type="text" id="pret1" style="width:50px;" value="" size="32" /></td>
                <td align="center"><input name="pret2" type="text" id="pret2" style="width:50px;" value="" size="32" /></td>
                <td align="center"><input name="pret3" type="text" id="pret3" style="width:50px;" value="" size="32" /></td>
                <td align="center"><input name="pret4" type="text" id="pret4" style="width:50px;" value="" size="32" /></td>
                <td align="center"><input name="pret42" type="text" id="pret42" style="width:50px;" value="" size="32" /></td>
              </tr>
            </table></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">&nbsp;</td>
            <td bgcolor="#EEEEEE">&nbsp;</td>
          </tr>
          <tr valign="baseline">
            <td nowrap align="right">&nbsp;</td>
            <td><input type="submit" value="Adauga inregistrarea"></td>
          </tr>
        </table>
        <input type="hidden" name="id" value="">
        <input type="hidden" name="MM_insert" value="form1">
      </form>
    <p>&nbsp;</p></td><!-- InstanceEndEditable -->
  </tr>
</table>
</body>
<!-- InstanceEnd --></html>
<?php
mysqli_free_result($Recordset1);

mysqli_free_result($ptrinsert);
?>

