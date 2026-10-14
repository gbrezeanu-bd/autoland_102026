<?php 

require_once('../includes/config.php');

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

$headerTitle = 'Vizualizare recenzii';
include 'header2.php';

if (isset($_GET['del']) && empty($_GET['del']) === false)
{
    $id = filter_var($_GET['del'], FILTER_VALIDATE_INT);
    $connection->query("DELETE FROM `recenzii` WHERE `recenzii`.`id` = '$id'") or die ($connection->error);
    unset($_GET['del']);
    echo "<div class=\"alert alert-success\"><strong>Succes!</strong> recenzia cu id $id a fost stearsa, singura copie care mai exista este cea primita pe email.</div>";
}

$co = array();
$c = "";
if ($_GET['online'] == 'online')
{
    $co[] = "`recenzii`.`online`='1'";
    $_GET['page'] = 1;
} 
if ($_GET['online'] == 'offline')
{
    $co[] = "`recenzii`.`online`='0'";
    $_GET['page'] = 1;
}
if ($_GET['review'] == 'reviewdone')
{
    $co[] = "`recenzii`.`review`='1'";
    $_GET['page'] = 1;
} 
if ($_GET['review'] == 'toreview')
{
    $co[] = "`recenzii`.`review`='0'";
    $_GET['page'] = 1;
}
if (count($co) > 1)
{
    $c = "WHERE ".implode(' AND ',$co);
} else {
    if (!empty($co[0]))
    {
        $c = "WHERE ".$co[0];
    }
}

?>
<td valign="top">
    <form id="view_recenzii" action="" method="get">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-heading"><h3>Filtrele ce le poti aplica pe pagina asta</h3></div>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <label for="online">Selectezi daca vrei sa filtrezi dupa recenziile care sunt online sau offline</label>
                                <fieldset id="online">
                                    <label class="checkbox-inline"> <input type="radio" value="online" name="online" <?php if (isset($_GET['online']) && $_GET['online'] == "online"){ echo "checked"; } ?>> Online</label>
                                    <label class="checkbox-inline"> <input type="radio" value="offline" name="online" <?php if (isset($_GET['online']) && $_GET['online'] == "offline"){ echo "checked"; } ?>> Offline</label>
                                </fieldset>
                            </li>
                            <li class="list-group-item">
                                <label for="online">Selectezi daca vrei sa filtrezi dupa recenziile care ai de revizuit sau au fost revizuite deja</label>
                                <fieldset id="review">
                                    <label class="checkbox-inline"> <input type="radio" value="reviewdone" name="review" <?php if (isset($_GET['review']) && $_GET['review'] == "reviewdone"){ echo "checked"; } ?>> Review done</label>
                                    <label class="checkbox-inline"> <input type="radio" value="toreview" name="review" <?php if (isset($_GET['review']) && $_GET['review'] == "toreview"){ echo "checked"; } ?>> To Review</label>            
                                </fieldset>
                            </li>
                            <li class="list-group-item">
                                <label for="all">Checked by default, self explanatory button</label><br />
                                <label class="checkbox-inline"><input id="all" type="checkbox" value="" name="all" <?php if (isset($_GET['all'])){ echo "checked"; } ?>> All</label>
                            </li>
                        </ul>
                        <?php 
                        if (isset($_GET['page'])) {
                            echo "<input type=\"hidden\" name=\"page\" value=\"{$_GET['page']}\">";    
                        }
                        ?>
                        <button type="submit" class="btn btn-primary" name="submit">Aplica</button>
                        <button type="button" class="btn btn-primary" id="reset">Reseteaza toate filtrele</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
<br />
<?php

$q = "SELECT `recenzii`.`nume`,`recenzii`.`online`,`recenzii`.`review`,`recenzii`.`time`,`recenzii`.`prenume`,`account_county`.`name`,`recenzii`.`titlu`,`recenzii`.`opinia` FROM `recenzii` LEFT JOIN `account_county` ON recenzii.judet = account_county.id $c ";
$o = "`recenzii`.`time` DESC";
$l = 10;
$t = "view_recenzii.php";
$pag = pagination($q, $o, $l, $t);
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php echo $pag['pag']; ?>
    <?php

        $a = $connection->query("SELECT `recenzii`.`id`,`recenzii`.`nume`,`recenzii`.`online`,`recenzii`.`review`,`recenzii`.`time`,`recenzii`.`prenume`,`account_county`.`name`,`recenzii`.`titlu`,`recenzii`.`opinia` FROM `recenzii` LEFT JOIN `account_county` ON recenzii.judet = account_county.id $c ORDER BY `recenzii`.`time` DESC LIMIT {$pag['start']},{$pag['limit']}") or die($connection->error);
        $url = "";
        foreach($_GET as $key => $value)
        {
            $url .= "&$key=$value";
        }
        while($r = $a->fetch_assoc())
        {
            if ($r['online'] == 1){ $s = '<span class="alert-success">online</span>'; } else { $s = '<span class="alert-danger">offline</span>';}
            if ($r['review'] == 0){ $rev = '<span class="alert-danger">to review</span>'; } else { $rev = '<span class="alert-success">review done</span>';}
    echo "        
    		<div class=\"testiminial-block\">
    			<div class=\"row\">
    				<div class=\"col-md-12 testimonial-content\">
    					<h4>
    					    id = {$r['id']}&nbsp;status = $s&nbsp;review = $rev
    					    <a href=\"edit_recenzii.php?id={$r['id']}\">{$r['titlu']}</a>&nbsp;&nbsp;&nbsp;
    					    <a href=\"edit_recenzii.php?id={$r['id']}\" class=\"btn btn-success\" role=\"button\">Editeaza</a>&nbsp;&nbsp;&nbsp;
    					    <a href=\"view_recenzii.php?del={$r['id']}$url\" class=\"btn btn-danger\" role=\"button\">Delete</a>
    					</h4>
    					<div class=\"testimonial-author\">
    						{$r['nume']} ".substr ($r['prenume'],0,1).".<span>din {$r[name]}</span> {$r[time]}
    					</div><br />
    					<p>{$r['opinia']}</p>
    				</div>
    			</div>
    		</div>
    	";
        }
        
    ?>
            <?php echo $pag['pag']; ?>		
    	</div>		
    </div>
</div>
</td>
  </tr>
</table>
<script>
    $(document).ready( function() {
        $('input:radio').change(function(){
            if (this.checked)
            {
                $('#all').prop('checked', false);
            }
        });
        $('#reset').click(function(){
                $('#all').prop('checked', false);
                $('input:radio').prop('checked', false);
        });
        
    });
</script>
</body>
</html>