# Audit de securitate – autolandromania.ro

**Data:** 27 octombrie 2026
**Metodă:** citirea codului (210 fișiere PHP, fără `tinymce/`) și verificare pasivă pe producție (doar coduri HTTP, fără payload-uri de atac).
**Stare:** nimic din lista de mai jos nu e rezolvat la data auditului. Bifați pe măsură ce rezolvați.

> Fișierul descrie vulnerabilități încă nereparate. `.htaccess` blochează accesul public la `*.md`. Nu-l copiați în alt loc accesibil de pe web.

---

## 0. PHP2Shell (CVE-2026-109618, CVE-2026-109619) – cel mai urgent

Publicate pe 21.10.2026. Ambele permit execuție de cod de la distanță, fără autentificare. Conform advisory-urilor, sunt afectate toate instalările PHP care servesc cereri web (mod_php, PHP-FPM, php-cgi, LiteSpeed). Sunt exploatate activ, iar CISA le-a trecut în catalogul KEV pe 26.10.2026.

- Fix-uri există doar pentru **8.2.35, 8.3.36, 8.4.27 și 8.5.12**.
- **Producția rulează PHP 7.0** (fără actualizări din ianuarie 2019). Serverul e LiteSpeed. Pentru 7.0 nu va apărea niciun fix.
- XAMPP-ul local rulează PHP 7.0.33.

Ce e de făcut:
- [ ] Întrebați hostingul dacă are un build 7.0 cu patch-uri aduse înapoi (CloudLinux alt-php) sau un WAF cu reguli pentru PHP2Shell.
- [ ] Ca variantă, puneți site-ul în spatele Cloudflare, care are reguli WAF pentru PHP2Shell.
- [ ] Planificați migrarea pe PHP 8.2+. Codul folosește funcții care nu mai există în PHP 8: `get_magic_quotes_gpc`, `mysql_*`, `split`.
- [ ] Verificați ca Apache-ul din XAMPP să asculte doar pe `127.0.0.1`.

**Node.js (CVE-2026-111497, Brotli):** nu afectează producția. Node e folosit doar pentru build-ul gulp de pe calculatorul de dezvoltare, iar `node_modules/` dă 404 pe site. Actualizați Node local dacă e pe 22.x, 24.x sau 26.x.

---

## 1. Critic – confirmat, accesibil pe producție

### 1.1 SQL injection în pagini vechi
- `vanzari3.php` liniile 17–19, 37–39, 95+ (`marcat`, `subcateg`, `txtsrc=#S10…`)
- `vanzari.bak.php` liniile 16–19, 40–42 (rulează ca PHP, pentru că `.htaccess` blochează doar fișierele care se *termină* în `.bak`)
- Valorile intră în query fără ghilimele (`WHERE id = %s` + `addslashes`). Erorile SQL se afișează (`or die($connection->error)`).
- Lanț de atac: prin injection se citește tabela `luser`. Parolele de admin sunt **în clar** (`tehnic/login.php:28`), deci atacatorul obține acces complet la admin.
- Producție: ambele pagini răspund **200**.

- [ ] Ștergeți `vanzari3.php`, `vanzari.bak.php` și `index (copie)--.php`.

### 1.2 Scripturi din `tehnic/` fără verificare de login
Nu verifică sesiunea: `actualizare_curs_valutar.php`, `anunturi-admin-card.php`, `check_video_paths.php`, `detect_os.php`, `edit.php`, `header.php`, `header2.php`, `raport_recenzii.php`, `raport_recenzii.bak.php`, `showfoto.php`, `showfoto1.php`, `showvideo.php`, `test.php`, `test_ffmpeg.php`, `test_thumbnail_generation.php`, `thumb.php`, `updatedb.php`, `upload.php`, `upload2.php`, `upload_video2.php`.

Cele mai grave:
- `upload2.php` și `upload_video2.php` permit oricui să adauge poze sau video la **orice anunț** (`?id=`). Pe producție dau 302, dar redirectul e la finalul scriptului, după ce codul a rulat.
- `upload.php` acceptă orice extensie în `foto/`. Acum crapă la `split()`, care nu există în PHP 7.
- `updatedb.php` rulează comenzi care modifică structura bazei de date (200 pe producție).
- `test_ffmpeg.php` și `detect_os.php` afișează informații despre server (200 pe producție).
- `raport_recenzii.php` poate fi apelat de oricine și trimite emailuri.

- [ ] Creați un fișier comun de autentificare (ex. `tehnic/_auth.php`) și includeți-l în toate scripturile admin care trebuie păstrate.
- [ ] Ștergeți `tehnic/test.php`, `test_ffmpeg.php`, `test_thumbnail_generation.php`, `detect_os.php`, `check_video_paths.php`, `updatedb.php`, `upload.php`, `edit.php`, `showfoto1.php`, `raport_recenzii.bak.php`.
- [ ] `raport_recenzii.php`, dacă rulează din cron: protejați-l cu verificarea `REQUEST_METHOD` (cron-ul folosește php-cgi).
- [ ] Puneți un `.htaccess` în `foto/` și `video/` care blochează execuția PHP.

### 1.3 Releu de phishing prin formularul „sunt interesat”
`includes/form_interesatde_mail_processor.php`:
- linia 86 trimite un email de pe `auto@autolandromania.ro` la **orice adresă** din `vizEmail`;
- linia 81: `$_POST['vizNume']` intră neescapat în corpul HTML;
- reCAPTCHA nu e verificat pe server nicăieri (nu există `siteverify` în cod).

