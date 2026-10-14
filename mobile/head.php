<?php 

// verde LAND #008000 / 0,128,0
// albastru AUTO #0000ff / 0,0,255

// include 'protect.php';

// $title=$row_vanzare['title'];
// $description=$row_vanzare['description'];
// $keywords=$row_vanzare['keywords'];

$title="";
$description="";
$keywords="";

echo "
<!DOCTYPE HTML>
<html>
<head>
<meta name=\"viewport\" content=\"width=device-width\">

";
// Titlul paginii - Begin
if ($title=="")
	{
		print "<title>Auto Second hand - PARC AUTO - Leasing si vanzari auto second hand - AutoLand Romania</title> \n";
	}
else
	{
		print "<title>".$title." Auto Second hand - PARC AUTO - Leasing si vanzari auto second hand - AutoLand Romania</title> \n";
	}
// Titlul paginii - End

// Descrierea paginii - Begin
if ($description=="")
	{
		print "<meta name=\"description\" content=\"Autoland este importator direct care furnizeaza diferite marci si modele de: autoturisme, autoutilitare, autocamioane, cap tractor, remorci, semiremorci, buldozere, excavatoare, motostivuitoare, incarcatoare frontale, macarale, dricuri, rulote, dormitoare mobile, atv, scutere\" /> \n";
	}
else
	{
		print "<meta name=\"description\" content=\"".$description."\" /> \n";
	}
// Descrierea paginii - Eng

// Cuvintele cheie ale paginii - Begin
if ($keywords=="")
	{
		print "<meta name=\"keywords\" content=\"autoland,vanzari auto,vanzari auto Romania,vanzari auto import,auto Romania,vanzari masini,masini second hand,vanzari masini noi,parcuri auto,dealeri auto,piata auto Romania,piata auto online\" /> \n";
	}
else
	{
		print "<meta name=\"keywords\" content=\"".$keywords."\" /> \n";
	}
// Cuvintele cheie ale paginii - End	

if (strcmp($pagina_curenta, "ANUNTURI") === 0)
	{
echo "
<style>
body {
	background: #42a2ce;
	font-size: 14px;
	font-family: Helvetica, 'Arial', sans-serif;
	-webkit-font-smoothing: antialiased;
	text-rendering: optimizeLegibility;
	height: auto;
}
</style>
";
	}
if (strcmp($pagina_curenta, "INDEX") === 0)
	{
echo "
	
";
	}	
if (strcmp($pagina_curenta, "CONTACT") === 0)
	{
echo "
<style>
body {color:black;}
</style>
";
	}	
if (strcmp($pagina_curenta, "FINANTARE") === 0)
	{
echo "

";
	}	

echo "
	<link rel=\"stylesheet\" href=\"css/slicknav.css\">
";


?>





<link rel="stylesheet" href="css/flexslider.css" type="text/css" media="screen" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.6.2/modernizr.min.js"></script>
<script type="text/javascript" src="https://autolandromania.ro/js/selectmake.js"></script>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

<script defer src="js/jquery.flexslider.js"></script>
<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="js/libs/jquery-1.7.min.js">\x3C/script>')</script>
<script type="text/javascript">
// Can also be used with $(document).ready()
$(window).load(function() {
  $('.flexslider').flexslider({
    animation: "slide"
  });
$('#teste').hide();
});
</script>
<script type="text/javascript">
	$(document).ready(function() {
		$('anunt').append('<p id="teste">Va rugam asteptati, pozele se incarca.</p>');
	});
</script>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/demo.css">

<style type="text/css">
.paginate {
	font-family: Arial, Helvetica, sans-serif;
	font-size: .7em;
}

a.paginate {
	border: 1px solid #000080;
	padding: 2px 6px 2px 6px;
	text-decoration: none;
	color: #000080;
}


a.paginate:hover {
	background-color: #000080;
	color: #FFF;
	text-decoration: underline;
}

a.current {
	border: 1px solid #000080;
	font: bold .7em Arial,Helvetica,sans-serif;
	padding: 2px 6px 2px 6px;
	cursor: default;
	background:#000080;
	color: #FFF;
	text-decoration: none;
}

span.inactive {
	border: 1px solid #999;
	font-family: Arial, Helvetica, sans-serif;
	font-size: .7em;
	padding: 2px 6px 2px 6px;
	color: #999;
	cursor: default;
}

</style>
<script>
function hilite(elem)
{
	elem.style.background = '#FFC';
}

function lowlite(elem)
{
	elem.style.background = '';
}
</script>


</head>
<body>