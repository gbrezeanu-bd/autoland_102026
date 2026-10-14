<?php require_once('../includes/config.php'); ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
    session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF'] . "?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")) {
    $logoutAction .= "&" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) && ($_GET['doLogout'] == "true")) {
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
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {
    $MM_qsChar = "?";
    $MM_referrer = $_SERVER['PHP_SELF'];
    if (strpos($MM_restrictGoTo, "?"))
        $MM_qsChar = "&";
    if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0)
        $MM_referrer .= "?" . $QUERY_STRING;
    $MM_restrictGoTo = $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
    header("Location: " . $MM_restrictGoTo);
    exit;
}
?>
<?php

function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") {
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

// AICI MERGE CODUL PENTRU FORME
//
// stergere de recenzie
if ((isset($_GET['sterge'])) && ($_GET['sterge'] != "")) {
    $deleteSQL = sprintf("DELETE FROM recenzii2 WHERE id=%s", GetSQLValueString($_GET['sterge'], "int"));

    $Result1 = $connection->query($deleteSQL) or die($connection->error);

    $deleteGoTo = "solicita_recenzii.php";
    // if (isset($_SERVER['QUERY_STRING'])) {
    //  $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    // $deleteGoTo .= $_SERVER['QUERY_STRING'];
    //}
    header(sprintf("Location: %s", $deleteGoTo));
}

// aprobare recenzie
if (isset($_GET['aproba']) && ($_GET['aproba'] !== '')) {
    $aprobaSQL = sprintf("UPDATE recenzii2 SET status='1' WHERE id=%s", GetSQLValueString($_GET['aproba'], 'int'));
    
    $Result1 = $connection->query($aprobaSQL) or die($connection->error);
    
    $aprobaGoTo = "solicita_recenzii.php";
    header(sprintf("Location: %s", $aprobaGoTo));
}
// depublicare recenzie
if (isset($_GET['dezaproba']) && ($_GET['dezaproba'] !== '')) {
    $aprobaSQL = sprintf("UPDATE recenzii2 SET status='0' WHERE id=%s", GetSQLValueString($_GET['dezaproba'], 'int'));
    
    $Result1 = $connection->query($aprobaSQL) or die($connection->error);
    
    $aprobaGoTo = "solicita_recenzii.php";
    header(sprintf("Location: %s", $aprobaGoTo));
}

// insert solicitare pentru recenzie
if (isset($_POST['addSolicitareRecenzie']) && ($_POST['addSolicitareRecenzie'] !== '')) {
    // trebui sa fie email
    if (filter_var($_POST['addSolicitareRecenzie'], FILTER_VALIDATE_EMAIL)) {

        $now_date = date('Y-m-d h:i:s', time());
        $hash = md5($now_date . $_POST['addSolicitareRecenzie']);

        $insertSQL = sprintf("INSERT INTO recenzii2 (data_solicitare, adresa_email, hash) VALUES(%s, %s, %s)", GetSQLValueString($now_date, 'text'), GetSQLValueString($_POST['addSolicitareRecenzie'], 'text'), GetSQLValueString($hash, 'text'));
        $Result1 = $connection->query($insertSQL) or die($connection->error);

        // trebuie sa trimitem email
        // get email template
        $mail_templateSQL = "SELECT email_recenzii FROM texte";
        $mail_template_query = $connection->query($mail_templateSQL) or die($connection->error);
        $mail_template = mysqli_fetch_assoc($mail_template_query)['email_recenzii'];


        $link_completare_recenzie = BASE_PATH . 'completare_recenzie.php?hash=' . $hash;
        $link_completare_recenzie = '<a href="' . $link_completare_recenzie . '">link</a>';

        $mail_template = str_replace('{$link_completare_recenzie}', $link_completare_recenzie, $mail_template);


        $to = $_POST['addSolicitareRecenzie'];
        $subject = "Autoland va solicita impresia dumneavoastra";
        $body = $mail_template;

        sendSMTPEmail($to, $subject, $body);
    }
}
//var_dump($_POST);


$query_Recordset1 = "SELECT * FROM recenzii2";
$query_where='';
// apend filter
if(isset($_GET['filter_by']) && $_GET['filter_by']!==''){
    //switch ($_GET['filter_by'])
    switch ($_GET['filter_by']) {
        case 'toate':
            $query_where='';
            break;
        
        case 'solicitate':
            $query_where=" WHERE status=0 AND hash!=''";
            break;
        
        case 'aprobate':
            $query_where=" WHERE status=1 AND hash=''";
            break;
        
        case 'neaprobate':
            $query_where=" WHERE status=0 AND hash=''";
            break;

        default:
            break;
    }
}
$query_Recordset1.=$query_where;
$Recordset1 = $connection->query($query_Recordset1) or die($connection->error);
$row_Recordset1 = mysqli_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysqli_num_rows($Recordset1);

//var_dump($totalRows_Recordset1, $row_Recordset1);

$headerTitle = 'Solicitare recenzie la achizitie';

$headerScripts = '<script
			  src="https://code.jquery.com/jquery-3.3.1.min.js"
			  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
			  crossorigin="anonymous"></script>' . PHP_EOL;
