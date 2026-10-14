<?php require_once('../includes/config.php');
 
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

function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

global $connection;


  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? $connection->real_escape_string($theValue) : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : 'NULL';
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

$for_csv = array (
					'stare_csv'			=> GetSQLValueString($_POST['stare_csv'], "int"),
					'marca'				=> GetSQLValueString($_POST['marca'], "text"),
					'model'				=> GetSQLValueString($_POST['model'], "text"),
					'an'				=> GetSQLValueString($_POST['an'], "int"),
					'combustibil'		=> GetSQLValueString($_POST['combustibil'], "text"),
					'norma_euro'		=> GetSQLValueString($_POST['norma_euro'], "int"),
					'putere_motor'		=> GetSQLValueString($_POST['putere_motor'], "int"),
					'kilometraj'		=> GetSQLValueString($_POST['kilometraj'], "int"),
					'pret'				=> GetSQLValueString($_POST['pret'], "int"),
					'moneda'			=> GetSQLValueString($_POST['moneda'], "text"),
					'descriere'			=> GetSQLValueString($_POST['descriere'], "text"),
					'varianta'			=> GetSQLValueString($_POST['varianta'], "text"),
					'cilindri'			=> GetSQLValueString($_POST['cilindri'], "int"),
					'culoarea'			=> GetSQLValueString($_POST['culoarea'], "text"),
					'caroserie'			=> GetSQLValueString($_POST['caroserie'], "text"),
					'nr_usi'			=> GetSQLValueString($_POST['nr_usi'], "int"),
					'nr_locuri'			=> GetSQLValueString($_POST['nr_locuri'], "int"),
					'inmatriculata'		=> GetSQLValueString($_POST['inmatriculata'], "text"),
					'transmisie'		=> GetSQLValueString($_POST['transmisie'], "text"),
					'tractiune'			=> GetSQLValueString($_POST['tractiune'], "text"),
					'dotari'			=> GetSQLValueString($_POST['dotari'], "text"),
					'url_filme'			=> GetSQLValueString($_POST['url_filme'], "text"),
					'keywords'			=> GetSQLValueString($_POST['keywords'], "text"),
					'oferta_speciala'	=> GetSQLValueString($_POST['oferta_speciala'], "int"),
					'description'		=> GetSQLValueString($_POST['description'], "text"),
					'title'				=> GetSQLValueString($_POST['title'], "text"),
					'stare_anunt'		=> GetSQLValueString($_POST['stare_anunt'], "int"),
					'cat'				=> GetSQLValueString($_POST['cat'], "text"),
					'tip'				=> GetSQLValueString($_POST['tip'], "text"),
					'pret_vechi'		=> GetSQLValueString($_POST['pret_vechi'], "int"),
					'prov_enienta'		=> GetSQLValueString($_POST['prov_enienta'], "text"),
					'subcateg'			=> GetSQLValueString($_POST['subcateg'], "text"),
          'pre_anunt'			=> GetSQLValueString($_POST['pre_anunt'], "text"),
					'tonaj'				=> GetSQLValueString(isset($_POST['tonaj']) ? $_POST['tonaj'] : '', "text"),
					'tip_deplasare'		=> GetSQLValueString(isset($_POST['tip_deplasare']) ? $_POST['tip_deplasare'] : '', "text")
				);

	$fc = '`' . implode('`, `',array_keys($for_csv)) . '`';
	$dc = '\'' . implode('\', \'',$for_csv) . '\'';
        
        $connection->query("SET @@SESSION.sql_mode= '';");
	
        $connection->query("INSERT INTO `for_csv` ($fc) VALUES ($dc)") or die($connection->error);
        
	$qm = $connection->query("SELECT `id` FROM `for_csv` ORDER BY `id` DESC LIMIT 1") or die($connection->error);
	$id = $qm->fetch_assoc()['id'];

  	// Check if this is a Utilaje (equipment) entry
	$is_utilaje = ($for_csv['cat'] === 'Utilaje');
	
	if ($is_utilaje) {
    if (!empty($for_csv['tip'])) {
      $tip = $for_csv['model'] . ' ' . $for_csv['tip'];
    } else {
      $tip = $for_csv['model'];
    }
	} else {
		// Original logic for vehicles
		if (!empty($for_csv['norma_euro']))
		{
			$tip = $for_csv['model'].' '.$for_csv['tip'].' '.round($for_csv['cilindri']/1000, 1).' Euro'.$for_csv['norma_euro'];
		} else {
			$tip = $for_csv['model'].' '.round($for_csv['cilindri']/1000, 1);
		}
	}
	
	$det = array();

	if ($is_utilaje) {
		// Build details for Utilaje
		if (!empty($for_csv['subcateg'])) {$det[] = 'Tip: '.$for_csv['subcateg'];}
		if (!empty($for_csv['an'])) {$det[] = 'An de fabricatie: '.$for_csv['an'];}
		if (!empty($for_csv['combustibil'])) {$det[] = 'Motor: '.$for_csv['combustibil'];}
		if (!empty($for_csv['putere_motor'])) {$det[] = 'Putere: '.$for_csv['putere_motor'].'CP';}
		if (!empty($for_csv['tonaj'])) {$det[] = 'Tonaj: '.$for_csv['tonaj'];}
		if (!empty($for_csv['tip_deplasare'])) {$det[] = 'Tip deplasare: '.$for_csv['tip_deplasare'];}
		if (!empty($for_csv['tractiune'])) {$det[] = 'Tractiune: '.$for_csv['tractiune'];}
		if (!empty($for_csv['kilometraj'])) {$det[] = 'Ore de functionare: '.$for_csv['kilometraj'];}
		// Add caracteristici tehnice from descriere
		if (!empty($for_csv['descriere'])) {
			$desc_lines = explode("\n", $for_csv['descriere']);
			foreach ($desc_lines as $line) {
				$line = trim($line);
				if (!empty($line)) {
					$det[] = $line;
				}
			}
		}
	} else {
		// Original logic for vehicles
		if (!empty($for_csv['combustibil'])) {$det[] = 'Motor '.round($for_csv['cilindri']/1000, 1).'L '.$for_csv['combustibil'].' Euro'.$for_csv['norma_euro'];}
		if (!empty($for_csv['putere_motor'])) {$det[] = 'Putere motor: '.round($for_csv['putere_motor']*0.745699872).' kW / '.$for_csv['putere_motor'].' CP';}
		if (!empty($for_csv['tractiune'])) {$det[] = 'Tractiune: '.$for_csv['tractiune'];}
		if (!empty($for_csv['kilometraj'])) {$det[] = 'Km. parcursi: '.$for_csv['kilometraj'];}
		foreach (explode(',',$for_csv['descriere']) as $val)
		{
			$det[] = $val;
		}
	}
	
	$detalii = '<ul><li><font size="2">'.implode(';</font></li><li><font size="2">',$det).'.</font></li></ul>';
	

