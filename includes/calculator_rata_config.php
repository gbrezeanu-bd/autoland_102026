<?php

/**
 * Setările calculatorului de rată (leasing), preluate din Excel-ul "calculator rate SH IDL PJ-PFA".
 * Se modifică aici, într-un singur loc; se aplică pe fișele de mașină și pe pagina Calculator rată.
 *
 * Formule (preț = prețul afișat pe site, considerat final, cu TVA):
 *   preț fără TVA        = preț / (1 + TVA)
 *   avans                = preț fără TVA × avans %
 *   sumă finanțată       = preț fără TVA − avans
 *   rată (fără TVA)      = PMT(dobândă / 12, perioadă, −sumă finanțată, valoare reziduală) + preț fără TVA × comision lunar %
 *   rată (cu TVA)        = rată fără TVA × (1 + TVA)
 *   total primă plată    = avans + preț fără TVA × taxă de management % + cheltuieli prestări servicii   (cu TVA: × (1 + TVA))
 */
// global: setările rămân disponibile și când fișierul e inclus din interiorul unei funcții
global $CALCULATOR_RATA;
$CALCULATOR_RATA = [
    'tva'                 => 21,      // % TVA
    'dobanda'             => 4.5,     // % pe an (fixă)
    'taxa_management'     => 2.5,     // % din prețul fără TVA, o singură dată
    'comision_lunar'      => 0.06,    // % din prețul fără TVA, lunar
    'cheltuieli_servicii' => 200,     // EUR fără TVA, o singură dată
    'valoare_reziduala'   => 0,       // % din prețul fără TVA
    'avans_min'           => 0,
    'avans_max'           => 50,
    'avans_pas'           => 5,
    'avans_implicit'      => 30,
    'luni_min'            => 12,
    'luni_max'            => 60,
    'luni_pas'            => 12,
    'luni_implicit'       => 60,
];

/**
 * Aceleași formule ca în themes/automotive/js/calculator-rata.js, pentru textele generate pe server
 * (de exemplu exemplul de calcul din pagina Calculator rată).
 * $pret = prețul afișat (cu TVA). Returnează sumele în EUR.
 */
function calculator_rata_calculeaza($pret, $avans_pct, $luni)
{
    global $CALCULATOR_RATA;
    $c = $CALCULATOR_RATA;
    $k = 1 + $c['tva'] / 100;
    $net = $pret / $k;
    $avans = $net * $avans_pct / 100;
    $finantat = $net - $avans;
    $reziduala = $net * $c['valoare_reziduala'] / 100;
    $r = $c['dobanda'] / 100 / 12;
    if ($r == 0) {
        $anuitate = ($finantat - $reziduala) / $luni;
    } else {
        $f = pow(1 + $r, $luni);
        $anuitate = $r * ($finantat * $f - $reziduala) / ($f - 1);
    }
    $rata_net = $anuitate + $net * $c['comision_lunar'] / 100;
    $prima_net = $avans + $net * $c['taxa_management'] / 100 + $c['cheltuieli_servicii'];
    return [
        'pret_net' => $net, 'pret' => $pret,
        'avans_net' => $avans, 'avans' => $avans * $k,
        'rata_net' => $rata_net, 'rata' => $rata_net * $k,
        'prima_net' => $prima_net, 'prima' => $prima_net * $k,
    ];
}

/** Număr în format românesc (1.234,56), fără zecimale inutile la procente */
function calculator_rata_nr($n, $zecimale = 2)
{
    return number_format($n, $zecimale, ',', '.');
}
function calculator_rata_procent($n)
{
    return rtrim(rtrim(number_format($n, 2, ',', ''), '0'), ',');
}
