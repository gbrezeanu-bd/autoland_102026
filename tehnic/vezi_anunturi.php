<?php
require_once('../includes/config.php'); 

/* check connection */
if ($connection->connect_errno) {
    printf("Connect failed: %s\n", $connection->connect_error);
    exit();
}

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
      $theValue = ($theValue != "") ? $theValue : "NULL";
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

// Funcție helper pentru a obține valoarea SQL cu ghilimele pentru sprintf
function GetSQLValueStringForSprintf($theValue, $theType) 
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
  }
  return $theValue;
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {
$update = array (
					'id'                  => GetSQLValueString($_POST['id'], "int"),
					'stare_csv'           => GetSQLValueString($_POST['stare_csv'], "int"),
					'marca'               => GetSQLValueString($_POST['marca'], "text"),
					'model'               => GetSQLValueString($_POST['model'], "text"),
					'an'                  => GetSQLValueString($_POST['an'], "int"),
					'combustibil'         => GetSQLValueString($_POST['combustibil'], "text"),
					'norma_euro'          => GetSQLValueString($_POST['norma_euro'], "int"),
					'putere_motor'        => GetSQLValueString($_POST['putere_motor'], "int"),
					'kilometraj'          => GetSQLValueString($_POST['kilometraj'], "int"),
					'pret'                => GetSQLValueString($_POST['pret'], "int"),
					'moneda'              => GetSQLValueString($_POST['moneda'], "text"),
					'descriere'           => GetSQLValueString(isset($_POST['descriere']) ? $_POST['descriere'] : '', "text"),
					'url_anunt'           => GetSQLValueString(isset($_POST['url_anunt']) ? $_POST['url_anunt'] : '', "text"),
					'url_poze'            => GetSQLValueString(isset($_POST['url_poze']) ? $_POST['url_poze'] : '', "text"),
					'varianta'            => GetSQLValueString(isset($_POST['varianta']) ? $_POST['varianta'] : '', "text"),
					'cilindri'            => GetSQLValueString(isset($_POST['cilindri']) ? $_POST['cilindri'] : '', "int"),
					'culoarea'            => GetSQLValueString(isset($_POST['culoarea']) ? $_POST['culoarea'] : '', "text"),
					'caroserie'           => GetSQLValueString(isset($_POST['caroserie']) ? $_POST['caroserie'] : '', "text"),
					'nr_usi'              => GetSQLValueString(isset($_POST['nr_usi']) ? $_POST['nr_usi'] : '', "int"),
					'nr_locuri'           => GetSQLValueString(isset($_POST['nr_locuri']) ? $_POST['nr_locuri'] : '', "int"),
					'inmatriculata'       => GetSQLValueString(isset($_POST['inmatriculata']) ? $_POST['inmatriculata'] : '', "text"),
					'transmisie'          => GetSQLValueString(isset($_POST['transmisie']) ? $_POST['transmisie'] : '', "text"),
					'tractiune'           => GetSQLValueString(isset($_POST['tractiune']) ? $_POST['tractiune'] : '', "text"),
					'dotari'              => GetSQLValueString(isset($_POST['dotari']) ? $_POST['dotari'] : '', "text"),
					'url_filme'           => GetSQLValueString(isset($_POST['url_filme']) ? $_POST['url_filme'] : '', "text"),
					'keywords'            => GetSQLValueString($_POST['keywords'], "text"),
					'oferta_speciala'     => GetSQLValueString($_POST['oferta_speciala'], "int"),
					'description'         => GetSQLValueString($_POST['description'], "text"),
					'title'               => GetSQLValueString($_POST['title'], "text"),
					'stare_anunt'         => GetSQLValueString($_POST['stare_anunt'], "int"),
					'cat'                 => GetSQLValueString($_POST['cat'], "text"),
					'tip'                 => GetSQLValueString($_POST['tip'], "text"),
					'pret_vechi'          => GetSQLValueString($_POST['pret_vechi'], "int"),
					'prov_enienta'        => GetSQLValueString($_POST['prov_enienta'], "text"),
					'subcateg'            => GetSQLValueString($_POST['subcateg'], "text"),
					'pre_anunt'           => GetSQLValueString($_POST['pre_anunt'], "text"),
					'tonaj'               => GetSQLValueString(isset($_POST['tonaj']) ? $_POST['tonaj'] : '', "text"),
					'tip_deplasare'       => GetSQLValueString(isset($_POST['tip_deplasare']) ? $_POST['tip_deplasare'] : '', "text")
			);     

  foreach($update as $f => $v)
  {
	  $u[] = '`'.$f.'`=\''.$v.'\'';
	}                                                 
  $q = "UPDATE `for_csv` SET ".implode(',',$u)." WHERE `id`='{$update['id']}'";
/*
echo "<pre>";
var_dump($q); die();
echo "</pre>";
*/
	if ($connection->query($q) === TRUE) {
		echo "Update facut cu success";
	} else {
		echo "Eroare la update: " . $connection->error;
	}

  $updateGoTo = "vezi_anunturi.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_GET['delfotoid'])) && ($_GET['delfotoid'] != "")) {
  $deleteSQL = sprintf("DELETE FROM `fotov` WHERE `oferta`=%s",
                       GetSQLValueString($_GET['delfotoid'], "int"));
                                                    
  $Result1 = $connection->query($deleteSQL) or die($connection->error);
  

  $deleteGoTo = "view_vanzare2.php";
  header(sprintf("Location: %s", $deleteGoTo));
}

