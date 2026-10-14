<?php 

require_once('../Connections/autoland.php');
require_once('../classes/class.phpmailer.php');

$q = $conn->query("SELECT `recenzii`.*,`account_county`.`name` FROM `recenzii` LEFT JOIN `account_county` ON `recenzii`.`judet` = `account_county`.`id` WHERE `recenzii`.`review` = '0' order by `recenzii`.`id` desc");

if (!$q)
{
    die();
}

    $total_to_review = $q->num_rows;  
//    $r = $q->fetch_assoc();  

if ($total_to_review > 0)
{
    $to_review = array();
    while ($r = $q->fetch_assoc())
    {
        $to_review[] = "Recenzia cu id = <b><a href=\"https://www.autolandromania.ro/tehnic/edit_recenzii.php?id={$r['id']}\">{$r['id']}</a></b> de la <b>{$r['nume']} {$r['prenume']}</b> din judetul <b>{$r['name']}</b>";
    }
    
    if (count($to_review) > 1)
    {
        $forbody = implode('<br />', $to_review);
    } else {
        $forbody = $to_review[0]."<br />";
    }
    
        $to="auto@autolandromania.ro";
//        $to="apostolacheac@gmail.com";
        $subject= "Recenzii to review $total_to_review";
        $body = "
            Salut, <br /><br />
            
            Eu sunt 'Robotelul de servici' care trimite raportul pentru recenzii de review<br /><br />
            
            Sunt un total de $total_to_review recenzii pentru review si le aveti listate mai jos si sunt ordonate descrescator:<br /><br />
            
            $forbody
            
            <br /><br />
            --<br />
            Robotelul de servici de pe autolandromania.ro
        ";
        $body = wordwrap($body, 65);
        $headers = 'From: autoland@autolandromania.ro' . "\r\n" .
            'Reply-To: office@autolanromania.ro' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        
        $mail = new PHPMailer();
        $mail->From = 'autoland@autolandromania.ro';
        $mail->FromName = 'Autoland Romania';
        $mail->Subject = $subject;
        $mail->MsgHTML($body);
        $mail->AddAddress($to,$to);
        $mail->Send();
}