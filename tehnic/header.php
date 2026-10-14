<?php
error_reporting(0);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/index.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!-- InstanceBeginEditable name="doctitle" -->
<title><?=$headerTitle?></title>
<?=$headerScripts?>
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
<script language="JavaScript" type="text/javascript">
function checkDelete(){
    return confirm('Are you sure?');
}
</script>
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
        <li class="lnk"><a href="editor_tandc.php" target="_self">politica confidentialitate</a></li>
        <li class="lnk"><a href="editor_rent.php">rent-a-car</a></li>
        <li class="lnk"><a href="editor_cond_rent.php">conditii inchiriere </a></li>
        <li class="lnk"><a href="editor_leasing.php" target="_self">leasing</a></li>
		<li class="lnk"><a href="editor_asistenta_rutiera.php" target="_self">asistenta rutiera</a></li>
		<li class="lnk"><a href="editor_garantie.php" target="_self">garantie</a></li>
		<li class="lnk"><a href="editor_inmatriculari.php" target="_self">inmatriculari</a></li>
        <li class="lnk"><a href="editor_contact.php" target="_self">contact</a> </li>
        <li class="lnk"><a href="editor_email_recenzii.php" target="_self">email recenzii</a> </li>
      </ul>
	  <p class="oferta1">Vizualizare/Adaugare Anunturi :</p>
	  <ul class="oferta">
		<li class="lnk"><a href="add_anunt.php" target="_self">Adauga oferta</a></li>
		<li class="lnk"><a href="view_vanzare2.php" target="_self">View Vanzare</a></li>
		<li class="lnk"><a href="view_vanzare_new.php" target="_self">View Vanzare New</a></li>
	  </ul>
  
      <p class="oferta1">Oferte speciala :</p>
      <ul class="oferta">
        <li class="lnk"><a href="view_vanzare_oferta.php" target="_self">Viz. oferta speciala</a></li>
      </ul>

      <p class="oferta1">Articole Blog :</p>
      <ul class="oferta">
        <li class="lnk"><a href="articole_add.php" target="_self">Adauga articol</a></li>
        <li class="lnk"><a href="articole_manage.php" target="_self">Vezi / Sterge articole</a></li>
      </ul>

      <p class="oferta1">General/Utile :</p>
      <ul class="oferta">
          <li class="lnk"><a href="solicita_recenzii.php" target="_self">Administrare recenzii</a></li>
<!--        <li class="lnk"><a href="view_recenzii.php" target="_self">Vezi Recenzii</a></li>-->
        <li class="lnk"><a href="statistici.php" target="_self">Statistici</a></li>
        <li class="lnk"><a href="cat-auto.php" target="_self">categorii auto </a></li>      
        <li class="lnk"><a href="marci-auto.php">marci auto</a></li>
        <li class="lnk"><a href="filtru.php" target="_self">management filtru marci</a> </li>
        <li class="lnk"><a href="subcateg-auto.php">subcategorii auto</a></li>
        <li class="lnk"><a href="subcateg-filtru.php" target="_self">management filtru subcategorii</a> </li>
      </ul>
      <p class="oferta1">Rapoarte de rulat manual :</p>
      <ul class="oferta">
        <li class="lnk"><a href="raport_recenzii.php" target="_self">Raport recenzii</a></li>
      </ul>
      <p class="oferta1">Setari</p>
      <ul class="oferta">
        <li class="lnk"><a href="schimbareparola.php" target="_self">schimbare parola</a></li>  
        <li class="lnk"><a href="<?php echo $logoutAction ?>">logout</a></li>
      </ul>      
      <p>&nbsp;</p>
      <p>&nbsp;</p>
    </td>