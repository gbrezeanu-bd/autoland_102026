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
                                                 
$query_continut = "SELECT * FROM statistici";
$continut = $connection->query($query_continut) or die($connection->error);
$row_continut = mysqli_fetch_assoc($continut);
$totalRows_continut = mysqli_num_rows($continut);

$headerTitle = 'Pagina de statistici pentru masinile vizitate';

include 'header.php';
?><!-- InstanceBeginEditable name="main" -->
    <td>&nbsp;

<table border=1>
	<tr>
		<td>Masina</td>
		<td>numar hituri</td>
	</tr>	

<?php
// imi da toate id-urile unice pt masini
function getAllCarsId()
{
	global $connection;
	$query_continut = "SELECT DISTINCT  `masini_id` FROM  `statistici` where timestamp BETWEEN SUBDATE(CURDATE(), INTERVAL 12 MONTH) AND NOW() ORDER BY  `masini_id` ASC";
	$continut = $connection->query($query_continut) or die($connection->error);
	while ($row_continut=mysqli_fetch_assoc($continut))
	{
		$cars[] = $row_continut['masini_id'];
	}	
	return $cars;
}

// imi da statisticile pentru un singur id de masina
function getStatisticsCarById($carid)
{
	global $connection;
	$stats = array ();
	$query_continut = "SELECT * FROM `statistici` where `masini_id`='$carid' and timestamp BETWEEN SUBDATE(CURDATE(), INTERVAL 12 MONTH) AND NOW()";
	$continut = $connection->query($query_continut) or die($connection->error);
	while ($row_continut=mysqli_fetch_assoc($continut))
	{
		$stats[] = $row_continut['timestamp'];
	}	
	return $stats;
}
// imi aduce informatiile despre o masina anume
function getCarInfo($carid)
{
	global $connection;
	$query_continut = "SELECT `masini`.`marca`, `masini`.`tip`, `fotov`.`file` FROM `masini` RIGHT JOIN `fotov` ON `masini`.`id`=`fotov`.`oferta`  WHERE `masini`.`id`='$carid' limit 1";
	$continut = $connection->query($query_continut) or die($connection->error);	
	return mysqli_fetch_assoc($continut);	
}
//

$cars = getAllCarsId();
foreach ($cars as $car) 
{
	$carinfo = getCarInfo($car);
	if ($carinfo !== false)
	{
		$cars2[$car] = count(getStatisticsCarById($car));
	}
}
arsort($cars2);

foreach ($cars2 as $key => $car) 
{
	$carinfo = getCarInfo($key);
	echo "
		<tr>
			<td><a href='".BASE_PATH."vanzari_detail.php?id={$key}&pic={$carinfo['file']}' target='_blank'>{$carinfo['marca']} {$carinfo['tip']}</a></td>
			<td>$car</td>
		</tr>	
		";
		
}

?>
</table>

    <p>&nbsp;</p></td>
    <!-- InstanceEndEditable -->
  </tr>
</table>
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($continut);
?>



