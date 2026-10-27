// Formatarea codului vine din tehnic/editor-continut.js (pagina care a deschis fereastra), daca e incarcat acolo
var adminSursa = null;

function adminPaginaEditor() {
	var w = window.opener || (window.parent !== window ? window.parent : null);
	try {
		return w && typeof(w.adminHtmlFormateaza) == 'function' ? w : null;
	} catch (e) {
		return null;
	}
}

function saveContent() {
	var v = document.getElementById('htmlSource').value;

	if (adminSursa) {
		// Nimic modificat: editorul ramane exact cum era
		if (v === adminSursa.text) {
			tinyMCE.closeWindow(window);
			return;
		}
		v = adminSursa.restaureaza(v);
	}

	tinyMCE.setContent(v);
	tinyMCE.closeWindow(window);
}

function onLoadInit() {
	tinyMCEPopup.resizeToInnerSize();

	// Remove Gecko spellchecking
	if (tinyMCE.isGecko)
		document.body.spellcheck = tinyMCE.getParam("gecko_spellcheck");

	var html = tinyMCE.getContent(tinyMCE.getWindowArg('editor_id')), pagina = adminPaginaEditor();

	if (pagina) {
		adminSursa = pagina.adminHtmlFormateaza(html);
		html = adminSursa.text;
		pagina.adminHtmlStilFereastra(document);
	}

	document.getElementById('htmlSource').value = html;

	resizeInputs();

	if (tinyMCE.getParam("theme_advanced_source_editor_wrap", true)) {
		setWrap('soft');
		document.getElementById('wraped').checked = true;
	}
}

function setWrap(val) {
	var s = document.getElementById('htmlSource');

	s.wrap = val;

	if (tinyMCE.isGecko || tinyMCE.isOpera) {
		var v = s.value;
		var n = s.cloneNode(false);
		n.setAttribute("wrap", val);
		s.parentNode.replaceChild(n, s);
		n.value = v;
	}
}

function toggleWordWrap(elm) {
	if (elm.checked)
		setWrap('soft');
	else
		setWrap('off');
}

var wHeight=0, wWidth=0, owHeight=0, owWidth=0;

function resizeInputs() {
	var el = document.getElementById('htmlSource');

	if (!tinyMCE.isMSIE) {
		 wHeight = self.innerHeight - 60;
		 wWidth = self.innerWidth - 16;
	} else {
		 wHeight = document.body.clientHeight - 60;
		 wWidth = document.body.clientWidth - 16;
	}

	el.style.height = Math.abs(wHeight) + 'px';
	el.style.width  = Math.abs(wWidth) + 'px';
}
