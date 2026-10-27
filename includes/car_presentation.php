<?php

/**
 * Prezentarea datelor unei mașini pe fișa din vanzari_detail.php:
 *  - car_descriere_structurata(): transformă lista de dotări din admin (<ul><li>…) într-un aspect modern
 *  - car_specs_iconite(): adaugă o clasă pe fiecare rând de specificații, ca să primească pictogramă din CSS
 * Dacă textul nu are forma unei liste, se afișează neschimbat.
 */

/**
 * Lista de dotări din câmpul "detalii": dotările obișnuite devin o listă cu bife, în două coloane;
 * rândurile îngroșate în admin (de ex. "Garanție 12 luni din data livrării") devin etichete evidențiate.
 * Pe telefon, după primele dotări apare butonul "Vezi toate dotările".
 */
function car_descriere_structurata($html, $vizibile_pe_telefon = 8)
{
    $html = (string)$html;
    if (!preg_match_all('/<li\b[^>]*>(.*?)<\/li>/is', $html, $m) || count($m[1]) < 3) {
        return $html;
    }

    $dotari = [];
    $evidentiate = [];
    foreach ($m[1] as $raw) {
        $bold = (bool)preg_match('/<(strong|b)\b/i', $raw);
        $text = html_entity_decode(strip_tags($raw), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/[\x{00A0}\s]+/u', ' ', $text);
        $text = trim($text, " \t\n\r;,.");
        if ($text === '') {
            continue;
        }
        if ($bold) {
            $evidentiate[] = $text;
        } else {
            $dotari[] = $text;
        }
    }
    if (count($dotari) + count($evidentiate) < 3) {
        return $html;
    }

    $li = function ($items) {
        $out = '';
        foreach ($items as $t) {
            $out .= '<li>' . htmlspecialchars($t, ENT_QUOTES, 'UTF-8') . '</li>';
        }
        return $out;
    };

    // Textul scris în afara listelor (ex. "Prețul este net.") se păstrează: cel de dinainte sus, restul jos.
    $parti = preg_split('/<(ul|ol)\b[^>]*>.*?<\/\1>/is', $html);
    $inainte = car_descriere_text_liber(array_shift($parti));
    $dupa = car_descriere_text_liber(implode('', $parti));

    $out = '<div class="car-desc">' . $inainte;
    if ($evidentiate) {
        $out .= '<ul class="car-desc-highlights">' . $li($evidentiate) . '</ul>';
    }
    if ($dotari) {
        $prime = array_slice($dotari, 0, $vizibile_pe_telefon);
        $rest = array_slice($dotari, $vizibile_pe_telefon);
        $out .= '<div class="car-desc-count">' . count($dotari) . ' dotări și caracteristici</div>';
        $out .= '<ul class="car-desc-list">' . $li($prime) . '</ul>';
        if ($rest) {
            $out .= '<details class="car-feat-more" open><summary><span>Vezi toate dotările (încă ' . count($rest) . ')</span></summary>'
                . '<ul class="car-desc-list">' . $li($rest) . '</ul></details>';
        }
    }
    return $out . $dupa . '</div>';
}

/**
 * Bucată de text liber din "detalii", afișată ca notă; întoarce '' dacă nu conține text vizibil.
 */
function car_descriere_text_liber($html)
{
    $text = html_entity_decode(strip_tags((string)$html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    if (trim(preg_replace('/[\x{00A0}\s]+/u', ' ', $text)) === '') {
        return '';
    }
    return '<div class="car-desc-note">' . $html . '</div>';
}

/**
 * Adaugă pe fiecare rând de specificații o clasă după eticheta lui (ex. car-spec-km), pentru pictograma din CSS.
 */
function car_specs_iconite($html)
{
    $map = [
        'proveniență' => 'origin', 'an fabricație' => 'year', 'motor' => 'engine', 'putere' => 'power',
        'km parcursi' => 'km', 'ore de funcționare' => 'km', 'norma euro' => 'euro', 'combustibil' => 'fuel',
        'transmisie' => 'gearbox', 'tracțiune' => 'drive', 'număr uși' => 'doors', 'număr locuri' => 'seats',
        'culoare' => 'color', 'caroserie' => 'body', 'înmatriculată' => 'registered', 'variantă' => 'variant',
        'tip' => 'body', 'tonaj' => 'weight', 'tip deplasare' => 'drive',
    ];
    return preg_replace_callback(
        '/<tr class="car-spec-row ([^"]*)">(\s*)<td class="car-spec-label">([^<]*)<\/td>/u',
        function ($m) use ($map) {
            $key = mb_strtolower(trim(html_entity_decode($m[3], ENT_QUOTES | ENT_HTML5, 'UTF-8')), 'UTF-8');
            $cls = isset($map[$key]) ? ' car-spec-' . $map[$key] : ' car-spec-other';
            return '<tr class="car-spec-row ' . $m[1] . $cls . '">' . $m[2] . '<td class="car-spec-label">' . $m[3] . '</td>';
        },
        (string)$html
    );
}
