/*
 * Setari comune pentru editorul TinyMCE 2 din paginile "Continut text" (editor_*.php).
 * Curatarea automata a editorului e reglata sa pastreze HTML-ul exact cum e salvat: nu mai sterge
 * celulele goale de tabel si etichetele HTML5 (ex. <mark>), nu rescrie linkurile, nu transforma
 * diacriticele in entitati si nu lipeste randurile. Textul se vede in editor cu stilurile de pe site.
 */

// Etichete pastrate cu toate atributele; fara ele, TinyMCE 2 (anterior HTML5) le-ar sterge sau goli.
var ADMIN_EDITOR_ETICHETE = 'mark[*],th[*],td[*],tr[*],a[*],table[*],div[*],span[*],p[*],img[*],ul[*],ol[*],li[*],'
    + 'h1[*],h2[*],h3[*],h4[*],blockquote[*],iframe[*],section[*],article[*],aside[*],header[*],footer[*],nav[*],'
    + 'figure[*],figcaption[*],small[*],details[*],summary[*],time[*]';

// Repaint-ul din TinyMCE 2 ascunde corpul editorului, selecteaza tot si il reafiseaza. In Chrome selectia pe un
// corp ascuns e goala, pasul urmator arunca o eroare (prinsa in tacere) si editorul ramane alb, de ex. dupa "Update"
// in fereastra HTML. Corpul se reafiseaza aici oricum.
function adminEditorRepaintSigur() {
    if (typeof TinyMCE_Control == 'undefined' || TinyMCE_Control.prototype.adminRepaintSigur) {
        return;
    }
    var original = TinyMCE_Control.prototype.repaint;
    TinyMCE_Control.prototype.repaint = function () {
        try {
            original.apply(this, arguments);
        } finally {
            try {
                if (this.getBody().style.display == 'none') {
                    this.getBody().style.display = 'block';
                }
            } catch (e) {}
        }
    };
    TinyMCE_Control.prototype.adminRepaintSigur = true;
}
adminEditorRepaintSigur();

// Meniurile de culoare din bara (text / fundal) se inchideau doar la alegerea unei culori sau la click in text.
// Acum se inchid si la click in afara lor, la al doilea click pe sageata si la Esc.
function adminEditorInchideMeniuri() {
    if (typeof TinyMCE_AdvancedTheme == 'undefined' || typeof tinyMCE == 'undefined' || !tinyMCE.instances) {
        return;
    }
    for (var n in tinyMCE.instances) {
        if (tinyMCE.instances[n] && tinyMCE.instances[n].editorId) {
            TinyMCE_AdvancedTheme._hideMenus(tinyMCE.instances[n].editorId);
        }
    }
}

(function () {
    if (!document.addEventListener || window.adminEditorMeniuriLegate) {
        return;
    }
    window.adminEditorMeniuriLegate = true;
    document.addEventListener('mousedown', function (e) {
        var n, link = null;
        for (n = e.target; n && n.nodeType == 1; n = n.parentNode) {
            if (/\bmceMenu\b/.test(n.className)) {
                return; // click in meniu, de ex. pe o culoare: il trateaza TinyMCE
            }
            if (n.nodeName == 'A' && !link) {
                link = n;
            }
            if (n.nodeName == 'SPAN' && /\bmceMenuButton(Focus)?\b/.test(n.className)) {
                // Sageata unui meniu deja deschis il inchide; restul butonului ramane cum era
                if (/\bmceMenuButtonFocus\b/.test(n.className) && link && /Menu'\)/.test(link.getAttribute('href') || '')) {
                    adminEditorInchideMeniuri();
                    var opreste = function (ev) {
                        ev.stopPropagation();
                        ev.preventDefault();
                        document.removeEventListener('click', opreste, true);
                    };
                    document.addEventListener('click', opreste, true);
                }
                return;
            }
        }
        adminEditorInchideMeniuri();
    }, true);
    document.addEventListener('keydown', function (e) {
        if (e.keyCode == 27) {
            adminEditorInchideMeniuri();
        }
    });
})();

function adminEditor(setari) {
    adminEditorRepaintSigur();
    var implicite = {
        mode: 'textareas',
        width: '100%',
        height: '640',
        remove_linebreaks: false,
        convert_urls: false,
        entity_encoding: 'raw',
        extended_valid_elements: ADMIN_EDITOR_ETICHETE,
        content_css: '../themes/automotive/css/pagini.css',
        theme_advanced_toolbar_location: 'top',
        theme_advanced_toolbar_align: 'left',
        theme_advanced_statusbar_location: 'bottom',
        theme_advanced_resizing: true,
        theme_advanced_resize_horizontal: false,
        theme_advanced_source_editor_width: '1000',
        theme_advanced_source_editor_height: '720',
        setupcontent_callback: 'adminEditorContinut'
    };
    for (var k in implicite) {
        if (!(k in setari)) {
            setari[k] = implicite[k];
        }
    }
    tinyMCE.init(setari);
}