$headerScripts .= '<link rel="stylesheet" type="text/css" href="./jquery.dynatable.css">' . PHP_EOL;
$headerScripts .= '<script type="text/javascript" charset="utf8" src="./jquery.dynatable.js"></script>' . PHP_EOL;
$headerScripts .= '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">' . PHP_EOL;
$headerScripts .= '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">' . PHP_EOL;
$headerScripts .= '<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>' . PHP_EOL;

$headerScripts .= '<script type="text/javascript">' . PHP_EOL . file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/tehnic/solicita_recenzii.js') . PHP_EOL . '</script>';

include 'header.php';
?><!-- InstanceBeginEditable name="main" -->
<td align="center" valign="middle">
    <h1>Administrare recenzii</h1>
    <div class="pull-left">
        <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
            <label for="addSolicitareRecenzie"><h3>Solicita recenzia:</h3></label>
            <input type="text" name="addSolicitareRecenzie" placeholder="Indrodu adresa de email">
            <input type="submit" value="Trimite solicitare recenzie">
        </form>
    </select>
</div>

<div class="pull-right">
    <form id="filtreaza_recenzii" name="form1" action="<?php echo $editFormAction; ?>">
        <label for="filtruRecenzii"><h3>Filtreaza recenziile:</h3></label>
        <select id="filtruRecenzii" name="filtruRecenzii">
            <option value="toate" <?php echo ((isset($_GET['filter_by'])) && $_GET['filter_by']=='toate')?'selected="selected"':''; ?>>Toate recenziile, indiferent de status</option>
            <option value="solicitate" <?php echo ((isset($_GET['filter_by'])) && $_GET['filter_by']=='solicitate')?'selected="selected"':''; ?>>Recenzii solicitate</option>
            <option value="aprobate" <?php echo ((isset($_GET['filter_by'])) && $_GET['filter_by']=='aprobate')?'selected="selected"':''; ?>>Recenzii aprobate</option>
            <option value="neaprobate" <?php echo ((isset($_GET['filter_by'])) && $_GET['filter_by']=='neaprobate')?'selected="selected"':''; ?>>Recenzii neaprobate</option>
        </select>
        <input type="hidden" name="filter_by" id="filter_by" value="toate">
    </form>
</div>

<div class="clearfix"></div>

<table id="recenzii" class="display table table-bordered table-responsive">
    <thead class="">
        <tr class="bg-primary">
            <th>Id</th>
            <th>Data solicitare</th>
            <th>Adresa de email</th>
            <th data-dynatable-no-sort>Prenume</th>
            <th data-dynatable-no-sort>Nume</th>
            <th>Recenzie</th>
            <th>Data recenzie</th>
            <th>Status</th>
            <th>Actiuni recenzie</th>
        </tr>
    </thead>
    <tbody class="table-striped">
<?php
do {
    ?>
            <tr>
                <td><?php echo $row_Recordset1['id']; ?></td>
            <?php
            $data_dolicitare = (is_null($row_Recordset1['data_solicitare'])) ? '' : date('d.m.Y', strtotime($row_Recordset1['data_solicitare']));
            ?>
                <td><?php echo $data_dolicitare; ?></td>
                <td><?php echo $row_Recordset1['adresa_email']; ?></td>
                <td><?php echo $row_Recordset1['prenume']; ?></td>
                <td><?php echo $row_Recordset1['nume']; ?></td>
                <td><?php echo $row_Recordset1['recenzie']; ?></td>
    <?php
    $data_recenzie = (is_null($row_Recordset1['data_recenzie'])) ? '' : date('d.m.Y', strtotime($row_Recordset1['data_recenzie']));
    ?>
                <td><?php echo $data_recenzie; ?></td>
                <?php
                $status_recenzie = '';
                
                if(is_null($row_Recordset1['recenzie']) || empty($row_Recordset1['recenzie'])){
                    $status_recenzie = 'Recenzie trimisa';
                } else {
                    if($row_Recordset1['status']==0){
                        $status_recenzie = 'Recenzie neaprobata';
                    } else {
                        $status_recenzie = 'Recenzie aprobata';
                    }
                }
                
                
                
                ?>
                <td><?php echo $status_recenzie; ?></td>
                <td>
		    <span><a href="edit_recenzii.php?id=<?php echo $row_Recordset1['id']; ?>"> edit </a></span>
                    <span><a class="<?php echo ($status_recenzie == 'Recenzie neaprobata') ? '' : 'disabled'; ?>" href="solicita_recenzii.php?aproba=<?php echo $row_Recordset1['id']; ?>"> aproba </a>
                    </span><span><a class="<?php echo ($status_recenzie == 'Recenzie aprobata') ? '' : 'disabled'; ?>" href="solicita_recenzii.php?dezaproba=<?php echo $row_Recordset1['id']; ?>"> dezaproba </a></span>
                    <span><a href="solicita_recenzii.php?sterge=<?php echo $row_Recordset1['id']; ?>"> sterge </a></span>
                </td>
            </tr>
    <?php
} while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1));
?>
    </tbody>
        <?php
        do {
            //echo 'id= ' . $row_Recordset1['id'];
        } while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1));
        ?>
</table>

</td>
<!-- InstanceEndEditable -->
</tr>
</table>
</body>
<!-- InstanceEnd --></html>
<?php
mysqli_free_result($Recordset1);
?>

