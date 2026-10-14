var xmlHttpsubcateg = null;
var xmlHttpmarca = null;

function updateSubCateg(str) {
	xmlHttpsubcateg = GetXmlHttpObject();
	if (xmlHttpsubcateg == null) {
		alert ("Browser does not support HTTP Request");
		return;
	}
	var url = "ajax_subcateg.php";
	url = url + "?q=" + str;
	url = url + "&sid=" + Math.random();
	xmlHttpsubcateg.onreadystatechange = stateSubCategChanged; 
	xmlHttpsubcateg.open("GET",url,true);
	xmlHttpsubcateg.send(null);	
}
function stateSubCategChanged() {
	if (xmlHttpsubcateg.readyState == 4 || xmlHttpsubcateg.readyState == "complete")
	{ 
		document.getElementById("subcategHint").innerHTML = xmlHttpsubcateg.responseText;
	}
}

function showUser(str)
{ 
	xmlHttpmarca=GetXmlHttpObject()
	if (xmlHttpmarca==null)
	{
		alert ("Browser does not support HTTP Request")
		return
	}
	var url="getmake.php"
	url=url+"?q="+str
	url=url+"&sid="+Math.random()
	xmlHttpmarca.onreadystatechange=stateChanged 
	xmlHttpmarca.open("GET",url,true)
	xmlHttpmarca.send(null)
}

function stateChanged() 
{ 
	if (xmlHttpmarca.readyState==4 || xmlHttpmarca.readyState=="complete")
	{ 
		document.getElementById("txtHint").innerHTML = xmlHttpmarca.responseText 
	} 
}

function GetXmlHttpObject()
{
var xmlHttp=null;
try
 {
 // Firefox, Opera 8.0+, Safari
 xmlHttp=new XMLHttpRequest();
 }
catch (e)
 {
 //Internet Explorer
 try
  {
  xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
  }
 catch (e)
  {
  xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
 }
return xmlHttp;
}

function selectProv() {
  var form2 = document.getElementById('form2');
  var provEnienta = document.getElementById('prov_enienta');
  if (form2 && provEnienta) {
    form2.action = ( provEnienta.selectedIndex == 2 ) ? "vanzari.php" : "vanzari.php";
  }
}

// Check if element exists before accessing its value
var usersElement = document.getElementById("users");
if (usersElement && !(usersElement.value == 'selectati')) {
	showUser(usersElement.value);
	updateSubCateg(usersElement.value);		
}