Riscuri: phishing trimis cu numele firmei și domeniul ajuns pe liste negre de spam.

- [ ] Verificați reCAPTCHA pe server în ambele procesoare de formulare.
- [ ] Escapați `vizNume` și limitați numărul de trimiteri per IP.

### 1.4 Cod sursă vizibil public
`.htaccess` blochează doar fișierele care se termină în `.bak`, `.old` etc. Fișierele de mai jos sunt servite ca text:
- `tehnic/edit_recenzii.php_old_18_04_2019` (confirmat pe producție)
- `tehnic/header2.php_old_18_04_2019`, `tehnic/solicita_recenzii.php_old_18_04_2019`
- `vanzari.php_backup_02_jul_2025` (confirmat pe producție)

- [ ] Ștergeți fișierele.
- [ ] Extindeți `FilesMatch` din `.htaccess` la nume de tipul `\.php_.*` și `_(old|backup)`.

### 1.5 Parole în cod și în git
- `tehnic/test.php:6` conține o parolă veche de bază de date. Pe producție scriptul afișează eroare fatală cu calea `/home/autoland/public_html/` (`display_errors` e pornit pentru scripturile care nu includ `config.php`).
- `includes/config.php` conține parolele DB și SMTP. Sunt în istoricul git.

- [ ] Schimbați parolele DB și SMTP, plus parola veche dacă mai e folosită undeva.
- [ ] Mutați parolele în afara repository-ului (fișier în afara `public_html` sau variabile de mediu).

---

## 2. Ridicat / mediu

### 2.1 XSS reflectat
- `vanzari.php?marca=</script><script>…`: `$_GET['marca']` ajunge în breadcrumb-ul JSON-LD (`vanzari.php:179`). JSON-ul e generat cu `JSON_UNESCAPED_SLASHES` și fără `JSON_HEX_TAG` (`includes/json_ld_schemas.php:683, 707, 714`; `impresii.php:35`).
- `vanzari_detail.php:30-31` afișează `var_export($_POST)` fără escapare, dacă există `vizUrl`.

- [ ] Adăugați `JSON_HEX_TAG` la toate `json_encode` care ajung în `<script>`.
- [ ] Ștergeți blocul `vizUrl` din `vanzari_detail.php`.

### 2.2 XSS stocat în admin, din recenzii
Textul trimis de clienți prin `completare_recenzie.php` se afișează brut în:
- `tehnic/solicita_recenzii.php:275-278` (nume, prenume, recenzie, email)
- `tehnic/edit_recenzii.php:166-199`
- `tehnic/cereri_rent.php:177`

- [ ] Folosiți `htmlspecialchars()` la afișare.

### 2.3 Autentificare admin slabă
- Parole în clar în `luser`.
- Nu există limită pentru încercările de login.
- Nu se apelează `session_regenerate_id()` după login.
- Redirect deschis: `login.php?accesscheck=https://site-extern` duce acolo după login.
- Cookie-ul `PHPSESSID` are doar `secure`; lipsesc `HttpOnly` și `SameSite`.
- **Nu există protecție CSRF** în admin, iar ștergerile se fac prin GET (`?sterge=`, `imgdel.php?imgId=`).
- `tehnic/imgdel.php:11` are SQL injection. Un link `imgId=1 OR 1=1` deschis de un admin logat șterge toate pozele.

- [ ] Treceți parolele pe `password_hash()` / `password_verify()`.
- [ ] Adăugați `session_regenerate_id(true)` la login și acceptați în `accesscheck` doar căi relative.
- [ ] Setați cookie-ul de sesiune cu `HttpOnly` și `SameSite=Lax`.
- [ ] Adăugați token CSRF pe formularele și acțiunile admin; convertiți `imgId` la `(int)`.

### 2.4 Librării vechi
- PHPMailer 6.0.6 (CVE-2020-13625, CVE-2021-3603, CVE-2021-34551) → actualizați la ≥ 6.5.0.
- TinyMCE 2.1.2 (2007) → actualizați sau înlocuiți.
- `includes/vendor/PHPMailer/get_oauth_token.php` e accesibil public (200) → ștergeți.

### 2.5 Configurare
- Lipsesc header-ele HSTS și Content-Security-Policy.
- `getClientIP()` (`includes/functions.php:478`) are încredere în `X-Forwarded-For`, care poate fi falsificat.
- `DEBUG` e `TRUE` în `config.php`; erorile SQL se afișează prin `or die($connection->error)`.
- `package.json` și `/test.php` (cu `var_dump`) sunt accesibile public.

---

## Ce e în regulă

- `includes/vanzari_catalog.php` – whitelist și escapare pentru filtre
- `pagina.php` – prepared statements și validare de slug
- `articol.php` – validare de slug
- `upload_video2.php` – whitelist de extensii
- Pe producție: `.git/` și `error_log` dau 403, `_backups/`, `_migrari/` și `node_modules/` dau 404

---

## Surse

- CISA – https://www.cisa.gov/news-events/alerts/2026/10/21/critical-vulnerabilities-php-require-immediate-patching
- Unit 42 – https://unit42.paloaltonetworks.com/cve-2026-109618-cve-2026-109619/
- Tenable – https://www.tenable.com/blog/php2shell-cve-2026-109618-cve-2026-109619-frequently-asked-questions-about-critical-php-remote
- watchTowr – https://watchtowr.com/intelligence/php-cve-2026-109618-faq/
- Rapid7 (Node.js) – https://www.rapid7.com/blog/post/etr-cve-2026-111497-critical-out-of-bounds-write-in-the-node-js-brotli-decoder/
