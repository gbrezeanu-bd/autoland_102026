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

if ((isset($_GET['id'])) && ($_GET['id'] != "")) {
  $deleteSQL = sprintf("DELETE FROM search WHERE id=%s",
                       GetSQLValueString($_GET['id'], "int"));
                                                    
  $Result1 = $connection->query($deleteSQL) or die($connection->error);

  $deleteGoTo = "filtru.php";
   header(sprintf("Location: %s", $deleteGoTo));
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  if ($_POST['marca']=="=") {$_POST['marca']=" ".$_POST['categorie'];} else {$_POST['marca']="&nbsp;&nbsp;&nbsp;".$_POST['marca'];}
  $insertSQL = sprintf("INSERT INTO search (id, categorie, marca) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['id'], "int"),
                       GetSQLValueString($_POST['categorie'], "text"),
                       GetSQLValueString($_POST['marca'], "text"));
                                                  
  $Result1 = $connection->query($insertSQL) or die($connection->error);

  $insertGoTo = "filtru.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
                                                   
$query_cat = "SELECT * FROM `zone` ORDER BY den ASC";
$cat = $connection->query($query_cat) or die($connection->error);
$row_cat = mysqli_fetch_assoc($cat);
$totalRows_cat = mysqli_num_rows($cat);
                                                  
$query_marci = "SELECT * FROM marci ORDER BY den ASC";
$marci = $connection->query($query_marci) or die($connection->error);
$row_marci = mysqli_fetch_assoc($marci);
$totalRows_marci = mysqli_num_rows($marci);
                                                  
$query_Recordset1 = "SELECT * FROM `search` ORDER BY categorie, marca ASC";
$Recordset1 = $connection->query($query_Recordset1) or die($connection->error);
$row_Recordset1 = mysqli_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysqli_num_rows($Recordset1);

$headerTitle = 'Administrare filtru selectie';
include 'header.php';
?><!-- InstanceBeginEditable name="main" -->
    <td valign="middle"><table width="60%" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td align="left" bgcolor="#CCCCCC"><strong>Vizualizare meniu </strong></td>
      </tr>
      <tr>
        <td align="center" bgcolor="#eeeeee">&nbsp;</td>
      </tr>
      <tr>
        <td align="center" bgcolor="#eeeeee"><form id="form2" name="form2" method="get" action="filtru.php">
          <select name="id" class="vanzari_box" id="id">
            <?php
do {  
?>
            <option value="<?php echo $row_Recordset1['id']?>" <? if ($row_Recordset1['marca']{0}==" ") {echo 'class="capat"';} ?>><?php echo $row_Recordset1['marca']?></option>
            <?php
} while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1));
  $rows = mysqli_num_rows($Recordset1);
  if($rows > 0) {
      mysqli_data_seek($Recordset1, 0);
	  $row_Recordset1 = mysqli_fetch_assoc($Recordset1);
  }
?>
          </select>
          <input type="submit" name="Submit" value="Sterge" />
        </form></td>
      </tr>
      <tr>
        <td align="center" bgcolor="#eeeeee">&nbsp;</td>
      </tr>
      <tr>
        <td align="center">&nbsp;</td>
      </tr>
      <tr>
        <td align="left" bgcolor="#CCCCCC"><strong>Adaugare item in meniu autovehicule </strong></td>
        </tr>
      <tr>
        <td bgcolor="#eeeeee">&nbsp;</td>
      </tr>
      <tr>
        <td bgcolor="#eeeeee">
          <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
            <table align="center">
              <tr valign="baseline">
                <td nowrap align="right">Categorie:</td>
                <td><select name="categorie" class="add_vanz" id="categorie">
                  <?php
do {  
?>
                  <option value="<?php echo $row_cat['den']?>"><?php echo $row_cat['den']?></option>
                  <?php
} while ($row_cat = mysqli_fetch_assoc($cat));
  $rows = mysqli_num_rows($cat);
  if($rows > 0) {
      mysqli_data_seek($cat, 0);
	  $row_cat = mysqli_fetch_assoc($cat);
  }
?>
                </select></td>
              </tr>
              <tr valign="baseline">
                <td nowrap align="right">Marca:</td>
                <td><select name="marca" class="add_vanz" id="marca">
                  <option value="=">=</option>
                  <?php
do {  
?>
                  <option value="<?php echo $row_marci['den']?>"><?php echo $row_marci['den']?></option>
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
                <td nowrap align="right">&nbsp;</td>
                <td><input type="submit" value="Insert record"></td>
              </tr>
            </table>
            <input type="hidden" name="id" value="">
            <input type="hidden" name="MM_insert" value="form1">
          </form>          </td>
        </tr>
      <tr>
        <td bgcolor="#eeeeee">&nbsp;</td>
      </tr>
    </table></td><!-- InstanceEndEditable -->
  </tr>
</table>
</body>
<!-- InstanceEnd --></html>
<?php
mysqli_free_result($cat);



mysqli_free_result($Recordset1);
?>

