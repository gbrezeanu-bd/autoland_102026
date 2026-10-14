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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {
  $updateSQL = sprintf("UPDATE masini SET oferta_speciala=%s, stare_anunt=%s, cat=%s, marca=%s, subcateg=%s, tip=%s, combustibil=%s, an=%s, detalii=%s, pret=%s WHERE id=%s",
  		       GetSQLValueString($_POST['oferta_speciala'], "int"),
			   GetSQLValueString($_POST['stare_anunt'], "int"),
                       GetSQLValueString($_POST['cat'], "text"),
                       GetSQLValueString($_POST['marca'], "text"),
                       GetSQLValueString($_POST['subcateg'], "text"),
                       GetSQLValueString($_POST['tip'], "text"),
                       GetSQLValueString($_POST['combustibil'], "text"),
                       GetSQLValueString($_POST['an'], "int"),
                       GetSQLValueString($_POST['detalii'], "text"),
                       GetSQLValueString($_POST['pret'], "int"),
                       GetSQLValueString($_POST['id'], "int"));
                                                      
  $Result1 = $connection->query($updateSQL) or die($connection->error);

  $updateGoTo = "view_vanzare_oferta.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_GET['delfotoid'])) && ($_GET['delfotoid'] != "")) {
  $deleteSQL = sprintf("DELETE FROM fotov WHERE oferta=%s",
                       GetSQLValueString($_GET['delfotoid'], "int"));
                                                    
  $Result1 = $connection->query($deleteSQL) or die($connection->error);

  $deleteGoTo = "view_vanzare_oferta.php";
  header(sprintf("Location: %s", $deleteGoTo));
}

if ((isset($_GET['del'])) && ($_GET['del'] != "")) {
  $deleteSQL = sprintf("DELETE FROM masini WHERE id=%s",
                       GetSQLValueString($_GET['del'], "int"));
                                                      
  $Result1 = $connection->query($deleteSQL) or die($connection->error);

  $deleteGoTo = "view_vanzare_oferta.php";
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
if (isset($_GET['pageNum_vehicule'])) {
  $pageNum_vehicule = $_GET['pageNum_vehicule'];
}
$startRow_vehicule = $pageNum_vehicule * $maxRows_vehicule;

$colname_vehicule = "-1";
if (isset($_GET['id'])) {
  $colname_vehicule = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}                                               
$query_vehicule = sprintf("SELECT * FROM masini WHERE id = %s ORDER BY pret ASC", $colname_vehicule);
$query_limit_vehicule = sprintf("%s LIMIT %d, %d", $query_vehicule, $startRow_vehicule, $maxRows_vehicule);
$vehicule = $connection->query($query_limit_vehicule) or die($connection->error);
$row_vehicule = mysqli_fetch_assoc($vehicule);

if (isset($_GET['totalRows_vehicule'])) {
  $totalRows_vehicule = $_GET['totalRows_vehicule'];
} else {
  $all_vehicule = $connection->query($query_vehicule);
  $totalRows_vehicule = mysqli_num_rows($all_vehicule);
}
$totalPages_vehicule = ceil($totalRows_vehicule/$maxRows_vehicule)-1;

$queryString_vehicule = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_vehicule") == false && 
        stristr($param, "totalRows_vehicule") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_vehicule = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_vehicule = sprintf("&totalRows_vehicule=%d%s", $totalRows_vehicule, $queryString_vehicule);

$headerTitle = 'Vizualizare baza de date vanzari';
$headerScripts = '<script language="javascript" type="text/javascript" src="../tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript" src="process.js"></script>
<script language="javascript" type="text/javascript">
	tinyMCE.init({
		mode : "textareas",
		theme : "advanced",
		plugins : "style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,imagemanager,filemanager",
		theme_advanced_buttons1_add_before : "save,newdocument,separator",
		theme_advanced_buttons1_add : "fontselect,fontsizeselect",
		theme_advanced_buttons2_add : "separator,insertdate,inserttime,preview,separator,forecolor,backcolor",
		theme_advanced_buttons2_add_before: "cut,copy,paste,pastetext,pasteword,separator,search,replace,separator",
		theme_advanced_buttons3_add_before : "tablecontrols,separator",
		theme_advanced_buttons3_add : "emotions,iespell,media,advhr,separator,print,separator,ltr,rtl,separator,fullscreen",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,spellchecker,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		content_css : "/example_data/example_full.css",
	    plugin_insertdate_dateFormat : "%Y-%m-%d",
	    plugin_insertdate_timeFormat : "%H:%M:%S",
		external_link_list_url : "example_data/example_link_list.js",
		external_image_list_url : "example_data/example_image_list.js",
		flash_external_list_url : "example_data/example_flash_list.js",
		template_external_list_url : "example_data/example_template_list.js",
		theme_advanced_resize_horizontal : false,
		theme_advanced_resizing : true,
		apply_source_formatting : true,
		spellchecker_languages : "+English=en,Danish=da,Dutch=nl,Finnish=fi,French=fr,German=de,Italian=it,Polish=pl,Portuguese=pt,Spanish=es,Swedish=sv"
	});

</script>
<!-- /tinyMCE -->
';
include 'header.php';
?><!-- InstanceBeginEditable name="main" -->
    <td valign="top">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td bgcolor="#eeeeee" style="border:1px solid #cccccc;">&nbsp;</td>
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
            <td>
              <form method="post" name="form2" action="<?php echo $editFormAction; ?>">
                <table width="570" align="center" bgcolor="#EEEEEE">
                  <tr valign="baseline">
                    <td width="70" align="right" nowrap>Cat:</td>
                    <td><select name="cat" id="cat" style="width:120px;">
                      <?php
do {  
?>
                      <option value="<?php echo $row_Recordset1['den']?>"<?php if (!(strcmp($row_Recordset1['den'], $row_vehicule['cat']))) {echo "selected=\"selected\"";} ?>><?php echo $row_Recordset1['den']?></option>
                      <?php
} while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1));
  $rows = mysqli_num_rows($Recordset1);
  if($rows > 0) {
      mysqli_data_seek($Recordset1, 0);
	  $row_Recordset1 = mysqli_fetch_assoc($Recordset1);
  }
