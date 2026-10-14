<?php
// get config file;
include_once './includes/config.php';

// /////////////////////////////////////////////////////////////////////////////
// try to get content
$query_text = "SELECT id, home FROM texte WHERE id = 1";

$result = $connection->query($query_text) or db_fail($connection);
$row = $result->fetch_assoc();

// Set optimized page title (direct in file for easier maintenance)
$page_title = "Politică de Confidențialitate | Autoland România";
$page_subtitle = '';
$content = is_null($row) ? "Content is missing" : $row['home'];

include './themes/automotive/template_parts/header.php';
$footer_scripts .= '';
?>

<section class="content">
    <div class="container">
        <div class="inner-page row">
            <!-- Sidebar START Here -->
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 padding-right-none sm-padding-left-none xs-padding-left-none padding-bottom-40 scroll_effect fadeInUp" data-wow-delay='.2s' style="z-index:100">
                <?php
                require_once './themes/automotive/template_parts/sidebar_left.php';
                ?>
            </div>
            <!-- Sidebar END Here -->


            <div class="with-sidebar col-lg-9 col-md-9 col-sm-12 col-xs-12 welcome padding-left-none md-padding-left-25 padding-bottom-40 scroll_effect fadeInUp information_head welcome-wrap">
                <div class="content-wrap car_listings">
                    <div class="information_head welcome-wrap">
                        <h3 class="information_head">Politica de Confidentialitate privind Colectarea, Prelucrarea si Protejarea Datelor cu Caracter Personal</h3>
                        <h4>Informații privind navigarea:</h4>
                        <p>Utilizatorii sunt informați că site-ul autolandromania.ro poate crea un cookie în calculatoarele lor. Fișierele cookie conțin informațiile furnizate de utilizatori, astfel încât acestea nu trebuie să completeze din nou formularul trimis la vizitele ulterioare ale site-ului.</p>
                        <p>Astfel de cookie-uri nu permit identificarea utilizatorilor. Utilizatorii pot, în cazul în care doresc, să dezactiveze utilizarea cookie-urilor prin selectarea parametrilor corespunzători din browserele lor, deși această dezactivare poate dezactiva anumite funcții ale site-ului.</p>
                        <p>Orice link care se găseste pe site-ul Web autolandromania.ro și care direcționează utilizatorii către alte site-uri web nu poate face responsabila in vreun fel societatea noastra si/sau să răspundă referitor la orice fel de pretentii, daune sau despagubiri - materiale sau de orice fel - ori în privința oricarui aspect referitor la aceste site-uri.</p>
                        
                        <h4>Google Analytics:</h4>
                        <p>Acest site web utilizează Google Analytics, un serviciu de analiză a site-urilor web furnizat de Google Inc. ("Google"). Google Analytics utilizează module cookie, care sunt fișiere text create în computerul dvs., pentru a permite site-ului web să analizeze modul în care site-ul este utilizat de utilizatori. Datele generate de cookie-urile privind utilizarea site-ului (inclusiv adresa dvs. IP) sunt transmise și stocate de Google pe servere situate în Statele Unite. Google utilizează astfel de date pentru a evalua utilizarea site-ului dvs. și pentru a compila rapoarte privind activitatea site-ului destinată editorului site-ului și pentru a furniza alte servicii.</p>
                        <p>Orice nume sau date personale (prenume, nume, numere de telefon, adrese de e-mail) colectate pe acest site prin intermediul formularelor on-line sunt prelucrate în mod corespunzător si sunt scutite de la obligația de a declara procesele de date efectuate în scopul informării sau comunicării externe. Prin urmare, acest site Web nu conține un număr de inregistrare in cadrul Registrului General al Autoritatii Nationale de Supraveghere a Prelucrarii Datelor cu Caracter Personal.</p>
                        
                        <h4>Date personale:</h4>
                        <p>Utilizatorii acestui site sunt informați că, atunci când navighează pe site-ul autolandromania.ro li se poate solicita să furnizeze date personale în formulare care trebuie completate.</p>
                        <p>Aceste date sunt folosite în scopul transmiterii către utilizatori a informatiilor solicitate (oferte, formulare tip, etc), atunci când doresc să aiba un contact direct cu personalul firmei sau când pun o întrebare, sa poata sa primească răspunsul.</p>

                        <h4>Utilizatorii sunt informați că datele lor:</h4>
                        <p>–  sunt colectate loial și in conformitatea prevederilor legale;</p>
                        <p>–  sunt colectat numai în scopuri specifice, explicite și legitime;</p>
                        <p>–  nu vor fi ulterior procesate în niciun mod incompatibil cu scopurile menționate;</p>
                        <p>–  nu este destinat transportului către terți decat in scopurile adecvate, pertinente dar nu excesive, în raport cu scopul colectării lor și a prelucrării ulterioare a acestora;</p>
                        <p>–  sunt păstrate în forma lor exactă și completă;</p>
                        <p>–  sunt stocate într-o formă care sa permita identificarea persoanelor în cauză dar pentru o durată care nu depășește timpul necesar pentru scopurile pentru care este colectată și prelucrată;</p>
                        <p>–  se iau toate măsurile necesare pentru păstrarea acestor date în siguranță și confidențialitate, în special pentru a împiedica manipularea, deteriorarea sau punerea la dispoziția acestora a unor terțe părți neautorizate.</p>
                        <p></p>
                        <p>În conformitate cu legislatia actuala, utilizatorii au dreptul să se opună accesului la datele cu caracter personal și să corecteze si/sau sa modifice orice date care ii privesc. Pentru a exercita drepturile menționate și pentru a comunica astfel de informații, cererile trebuie prezentate la:</p>
                        <p>S.C. AUTOLAND S.R.L.<br>
                        Adresă: Iasi, str. Serg. Grigore Ioan, nr.7, jud IASI, ROMANIA<br>
                        Tel. Fix: 0032 887 698<br>
                        Tel. Mobil: 0721 936 948<br>
                        e-mail: office@autolandromania.ro</p>
                        

                    </div>

                </div>
            </div>
            <!--welcome ends-->
        </div>
    </div>

</section>

<?php
include './themes/automotive/template_parts/footer.php';
