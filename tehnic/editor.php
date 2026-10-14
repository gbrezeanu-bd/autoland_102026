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

$MM_restrictGoTo = "login.php";
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
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/index.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>Untitled Document</title>
<!-- InstanceEndEditable -->
<link href="style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.style1 {color: #0000FF}
.style3 {color: #009900}
-->
</style>
<link href="style.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" --><!-- InstanceEndEditable -->
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
    <!-- InstanceBeginEditable name="main" --><td>&nbsp;</td><!-- InstanceEndEditable -->
  </tr>
</table>
</body>
<!-- InstanceEnd --></html>

