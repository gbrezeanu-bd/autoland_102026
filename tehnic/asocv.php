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
$query_webpics = "SELECT id,file FROM fotov UNION SELECT id,file FROM fotor";
$webpics = $connection->query($query_webpics) or die($connection->error);
$row_webpics = mysqli_fetch_assoc($webpics);
$totalRows_webpics = mysqli_num_rows($webpics);
do {
  $array_fisiere[] = $row_webpics['file'];
 } while ($row_webpics = mysqli_fetch_assoc($webpics));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/index.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Asociere foto baza de date vanzari</title>
<!-- InstanceEndEditable -->
<link href="style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.style1 {color: #0000FF}
.style3 {color: #009900}
-->
</style>
<link href="style.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<style type="text/css">
<!--
.style4 {
	color: #000000;
	font-weight: bold;
}
-->
</style>
<!-- InstanceEndEditable -->
</head>

<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="250" align="center" bgcolor="#CCCCCC"><h1><span class="style1">AUTO</span><span class="style3">LAND</span></h1></td>
    <td align="center" bgcolor="#CCCCCC"><h3>SECTIUNE ADMINISTRARE </h3></td>
  </tr>
  <tr>
    <td valign="top"><p class="oferta1">&nbsp;</p>
      <p class="oferta1">Continut text :</p>
      <ul class="oferta">
        <li class="lnk"><a href="editor_home.php" target="_self">pagina principala</a></li>
        <li class="lnk"><a href="editor_rent.php">rent-a-car</a></li>
        <li class="lnk"><a href="editor_cond_rent.php">conditii inchiriere </a></li>
        <li class="lnk"><a href="editor_leasing.php" target="_self">leasing</a></li>
        <li class="lnk"><a href="editor_servicii.php" target="_self">servicii</a></li>
        <li class="lnk"><a href="editor_contact.php" target="_self">contact</a> </li>
      </ul>
      <p class="oferta1">Oferte vanzari auto :</p>
      <ul class="oferta">
	    <li class="lnk"><a href="view_vanzare.php">viz. baza de date</a></li>
        <li class="lnk"><a href="add_vanzare.php">adauga oferta</a></li>
      </ul>
      <p class="oferta1">Oferte rent-a-car :</p>
      <ul class="oferta"><li class="lnk"><a href="add_rent.php" target="_self">adauga oferta </a></li>
        <li class="lnk"><a href="view_rent.php" target="_self">viz. baza de date</a></li>
      </ul>      
      <p class="oferta1">Mesagerie :</p>
      <ul class="oferta">
        <li class="lnk"><a href="#">form. contact  </a></li>
        <li class="lnk"><a href="#"> cerere masina vanzare </a></li>
        <li class="lnk"><a href="cereri_rent.php" target="_self">cerere rent-a-car </a></li>
        <li class="lnk"><a href="#">CV depuse </a></li>
      </ul>
      <p class="oferta1">General/Utile :</p>
      <ul class="oferta"><li class="lnk"><a href="cat-auto.php" target="_self">categorii auto </a></li>
        <li class="lnk"><a href="marci-auto.php">marci auto</a></li>
        <li class="lnk"><a href="filtru.php" target="_self">management filtru</a> </li>
        <li class="lnk"><a href="foto_up.php" target="_self">fotoupload</a></li>
        <li class="lnk"><a href="schimbareparola.php" target="_self">schimbare parola</a></li>
        <li class="lnk"><a href="<?php echo $logoutAction ?>">logout</a></li>
      </ul>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
    </td>
    <!-- InstanceBeginEditable name="main" -->
    <td align="center" valign="middle"><table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#eeeeee">
      <tr>
        <td align="center" bgcolor="#cccccc"><span class="style4">Fotografii neasociate </span></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td width="600" align="center"><p align="center">
            <?php
// initialize counter
$count = 0;
$count_total = 0;
// set directory name
$dir = "../foto";
// open directory and parse file list
if (is_dir($dir))
{
if ($dh = opendir($dir))
{
// iterate over file list
// print filenames
while (($filename = readdir($dh)) !== false)
{
if (($filename != ".") && ($filename != ".."))
{
$count_total ++;
if (!(in_array($filename, $array_fisiere))) {
$count ++;
$asocAction='pas.php?idf='.$filename ;
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
     $asocAction .="&". htmlentities($_SERVER['QUERY_STRING']);
} 


echo '<a href="'. $asocAction .'"><img data-no-retina src= "thumb.php?src=foto/'. $filename .'&wmax=133&hmax=100&quality=90&bgcol=FFFFff" hspace="5" vspace="5" border="1"></a></img>'; 
//echo $dir . "/"  "\n";
}
}
}
// close directory
closedir($dh);
}
}
echo "<br> Numar de imagini neasociate: $count";
echo "<br> Numar total de imagini in album: $count_total";
?>
        </p></td>
      </tr>
      <tr>
        <td align="center">&nbsp;</td>
      </tr>
      <tr>
        <td align="center" bgcolor="#FFFFFF">&nbsp;</td>
      </tr>
    </table></td><!-- InstanceEndEditable -->
  </tr>
</table>
</body>
<!-- InstanceEnd --></html>
<?php
mysqli_free_result($webpics);
?>