// TinyMCE 2 nu cunoaste body_class: clasa paginilor de pe site si asezarea textului se pun aici.
function adminEditorContinut(editor_id, body, doc) {
    body.className = (body.className ? body.className + ' ' : '') + 'inner-page';
    body.style.maxWidth = '920px';
    body.style.margin = '0 auto';
    body.style.padding = '18px 26px';
    body.style.fontFamily = "'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif";
    body.style.fontSize = '15px';
    body.style.lineHeight = '1.6';
    body.style.color = '#2b3a42';
    doc.addEventListener('mousedown', adminEditorInchideMeniuri);

    var linkuri = doc.getElementsByTagName('link');
    for (var i = 0; i < linkuri.length; i++) {
        if (linkuri[i].href.indexOf('pagini.css') !== -1) {
            adminEditorStiluriBold(doc, linkuri[i]);
        }
    }
}

// In editor, TinyMCE 2 afiseaza <strong>/<em> ca <b>/<i> (la salvare le pune la loc); copiaza stilurile
// site-ului pentru strong/em si pe b/i, ca textul ingrosat sa arate in editor ca pe site.
function adminEditorStiluriBold(doc, link) {
    var copiaza = function () {
        var reguli = [], foaie = link.sheet, i, j, r;
        try {
            for (i = 0; i < foaie.cssRules.length; i++) {
                r = foaie.cssRules[i];
                if (r.selectorText && /\b(strong|em)\b/.test(r.selectorText)) {
                    reguli.push(r.cssText.replace(/\bstrong\b/g, 'b').replace(/\bem\b/g, 'i'));
                } else if (r.media && r.cssRules) {
                    var interne = [];
                    for (j = 0; j < r.cssRules.length; j++) {
                        if (r.cssRules[j].selectorText && /\b(strong|em)\b/.test(r.cssRules[j].selectorText)) {
                            interne.push(r.cssRules[j].cssText.replace(/\bstrong\b/g, 'b').replace(/\bem\b/g, 'i'));
                        }
                    }
                    if (interne.length) {
                        reguli.push('@media ' + r.media.mediaText + ' {' + interne.join('\n') + '}');
                    }
                }
            }
        } catch (e) {
            return;
        }
        var stil = doc.createElement('style');
        stil.appendChild(doc.createTextNode(reguli.join('\n')));
        doc.getElementsByTagName('head')[0].appendChild(stil);
    };
    if (link.sheet && link.sheet.cssRules && link.sheet.cssRules.length) {
        copiaza();
    } else {
        link.addEventListener('load', copiaza);
    }
}

/*
 * Fereastra "HTML" a editorului (themes/advanced/source_editor.htm) afiseaza codul indentat:
 * fiecare bloc (div, p, h2, tabel, rand, celula...) pe randul lui. Se schimba doar spatiile dintre
 * doua etichete de bloc; textul si etichetele din interiorul randurilor raman neatinse.
 * La "Update", spatiile originale se pun la loc peste tot unde codul n-a fost modificat,
 * iar daca nu s-a modificat nimic, editorul nu e atins deloc.
 */
var ADMIN_HTML_BLOCURI = /^(div|p|h[1-6]|ul|ol|li|table|thead|tbody|tfoot|tr|td|th|caption|blockquote|section|article|aside|header|footer|nav|figure|figcaption|details|summary|hr|dl|dt|dd|form|address|center)$/i;
var ADMIN_HTML_NEATINSE = /^(pre|textarea|script|style)$/i;

