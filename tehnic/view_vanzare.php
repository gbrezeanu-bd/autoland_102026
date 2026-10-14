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

$currentPage = $_SERVER["PHP_SELF"];
require_once('../includes/config.php'); 

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

if ((isset($_GET['delfotoid'])) && ($_GET['delfotoid'] != "")) {
  $deleteSQL = sprintf("DELETE FROM fotov WHERE oferta=%s",
                       GetSQLValueString($_GET['delfotoid'], "int"));
                                                  
  $Result1 = $connection->query($deleteSQL) or die($connection->error);

  $deleteGoTo = "view_vanzare.php";
  header(sprintf("Location: %s", $deleteGoTo));
}

if ((isset($_GET['del'])) && ($_GET['del'] != "")) {
    exit('aici trebui sa stergi si imaginile, nu doar anuntul');
  $deleteSQL = sprintf("DELETE FROM masini WHERE id=%s",
                       GetSQLValueString($_GET['del'], "int"));
                                                   
  $Result1 = $connection->query($deleteSQL) or die($connection->error);

  $deleteGoTo = "view_vanzare.php";
  header(sprintf("Location: %s", $deleteGoTo));
}
                                                
$query_Recordset1 = "SELECT * FROM `zone`";
$Recordset1 = $connection->query($query_Recordset1) or die($connection->error);
$row_Recordset1 = mysqli_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysqli_num_rows($Recordset1);
                                                  
$query_narca = "SELECT * FROM marci ORDER BY den ASC";
$narca = $connection->query($query_narca) or die($connection->error);
$row_narca = mysqli_fetch_assoc($narca);
$totalRows_narca = mysqli_num_rows($narca);

$query_subcateg = "SELECT * FROM subcateg ORDER BY den ASC";
$result_subcateg = $connection->query($query_subcateg) or die($connection->error);
$totalRows_subcateg = mysqli_num_rows($result_subcateg);

$maxRows_vehicule = 5;
$pageNum_vehicule = 0;
if (isset($_GET['pageNum_vehicule']) && empty($_POST)) {
  $pageNum_vehicule = $_GET['pageNum_vehicule'];
}
$startRow_vehicule = $pageNum_vehicule * $maxRows_vehicule;

//---- sonix
$categ 		= (isset($_REQUEST['acateg'])) ? $_REQUEST['acateg'] : '';
$marca 		= (isset($_REQUEST['amarca'])) ? $_REQUEST['amarca'] : ''; 
$subcateg	= (isset($_REQUEST['asubcateg'])) ? $_REQUEST['asubcateg'] : ''; 
$combustibilie 	= (isset($_REQUEST['acombustibilibil'])) ? $_REQUEST['acombustibilibil'] : ''; 
$anfabric 	= (isset($_REQUEST['aanfabricatie'])) ? $_REQUEST['aanfabricatie'] : '';

$wheresql	= " WHERE prov_enienta='Import' ";
if( !empty($categ) ) $wheresql 		.= ' AND cat="'.$categ.'"';
if( !empty($marca) ) $wheresql 		.= ' AND marca="'.$marca.'"';
if( !empty($subcateg) ) $wheresql 		.= ' AND subcateg="'.$subcateg.'"';
if( !empty($combustibilie) ) $wheresql 	.= ' AND combustibil="'.$combustibilie.'"';
if( !empty($anfabric) ) $wheresql 	.= ' AND an>="'.$anfabric.'"';
//----
                                                 
$query_vehicule = sprintf("SELECT * FROM masini %s ORDER BY pret ASC", $wheresql);
$query_limit_vehicule = sprintf("%s LIMIT %d, %d", $query_vehicule, $startRow_vehicule, $maxRows_vehicule);
$vehicule = $connection->query($query_limit_vehicule) or die($connection->error);
$row_vehicule = mysqli_fetch_assoc($vehicule);

if (isset($_GET['totalRows_vehicule']) && empty($_POST)) {
  $totalRows_vehicule = $_GET['totalRows_vehicule'];
} else {
  $all_vehicule = $connection->query($query_vehicule);
  $totalRows_vehicule = mysqli_num_rows($all_vehicule);
}
$totalPages_vehicule = ceil($totalRows_vehicule/$maxRows_vehicule)-1;

$queryString_vehicule = "";
if (!empty($_POST) || !empty($_GET)) {
//  $params = explode("&", $_SERVER['QUERY_STRING']);
  $params = $_POST+$_GET;
  $newParams = array();
  foreach ($params as $param=>$value) {
    if (stristr($param, "pageNum_vehicule") == false &&
        stristr($param, "totalRows_vehicule") == false) {
      array_push($newParams, $param.'='.$value);
    }
  }
  if (count($newParams) != 0) {
    $queryString_vehicule = "&" . htmlentities(implode("&", $newParams));
  }
}

$queryString_vehicule = sprintf("&totalRows_vehicule=%d%s", $totalRows_vehicule, $queryString_vehicule);