$masini = array (
					'description'		=> $for_csv['description'],
					'keywords'			=> $for_csv['keywords'],
					'title'				=> $for_csv['title'],
					'oferta_speciala'	=> $for_csv['oferta_speciala'],
					'stare_anunt'		=> $for_csv['stare_anunt'],
					'id'				=> $id,
					'cat'				=> $for_csv['cat'],
					'marca'				=> $for_csv['marca'],
					'tip'				=> $tip,
					'combustibil'		=> $for_csv['combustibil'],
					'an'				=> $for_csv['an'],
					'detalii'			=> $detalii,
					'pret'				=> $for_csv['pret'],
					'pret_vechi'		=> $for_csv['pret_vechi'],
					'prov_enienta'		=> $for_csv['prov_enienta'],
					'subcateg'			=> $for_csv['subcateg'],
					'pre_anunt'			=> $for_csv['pre_anunt'],
				);

	$fm = '`' . implode('`, `',array_keys($masini)) . '`';
	$dm = '\'' . implode('\', \'',$masini) . '\'';
	$connection->query("INSERT INTO `masini` ($fm) VALUES ($dm)") or die($connection->error);
	
/*	
	echo "<pre>";
		var_dump($for_csv, $masini, "INSERT INTO `masini` ($fm) VALUES ($dm)", "INSERT INTO `for_csv` ($fc) VALUES ($dc)");
	echo "</pre>";
	die();
*/
	$insertGoTo = "vezi_anunturi.php?id=".$id;
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

