function XHConn()
{
	var xmlhttp, bComplete = false;
	try { xmlhttp = new ActiveXObject("Msxml2.XMLHTTP"); }
	catch (e) { try { xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); }
	catch (e) { try { xmlhttp = new XMLHttpRequest(); }
	catch (e) { xmlhttp = false; }}}
	if (!xmlhttp) return null;
	this.connect = function(sURL, sMethod, sVars, fnDone)
	{
	  if (!xmlhttp) return false;
	  bComplete = false;
	  sMethod = sMethod.toUpperCase();
  
	  try
	  {
		if (sMethod == "GET")
		{
		  xmlhttp.open(sMethod, sURL+"?"+sVars, true);
		  sVars = "";
		}
		else
		{
		  xmlhttp.open(sMethod, sURL, true);
		  xmlhttp.setRequestHeader("Method", "POST "+sURL+" HTTP/1.1");
		  xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		}
		xmlhttp.onreadystatechange = function()
		{
		  if (xmlhttp.readyState == 4 && !bComplete)
		  {
			bComplete = true;
			fnDone(xmlhttp);
		  }
		};
		xmlhttp.send(sVars);
	  }
	  catch(z) { return false; }
	  return true;
	};
 
	return this;
}

//---- AJAX REQUEST for deleting Images
function delImage(imgId)
{
    delObj("foto_"+imgId);
	var ajaxConn = new XHConn();                  
	var sParams = "imgId="+imgId;                
	ajaxConn.connect("imgdel.php", "GET", sParams, processDel);   
}

function processDel(objReturn)
{
	alert(objReturn.responseText);
}

function delObj(objId)
{
	var obj2del = document.getElementById(objId);
	obj2del.parentNode.removeChild(obj2del);
}

//---- AJAX REQUEST for deleting Videos
function delVideo(videoId)
{
    delObj("video_"+videoId);
	var ajaxConn = new XHConn();                  
	var sParams = "videoId="+videoId;                
	ajaxConn.connect("videodel.php", "GET", sParams, processDel);   
}

// Salvează poziția de scroll înainte de submit
function saveScrollPosition() {
	var scrollPosition = window.pageYOffset || document.documentElement.scrollTop;
	if (typeof(Storage) !== "undefined") {
		sessionStorage.setItem('scrollPosition', scrollPosition);
	}
}

// Restaurează poziția de scroll după reîncărcare
if (typeof(Storage) !== "undefined") {
	window.addEventListener('load', function() {
		var savedScrollPosition = sessionStorage.getItem('scrollPosition');
		if (savedScrollPosition !== null) {
			// Folosește setTimeout pentru a se asigura că DOM-ul este complet încărcat
			setTimeout(function() {
				window.scrollTo(0, parseInt(savedScrollPosition));
				sessionStorage.removeItem('scrollPosition');
			}, 100);
		}
	});
}