?>
                    </select></td>
                    <td width="70" align="right">Marca:</td>
                    <td><select name="marca" id="marca" style="width:120px;">
                      <?php
do {  
?>
                      <option value="<?php echo $row_narca['den']?>"<?php if (!(strcmp($row_narca['den'], $row_vehicule['marca']))) {echo "selected=\"selected\"";} ?>><?php echo $row_narca['den']?></option>
                      <?php
} while ($row_narca = mysqli_fetch_assoc($narca));
  $rows = mysqli_num_rows($narca);
  if($rows > 0) {
      mysqli_data_seek($narca, 0);
	  $row_narca = mysqli_fetch_assoc($narca);
  }
?>
                    </select></td>
                    <td width="70" align="right">Tip:</td>
                    <td><input name="tip" type="text" class="add_vanz" value="<?php echo $row_vehicule['tip']; ?>" size="32"style="width:120px;" /></td>
                  </tr>
                  <tr valign="baseline">
                    <td nowrap align="right">combustibil:</td>
                    <td><select name="combustibil" class="add_vanz" id="combustibil" style="width:120px;">
                      <option value="fara" <?php if (!(strcmp("fara", $row_vehicule['combustibil']))) {echo "selected=\"selected\"";} ?>>fara</option>
                      <option value="motorina" <?php if (!(strcmp("motorina", $row_vehicule['combustibil']))) {echo "selected=\"selected\"";} ?>>motorina</option>
                      <option value="benzina" <?php if (!(strcmp("benzina", $row_vehicule['combustibil']))) {echo "selected=\"selected\"";} ?>>benzina</option>
                    </select></td>
                    <td align="right">An:</td>
                    <td><input name="an" type="text" class="add_vanz" value="<?php echo $row_vehicule['an']; ?>" size="32" style="width:60px;"/></td>
                    <td align="right">Pret : </td>
                    <td><input name="pret" type="text" class="add_vanz" value="<?php echo $row_vehicule['pret']; ?>" size="32" style="width:120px;"/></td>
                  </tr>
				<tr valign="baseline">
	                <td nowrap align="right">Subcateg:</td>
                    <td><select name="subcateg" id="subcateg" style="width:120px;">
