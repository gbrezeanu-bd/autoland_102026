<?php
require_once('../includes/config.php');

//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
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

$statusMessage = '';
if ( isset($_POST['Submit']) ) {
	error_log(var_export($_SESSION,true));
	$newpass = (!get_magic_quotes_gpc()) ? addslashes($_POST['newpass']) : $_POST['newpass'];	
	$currentpass = (!get_magic_quotes_gpc()) ? addslashes($_POST['currentpass']) : $_POST['currentpass'];

	$sql = "UPDATE luser SET pass='".$newpass."' ".
			" WHERE user='".$_SESSION['MM_Username']."' AND pass='".$currentpass."'";
	$result = $connection->query($sql) or die($connection->error);

	if ( mysqli_affected_rows($autoland) == 1 ) {
		$statusMessage = '<span style="color:green">Parola a fost schimbata</span>';
	} else {
		$statusMessage = '<span style="color:red">Parola nu a putut fi schimbata. Parola curenta incorecta!</span>';
	}
}
$headerTitle = 'Schimbare parola';
include 'header.php';
?><!-- InstanceBeginEditable name="main" -->
    <td valign="middle"><table width="60%" border="0" align="center" cellpadding="0" cellspacing="0">
<?php
if ( !empty($statusMessage) ) {
	echo '<tr><td align="center">'.$statusMessage.'</td></tr><tr><td>&nbsp;</td></tr>';
}
?>
      <tr>
        <td align="left" bgcolor="#CCCCCC"><strong>Schimbare parola</strong></td>
      </tr>
      <tr>
        <td align="center" bgcolor="#eeeeee">&nbsp;</td>
      </tr>
      <tr>
        <td align="center" bgcolor="#eeeeee"><form id="schimbareparola" name="schimbareparola" method="post" action="schimbareparola.php">
		  <table>
			<tr>
			<td align="left">Parola curenta</td><td><input type="password" name="currentpass" value=""/></td>
			</tr>
			<tr>
			<td align="left">Parola noua</td><td><input type="password" name="newpass" value="" /></td>
			</tr>
			<tr>
			<td colspan="2" align="center"><input type="submit" name="Submit" value="Schimba parola" /></td>
			</tr>
		  </table>
        </form></td>
      </tr>
    </table></td><!-- InstanceEndEditable -->
  </tr>
</table>
</body>
<!-- InstanceEnd --></html>