if ((isset($_GET['del'])) && ($_GET['del'] != "")) {
  $deleteSQL = sprintf("DELETE FROM `masini` WHERE `id`=%s",
                       GetSQLValueString($_GET['del'], "int"));
  $Result1 = $connection->query($deleteSQL) or die($connection->error);
  
  $deleteSQL2 = sprintf("DELETE FROM `for_csv` WHERE `id`=%s",
                       GetSQLValueString($_GET['del'], "int"));
  
  $Result2 = $connection->query($deleteSQL2) or die($connection->error);
  
  $deleteGoTo = "view_vanzare2.php";
  header(sprintf("Location: %s", $deleteGoTo));
}
  
// $conn->query() or die($conn->error);
$query_Recordset1 = "SELECT * FROM `zone`";
$Recordset1 = $connection->query($query_Recordset1) or die($connection->error);
$row_Recordset1 = $Recordset1->fetch_assoc();
$totalRows_Recordset1 = $Recordset1->num_rows;

$query_narca = "SELECT * FROM `marci` ORDER BY `den` ASC";
$narca = $connection->query($query_narca) or die($connection->error);
$row_narca = $narca->fetch_assoc();
$totalRows_narca = $narca->num_rows;

$query_subcateg = "SELECT * FROM `subcateg` ORDER BY `den` ASC";
$result_subcateg = $connection->query($query_subcateg) or die($connection->error);
$totalRows_subcateg = $result_subcateg->num_rows;

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
                                
$query_vehicule = "SELECT * FROM `for_csv` WHERE `id` = '$colname_vehicule' ORDER BY `pret` ASC";
$query_limit_vehicule = "$query_vehicule LIMIT $startRow_vehicule, $maxRows_vehicule";
$vehicule = $connection->query($query_limit_vehicule) or die($connection->error);
$row_vehicule = $vehicule->fetch_assoc();

// Aici verific si pun updatela nota poza
// START

$query_assoc 		=  	"SELECT * FROM `fotov` WHERE `oferta` = '$colname_vehicule' ORDER BY `nota` ASC";
$assoc 				= 	$connection->query($query_assoc) or die($connection->error);
$totalRows_assoc 	= 	$assoc->num_rows;

$url_poze = array();
$i=0;
$id_array = array(); // Array pentru ID-urile imaginilor
$car_id = isset($_GET['id']) ? $_GET['id'] : null; // ID-ul mașinii

