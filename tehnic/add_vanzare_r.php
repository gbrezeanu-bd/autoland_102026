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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO masini (oferta_speciala, id, cat, marca, tip, combustibil, an, detalii, pret, prov_enienta) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, 'Romania')",
		       GetSQLValueString($_POST['oferta_speciala'], "int"),
                       GetSQLValueString($_POST['id'], "int"),
                       GetSQLValueString($_POST['cat'], "text"),
                       GetSQLValueString($_POST['marca'], "text"),
                       GetSQLValueString($_POST['tip'], "text"),
                       GetSQLValueString($_POST['combustibil'], "text"),
                       GetSQLValueString($_POST['an'], "int"),
                       GetSQLValueString($_POST['detalii'], "text"),
                       GetSQLValueString($_POST['pret'], "int"));
                                                      
  $Result1 = $connection->query($insertSQL) or die($connection->error);

  $insertGoTo = "view_vanzare_r.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
                                                  
$query_categorie = "SELECT * FROM `zone`";
$categorie = $connection->query($query_categorie) or die($connection->error);
$row_categorie = mysqli_fetch_assoc($categorie);
$totalRows_categorie = mysqli_num_rows($categorie);
                                                  
$query_marci = "SELECT * FROM marci ORDER BY den ASC";
$marci = $connection->query($query_marci) or die($connection->error);
$row_marci = mysqli_fetch_assoc($marci);
$totalRows_marci = mysqli_num_rows($marci);

$headerTitle = 'Vanzari din Romania :: adaugare vehicul';
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
	theme : "advanced"
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
            <td colspan="2" align="left" nowrap bgcolor="#CCCCCC"><strong>Adaugare vehicule in baza de date vanzari</strong> : </td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">Cat:</td>
            <td bgcolor="#EEEEEE"><select name="cat" class="add_vanz" id="cat">
              <?php
do {  
?>
              <option value="<?php echo $row_categorie['den']?>"<?php if (!(strcmp($row_categorie['den'], $row_categorie['den']))) {echo "selected=\"selected\"";} ?>><?php echo $row_categorie['den']?></option>
              <?php
} while ($row_categorie = mysqli_fetch_assoc($categorie));
  $rows = mysqli_num_rows($categorie);
  if($rows > 0) {
      mysqli_data_seek($categorie, 0);
	  $row_categorie = mysqli_fetch_assoc($categorie);
  }
?>
            </select></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">Marca:</td>
            <td bgcolor="#EEEEEE"><select name="marca" class="add_vanz" id="marca">
              <?php
do {  
?>
              <option value="<?php echo $row_marci['den']?>"<?php if (!(strcmp($row_marci['den'], $row_marci['den']))) {echo "selected=\"selected\"";} ?>><?php echo $row_marci['den']?></option>
              <?php
} while ($row_marci = mysqli_fetch_assoc($marci));
  $rows = mysqli_num_rows($marci);
  if($rows > 0) {
      mysqli_data_seek($marci, 0);
	  $row_marci = mysqli_fetch_assoc($marci);
  }
?>
            </select></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">Tip:</td>
            <td bgcolor="#EEEEEE"><input name="tip" type="text" class="add_vanz" value="" size="32"></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">combustibil:</td>
            <td bgcolor="#EEEEEE"><select name="combustibil" class="add_vanz" id="combustibil">
              <option value="fara">fara</option>
              <option value="benzina">benzina</option>
              <option value="motorina">motorina</option>
            </select>            </td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">An:</td>
            <td bgcolor="#EEEEEE"><input name="an" type="text" class="add_vanz" value="" size="32"></td>
          </tr>
<!-- Oferta Speciala -->
          <tr valign="baseline">
                    <td nowrap align="right" nowrap bgcolor="#EEEEEE">Oferta:</td>
                    <td bgcolor="#EEEEEE"><select name="oferta_speciala" class="add_vanz" id="oferta_speciala" style="width:120px;">
                      <option value="0" <?php if (!(strcmp("0", $row_vehicule['oferta_speciala']))) {echo "selected=\"selected\"";} ?>>fara</option>
                      <option value="1" <?php if (!(strcmp("1", $row_vehicule['oferta_speciala']))) {echo "selected=\"selected\"";} ?>>oferta</option>                    
                    </select></td>
          </tr>
          <tr valign="baseline">
            <td align="right" valign="top" nowrap bgcolor="#EEEEEE">Detalii:</td>
            <td bgcolor="#EEEEEE"><textarea name="detalii" cols="50" rows="6"></textarea>            </td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">Pret:</td>
            <td bgcolor="#EEEEEE"><input name="pret" type="text" class="add_vanz" value="" size="32">
            Euro</td>
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
mysqli_free_result($categorie);


?>