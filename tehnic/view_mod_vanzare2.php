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
$currentPage = $_SERVER["PHP_SELF"];
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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {
  $updateSQL = sprintf("UPDATE masini SET description=%s, keywords=%s, title=%s, oferta_speciala=%s, stare_anunt=%s, cat=%s, marca=%s, subcateg=%s, tip=%s, combustibil=%s, an=%s, detalii=%s, pret=%s, pret_vechi=%s, prov_enienta=%s, pre_anunt=%s WHERE id=%s",
  		       GetSQLValueString($_POST['description'], "text"),
			   GetSQLValueString($_POST['keywords'], "text"),
			   GetSQLValueString($_POST['title'], "text"),
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
                       GetSQLValueString($_POST['pret_vechi'], "int"),
					   GetSQLValueString($_POST['prov_enienta'], "text"),
					   GetSQLValueString($_POST['pre_anunt'], "text"),
                       GetSQLValueString($_POST['id'], "int"));
                                                      
  $Result1 = $connection->query($updateSQL) or die($connection->error);

  // Update for_csv table with tonaj (only for Utilaje)
  if (isset($_POST['tonaj'])) {
    $is_utilaje = (!empty($_POST['cat']) && $_POST['cat'] === 'Utilaje');
    if ($is_utilaje) {
      $updateForCsvSQL = sprintf("UPDATE for_csv SET tonaj=%s WHERE id=%s",
        GetSQLValueString($_POST['tonaj'], "text"),
        GetSQLValueString($_POST['id'], "int"));
      $Result2 = $connection->query($updateForCsvSQL) or die($connection->error);
    }
  }

  $updateGoTo = "view_mod_vanzare2.php";
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

  $deleteGoTo = "view_vanzare2.php";
  header(sprintf("Location: %s", $deleteGoTo));
}

