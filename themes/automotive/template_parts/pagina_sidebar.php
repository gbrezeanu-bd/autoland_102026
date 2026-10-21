<?php
/**
 * Bara laterală a paginilor din secțiuni (pagina.php, leasing.php):
 * meniul secțiunii și datele de contact.
 * Așteaptă $sectiune și $slug (slug gol = pagina principală a secțiunii).
 */
$sidebar_meniu = isset($PAGINI_SECTIUNI[$sectiune]['meniu']) ? $PAGINI_SECTIUNI[$sectiune]['meniu'] : [];
$sidebar_hub = $PAGINI_SECTIUNI[$sectiune]['hub'];
?>
<aside class="ap-aside">
    <?php if ($sidebar_meniu): ?>
    <nav class="ap-sidenav" aria-label="<?php echo htmlspecialchars($PAGINI_SECTIUNI[$sectiune]['eticheta']); ?>">
        <div class="ap-side-title"><?php echo htmlspecialchars($PAGINI_SECTIUNI[$sectiune]['eticheta']); ?></div>
        <?php $pagina_curenta_label = isset($sidebar_meniu[(string)$slug]) ? $sidebar_meniu[(string)$slug] : $PAGINI_SECTIUNI[$sectiune]['eticheta']; ?>
        <!-- Pe desktop lista e mereu deschisă. Pe telefon și tabletă se închide la încărcare și se deschide din buton. -->
        <details class="ap-navbox" id="ap-navbox" open>
            <summary class="ap-navbtn">
                <span><small><?php echo htmlspecialchars($PAGINI_SECTIUNI[$sectiune]['eticheta']); ?></small><?php echo htmlspecialchars($pagina_curenta_label); ?></span>
            </summary>
            <ul>
                <?php foreach ($sidebar_meniu as $k => $label): ?>
                <li<?php echo ((string)$slug === (string)$k) ? ' class="current"' : ''; ?>>
                    <a href="<?php echo BASE_PATH . pagini_url_meniu($sectiune, (string)$k); ?>"><?php echo htmlspecialchars($label); ?></a>
                </li>
                <?php endforeach; ?>
            </ul>
        </details>
        <script>
        (function () {
            var d = document.getElementById('ap-navbox');
            if (d && window.matchMedia && window.matchMedia('(max-width: 991px)').matches) { d.removeAttribute('open'); }
        })();
        </script>
    </nav>
    <?php endif; ?>

    <?php
    // WhatsApp: același număr și același format ca pe fișele de mașină
    $wa_text = ($sectiune === 'finantare' ? 'Bună ziua, am o întrebare despre finanțarea auto.' : 'Bună ziua, am o întrebare.') . (!empty($canonical_url) ? ' (' . $canonical_url . ')' : '');
    $wa_url = 'https://wa.me/40742377001?text=' . urlencode($wa_text);
    ?>
    <div class="ap-contact">
        <div class="ap-side-title">Contactează-ne</div>
        <p class="ap-contact-line">Luni–vineri, 10:00–18:00<br>Str. Luca Arbore nr. 3, Iași</p>
        <a class="ap-btn ap-call" href="tel:+40742377001"><i class="fa fa-phone" aria-hidden="true"></i> Sună: 0742 377 001</a>
        <a class="ap-btn ap-wa" href="<?php echo htmlspecialchars($wa_url); ?>" target="_blank" rel="noopener"><i class="fa fa-whatsapp" aria-hidden="true"></i> Scrie pe WhatsApp</a>
        <a class="ap-btn ap-form" href="<?php echo BASE_PATH; ?>cerere_oferta.php">Cere o ofertă personalizată</a>
    </div>

    <div class="ap-mobilebar">
        <a class="ap-mb-call" href="tel:+40742377001"><i class="fa fa-phone" aria-hidden="true"></i> Sună</a>
        <a class="ap-mb-wa" href="<?php echo htmlspecialchars($wa_url); ?>" target="_blank" rel="noopener"><i class="fa fa-whatsapp" aria-hidden="true"></i> WhatsApp</a>
        <a class="ap-mb-form" href="<?php echo BASE_PATH; ?>cerere_oferta.php">Ofertă</a>
    </div>
</aside>
