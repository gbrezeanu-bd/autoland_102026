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

if ((isset($_GET['del'])) && ($_GET['del'] != "")) {
  $deleteSQL = sprintf("DELETE FROM crere_rent WHERE id=%s",
                       GetSQLValueString($_GET['del'], "int"));
                                                    
  $Result1 = $connection->query($deleteSQL) or die($connection->error);

  $deleteGoTo = "cereri_rent.php";
  header(sprintf("Location: %s", $deleteGoTo));
}

$maxRows_rent = 20;
$pageNum_rent = 0;
if (isset($_GET['pageNum_rent'])) {
  $pageNum_rent = $_GET['pageNum_rent'];
}
$startRow_rent = $pageNum_rent * $maxRows_rent;
                                                 
$query_rent = "SELECT * FROM crere_rent ORDER BY id DESC";
$query_limit_rent = sprintf("%s LIMIT %d, %d", $query_rent, $startRow_rent, $maxRows_rent);
$rent = $connection->query($query_limit_rent) or die($connection->error);
$row_rent = mysqli_fetch_assoc($rent);

if (isset($_GET['totalRows_rent'])) {
  $totalRows_rent = $_GET['totalRows_rent'];
} else {
  $all_rent = mysqli_query($query_rent);
  $totalRows_rent = mysqli_num_rows($all_rent);
}
$totalPages_rent = ceil($totalRows_rent/$maxRows_rent)-1;

$headerTitle = 'Admin cereri rent';
include 'header.php';
?><!-- InstanceBeginEditable name="main" --><td valign="top">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr><?php do { ?>
          <tr>
            <td><table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" style="border:1PX SOLID #CCCCCC;">
              <tr>
                <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="50%"><strong>Auto: <?php echo $row_rent['auto']; ?></strong></td>
                    <td align="right">[ <a href="cereri_rent.php?del=<?php echo $row_rent['id']; ?>">sterge rezervarea</a>]</td>
                  </tr>
                  <tr>
                    <td><strong>ID: <?php echo $row_rent['cod_auto']; ?></strong></td>
                    <td align="right">&nbsp;</td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td width="110" align="right" bgcolor="#eeeeee">Data ridicarii : </td>
                    <td bgcolor="#eeeeee"><?php echo $row_rent['data_ridic']; ?></td>
                    <td width="150" align="right" bgcolor="#eeeeee">Ora ridicarii : </td>
                    <td width="30" align="right" bgcolor="#eeeeee"><?php echo $row_rent['hh_ridic']; ?>:</td>
                    <td width="30" align="left" bgcolor="#eeeeee"><?php echo $row_rent['mm_ridic']; ?></td>
                  </tr>
                  <tr>
                    <td align="right">Data returnarii : </td>
                    <td><?php echo $row_rent['data_retur']; ?></td>
                    <td align="right">Ora returnarii : </td>
                    <td align="right"><?php echo $row_rent['hh_retur']; ?>:</td>
                    <td align="left"><?php echo $row_rent['mm_retur']; ?></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td><table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#eeeeee">
                  <tr>
                    <td width="110" align="right">Nume :</td>
                    <td align="left"><?php echo $row_rent['nume']; ?></td>
                    <td width="60" align="right">Telefon : </td>
                    <td align="left"><?php echo $row_rent['telefon']; ?></td>
                    <td width="60" align="right">E-mail : </td>
                    <td align="left"><?php echo $row_rent['email']; ?></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="110" align="right" valign="top">Mesaj : </td>
                    <td><?php echo $row_rent['mesaj']; ?></td>
                  </tr>
                </table></td>
              </tr>
              
            </table></td>
          </tr><?php } while ($row_rent = mysqli_fetch_assoc($rent)); ?>
                </table>
        </td><!-- InstanceEndEditable -->
  </tr>
</table>
</body>
<!-- InstanceEnd --></html>
<?php
mysqli_free_result($rent);
?>