$headerTitle = 'Vanzari din import :: adaugare vehicul';
$headerScripts = '';
include 'header.php';
?><!-- InstanceBeginEditable name="main" --><td>
      <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
        <table align="center" cellpadding="0" cellspacing="0">
          <tr valign="baseline">
            <td colspan="2" align="left" nowrap bgcolor="#CCCCCC"><strong>Adaugare vehicule in baza de date vanzari</strong> : </td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">Provenienta:</td>
            <td bgcolor="#EEEEEE">
                    	<select name="prov_enienta" class="add_vanz" id="prov_enienta" style="width:120px;">
							<option value="Import">Import</option>
							<option value="Romania">Romania</option>
                    	</select>
			</td>
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
          <tr>
			<td align="right" nowrap bgcolor="#EEEEEE">SubCat:</td>
			<td>
				<select name="subcateg" class="add_vanz" id="subcateg">
				<?php
				$query_subcateg = "SELECT * FROM `subcateg` ORDER BY `den` ASC";
				$result_subcateg = $connection->query($query_subcateg) or die($connection->error);
				
				while ($row_subcateg = $result_subcateg->fetch_assoc())
				{
					echo "<option value=\"{$row_subcateg['den']}\">{$row_subcateg['den']}</option>";
					}
				?>
				</select>
			</td>
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
            <td align="right" nowrap bgcolor="#EEEEEE">Model:</td>
            <td bgcolor="#EEEEEE"><input name="model" type="text" class="add_vanz" value="" size="60"></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">Varianta:</td>
            <td bgcolor="#EEEEEE"><input name="varianta" type="text" class="add_vanz" value="" size="60"></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">Cilindri:</td>
            <td bgcolor="#EEEEEE"><input name="cilindri" type="text" class="add_vanz" value="" size="60"></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">Culoarea:</td>
            <td bgcolor="#EEEEEE"><input name="culoarea" type="text" class="add_vanz" value="" size="60"></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">Caroserie:</td>
            <td bgcolor="#EEEEEE"><input name="caroserie" type="text" class="add_vanz" value="" size="60"></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">Nr usi:</td>
            <td bgcolor="#EEEEEE"><input name="nr_usi" type="text" class="add_vanz" value="" size="60"></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">Nr locuri:</td>
            <td bgcolor="#EEEEEE"><input name="nr_locuri" type="text" class="add_vanz" value="" size="60"></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">Inmatriculata:</td>
            <td bgcolor="#EEEEEE"><input name="inmatriculata" type="text" class="add_vanz" value="" size="60"></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">Transmisie:</td>
            <td bgcolor="#EEEEEE"><input name="transmisie" type="text" class="add_vanz" value="" size="60"></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">Tractiune:</td>
            <td bgcolor="#EEEEEE"><input name="tractiune" type="text" class="add_vanz" value="" size="60"></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">Norma Euro:</td>
            <td bgcolor="#EEEEEE"><input name="norma_euro" type="text" class="add_vanz" value="" size="60"></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">Tip:</td>
            <td bgcolor="#EEEEEE"><input name="tip" type="text" class="add_vanz" value="" size="60"></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">Putere Motor:</td>
            <td bgcolor="#EEEEEE"><input name="putere_motor" type="text" class="add_vanz" value="" size="60"></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">Kilometraj / Ore funcționare:</td>
            <td bgcolor="#EEEEEE"><input name="kilometraj" type="text" class="add_vanz" value="" size="60" placeholder="Mașini: km, Utilaje: ore"></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">Moneda:</td>
            <td bgcolor="#EEEEEE"><input name="moneda" type="text" class="add_vanz" value="EUR" size="60"></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">Titlu:</td>
            <td bgcolor="#EEEEEE"><input name="title" type="text" class="add_vanz" value="" size="60"></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">Descriere:</td>
            <td bgcolor="#EEEEEE"><input name="description" type="text" class="add_vanz" value="" size="60"></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">Cuvinte cheie:</td>
            <td bgcolor="#EEEEEE"><input name="keywords" type="text" class="add_vanz" value="" size="60"></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">Header Anunt:</td>
            <td bgcolor="#EEEEEE"><input name="pre_anunt" type="text" class="add_vanz" value="" size="60"></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">Url Filme:</td>
            <td bgcolor="#EEEEEE"><input name="url_filme" type="text" class="add_vanz" value="" size="60"></td>
          </tr>
		<tr valign="baseline">
			<td align="right" nowrap bgcolor="#EEEEEE">Combustibil:</td>
			<td bgcolor="#EEEEEE">
				<select name="combustibil" class="add_vanz" id="combustibil">
					<option value="fara">fara</option>
					<option value="benzina">benzina</option>
					<option value="motorina">motorina</option>
					<option value="hybrid">hybrid</option>
					<option value="electric">electric</option>					
				</select>            
			</td>
		</tr>
		<tr valign="baseline">
			<td align="right" nowrap bgcolor="#EEEEEE">Stare Anunt:</td>
			<td bgcolor="#EEEEEE">
				<select name="stare_anunt" class="add_vanz" id="combustibil">
					<option value="1">On</option>
					<option value="0">Off</option>
				</select>            
			</td>
		</tr>
		<tr valign="baseline">
			<td align="right" nowrap bgcolor="#EEEEEE">Stare CSV:</td>
			<td bgcolor="#EEEEEE">
				<select name="stare_csv" class="add_vanz" id="combustibil">
					<option value="0">Off</option>
					<option value="1">On</option>
				</select>            
			</td>
		</tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">An:</td>
            <td bgcolor="#EEEEEE"><input name="an" type="text" class="add_vanz" value="" size="60"></td>
          </tr>
