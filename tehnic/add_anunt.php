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
      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="adm-form-page">
        <h1 class="adm-page-title">Adaugă ofertă</h1>

        <fieldset class="adm-sec">
          <legend>Clasificare</legend>
          <div class="adm-grid">
            <label class="adm-field"><span>Proveniență</span>
                    	<select name="prov_enienta" class="add_vanz" id="prov_enienta" style="width:120px;">
							<option value="Import">Import</option>
							<option value="Romania">România</option>
                    	</select>
            </label>
            <label class="adm-field"><span>Categorie</span>
            <select name="cat" class="add_vanz" id="cat">
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
            </select>
            </label>
            <label class="adm-field"><span>Subcategorie</span>
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
            </label>
            <label class="adm-field"><span>Marcă</span>
            <select name="marca" class="add_vanz" id="marca">
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
            </select>
            </label>
            <label class="adm-field"><span>Model</span><input name="model" type="text" class="add_vanz" value="" size="60"></label>
            <label class="adm-field"><span>Variantă</span><input name="varianta" type="text" class="add_vanz" value="" size="60"></label>
            <label class="adm-field"><span>Tip</span><input name="tip" type="text" class="add_vanz" value="" size="60"></label>
            <label class="adm-field"><span>An</span><input name="an" type="text" class="add_vanz" value="" size="60"></label>
          </div>
        </fieldset>

        <fieldset class="adm-sec">
          <legend>Date tehnice</legend>
          <div class="adm-grid">
            <label class="adm-field"><span>Combustibil</span>
				<select name="combustibil" class="add_vanz" id="combustibil">
					<option value="fara">fără</option>
					<option value="benzina">benzină</option>
					<option value="motorina">motorină</option>
					<option value="hybrid">hybrid</option>
					<option value="electric">electric</option>
				</select>
            </label>
            <label class="adm-field"><span>Cilindri</span><input name="cilindri" type="text" class="add_vanz" value="" size="60"></label>
            <label class="adm-field"><span>Putere motor</span><input name="putere_motor" type="text" class="add_vanz" value="" size="60"></label>
            <label class="adm-field"><span>Normă Euro</span><input name="norma_euro" type="text" class="add_vanz" value="" size="60"></label>
            <label class="adm-field"><span>Kilometraj / Ore funcționare</span><input name="kilometraj" type="text" class="add_vanz" value="" size="60" placeholder="Mașini: km, Utilaje: ore"></label>
            <label class="adm-field"><span>Transmisie</span><input name="transmisie" type="text" class="add_vanz" value="" size="60"></label>
            <label class="adm-field"><span>Tracțiune</span><input name="tractiune" type="text" class="add_vanz" value="" size="60"></label>
            <label class="adm-field"><span>Caroserie</span><input name="caroserie" type="text" class="add_vanz" value="" size="60"></label>
            <label class="adm-field"><span>Culoare</span><input name="culoarea" type="text" class="add_vanz" value="" size="60"></label>
            <label class="adm-field"><span>Nr. uși</span><input name="nr_usi" type="text" class="add_vanz" value="" size="60"></label>
            <label class="adm-field"><span>Nr. locuri</span><input name="nr_locuri" type="text" class="add_vanz" value="" size="60"></label>
            <label class="adm-field"><span>Înmatriculată</span><input name="inmatriculata" type="text" class="add_vanz" value="" size="60"></label>
            <label class="adm-field"><span>Tonaj <em>(utilaje)</em></span><input name="tonaj" type="text" class="add_vanz" value="" size="60" placeholder="ex: 4tone"></label>
            <label class="adm-field"><span>Tip deplasare <em>(utilaje)</em></span>
              <select name="tip_deplasare" class="add_vanz" id="tip_deplasare" style="width:120px;">
                <option value="">-- Selectează --</option>
                <option value="Roti">Roti</option>
                <option value="Senile">Senile</option>
                <option value="Roti/Senile">Roti/Senile</option>
              </select>
            </label>
          </div>
        </fieldset>

        <fieldset class="adm-sec">
          <legend>Preț și publicare</legend>
          <div class="adm-grid">
            <label class="adm-field"><span>Preț</span><input name="pret" type="text" class="add_vanz" value="" size="60"></label>
            <label class="adm-field"><span>Preț vechi</span><input name="pret_vechi" type="text" class="add_vanz" value="" size="60"></label>
            <label class="adm-field"><span>Monedă</span><input name="moneda" type="text" class="add_vanz" value="EUR" size="60"></label>
            <label class="adm-field"><span>Ofertă specială</span>
              <select name="oferta_speciala" class="add_vanz" id="oferta_speciala" style="width:120px;">
                            <?php
                            if(!isset($row_vehicule['oferta_speciala'])){
                                $row_vehicule['oferta_speciala']=NULL;
                            }
                            ?>
                      <option value="0" <?php if (!(strcmp("0", $row_vehicule['oferta_speciala']))) {echo "selected=\"selected\"";} ?>>fără</option>
                      <option value="1" <?php if (!(strcmp("1", $row_vehicule['oferta_speciala']))) {echo "selected=\"selected\"";} ?>>ofertă</option>
              </select>
            </label>
            <label class="adm-field"><span>Stare anunț</span>
				<select name="stare_anunt" class="add_vanz" id="combustibil">
					<option value="1">On</option>
					<option value="0">Off</option>
				</select>
            </label>
            <label class="adm-field"><span>Stare CSV</span>
				<select name="stare_csv" class="add_vanz" id="combustibil">
					<option value="0">Off</option>
					<option value="1">On</option>
				</select>
            </label>
          </div>
        </fieldset>

        <fieldset class="adm-sec">
          <legend>Texte anunț și SEO</legend>
          <div class="adm-grid adm-grid-lat">
            <label class="adm-field"><span>Titlu</span><input name="title" type="text" class="add_vanz" value="" size="60"></label>
            <label class="adm-field"><span>Descriere (meta)</span><input name="description" type="text" class="add_vanz" value="" size="60"></label>
            <label class="adm-field"><span>Cuvinte cheie</span><input name="keywords" type="text" class="add_vanz" value="" size="60"></label>
            <label class="adm-field"><span>Header anunț</span><input name="pre_anunt" type="text" class="add_vanz" value="" size="60"></label>
            <label class="adm-field"><span>Url filme</span><input name="url_filme" type="text" class="add_vanz" value="" size="60"></label>
          </div>
          <div class="adm-grid adm-grid-2">
            <label class="adm-field"><span>Descriere</span><textarea name="descriere" cols="50" rows="10"></textarea></label>
            <label class="adm-field"><span>Dotări</span><textarea name="dotari" cols="50" rows="10"></textarea></label>
          </div>
        </fieldset>

        <div class="adm-form-actions">
          <input type="submit" value="Adaugă înregistrarea">
        </div>
        <input type="hidden" name="id" value="">
        <input type="hidden" name="MM_insert" value="form1">
      </form>
    </td><!-- InstanceEndEditable -->
  </tr>
</table>
</body>
<!-- InstanceEnd --></html>
<?php
mysqli_free_result($categorie);


?>