<?php
while ($row_subcateg = mysqli_fetch_assoc($result_subcateg)) {
?>
                      <option value="<?php echo $row_subcateg['den']?>"<?php if (!(strcmp($row_subcateg['den'], $row_vehicule['subcateg']))) {echo "selected=\"selected\"";} ?>><?php echo $row_subcateg['den']?></option>
<?php
}
?>
                    </select></td>
<!-- Oferta Speciala -->
                    <td nowrap align="right">Oferta:</td>
                    <td>
                    	<select name="oferta_speciala" class="add_vanz" id="oferta_speciala" style="width:120px;">
                      		<option value="0" <?php if (!(strcmp("0", $row_vehicule['oferta_speciala']))) {echo "selected=\"selected\"";} ?>>fara</option>
                      		<option value="1" <?php if (!(strcmp("1", $row_vehicule['oferta_speciala']))) {echo "selected=\"selected\"";} ?>>oferta</option>                    
                    	</select>
                    </td>  
                    <td nowrap align="right">Stare Anunt:</td>
                    <td>
                    	<select name="stare_anunt" class="add_vanz" id="stare_anunt" style="width:120px;">
                      		<option value="0" <?php if (!(strcmp("0", $row_vehicule['stare_anunt']))) {echo "selected=\"selected\"";} ?>>OFF</option>
                      		<option value="1" <?php if (!(strcmp("1", $row_vehicule['stare_anunt']))) {echo "selected=\"selected\"";} ?>>ON</option>                    
                    	</select>
                    </td>                    
                    <td colspan="4">&nbsp;</td>						
				</tr>

                  <tr valign="baseline">
                    <td align="right" valign="top" nowrap>Detalii:</td>
                    <td colspan="5"><textarea name="detalii" cols="60" rows="15" style="width:500px;"><?php echo $row_vehicule['detalii']; ?></textarea></td>
                  </tr>
                  <tr valign="baseline">
                    <td nowrap align="right">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td colspan="3" align="center">[<a href="view_vanzare_oferta.php?delfotoid=<?php echo $row_vehicule['id']; ?>">sterge asocierile</a>] [<a href="asocv.php?ofid=<?php echo $row_vehicule['id']; ?>">asociaza foto</a>] </td>
                  </tr>
                  <tr valign="baseline">
                    <td nowrap align="right">&nbsp;</td>
                    <td colspan="5" style="border-top: 1px solid #CCCCCC;border-bottom: 1px solid #CCCCCC;"><?php include "showfoto.php"; ?></td>
                  </tr>
                  <tr valign="baseline">
                    <td nowrap align="right">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td colspan="3" align="center">&nbsp;</td>
                  </tr>
                  <tr valign="baseline">
                    <td nowrap align="left"><strong>ID: <span class="style4"><?php echo $row_vehicule['id']; ?></span></strong></td>
                    <td><input name="submit" type="submit" value="Salveaza modificarile" /></td>
                    <td colspan="2" valign="middle">[<a href="view_vanzare_oferta.php?del=<?php echo $row_vehicule['id']; ?>">sterge complet oferta</a>]</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr valign="baseline">
                    <td colspan="6" align="right" nowrap><hr />
</td>
                  </tr>
                </table>
                <input type="hidden" name="id" value="<?php echo $row_vehicule['id']; ?>">
                <input type="hidden" name="MM_update" value="form2">
                <input type="hidden" name="id" value="<?php echo $row_vehicule['id']; ?>">
              </form>
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