$headerTitle = 'Vizualizare baza de date vanzari din import';
include 'header.php';
?><!-- InstanceBeginEditable name="main" --><td valign="top">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td bgcolor="#eeeeee" style="border:1px solid #cccccc;"><form id="form1" name="form1" method="post" action="">
              <table width="90%" border="0" align="center" cellpadding="5" cellspacing="0">
                <tr>
                  <td width="50%" align="right"><label>Cat. :
                      <select name="acateg" class="add_vanz"  onchange="this.form.submit();">
						<option value="">Selecteaza</option>
                        <?php
do {
?>
                        <option value="<?php echo $row_Recordset1['den']?>"<?php if (!(strcmp($row_Recordset1['den'], $_REQUEST['acateg']))) {echo "selected=\"selected\"";} ?>><?php echo $row_Recordset1['den']?></option>
                        <?php
} while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1));
  $rows = mysqli_num_rows($Recordset1);
  if($rows > 0) {
      mysqli_data_seek($Recordset1, 0);
	  $row_Recordset1 = mysqli_fetch_assoc($Recordset1);
  }
?>
                      </select>

                    </label></td>
                  <td align="right"><label>Marca :
                      <select name="amarca" class="add_vanz" onchange="this.form.submit();">
						<option value="">Selecteaza</option>
                        <?php
do {
?>
                        <option value="<?php echo $row_narca['den']?>"<?php if (!(strcmp($row_narca['den'], $_REQUEST['amarca']))) {echo "selected=\"selected\"";} ?>><?php echo $row_narca['den']?></option>
                        <?php
} while ($row_narca = mysqli_fetch_assoc($narca));
  $rows = mysqli_num_rows($narca);
  if($rows > 0) {
      mysqli_data_seek($narca, 0);
	  $row_narca = mysqli_fetch_assoc($narca);
  }
?>
                      </select>

                    </label></td>
                </tr>
                <tr>
                  <td align="right"><label>combustibil. :
                      <select name="acombustibilibil" class="add_vanz" onchange="this.form.submit();">
						<option value="">Selecteaza</option>
                        <option value="motorina" <?php if (!(strcmp("motorina", $_REQUEST['acombustibilibil']))) {echo "selected=\"selected\"";} ?>>motorina</option>
                        <option value="benzina" <?php if (!(strcmp("benzina", $_REQUEST['acombustibilibil']))) {echo "selected=\"selected\"";} ?>>benzina</option>
                        <option value="fara" <?php if (!(strcmp("fara", $_REQUEST['acombustibilibil']))) {echo "selected=\"selected\"";} ?>>fara</option>
                      </select>
                    </label></td>
                  <td align="right"><label>An fabricatie :
                      <select name="aanfabricatie" class="add_vanz" onchange="this.form.submit();">
						<option value="">Selecteaza</option>
                        <option value="1997" <?php if (!(strcmp(1997, $_REQUEST['aanfabricatie']))) {echo "selected=\"selected\"";} ?>>1998 sau mai nou</option>
                        <option value="1998" <?php if (!(strcmp(1998, $_REQUEST['aanfabricatie']))) {echo "selected=\"selected\"";} ?>>1998 sau mai nou</option>
                        <option value="1999" <?php if (!(strcmp(1999, $_REQUEST['aanfabricatie']))) {echo "selected=\"selected\"";} ?>>1999 sau mai nou</option>
                        <option value="2000" <?php if (!(strcmp(2000, $_REQUEST['aanfabricatie']))) {echo "selected=\"selected\"";} ?>>2000 sau mai nou</option>
                        <option value="2001" <?php if (!(strcmp(2001, $_REQUEST['aanfabricatie']))) {echo "selected=\"selected\"";} ?>>2001 sau mai nou</option>
                        <option value="2002" <?php if (!(strcmp(2002, $_REQUEST['aanfabricatie']))) {echo "selected=\"selected\"";} ?>>2002 sau mai nou</option>
                        <option value="2003" <?php if (!(strcmp(2003, $_REQUEST['aanfabricatie']))) {echo "selected=\"selected\"";} ?>>2003 sau mai nou</option>
                        <option value="2004" <?php if (!(strcmp(2004, $_REQUEST['aanfabricatie']))) {echo "selected=\"selected\"";} ?>>2004 sau mai nou</option>
                        <option value="2005" <?php if (!(strcmp(2005, $_REQUEST['aanfabricatie']))) {echo "selected=\"selected\"";} ?>>2005 sau mai nou</option>
                        <option value="2006" <?php if (!(strcmp(2006, $_REQUEST['aanfabricatie']))) {echo "selected=\"selected\"";} ?>>2006 sau mai nou</option>
                      </select>
                    </label></td>
                </tr>
                <tr>
                  <td align="right">Subcateg:
                      <select name="asubcateg" class="add_vanz" onchange="this.form.submit();">
						<option value="">Selecteaza</option>