while($row_assoc = $assoc->fetch_assoc())
{

	if (isset($row_vehicule['url_anunt']) && !empty($row_vehicule['url_anunt']))
	{
		$p0 = $row_assoc['file'];
		if (isset($p0) && !empty($p0) && isset($car_id) && !empty($car_id) && !filter_var($car_id, FILTER_VALIDATE_INT) === false)
		{
			$update_url_anunt = BASE_PATH.'vanzari_detail.php?id='.$car_id.'&pic='.$p0;	
			$connection->query("UPDATE `for_csv` SET `url_anunt`='$update_url_anunt' WHERE `id`=$car_id");	
		}
	}

	$ok = "";

	$id_array[$i]=$row_assoc['id'];
        
	$ok_minus = isset($_POST['nota_'.$id_array[$i].'_minus']) ? $_POST['nota_'.$id_array[$i].'_minus'] : null;
	$ok_plus = isset($_POST['nota_'.$id_array[$i].'_plus']) ? $_POST['nota_'.$id_array[$i].'_plus'] : null;

	if (isset($ok_minus))
	{
		$nota=$row_assoc['nota']-1;
		$updateSQL = sprintf("UPDATE `fotov` SET `nota`=%s WHERE `id`=%s",
					GetSQLValueString($nota, "int"),
					GetSQLValueString($id_array[$i], "int"));                                      
		$Result1 = $connection->query($updateSQL) or die($connection->error);
	}
	if (isset($ok_plus))
	{
		$nota=$row_assoc['nota']+1;
		$updateSQL = sprintf("UPDATE `fotov` SET `nota`=%s WHERE `id`=%s",
					GetSQLValueString($nota, "int"),
					GetSQLValueString($id_array[$i], "int"));                                      
		$Result1 = $connection->query($updateSQL) or die($connection->error);
	}
	$i++;	
	$url_poze[] = BASE_PATH."foto/{$row_assoc['file']}";
}

if (count($url_poze) > 0 && isset($car_id))
{
		$connection->query("UPDATE `for_csv` SET `url_poze`='".implode(',',$url_poze)."' WHERE `id`=$car_id");	
	}

$assoc->free_result();

// END

// START VIDEO

$query_video 		=  	"SELECT * FROM `fotov_video` WHERE `oferta` = '$colname_vehicule' ORDER BY `nota` ASC";
$assoc_video 		= 	$connection->query($query_video) or die($connection->error);
$totalRows_video 	= 	$assoc_video->num_rows;

$j=0;

while($row_video = $assoc_video->fetch_assoc())
{
	$ok_video = "";

	$id_video[$j]=$row_video['id'];
        
	$ok_video_minus = isset($_POST['nota_video_'.$id_video[$j].'_minus']) ? $_POST['nota_video_'.$id_video[$j].'_minus'] : null;
	$ok_video_plus = isset($_POST['nota_video_'.$id_video[$j].'_plus']) ? $_POST['nota_video_'.$id_video[$j].'_plus'] : null;

	if (isset($ok_video_minus))
	{
		$nota_video=$row_video['nota']-1;
		$updateSQL = sprintf("UPDATE `fotov_video` SET `nota`=%s WHERE `id`=%s",
					GetSQLValueString($nota_video, "int"),
					GetSQLValueString($id_video[$j], "int"));                                      
		$Result1 = $connection->query($updateSQL) or die($connection->error);
	}
	if (isset($ok_video_plus))
	{
		$nota_video=$row_video['nota']+1;
		$updateSQL = sprintf("UPDATE `fotov_video` SET `nota`=%s WHERE `id`=%s",
					GetSQLValueString($nota_video, "int"),
					GetSQLValueString($id_video[$j], "int"));                                      
		$Result1 = $connection->query($updateSQL) or die($connection->error);
	}
	$j++;	
}

$assoc_video->free_result();

// END VIDEO