// Imparte codul in etichete, comentarii si text; continutul din pre/textarea/script/style ramane un singur bloc.
function adminHtmlBucati(html) {
    var re = /<!--[\s\S]*?-->|<\/?[a-zA-Z][^>]*>|[^<]+|</g, bucati = [], m, nume, inchidere;
    while ((m = re.exec(html)) !== null) {
        var b = { raw: m[0], tag: false };
        nume = /^<(\/?)([a-zA-Z][a-zA-Z0-9]*)/.exec(m[0]);
        if (nume) {
            b.tag = true;
            b.inchidere = nume[1] === '/';
            b.nume = nume[2].toLowerCase();
            b.bloc = ADMIN_HTML_BLOCURI.test(b.nume);
            b.gol = b.nume === 'hr' || /\/>$/.test(m[0]);
            if (!b.inchidere && ADMIN_HTML_NEATINSE.test(b.nume)) {
                inchidere = html.toLowerCase().indexOf('</' + b.nume, re.lastIndex);
                if (inchidere !== -1) {
                    var sfarsit = html.indexOf('>', inchidere) + 1;
                    b.raw = html.substring(m.index, sfarsit);
                    b.bloc = false;
                    re.lastIndex = sfarsit;
                }
            }
        }
        bucati.push(b);
    }
    return bucati;
}

// Trece prin cod si apeleaza fn(stanga, spatiu, dreapta, adancime) la fiecare granita dintre doua etichete
// unde se rupe randul; fn intoarce spatiul care se pune acolo.
function adminHtmlGranite(html, fn) {
    var bucati = adminHtmlBucati(html), out = '', ad = 0, prec = null, spatiu = '', i, b, rupe;
    for (i = 0; i < bucati.length; i++) {
        b = bucati[i];
        if (!b.tag && /^\s+$/.test(b.raw)) {
            spatiu += b.raw;
            continue;
        }
        if (b.tag && b.bloc && b.inchidere) {
            ad = Math.max(0, ad - 1);
        }
        rupe = prec && prec.tag && b.tag && ((prec.bloc && (prec.inchidere || prec.gol)) || (b.bloc && !b.inchidere));
        out += rupe ? fn(prec.raw, spatiu, b.raw, ad) : spatiu;
        out += b.raw;
        spatiu = '';
        if (b.tag && b.bloc && !b.inchidere && !b.gol) {
            ad++;
        }
        prec = b;
    }
    return out + spatiu;
}

function adminHtmlFormateaza(html) {
    var granite = [];
    var text = adminHtmlGranite(html, function (stanga, spatiu, dreapta, ad) {
        var afisat = '\n' + new Array(ad + 1).join('  ');
        granite.push({ semn: stanga + '|' + dreapta, original: spatiu, afisat: afisat });
        return afisat;
    });
    return {
        original: html,
        text: text,
        // Pune la loc spatiile originale la granitele nemodificate; granitele noi sau schimbate raman cum le-a scris utilizatorul
        restaureaza: function (nou) {
            var p = 0;
            return adminHtmlGranite(nou, function (stanga, spatiu, dreapta) {
                var semn = stanga + '|' + dreapta, j;
                for (j = p; j < granite.length && j < p + 60; j++) {
                    if (granite[j].semn === semn) {
                        p = j + 1;
                        return spatiu === granite[j].afisat ? granite[j].original : spatiu;
                    }
                }
                return spatiu;
            });
        }
    };
}

// Aspectul ferestrei "HTML": font monospatiat lizibil si butoane ca in restul adminului
function adminHtmlStilFereastra(doc) {
    var css = 'body{margin:0;padding:12px 14px;background:#f6f8f9;font-family:"Segoe UI",Roboto,Arial,sans-serif;font-size:13px;color:#12303b}'
        + '.title{font-size:15px;font-weight:700;margin:2px 0 8px}'
        + '#wrapline{font-size:12.5px;color:#6b7782;margin-top:4px}'
        + '#htmlSource{box-sizing:border-box;margin-top:4px;padding:12px 14px;border:1px solid #cfd6dc;border-radius:8px;background:#fff;'
        + 'font-family:Consolas,"Cascadia Mono","Courier New",monospace !important;font-size:13.5px !important;line-height:1.6;color:#12303b;tab-size:2;white-space:pre-wrap}'
        + '#htmlSource:focus{outline:2px solid #e4f1f5;border-color:#0e7490}'
        + '.mceActionPanel{margin-top:10px}'
        // #insert/#cancel au in tema TinyMCE imagini de fundal si dimensiuni fixe; se anuleaza ca textul sa se vada
        + '.mceActionPanel #insert,.mceActionPanel #cancel{width:auto!important;height:auto!important;padding:7px 18px!important;border:1px solid #0e7490!important;'
        + 'border-radius:6px;background:#0e7490 none!important;color:#fff!important;font:600 13px "Segoe UI",Arial,sans-serif;cursor:pointer}'
        + '.mceActionPanel #cancel{background:#fff none!important;color:#0e7490!important}';
    var stil = doc.createElement('style');
    stil.appendChild(doc.createTextNode(css));
    doc.getElementsByTagName('head')[0].appendChild(stil);
}
