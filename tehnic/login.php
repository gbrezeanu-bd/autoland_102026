<?php 
require_once('../includes/config.php'); 
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['textfield'])) {
  $loginUsername=$_POST['textfield'];
  $password=$_POST['textfield2'];
  $MM_fldUserAuthorization = "";
  $MM_redirectLoginSuccess = "index.php";
  $MM_redirectLoginFailed = "login.php";
  $MM_redirecttoReferrer = true;                     
  
  // $LoginRS__query=sprintf("SELECT user, pass FROM luser WHERE user='%s' AND pass='%s'",
  // get_magic_quotes_gpc() ? $loginUsername : addslashes($loginUsername), get_magic_quotes_gpc() ? $password : addslashes($password)); 

  // PHP 8+ compatible - magic_quotes_gpc removed
  $loginUsername_safe = $connection->real_escape_string($loginUsername);
  $password_safe = $connection->real_escape_string($password);
  
  $LoginRS__query=sprintf("SELECT user, pass FROM luser WHERE user='%s' AND pass='%s'",
    $loginUsername_safe, $password_safe); 
   
  $LoginRS = $connection->query($LoginRS__query) or die($connection->error);
  $loginFoundUser = mysqli_num_rows($LoginRS);
  if ($loginFoundUser) {
     $loginStrGroup = "";
    
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	      

    if (isset($_SESSION['PrevUrl']) && true) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Log-in</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.style1 {color: #0000FF}
.style3 {color: #009900}
-->
</style>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center" bgcolor="#CCCCCC"><h1><span class="style1">AUTO</span><span class="style3">LAND</span></h1>      <h3>SECTIUNE ADMINISTRARE </h3></td>
  </tr>
  <tr>
    <td align="center" valign="top"><form id="form1" name="form1" method="post" action="<?php echo $loginFormAction; ?>">
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <table width="250" border="0" cellpadding="0" cellspacing="0" bgcolor="#eeeeee" style="border: 1px solif black;">
        <tr>
          <td colspan="2" bgcolor="#CCCCCC"><strong>Login</strong></td>
          </tr>
        <tr>
          <td align="right">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        <tr>
          <td align="right">Utilizator : </td>
            <td><input type="text" name="textfield" /></td>
          </tr>
        <tr>
          <td align="right">Parola : </td>
            <td><input type="password" name="textfield2" /></td>
          </tr>
        <tr>
          <td colspan="2">&nbsp;</td>
          </tr>
        <tr>
          <td colspan="2" align="center"><input type="submit" name="Submit" value="Login" /></td>
          </tr>
        <tr>
          <td colspan="2">&nbsp;</td>
          </tr>
        </table>
    </form></td>
  </tr>
</table>
</body>
</html>