if (isset($_GET['totalRows_vehicule'])) {
  $totalRows_vehicule = $_GET['totalRows_vehicule'];
} else {
  $all_vehicule = $connection->query($query_vehicule);
  $totalRows_vehicule = $all_vehicule->num_rows;
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
$headerScripts = '<link href="anunturi-admin.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="process.js"></script>';
include 'header.php';
?><!-- InstanceBeginEditable name="main" -->
    <td valign="top" class="adm-anunturi">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><?php if ($row_vehicule) { ?><div class="adm-edit-head">Editare anunț #<?php echo $row_vehicule['id']; ?>
              <span class="adm-edit-sub"><?php echo $row_vehicule['model'] ?: $row_vehicule['marca']; ?></span>
              <span class="adm-badge <?php echo $row_vehicule['stare_anunt'] == '1' ? 'is-on' : 'is-off'; ?>"><?php echo $row_vehicule['stare_anunt'] == '1' ? 'ON' : 'OFF'; ?></span>
              <?php if ($row_vehicule['oferta_speciala'] == '1') { ?><span class="adm-badge is-offer">OFERTĂ</span><?php } ?>
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
						} while ($row_Recordset1 = $Recordset1->fetch_assoc());
						?>
                    </select></td>
                    <td width="70" align="right">Marcă:</td>
                    <td><select name="marca" id="marca" style="width:120px;">
                      <?php
						do {  
						?>
                      <option value="<?php echo $row_narca['den']?>" <?php if (!(strcmp($row_narca['den'], $row_vehicule['marca']))) {echo "selected=\"selected\"";} ?>><?php echo $row_narca['den']?></option>
                      <?php
						} while ($row_narca = $narca->fetch_assoc());
						?>
                    </select></td>
                    <td width="70" align="right">Model:</td>
                    <td><input name="model" type="text" class="add_vanz" value="<?php echo $row_vehicule['model']; ?>" size="32" style="width:120px;" /></td>
				  </tr>
				  <tr valign="baseline">
                    <td width="70" align="right">Variantă:</td>
                    <td><input name="varianta" type="text" class="add_vanz" value="<?php echo $row_vehicule['varianta']; ?>" size="32" style="width:120px;" /></td>

                    <td width="70" align="right">Cilindri:</td>
                    <td><input name="cilindri" type="text" class="add_vanz" value="<?php echo $row_vehicule['cilindri']; ?>" size="32" style="width:120px;" /></td>

                    <td width="70" align="right">Culoare:</td>
                    <td><input name="culoarea" type="text" class="add_vanz" value="<?php echo $row_vehicule['culoarea']; ?>" size="32" style="width:120px;" /></td>

				  </tr>				  
				  <tr valign="baseline">
                    <td width="70" align="right">Caroserie:</td>
                    <td><input name="caroserie" type="text" class="add_vanz" value="<?php echo $row_vehicule['caroserie']; ?>" size="32" style="width:120px;" /></td>

                    <td width="70" align="right">Nr. uși:</td>
                    <td><input name="nr_usi" type="text" class="add_vanz" value="<?php echo $row_vehicule['nr_usi']; ?>" size="32" style="width:120px;" /></td>

                    <td width="70" align="right">Nr. locuri:</td>
                    <td><input name="nr_locuri" type="text" class="add_vanz" value="<?php echo $row_vehicule['nr_locuri']; ?>" size="32" style="width:120px;" /></td>

				  </tr>				  
				  <tr valign="baseline">
                    <td width="70" align="right">Înmatriculată:</td>
                    <td><input name="inmatriculata" type="text" class="add_vanz" value="<?php echo $row_vehicule['inmatriculata']; ?>" size="32" style="width:120px;" /></td>

                    <td width="70" align="right">Transmisie:</td>
                    <td><input name="transmisie" type="text" class="add_vanz" value="<?php echo $row_vehicule['transmisie']; ?>" size="32" style="width:120px;" /></td>

                    <td width="70" align="right">Tracțiune:</td>
                    <td><input name="tractiune" type="text" class="add_vanz" value="<?php echo $row_vehicule['tractiune']; ?>" size="32" style="width:120px;" /></td>

				  </tr>				  
				  <tr valign="baseline">
                    <td width="70" align="right">Normă Euro:</td>
                    <td><input name="norma_euro" type="text" class="add_vanz" value="<?php echo $row_vehicule['norma_euro']; ?>" size="32" style="width:120px;" /></td>

                    <td width="70" align="right">Tip:</td>
                    <td><input name="tip" type="text" class="add_vanz" value="<?php echo $row_vehicule['tip']; ?>" size="32" style="width:120px;" /></td>

                    <td width="70" align="right">Putere motor:</td>
                    <td><input name="putere_motor" type="text" class="add_vanz" value="<?php echo $row_vehicule['putere_motor']; ?>" size="32" style="width:120px;" /></td>

				  </tr>
				  <tr valign="baseline">
					<td width="70" align="right">Kilometraj / Ore funcționare:</td>
					<td><input name="kilometraj" type="text" class="add_vanz" value="<?php echo $row_vehicule['kilometraj']; ?>" size="32" style="width:120px;" placeholder="Mașini: km, Utilaje: ore" /></td>

					<?php 
					// Check if this is Utilaje to show tonaj field
					$is_utilaje = (!empty($row_vehicule['cat']) && $row_vehicule['cat'] === 'Utilaje');
					if ($is_utilaje): 
					?>
					<td width="70" align="right">Tonaj:</td>
					<td><input name="tonaj" type="text" class="add_vanz" value="<?php echo isset($row_vehicule['tonaj']) ? htmlspecialchars($row_vehicule['tonaj']) : ''; ?>" size="32" style="width:120px;" placeholder="ex: 4tone" /></td>
					<?php else: ?>
					<td width="70" align="right">&nbsp;</td>
					<td>&nbsp;</td>
					<?php endif; ?>

					<td width="70" align="right">Monedă:</td>
					<td><input name="moneda" type="text" class="add_vanz" value="<?php echo $row_vehicule['moneda']; ?>" size="32" style="width:120px;" placeholder="EUR sau RON"/></td>

				  </tr>
				  <tr valign="baseline">
					<td width="70" align="right">Tip deplasare:</td>
					<td>
						<select name="tip_deplasare" class="add_vanz" id="tip_deplasare" style="width:120px;">
							<option value="">-- Selectează --</option>
							<option value="Roti" <?php if (isset($row_vehicule['tip_deplasare']) && $row_vehicule['tip_deplasare'] == 'Roti') {echo "selected=\"selected\"";} ?>>Roti</option>
							<option value="Senile" <?php if (isset($row_vehicule['tip_deplasare']) && $row_vehicule['tip_deplasare'] == 'Senile') {echo "selected=\"selected\"";} ?>>Senile</option>
							<option value="Roti/Senile" <?php if (isset($row_vehicule['tip_deplasare']) && $row_vehicule['tip_deplasare'] == 'Roti/Senile') {echo "selected=\"selected\"";} ?>>Roti/Senile</option>
						</select>
					</td>
					<td colspan="4">&nbsp;</td>
				  </tr>
                  <tr valign="baseline">
                    <td nowrap align="right">Combustibil:</td>
                    <td><select name="combustibil" class="add_vanz" id="combustibil" style="width:120px;">
                      <option value="fara" <?php if (!(strcmp("fara", $row_vehicule['combustibil']))) {echo "selected=\"selected\"";} ?>>fără</option>
                      <option value="motorina" <?php if (!(strcmp("motorina", $row_vehicule['combustibil'])) || !(strcmp("diesel", $row_vehicule['combustibil']))) {echo "selected=\"selected\"";} ?>>motorină</option>
                      <option value="benzina" <?php if (!(strcmp("benzina", $row_vehicule['combustibil']))) {echo "selected=\"selected\"";} ?>>benzină</option>
                    </select></td>
                    <td align="right">An:</td>
                    <td><input name="an" type="text" class="add_vanz" value="<?php echo $row_vehicule['an']; ?>" size="32" style="width:60px;"/></td>
                    <td align="right">Preț:</td>
                    <td><input name="pret" type="text" class="add_vanz" value="<?php echo $row_vehicule['pret']; ?>" size="32" style="width:120px;"/></td>
                  </tr>
				<tr valign="baseline">
	                <td nowrap align="right">Subcategorie:</td>
                    <td><select name="subcateg" id="subcateg" style="width:120px;">
							<option value="">--</option>
						<?php
						while ($row_subcateg = $result_subcateg->fetch_assoc()) {
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
					<td nowrap align="right">Stare CSV:</td>
                    <td>
                    	<select name="stare_csv" class="add_vanz" id="stare_csv" style="width:120px;">
                      		<option value="0" <?php if (!(strcmp("0", $row_vehicule['stare_csv']))) {echo "selected=\"selected\"";} ?>>OFF</option>
                      		<option value="1" <?php if (!(strcmp("1", $row_vehicule['stare_csv']))) {echo "selected=\"selected\"";} ?>>ON</option>
                    	</select>
                    </td>
				</tr>
                  <tr valign="baseline">
                    <td align="right" valign="top" nowrap>Titlu:</td>
                    <td colspan="5"><input name="title" type="text" class="add_vanz" value="<?php echo $row_vehicule['title']; ?>" style="width:480px;"/></td>
                  </tr>
                  <tr valign="baseline">
                    <td align="right" valign="top" nowrap>Descriere:</td>
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
                    <td align="right" valign="top" nowrap>Descriere:</td>
                    <td colspan="5"><textarea name="descriere" cols="60" rows="15" style="width:500px;"><?php echo $row_vehicule['descriere']; ?></textarea></td>
                  </tr>
                  <tr valign="baseline">
                    <td align="right" valign="top" nowrap>Dotari:</td>
                    <td colspan="5"><textarea name="dotari" cols="60" rows="8" style="width:500px;"><?php echo $row_vehicule['dotari']; ?></textarea></td>
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
          </tr><?php } while ($row_vehicule = $vehicule->fetch_assoc()); ?>
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

$Recordset1->free_result();

$narca->free_result();

$vehicule->free_result();
?>
