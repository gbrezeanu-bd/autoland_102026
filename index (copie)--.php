<?php
// get config file;
include_once './includes/config.php';
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
                        <h3 class="information_head">Autoland România</h3>
                        <h4 class="information_head">1. De ce o colaborare cu AUTOLAND?</h4>
                        <p>De ce nu?</p>

                        <ul>
                            <li>
                                De ce să va riscaţi banii cu achiziţii nesigure de prin târguri de maşini, bazare sau anunţuri în ziare?
                            </li>
                            <li>
                                De ce să pierdeţi tot dumneavoastra timpul şi resursele pentru cautari nesfarsite, prin deplasări nenumărate pe la târgurile de maşini din ţară sau chiar din afara?  Pe la bănci? Pe la firme de leasing? Lasati o firma specializata sa se ocupe de toate!
                            </li>
                        </ul>
                        <p>
                            O scurtă vizită la sediul nostru sau un singur contact direct cu AUTOLAND (telefonic, prin email sau fax) va fi suficient. Evitati de la bun inceput toată bataia de cap aferenta găsirii unui autovehicul cat mai apropiat de cerintele dumneavoastra, dpdv tehnic dar si ca pret.  Chiar daca sunteti la distante mari fata de noi, toate etapele necesare pentru cumpărarea unei maşini pot fi începute impreuna, direct de acasa sau de la birou. De asemeni, intreaga parte de documentaţie necesară pentru cumpararea in leasing sau rate a unei masini o putem derula impreuna prin email sau fax, urmând ca la final, după vederea şi constatarea (impreună cu un reprezentant  AUTOLAND) a tuturor aspectelor legate de maşină, să primiţi la livrarea bunului toate documentele maşinii in original, cheile şi actele aferente (asigurari, contracte, facturi, etc).
                        </p>

                        <p>
                            De ce da?
                        </p>
                        <ul>
                            <li>
                                Intrucât AUTOLAND este un dealer autorizat, în primul rând serios şi responsabil, care vă poate furniza o gama larga si varianta de mărci şi modele auto de pe piaţă: turisme mai mici sau mai mari, limuzine, auto 4x4, sport, decapotabile, autoutilitare cu diverse capacităţi, sarcini şi dimensiuni utile, autospecializate pentru transport persoane, frigorifice, platforme mobile, cu nacelă, etc. Autovehiculele pot fi atât noi cât şi de ocazie, cu o vechime de la câteva săptămâni la câţiva ani;
                            </li>
                            <li>
                                Pentru că AUTOLAND are întotdeauna soluţii flexibile şi avantajoase de finanţare prin rate sau leasing. Lucrând la standarde europene, AUTOLAND are la baza principiul ca încrederea reciprocă într-o relaţie comercială trebuie construită încă din primul minut şi doar prin onestitate, seriozitate, responsabilitate şi respect.
                            </li>
                            <li>
                                Deoarece furnizăm servicii complexe şi complete, atât pentru persoanele juridice cât şi pentru persoanele fizice;
                            </li>
                            <li>
                                Fiindcă AUTOLAND vă ofera o Garanţie Pentru Autovehicule Second Hand !!! pe o perioadă cuprinsă între 6 luni şi 2 ani, în funcţie de vechimea autovehiculului, tip, model, nr. de km, etc. La livrare veti primi astfel propriul carnet de service şi garanţie pentru maşina achiziţionată contra unui cost de cca 2,5% (dar nu mai putin de 200eur) din preţul de achiziţie pentru fiecare interval de 6 luni sau 10 000 km ( ex: pt. o garanţie de 6 luni sau 10 000 km se achită 2,5% din preţul maşinii, pentru 1 an sau 20 000 km costul este de 5%, iar pentru maximum 2 ani sau 40 000 km înseamnă 10%);
                            </li>
                            <li>
                                AUTOLAND vă oferă rezultatele testelor de diagnoză efectuate în service-uri auto autorizate, înainte de a vi se livra masina. De asemeni suntem de acord cu orice test (in limita bunului simt) pe care doriti să îl efectuaţi pentru maşina cumpărată, având convingerea fermă că şi rezultatul testului dumneavoastra va fi conform cu cel efectuat de către noi. In acest mod veţi fi convinşi că achiziţionaţi o maşină 100% sigură d.p.d.v. tehnic.
                            </li>
                        </ul>
                    </div>

                    <div class="fadeInDown">
                        <h4>2. Cu ce servicii vă vine în întâmpinare AUTOLAND?</h4>
                        <p>AUTOLAND vă oferă seriozitate, promptitudine şi profesionalism integrate în următorul pachet de servicii concepute în sprijinul dumneavoastră: </p>
                        <ul>
                            <li>
                                <b>Consultanţă tehnică de specialitate</b>, gratuită, la achiziţia oricărei maşini noi sau de ocazie; ştim că cel mai greu pas este acela de a vă decide ce doriţi să cumpăraţi, iar varietatea pieţei auto vă face să vă decideţi destul de greu: aţi dori să cumpăraţi autoturismul X pentru aspectul său exterior, dar doriţi să cumpăraţi în acelaşi timp şi autoturismul Y pentru dotările sale şi autoturismul Z pentru performanţele motorului; toate aceste aspecte vă vor da numai dureri de cap, care pot fi evitate prin consultarea unor persoane avizate, cu experienţă în domeniul auto, care pot recomanda un autoturism în funcţie de necesităţile fiecăruia.
                            </li>
                            <li>
                                <b>Stabilirea de comun acord a unor solutii auto complete si finantari complexe</b> - obţinute printr-o permanentă legătură cu dealerii autorizaţi parteneri, cu băncile şi firmele de leasing, cu societăţile de finanţare şi cu firmele de asigurare.
                            </li>
                            <li>
                                <b>Oferte auto alternative, sigure şi serioase</b> - vă putem transmite şi pentru alte mărci şi modele auto similare cu cel solicitat, (după principiul că o oferta  în plus  dacă este bună nu strică nimănui), acestea fiind într-o formă completă şi complexă pentru fiecare bun în parte; fiind o firmă specializată de vânzări auto putem merge noi să vizualizăm bunul solicitat, putem negocia pentru dumneavoastră, putem face toate demersurile necesare pentru transportul bunului la noi în curte;  veţi economisi astfel timp, bani şi nervi, nemaiavând dureri de cap, dacă: poate nu vorbiţi aceeaşi limbă ca şi vânzătorul extern, poate că unii după ce îşi primesc banii nu mai oferă sprijinul necesar pentru a se putea exporta bunul, poate că veţi plăti banii unei căsuţe poştale şi nu unui dealer, poate că actele de achiziţie nu sunt corecte sau pot să fie incomplete şi mai plătiţi pe urmă taxe suplimentare şi multe alte probleme.
                            </li>
                            <li>
                                <b>Procedură de vânzare - cumpărare auto sigură, garantată şi profesională</b> - cumpăraţi maşina de care aveţi nevoie într-un cadru organizat, competent şi fără nici un risc;
                            </li>
                            <li>
                                <b>Acte auto complete</b> - cuprinzând întocmirea tuturor formalităţilor şi a actelor necesare (traduceri, legalizări, contracte de vânzare - cumpărare auto, fişa auto de înmatriculare, etc.) în vederea obţinerii unei înmatriculări auto provizorii sau definitive - dupa caz, conform cererii dumneavoastră - pentru a nu va mai crea personal disconfort, stres sau chiar nervi provocati de birocraţia din România în acest domeniu;
                            </li>
                            <li>
                                <b>Omologare, inspecţie tehnică RAR, I.T.P.</b> - pentru ca dumneavoastră să nu mai fiţi nevoit să pierdeţi timp stând la cozi pe la diverse instituţii;
                            </li>
                            <li>
                                <b>Asigurări auto cu discount-urile aferente unui dealer auto</b> - valabile atât pentru asigurarile obligatorii (RCA) cât şi pentru cele facultative (CASCO);
                            </li>
                            <li>
                                <b>Servicii de înmatriculare</b> - provizorie sau definitivă - după fiecare caz în parte;
                            </li>
                            <li>
                                <b>Adaptări sau modificări de structură omologate RAR</b> - posibilitate de carosare demontabilă (cu prelata) sau nedemontabilă (cu panouri din Aluminiu), echipare cu frig (congelare sau refrigerare), diverse alte modificări, lucrări de reparaţii şi întreţinere, autorizări ISCIR, toate acestea posibile ca urmare a colaborării cu specialişti foarte buni, care in prezent sunt partenerii pe care ne putem baza impreuna;
                            </li>
                            <li>
                                <b>Asistenţă Rutieră Naţională şi/sau Internaţională</b>, valabilă timp de 1 an pe teritoriul României şi/sau al Uniunii Europene pentru următoarele servicii: Start (alimentare curent pentru pornire), Depanare (pană de cauciuc, de combustibil sau alt defect uşor remediabil), Tractare (în cazul unui defect mai grav sau al unui accident), Transport Pasageri, Cazare, Autoturism la schimb, Informaţii Auto, Meteo, Rutiere, Turistice, Optimizare Rute - pentru orice client al AUTOLAND, fie persoana fizică sau juridică română.
                            </li>
                            <li>
                                <b>Garanţie pentru Autovehiculul cumpărat</b> - cu scopul de a spori în primul rând siguranţa interioară a dumneavoastră dar şi pentru un grad mai mare de încredere privind o cu totul altă modalitate de achiziţie auto faţă de cumpărarea unei maşini din târg şi pentru un confort suplimentar la drum - presupune daca este cazul, de la aparitia unui defect - remedierea în cel mai scurt timp a acestuia, în funcţie de gravitatea situatiei, cu conditia ca defectul sa ne fie comunicat in termen de max. 2 zile lucratoare de la aparitie iar termenul de revizie privind cosumabilele de la masina (ulei si filtre) sa fie valabil; responsabilitatea reviziilor revine exclusiv dvs.in timpul utilizarii masinii, garantia acoperind evident atat costul pieselor cat si al manoperei de inlocuire/reparatie - integral, pe baza de deviz si factura.
                            </li>
                            <li>
                                <b>Livrare la domiciliu</b> - la cerere - pentru aceia dintre dumneavoastră care nu au timpul necesar sau nu se pot deplasa din diverse motive la sediul AUTOLAND în vederea livrării, oriunde pe teritoriul României contra unui cost standard de cca 150eur la un autoturism, 200eur la o autoutilitară.
                            </li>
                        </ul>
                    </div>

                    <div class="fadeOutDown">
                        <h4>3. Ce aduce nou AUTOLAND?</h4>
                        <p>AUTOLAND oferă clienţilor săi posibilitatea de a îşi schimba ulterior maşina prin sistemul de Buy – Back, pus la dispoziţia acestora. Practic, va fi mai uşor ca niciodată să aveţi în permanenţă o maşina sigură, comodă şi confortabilă, fără a mai avea grijile şi stresul aferent vânzării autoturismului vechi, găsirii unui cumpărător corect şi a unui alt autoturism dorit, în conformitate cu noile cerinţe şi nevoi ale dumneavoastră.</p>
                        <p>De asemeni, AUTOLAND introduce conceptul de serviciu complex, venind în întâmpinare atât celor ce vor să cumpere un autovehicul cât şi celor ce deţin unul şi doresc să îl vândă sau să îl schimbe. AUTOLAND pune astfel la dispoziţia clienţilor săi un formular on-line pe care cei interesaţi vor putea să îl completeze şi să îl trimită pe fax (la nr. 0332 816713) sau prin email, la adresa  urmând ca acel autoturism să fie introdus in lista de oferte iar în cel mai scurt timp posibil acelui autovehicul să i se găsească un proprietar nou.</p>
                    </div>

                    <div class="">
                        <h4>4. De ce o maşina de ocazie prin AUTOLAND?</h4>
                        <ul>
                            <li>
                                In cateva randuri să presupunem că doriţi să vă cautaţi singur maşina şi veţi merge personal cu un microbuz până în Germania sau altă ţară (150-200eur);
                            </li>
                            <li>
                                Veţi avea însă acces doar la anumite parcuri auto de pe traseul acestuia, iar daca nu găsiţi nimic vă puteţi intoarce tot cu microbuzul...dar contra cost (încă aprox.150 euro);
                            </li>
                            <li>
                                Este riscant sa mergeti cu sume de bani asupra dumneavoastra iar de cele mai multe ori plata prin banca va mai retine inca o zi sau doua pana se confirma plata banilor;
                            </li>
                            <li>
                                Fiind mai multe persoane în microbuz, acesta nu va mai asteapta ca sa faceti actele, daca ati gasit masina vă lasa in parcul respectiv iar ce faceti mai departe va priveste: cazare, masa, transport hotel - parc auto de unde urmeaza sa ridicati masina;
                            </li>
                            <li>
                                In general se pot vizita prea putine parcuri auto pe zi, ele nu sunt foarte apropiate intre ele, iar un microbuz sta in Germania de obicei pana joi pentru ca sambata cel tarziu acesta trebuie sa fie in tara ca duminica dimineata sa plece din nou in Germania.
                            </li>
                            <li>
                                Daca aveti vreo oferta anume in orasul X pe strada Y nu o sa va duca direct acolo decat daca din intamplare este foarte aproape de itinerariul respectivului microbuz; toate acestea sunt urmarite acum prin GPS, soferul va lasa la o adresa anume doar contra cost (si cu aprobare de la seful lui din tara).
                            </li>
                            <li>
                                Cand se opreste microbuzul la un parc auto, trebuie sa va hotarati repede daca va place vreo masina sau nu pentru ca nu poate sta microbuzul sa va astepte prea mult.
                            </li>
                            <li>
                                De fiecare data este absolut necesara o rezervare prealabila a masinii; o puteti obtine telefonic inca din tara, insa aceasta este valabila de principiu pe o perioada de maximum 2 - 3 zile. Sunt foarte multe cazuri în care dealerul respectiv neavand nimic in scris cu dumneavoastra nu ţine cont de rezervarea telefonică facuta şi vinde maşina primului venit, in acest caz dumneavoastra ajungeti şi constatati că ati cheltuit bani, timp şi nervi pentru o dorinţă care nu s-a concretizat.
                            </li>
                            <li>
                                Daca mai gasiti masina disponibila la vanzare, aproape niciodata nu este timp suficient pentru a o testa asa cum vreti si in multe cazuri oamenii isi cumpara pana la urma ceva numai ca sa nu se intoarca fara nimic de acolo si sa nu piarda banii pe drumul deja platit.
                            </li>
                            <li>
                                În cazul în care nu sunteti mulţumiţi de ceea ce găsiti trebuie să căutăti următoarea maşină, in acest caz fiind indicat sa nu plecati la cumpărat maşini având doar o singura variantă, ci minimum două sau chiar trei.
                            </li>
                            <li>
                                In medie, cam jumatate din pasagerii unui microbuz isi cumpara masini, ceilalti se intorc cu microbuzul inapoi.
                            </li>
                            <li>
                                O maşina de ocazie prin AUTOLAND nu înseamnă niciodată o maşina defectă, ruginită sau murdară, ci din contră, cu o stare tehnică foarte bună, bine întreţinută, cu un aspect îngrijit, curat, atrăgător, fiind de fiecare data aleasă cu grijă şi selectata pe niste criterii bine definite de noi. Există şi maşini ce fac parte din categoria second hand care nu au nici măcar rulajul corespunzător pentru efectuarea primei revizii (sub 15-20 mii km efectuati). Valoarea unei astfel de maşini este semnificativ mai mica decat la o masina noua şi astfel poate să reprezinte o diferenţă importantă pentru dumneavoastră.
                            </li>
                            <li>
                                Masinile vandute de AUTOLAND provin direct de la proprietarul unei masini şi nu de la diversi intermediari, astfel mai putem economisi bani pentru dumneavoastra.
                            </li>
                            <li>
                                Putem asigura transport pe trailer auto sau platforma, la cerere, fara ca masina sa mai circule pe roti si sa faca un rulaj suplimentar;
                            </li>
                            <li>
                                Oricum maşinile noi pierd in general intre 15-30% din preţ în primul an de la cumpărare.
                            </li>
                            <li>
                                Maşinile de ocazie îşi menţin o valoarea mai apropiata de valoarea de achiziţie pentru mai mult timp.
                            </li>
                        </ul>
                    </div>


                    <div class="">
                        <h4>5. De ce o maşina nouă prin AUTOLAND?</h4>
                        <ul>
                            <li>
                                AUTOLAND vinde maşinile direct din import, cu standarde tehnice şi finisaje superioare.
                            </li>
                            <li>
                                Pachetele de dotări sunt superioare celor din România iar calitatea se resimte încă de la primul contact cu noua dumneavoastră maşina.
                            </li>
                            <li>
                                Timpul de livrare este iarăşi foarte important atunci când stilul de viata sau afacerea dumneavoastră impune existenţa în timp util şi real a unui mijloc auto de transport de care aveţi nevoie.
                            </li>
                        </ul>

                        <p>
                            Astfel, AUTOLAND ofera garanţia contractuala a faptului ca în termen de maximum o lună, autovehiculul solicitat va sta la dispoziţia dumneavoastră. In 9 din 10 cazuri intervalul pana la inmatriculare si livrare este mult mai scurt, de aproximativ 2 saptamani. In acest mod va asiguram de la bun inceput de faptul ca AUTOLAND si-a impus ca prioritate majora confortul si increderea clientului, rezultate date doar de servicii foarte profesionale in domeniul auto. 
                        </p>
                        <p>
                            Întrucât scopul primordial al AUTOLAND este acela de a oferi permanent produse şi servicii de înaltã calitate, aşteptăm cu deosebit interes din partea dumneavoastră sugestii, propuneri sau observaţii referitoare la activitatea noastra, pe care va rugăm să ni le transmiteţi prin e-mail.
                        </p>
                    </div>
                </div>
            </div>
            <!--welcome ends-->
        </div>
    </div>

</section>

<?php
include './themes/automotive/template_parts/footer.php';