<!-- Oferta Speciala -->
                    <td nowrap align="right" nowrap bgcolor="#EEEEEE">Oferta:</td>
                    <td bgcolor="#EEEEEE"><select name="oferta_speciala" class="add_vanz" id="oferta_speciala" style="width:120px;">
                            <?php
                            if(!isset($row_vehicule['oferta_speciala'])){
                                $row_vehicule['oferta_speciala']=NULL;
                            }
                            ?>
                      <option value="0" <?php if (!(strcmp("0", $row_vehicule['oferta_speciala']))) {echo "selected=\"selected\"";} ?>>fara</option>
                      <option value="1" <?php if (!(strcmp("1", $row_vehicule['oferta_speciala']))) {echo "selected=\"selected\"";} ?>>oferta</option>                    
                    </select></td>
          <tr valign="baseline">
            <td align="right" valign="top" nowrap bgcolor="#EEEEEE">Descriere:</td>
            <td bgcolor="#EEEEEE"><textarea name="descriere" cols="50" rows="10"></textarea>            </td>
          </tr>
          <tr valign="baseline">
            <td align="right" valign="top" nowrap bgcolor="#EEEEEE">Dotari:</td>
            <td bgcolor="#EEEEEE"><textarea name="dotari" cols="50" rows="10"></textarea>            </td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">Tonaj:</td>
            <td bgcolor="#EEEEEE"><input name="tonaj" type="text" class="add_vanz" value="" size="60" placeholder="ex: 4tone"></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">Tip deplasare:</td>
            <td bgcolor="#EEEEEE">
              <select name="tip_deplasare" class="add_vanz" id="tip_deplasare" style="width:120px;">
                <option value="">-- Selectează --</option>
                <option value="Roti">Roti</option>
                <option value="Senile">Senile</option>
                <option value="Roti/Senile">Roti/Senile</option>
              </select>
            </td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">Pret:</td>
            <td bgcolor="#EEEEEE"><input name="pret" type="text" class="add_vanz" value="" size="60">
            </td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#EEEEEE">Pret vechi:</td>
            <td bgcolor="#EEEEEE"><input name="pret_vechi" type="text" class="add_vanz" value="" size="60">
            </td>
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
