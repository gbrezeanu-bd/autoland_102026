/**
 * Calculator de rată (leasing), model orientativ. Rulează în browser, fără cereri către server.
 * Setările vin din atributele data-* ale elementului .ap-calc (includes/calculator_rata_config.php).
 * Formulele sunt cele din Excel-ul "calculator rate SH IDL PJ-PFA".
 *
 * Evenimentele sunt delegate la nivel de document, iar starea (avans, perioadă) se ține în atributele
 * elementului: astfel calculatorul merge și într-o copie a elementului (fișa mașinii copiază bara laterală pe mobil).
 */
(function () {
    'use strict';

    var nf = typeof Intl !== 'undefined' ? new Intl.NumberFormat('ro-RO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : null;
    var nf0 = typeof Intl !== 'undefined' ? new Intl.NumberFormat('ro-RO', { maximumFractionDigits: 0 }) : null;

    function fmt(n) { return nf ? nf.format(n) : n.toFixed(2).replace('.', ','); }
    function fmt0(n) { return nf0 ? nf0.format(n) : String(Math.round(n)); }

    // Echivalent cu PMT din Excel (plată la sfârșit de lună), cu valoare reziduală
    function pmt(rateMonthly, months, financed, residual) {
        if (rateMonthly === 0) { return (financed - residual) / months; }
        var f = Math.pow(1 + rateMonthly, months);
        return rateMonthly * (financed * f - residual) / (f - 1);
    }

    function calculate(c) {
        var net = c.price / (1 + c.vat / 100);
        var advance = net * c.advancePct / 100;
        var financed = net - advance;
        var residual = net * c.residualPct / 100;
        var monthlyNet = pmt(c.rate / 100 / 12, c.months, financed, residual) + net * c.monthlyFeePct / 100;
        var firstNet = advance + net * c.mgmtPct / 100 + c.services;
        var k = 1 + c.vat / 100;
        return {
            net: net,
            advanceNet: advance,
            advanceGross: advance * k,
            monthlyNet: monthlyNet,
            monthlyGross: monthlyNet * k,
            mgmtGross: net * c.mgmtPct / 100 * k,
            servicesGross: c.services * k,
            firstNet: firstNet,
            firstGross: firstNet * k
        };
    }

    function num(el, name, fallback) {
        var v = parseFloat(el.getAttribute('data-' + name));
        return isNaN(v) ? fallback : v;
    }

    function part(root, name) { return root.querySelector('[data-calc="' + name + '"]'); }
    function put(root, name, text) { var el = part(root, name); if (el) { el.textContent = text; } }
    // bara pliată (.ap-calc-wrap) este părintele calculatorului, nu un element din interior
    function putSummary(root, text) { var w = root.closest ? root.closest('.ap-calc-wrap') : null; var el = w ? w.querySelector('[data-calc="summary"]') : null; if (el) { el.textContent = text; } }

    function render(root) {
        var priceInput = part(root, 'price');
        var price = num(root, 'price', 0);
        if (priceInput) {
            var raw = priceInput.value.replace(/\./g, '').replace(',', '.').replace(/[^0-9.]/g, '');
            var p = parseFloat(raw);
            price = isNaN(p) ? 0 : p;
        }
        var advPct = num(root, 'adv-value', 30);
        var months = num(root, 'months-value', 60);

        put(root, 'adv-value', advPct + '%');
        put(root, 'months-value', months + ' luni');
        put(root, 'adv-label', advPct + '%');

        if (!(price > 0)) {
            put(root, 'monthly', '–'); put(root, 'monthly-net', ''); put(root, 'advance', '–'); put(root, 'mgmt', '–'); put(root, 'services', '–');
            put(root, 'first', '–'); put(root, 'first-net', ''); putSummary(root, '–');
            return;
        }
        var r = calculate({
            price: price, vat: num(root, 'vat', 21), advancePct: advPct, months: months, rate: num(root, 'rate', 6.5),
            mgmtPct: num(root, 'mgmt', 2.5), monthlyFeePct: num(root, 'monthly-fee', 0.06),
            services: num(root, 'services', 200), residualPct: num(root, 'residual', 0)
        });
        put(root, 'monthly', fmt(r.monthlyGross));
        put(root, 'monthly-net', fmt(r.monthlyNet));
        put(root, 'advance', fmt(r.advanceGross));
        put(root, 'mgmt', fmt(r.mgmtGross));
        put(root, 'services', fmt(r.servicesGross));
        put(root, 'first', fmt(r.firstGross));
        put(root, 'first-net', fmt(r.firstNet));
        putSummary(root, fmt(r.monthlyGross));

        // Mesajul WhatsApp include estimarea aleasă
        var cta = part(root, 'cta-wa');
        if (cta) {
            var msg = (cta.getAttribute('data-wa-base') || '') + ' Estimare: avans ' + advPct + '%, ' + months +
                ' luni, rată aprox. ' + fmt(r.monthlyGross) + ' EUR cu TVA.';
            cta.setAttribute('href', 'https://wa.me/40742377001?text=' + encodeURIComponent(msg));
        }
    }

    function step(root, kind, dir) {
        var isAdv = kind === 'adv';
        var key = isAdv ? 'adv-value' : 'months-value';
        var min = num(root, isAdv ? 'adv-min' : 'months-min', 0);
        var max = num(root, isAdv ? 'adv-max' : 'months-max', 100);
        var inc = num(root, isAdv ? 'adv-step' : 'months-step', 1);
        var cur = num(root, key, min);
        root.setAttribute('data-' + key, String(Math.min(max, Math.max(min, cur + dir * inc))));
        render(root);
    }

    document.addEventListener('click', function (e) {
        var btn = e.target && e.target.closest ? e.target.closest('[data-calc-step]') : null;
        if (!btn) { return; }
        var root = btn.closest('.ap-calc');
        if (!root) { return; }
        e.preventDefault();
        step(root, btn.getAttribute('data-calc-step'), parseInt(btn.getAttribute('data-dir'), 10) || 1);
    });

    function onPriceChange(e) {
        if (e.target && e.target.getAttribute && e.target.getAttribute('data-calc') === 'price') {
            var root = e.target.closest('.ap-calc');
            if (root) { render(root); }
        }
    }
    document.addEventListener('input', onPriceChange);
    document.addEventListener('change', onPriceChange);

    // Expus pentru verificări (în consolă): CalculatorRata.calculate({...})
    window.CalculatorRata = { calculate: calculate, pmt: pmt };

    function ready(fn) {
        if (document.readyState !== 'loading') { fn(); } else { document.addEventListener('DOMContentLoaded', fn); }
    }
    ready(function () {
        var nodes = document.querySelectorAll('.ap-calc');
        for (var i = 0; i < nodes.length; i++) { render(nodes[i]); }
    });
})();