<?php
while ( $row_subcateg = mysqli_fetch_assoc($result_subcateg) ) {
?>
	                    <option value="<?php echo $row_subcateg['den']?>"<?php if (!(strcmp($row_subcateg['den'], $_REQUEST['asubcateg']))) {echo "selected=\"selected\"";} ?>><?php echo $row_subcateg['den']?></option>
<?php
}
?>
                      </select>
				</td>
                <td>&nbsp;</td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
              </table>
                  </form>        </td>
          </tr>
          <tr>
            <td bgcolor="#eeeeee" style="border:1px solid #cccccc;"><table width="90%" border="0" align="center" cellpadding="5" cellspacing="0">
                <tr>
                  <td width="50%">
                  Records <?php echo ($startRow_vehicule + 1) ?> to <?php echo min($startRow_vehicule + $maxRows_vehicule, $totalRows_vehicule) ?> of <?php echo $totalRows_vehicule ?> </td>
                  <td>
                    <table border="0" width="50%" align="center">
                      <tr>
                        <td width="23%" align="center"><?php if ($pageNum_vehicule > 0) { // Show if not first page ?>
                            <a href="<?php printf("%s?pageNum_vehicule=%d%s", $currentPage, 0, $queryString_vehicule); ?>">First</a>
                            <?php } // Show if not first page ?>                                                      </td>
                        <td width="31%" align="center"><?php if ($pageNum_vehicule > 0) { // Show if not first page ?>
                            <a href="<?php printf("%s?pageNum_vehicule=%d%s", $currentPage, max(0, $pageNum_vehicule - 1), $queryString_vehicule); ?>">Previous</a>
                            <?php } // Show if not first page ?>                                                      </td>
                        <td width="23%" align="center"><?php if ($pageNum_vehicule < $totalPages_vehicule) { // Show if not last page ?>
                            <a href="<?php printf("%s?pageNum_vehicule=%d%s", $currentPage, min($totalPages_vehicule, $pageNum_vehicule + 1), $queryString_vehicule); ?>">Next</a>
                            <?php } // Show if not last page ?>                                                      </td>
                        <td width="23%" align="center"><?php if ($pageNum_vehicule < $totalPages_vehicule) { // Show if not last page ?>
                            <a href="<?php printf("%s?pageNum_vehicule=%d%s", $currentPage, $totalPages_vehicule, $queryString_vehicule); ?>">Last</a>
                            <?php } // Show if not last page ?>                                                      </td>
                      </tr>
                    </table></td>
                </tr>
                      </table></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
		  <?php do { ?>
          <tr>
            <td>              <table width="570" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#EEEEEE">
                <tr valign="baseline">
                  <td width="50" align="left" nowrap bgcolor="#DCEAF1"><strong>ID: <span class="style4"><?php echo $row_vehicule['id']; ?></span></strong></td>
                  <td width="70" align="right" nowrap>Marca:</td>
                  <td align="left"><?php echo $row_vehicule['marca']; ?></td>
                  <td width="70" align="right">Tip:</td>
                  <td align="left"><?php echo $row_vehicule['tip']; ?></td>
                  <td width="70" align="right">Pret :</td>
                  <td align="left"><?php echo $row_vehicule['pret']; ?></td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap bgcolor="#DCEAF1">&nbsp;</td>
                  <td nowrap align="right">Cat:</td>
                  <td align="left"><?php echo $row_vehicule['cat']; ?></td>
                  <td align="right">combustibil:</td>
                  <td align="left"><?php echo $row_vehicule['combustibil']; ?></td>
                  <td align="right">An:</td>
                  <td align="left"><?php echo $row_vehicule['an']; ?></td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap bgcolor="#DCEAF1">&nbsp;</td>
                  <td align="right">Subcateg:</td>
                  <td align="left" colspan="5"><?php echo $row_vehicule['subcateg']; ?></td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap bgcolor="#DCEAF1">&nbsp;</td>
                  <td nowrap align="right">&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td colspan="3" align="center">&nbsp;</td>
                </tr>
                <tr valign="baseline">
                  <td align="left" nowrap bgcolor="#DCEAF1">&nbsp;</td>
                  <td nowrap align="left">&nbsp;</td>
                  <td colspan="3" align="left">[<a href="view_vanzare.php?del=<?php echo $row_vehicule['id']; ?>">sterge oferta</a>]&nbsp;&nbsp;&nbsp;&nbsp;[<a href="view_mod_vanzare.php?id=<?php echo $row_vehicule['id']; ?>">modifica oferta</a>]</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <tr valign="baseline">
                  <td align="left" nowrap bgcolor="#FFFFFF">&nbsp;</td>
                  <td align="left" nowrap bgcolor="#FFFFFF">&nbsp;</td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                  <td colspan="2" valign="middle" bgcolor="#FFFFFF">&nbsp;</td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
               </table>
            </td>
          </tr><?php } while ($row_vehicule = mysqli_fetch_assoc($vehicule)); ?>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
      </table>
        </td>
    <!-- InstanceEndEditable -->
  </tr>
</table>
</body>
<!-- InstanceEnd --></html>
<?php
mysqli_free_result($Recordset1);

mysqli_free_result($narca);

mysqli_free_result($vehicule);
?>