if ((isset($_GET['del'])) && ($_GET['del'] != "")) {
  $deleteSQL = sprintf("DELETE FROM masini WHERE id=%s",
                       GetSQLValueString($_GET['del'], "int"));
                                                      
  $Result1 = $connection->query($deleteSQL) or die($connection->error);
  
  $deleteSQL = sprintf("DELETE FROM for_csv WHERE id=%s",
                       GetSQLValueString($_GET['del'], "int"));
                                                      
  $Result1 = $connection->query($deleteSQL) or die($connection->error);
  
  $deleteGoTo = "view_vanzare2.php";
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

// Get for_csv data for tonaj field
$query_for_csv = sprintf("SELECT * FROM for_csv WHERE id = %s", $colname_vehicule);
$for_csv_result = $connection->query($query_for_csv) or die($connection->error);
$row_for_csv = mysqli_fetch_assoc($for_csv_result);
if (!$row_for_csv) {
  $row_for_csv = array('tonaj' => '');
}

// Aici verific si pun updatela nota poza
// START

$query_assoc 		=  	sprintf("SELECT * FROM fotov WHERE oferta = %s ORDER BY nota ASC",$colname_vehicule);
$assoc 			= 	$connection->query($query_assoc) or die($connection->error);
//$row_assoc 		= 	mysqli_fetch_assoc($assoc);
$totalRows_assoc 	= 	mysqli_num_rows($assoc);

$i=0;
while($row_assoc = mysqli_fetch_assoc($assoc))
{
	$ok = "";

	$id[$i]=$row_assoc['id'];
	$ok_minus = isset($_POST['nota_'.$id[$i].'_minus']) ? $_POST['nota_'.$id[$i].'_minus'] : null;
	$ok_plus = isset($_POST['nota_'.$id[$i].'_plus']) ? $_POST['nota_'.$id[$i].'_plus'] : null;

	if (isset($ok_minus))
	{
		$nota=$row_assoc['nota']-1;
		$updateSQL = sprintf("UPDATE fotov SET nota=%s WHERE id=%s",
					GetSQLValueString($nota, "int"),
					GetSQLValueString($id[$i], "int"));                                      
		$Result1 = $connection->query($updateSQL) or die($connection->error);
	}
	if (isset($ok_plus))
	{
		$nota=$row_assoc['nota']+1;
		$updateSQL = sprintf("UPDATE fotov SET nota=%s WHERE id=%s",
					GetSQLValueString($nota, "int"),
					GetSQLValueString($id[$i], "int"));                                      
		$Result1 = $connection->query($updateSQL) or die($connection->error);
	}
	$i++;	
}

// Gestionare ordine videouri (similar cu imagini)
$query_video = sprintf("SELECT * FROM fotov_video WHERE oferta = %s ORDER BY nota ASC", $colname_vehicule);
$assoc_video = $connection->query($query_video) or die($connection->error);

$j=0;
while($row_video = mysqli_fetch_assoc($assoc_video))
{
	$ok_video = "";

	$id_video[$j]=$row_video['id'];
	$ok_video_minus = isset($_POST['nota_video_'.$id_video[$j].'_minus']) ? $_POST['nota_video_'.$id_video[$j].'_minus'] : null;
	$ok_video_plus = isset($_POST['nota_video_'.$id_video[$j].'_plus']) ? $_POST['nota_video_'.$id_video[$j].'_plus'] : null;

	if (isset($ok_video_minus))
	{
		$nota_video=$row_video['nota']-1;
		$updateSQL = sprintf("UPDATE fotov_video SET nota=%s WHERE id=%s",
					GetSQLValueString($nota_video, "int"),
					GetSQLValueString($id_video[$j], "int"));                                      
		$Result1 = $connection->query($updateSQL) or die($connection->error);
	}
	if (isset($ok_video_plus))
	{
		$nota_video=$row_video['nota']+1;
		$updateSQL = sprintf("UPDATE fotov_video SET nota=%s WHERE id=%s",
					GetSQLValueString($nota_video, "int"),
					GetSQLValueString($id_video[$j], "int"));                                      
		$Result1 = $connection->query($updateSQL) or die($connection->error);
	}
	$j++;	
}

if (isset($assoc_video)) {
    mysqli_free_result($assoc_video);
}

mysqli_free_result($assoc);



// END
//exit('la else verifica daca ai mysql_query, mysqli_query or connection->query');
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

require_once 'anunturi-admin-card.php';
$headerTitle = 'Vizualizare baza de date vanzari';
$headerScripts = '<link href="anunturi-admin.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="../tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="editor-continut.js"></script>
<script language="javascript" type="text/javascript" src="process.js"></script>
<script language="javascript" type="text/javascript">
	tinyMCE.init({
		mode : "textareas",
		theme : "advanced",
		plugins : "style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
		theme_advanced_buttons1_add_before : "save,newdocument,separator",
		theme_advanced_buttons1_add : "fontselect,fontsizeselect,forecolor,backcolor",
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
    <td valign="top" class="adm-anunturi">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><?php if ($row_vehicule) { ?><div class="adm-edit-head"><a class="adm-back" href="<?php echo adm_h(adm_link_inapoi('view_vanzare2.php')); ?>">&larr; Înapoi</a>Editare anunț #<?php echo $row_vehicule['id']; ?>
              <span class="adm-edit-sub"><?php echo $row_vehicule['marca']; ?> <?php echo $row_vehicule['tip']; ?></span>
              <span class="adm-badge <?php echo $row_vehicule['stare_anunt'] == '1' ? 'is-on' : 'is-off'; ?>"><?php echo $row_vehicule['stare_anunt'] == '1' ? 'ON' : 'OFF'; ?></span>
              <?php if ($row_vehicule['oferta_speciala'] == '1') { ?><span class="adm-badge is-offer">OFERTĂ</span><?php } ?>
              <a class="adm-btn-ghost adm-edit-site" href="<?php echo BASE_PATH . 'vanzari_detail.php?id=' . (int)$row_vehicule['id']; ?>" target="_blank" rel="noopener">Vezi anunțul pe site &#8599;</a>
            </div><?php } ?></td>
          </tr>
          <tr>
            <td class="adm-gap">&nbsp;</td>
          </tr>
          <tr>
            <td class="adm-paginare"><table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
                <tr>
                  <td width="50%">
                  Anunțurile <?php echo ($startRow_vehicule + 1) ?>–<?php echo min($startRow_vehicule + $maxRows_vehicule, $totalRows_vehicule) ?> din <?php echo $totalRows_vehicule ?> </td>
                  <td>
                    <table border="0" width="50%" align="center">
                      <tr>
                        <td width="23%" align="center"><?php if ($pageNum_vehicule > 0) { // Show if not first page ?>
                            <a href="<?php printf("%s?pageNum_vehicule=%d%s", $currentPage, 0, $queryString_vehicule); ?>">Prima</a>
                            <?php } // Show if not first page ?>                                                      </td>
                        <td width="31%" align="center"><?php if ($pageNum_vehicule > 0) { // Show if not first page ?>
                            <a href="<?php printf("%s?pageNum_vehicule=%d%s", $currentPage, max(0, $pageNum_vehicule - 1), $queryString_vehicule); ?>">Anterioara</a>
                            <?php } // Show if not first page ?>                                                      </td>
                        <td width="23%" align="center"><?php if ($pageNum_vehicule < $totalPages_vehicule) { // Show if not last page ?>
                            <a href="<?php printf("%s?pageNum_vehicule=%d%s", $currentPage, min($totalPages_vehicule, $pageNum_vehicule + 1), $queryString_vehicule); ?>">Următoarea</a>
                            <?php } // Show if not last page ?>                                                      </td>
                        <td width="23%" align="center"><?php if ($pageNum_vehicule < $totalPages_vehicule) { // Show if not last page ?>
                            <a href="<?php printf("%s?pageNum_vehicule=%d%s", $currentPage, $totalPages_vehicule, $queryString_vehicule); ?>">Ultima</a>
                            <?php } // Show if not last page ?>                                                      </td>
                      </tr>
                    </table></td>
                </tr>
                      </table></td>
          </tr>
          <tr>
            <td class="adm-gap">&nbsp;</td>
          </tr>
		  <?php do { ?>
          <tr>
            <td>
              <form method="post" name="form2" action="<?php echo $editFormAction; ?>">
                <table width="570" align="center" bgcolor="#EEEEEE" class="adm-form">
                  <tr valign="baseline">
                    <td width="70" align="right" nowrap>Categorie:</td>
                    <td><select name="cat" id="cat" style="width:120px;">
                      <?php
do {  
?>
                      <option value="<?php echo $row_Recordset1['den']?>" <?php if (!(strcmp($row_Recordset1['den'], $row_vehicule['cat']))) {echo "selected=\"selected\"";} ?>><?php echo $row_Recordset1['den']?></option>
                      <?php
} while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1));
  $rows = mysqli_num_rows($Recordset1);
  if($rows > 0) {
      mysqli_data_seek($Recordset1, 0);
	  $row_Recordset1 = mysqli_fetch_assoc($Recordset1);
  }
?>
                    </select></td>
                    <td width="70" align="right">Marcă:</td>
                    <td><select name="marca" id="marca" style="width:120px;">
                      <?php
do {  
?>
                      <option value="<?php echo $row_narca['den']?>" <?php if (!(strcmp($row_narca['den'], $row_vehicule['marca']))) {echo "selected=\"selected\"";} ?>><?php echo $row_narca['den']?></option>
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
                    <td><input name="tip" type="text" class="add_vanz" value="<?php echo $row_vehicule['tip']; ?>" size="32" style="width:120px;" /></td>
                  </tr>
                  <tr valign="baseline">
                    <td nowrap align="right">Combustibil:</td>
                    <td><select name="combustibil" class="add_vanz" id="combustibil" style="width:120px;">
                      <option value="fara" <?php if (!(strcmp("fara", $row_vehicule['combustibil']))) {echo "selected=\"selected\"";} ?>>fără</option>
                      <option value="motorina" <?php if (!(strcmp("motorina", $row_vehicule['combustibil']))) {echo "selected=\"selected\"";} ?>>motorină</option>
                      <option value="benzina" <?php if (!(strcmp("benzina", $row_vehicule['combustibil']))) {echo "selected=\"selected\"";} ?>>benzină</option>
                      <option value="hybrid" <?php if (!(strcmp("hybrid", $row_vehicule['combustibil']))) {echo "selected=\"selected\"";} ?>>hybrid</option>
                      <option value="electric" <?php if (!(strcmp("electric", $row_vehicule['combustibil']))) {echo "selected=\"selected\"";} ?>>electric</option>
                    </select></td>
                    <td align="right">An:</td>
                    <td><input name="an" type="text" class="add_vanz" value="<?php echo $row_vehicule['an']; ?>" size="32" style="width:60px;"/></td>
                    <td align="right">Preț:</td>
                    <td><input name="pret" type="text" class="add_vanz" value="<?php echo $row_vehicule['pret']; ?>" size="32" style="width:120px;"/></td>
                  </tr>
				<tr valign="baseline">
	                <td nowrap align="right">Subcategorie:</td>
                    <td><select name="subcateg" id="subcateg" style="width:120px;">
<?php
while ($row_subcateg = mysqli_fetch_assoc($result_subcateg)) {
?>
                      <option value="<?php echo $row_subcateg['den']?>" <?php if (!(strcmp($row_subcateg['den'], $row_vehicule['subcateg']))) {echo "selected=\"selected\"";} ?>><?php echo $row_subcateg['den']?></option>
<?php
}
?>
                    </select></td>
<!-- Oferta Speciala -->
                    <td nowrap align="right">Oferta:</td>
                    <td>
                    	<select name="oferta_speciala" class="add_vanz" id="oferta_speciala" style="width:120px;">
                      		<option value="0" <?php if (!(strcmp("0", $row_vehicule['oferta_speciala']))) {echo "selected=\"selected\"";} ?>>fără</option>
                      		<option value="1" <?php if (!(strcmp("1", $row_vehicule['oferta_speciala']))) {echo "selected=\"selected\"";} ?>>oferta</option>                    
                    	</select>
                    </td>  
                    <td align="right">Preț vechi:</td>
                    <td>
                    	<input name="pret_vechi" type="text" class="add_vanz" value="<?php echo $row_vehicule['pret_vechi']; ?>" size="32" style="width:120px;"/>
                    </td>
                    <td colspan="4">&nbsp;</td>						
				</tr>
				<tr>
					<td nowrap align="right">Proveniență:</td>
				                    <td>
                    	<select name="prov_enienta" class="add_vanz" id="prov_enienta" style="width:120px;">
							<option value="Import" <?php if (!(strcmp("Import", $row_vehicule['prov_enienta']))) {echo "selected=\"selected\"";} ?>>Import</option>
							<option value="Romania" <?php if (!(strcmp("Romania", $row_vehicule['prov_enienta']))) {echo "selected=\"selected\"";} ?>>România</option>
                    	</select>
                    </td> 
                    <td nowrap align="right">Stare anunț:</td>
                    <td>
                    	<select name="stare_anunt" class="add_vanz" id="stare_anunt" style="width:120px;">
                      		<option value="0" <?php if (!(strcmp("0", $row_vehicule['stare_anunt']))) {echo "selected=\"selected\"";} ?>>OFF</option>
                      		<option value="1" <?php if (!(strcmp("1", $row_vehicule['stare_anunt']))) {echo "selected=\"selected\"";} ?>>ON</option>                    
                    	</select>
                    </td>                    

					<td>&nbsp;</td>

				</tr>
				<?php 
				// Check if this is Utilaje to show tonaj field
				$is_utilaje = (!empty($row_vehicule['cat']) && $row_vehicule['cat'] === 'Utilaje');
				if ($is_utilaje): 
				?>
				<tr valign="baseline">
					<td nowrap align="right">Tonaj:</td>
					<td>
						<input name="tonaj" type="text" class="add_vanz" value="<?php echo isset($row_for_csv['tonaj']) ? htmlspecialchars($row_for_csv['tonaj']) : ''; ?>" size="32" style="width:120px;"/>
					</td>
					<td colspan="4">&nbsp;</td>
				</tr>
				<?php endif; ?>
                  <tr valign="baseline">
                    <td align="right" valign="top" nowrap>Titlu:</td>
                    <td colspan="5"><input name="title" type="text" class="add_vanz" value="<?php echo $row_vehicule['title']; ?>" style="width:480px;"/></td>
                  </tr>
                  <tr valign="baseline">
                    <td align="right" valign="top" nowrap>Descriere SEO:</td>
                    <td colspan="5"><input name="description" type="text" class="add_vanz" value="<?php echo $row_vehicule['description']; ?>" style="width:480px;"/></td>
                  </tr>
                  <tr valign="baseline">
                    <td align="right" valign="top" nowrap>Cuvinte cheie:</td>
                    <td colspan="5"><input name="keywords" type="text" class="add_vanz" value="<?php echo $row_vehicule['keywords']; ?>" style="width:480px;"/></td>
                  </tr>
                  <tr valign="baseline">
                    <td align="right" valign="top" nowrap>Header anunț:</td>
                    <td colspan="5"><input name="pre_anunt" type="text" class="add_vanz" value="<?php echo $row_vehicule['pre_anunt']; ?>" style="width:480px;"/></td>
                  </tr>				  
                  <tr valign="baseline">
                    <td align="right" valign="top" nowrap>Detalii:</td>
                    <td colspan="5"><textarea name="detalii" cols="60" rows="15" style="width:500px;"><?php echo $row_vehicule['detalii']; ?></textarea></td>
                  </tr>
                  <tr valign="baseline">
                    <td nowrap align="right">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td colspan="3" align="center"><a class="adm-btn-ghost" href="foto_up2.php?id=<?php echo $row_vehicule['id']; ?>">asociază foto</a> <a class="adm-btn-ghost" href="video_up2.php?id=<?php echo $row_vehicule['id']; ?>">asociază video</a> </td>
                  </tr>
                  <tr valign="baseline">
                    <td nowrap align="right">Imagini:</td>
                    <td colspan="5" class="adm-media"><?php include "showfoto.php"; ?></td>
                  </tr>
                  <tr valign="baseline">
                    <td nowrap align="right">Videouri:</td>
                    <td colspan="5" class="adm-media"><?php include "showvideo.php"; ?></td>
                  </tr>
                  <tr valign="baseline">
                    <td nowrap align="right">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td colspan="3" align="center">&nbsp;</td>
                  </tr>
                  <tr valign="baseline" class="adm-actions">
                    <td nowrap align="left"><span class="adm-id">ID <strong><?php echo $row_vehicule['id']; ?></strong></span></td>
                    <td><input name="submit" type="submit" value="Salvează modificările" /></td>
                    <td colspan="2" valign="middle"><a class="adm-del" href="view_vanzare2.php?del=<?php echo $row_vehicule['id']; ?>" onclick="return checkDelete()">șterge complet oferta</a></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr valign="baseline">
                    <td colspan="6" align="right" nowrap><hr /></td>
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