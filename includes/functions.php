<?php

/**
 * Common functions
 * 
 * @copyright (c) 2018, Marius Ionel <webmaster@grupnet.ro>
 * @version 0.0.1
 */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/vendor/PHPMailer/src/Exception.php';
require_once __DIR__ . '/vendor/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/vendor/PHPMailer/src/SMTP.php';

check_min_requirements();

// setup timezone
date_default_timezone_set("Europe/Bucharest");

/**
 * Check minimum php version
 * 
 */
function check_min_requirements() {
    if (version_compare(phpversion(), '7.0', '<=')) {
        // php version isn't high enough
        die('Unsuported php version. Please set php to be at least 7.0');
    }
}

/**
 * Stop on a failed query without showing the SQL error to visitors
 *
 * @param mysqli $connection
 */
function db_fail($connection) {
    error_log("SQL error in " . $_SERVER['SCRIPT_NAME'] . ": " . $connection->error);
    if (!headers_sent()) {
        http_response_code(500);
    }
    exit('Eroare temporară. Vă rugăm să reîncercați.');
}

/**
 * Get the picturs array for a carID
 * 
 * @param integer $carID
 * @param resource $connection
 * @return array
 */
function getCarPictures($carID, $connection) {

    $query_picture_string = sprintf("SELECT * FROM fotov WHERE oferta = %s ORDER BY nota ASC", $carID);
    $query_picture = $connection->query($query_picture_string) or db_fail($connection);

    $pictures = array();
    while ($row = mysqli_fetch_assoc($query_picture)) {
        $pictures[] = $row;
    }

    return $pictures;
}

/**
 * Get first picture of the car based on carID
 * 
 * @param integer $carID
 * @param resource $connection MySQL resource connection
 * @return string Path to picture
 */
function getCarFirstPicture($carID, $connection) {
    $pictures = getCarPictures($carID, $connection);

    if (count($pictures) == 0) {
        $pictures[0]['file'] = "image-not-found.png";
    }
    return $pictures[0];
}

/**
 * Get the videos array for a carID
 * 
 * @param integer $carID
 * @param resource $connection
 * @return array
 */
function getCarVideos($carID, $connection) {

    $query_video_string = sprintf("SELECT * FROM fotov_video WHERE oferta = %s ORDER BY nota ASC", $carID);
    $query_video = $connection->query($query_video_string) or db_fail($connection);

    $videos = array();
    while ($row = mysqli_fetch_assoc($query_video)) {
        $videos[] = $row;
    }

    return $videos;
}

/**
 * Paginate the query
 * 
 * @param mysqli $connection Database connection
 * @param string $query MySQL Query
 * @param int $resultItemsPerPage Number of query results per page
 * @param string $pagerClass Class for the unoredered ul pager
 * @param bool $resultSetToArray Set if the query results is array or mysql results
 * @param string $targetPage Filename of file that will display the pager
 * @param string $uriPager The GET uri string to be appended to the target page
 * @return array() Array of paginated query ('pager_arr', 'pager_html', 'pager_resultset')
 */
function paginateQuery($connection, $query, $resultItemsPerPage = NULL, $pagerClass = '', $adiacents = NULL, $resultSetToArray = FALSE, $targetPage = NULL, $uriPager = 'page') {

    // set defaults
    if (is_null($resultItemsPerPage)) {
        $resultItemsPerPage = 10;
    }
    //var_dump($_GET);
    if (is_null($targetPage)) {
        // we must use request uri, but stri $uriPager from it
//        if (isset($_SERVER['REQUEST_URI'])) {
//            $targetPage = remove_querystring_var($_SERVER['REQUEST_URI'], $uriPager);
//        }
        $targetPage = $_SERVER['PHP_SELF'] ."?" .$_SERVER['QUERY_STRING'];
        //var_dump($_SERVER);
    }
    // remove $uriPager from $targetPage
    
    $targetPage = remove_querystring_var($_SERVER['REQUEST_URI'], $uriPager);
    

    if (is_null($adiacents)) {
        $adiacents = 2;
    }

    // try to execute te query in order to validate it
    // and get the number of results
    $result = $connection->query($query) or db_fail($connection);
    $result_count = $result->num_rows;

    if ($result_count == 0) {
        //exit('Avoid division by zero');
        return [
            'pager_arr' => [],
            'pager_html' => [],
            'pager_resultset' => $result
        ];
    }

    // calculate the number of required pages to display the results
    $pages = intval($result_count / $resultItemsPerPage);
    if (($resultItemsPerPage % $result_count) !== 0) {
        // we have orphan results, so increment te number of pages
        $pages += 1;
    }

    // get the curent page, if exists, or set to 1 otherwise
    $currentPage = intval(filter_input(INPUT_GET, $uriPager, FILTER_SANITIZE_NUMBER_INT));

    if ($currentPage > $pages || $currentPage < 0) {
        $currentPage = 1; // don't let the users to fuck with us
    }

    if (!$currentPage) {
        $currentPage = 1;
    }


    /**
     * Return pagination array of the query
     * @var Closure Get the pagination array of the query
     */
    $pagination = (function ()use ($currentPage, $pages, $targetPage, $uriPager, $result_count, $resultItemsPerPage, $adiacents) {
                //echo $pages . " ". $currentPage . " " . $resultItemsPerPage;
                $pagination = [];

                
                // Helper function to properly append query parameters -- added by Gabriela Brezeanu
                $appendParam = function($url, $param) {
                    return strpos($url, '?') !== false ? $url . '&' . $param : $url . '?' . $param;
                };

//                if ($currentPage !== 1) {
//                    $pagination[] = [
//                        'page' => 'Previous',
//                        'link' => "$targetPage?$uriPager=" . ($currentPage - 1),
//                        'active' => FALSE,
//                        'enabled'=>TRUE
//                    ];
//                }
                // 'First' link
                $pagination[] = [
                    'page' => 'First',
                    //'link' => "$targetPage?$uriPager=1",
                    //'link' => "$targetPage&$uriPager=1",
		    'link' => $appendParam($targetPage, "$uriPager=1"),
                    //'link' => (isset($_SERVER['REQUEST_URI'])? "$targetPage&$uriPager=1": "$targetPage?$uriPager=1"),
                    'current' => FALSE,
                    'enabled' => (($currentPage !== 1) ? TRUE : FALSE)
                ];

                // 'Previous' link
                $pagination[] = [
                    'page' => 'Previous',
                    //'link' => (($currentPage !== 1) ? ("$targetPage?$uriPager=" . ($currentPage - 1)) : "$targetPage?$uriPager=1"),
                    //'link' => (($currentPage !== 1) ? ("$targetPage&$uriPager=" . ($currentPage - 1)) : "$targetPage&$uriPager=1"),
                    'link' => (($currentPage !== 1) ? $appendParam($targetPage, "$uriPager=" . ($currentPage - 1)) : $appendParam($targetPage, "$uriPager=1")),
                    'current' => FALSE,
                    'enabled' => (($currentPage !== 1) ? TRUE : FALSE)
                ];

                // pages links ---
                for ($i = 1; $i <= $pages; $i++) {

                    //$link = "$targetPage?$uriPager=" . $i;
                    //$link = "$targetPage&$uriPager=" . $i;
                    $link = $appendParam($targetPage, "$uriPager=" . $i);

                    // disable link for current page
                    if ($i == $currentPage) {
                        $link = "#";
                    }

                    // remove pages links if they are too many
                    // and leave only adiacents around curent page
                    if (($i < ($currentPage - $adiacents)) || ($i > ($currentPage + $adiacents))) {
                        continue;
                    }

                    $pagination[] = [
                        'page' => $i,
                        'link' => $link,
                        'current' => ( $i === $currentPage ),
                        'enabled' => FALSE
                    ];
                }


                // pages link --
                // 'Next' link
                $pagination[] = [
                    'page' => 'Next',
                    //'link' => ((($currentPage !== $pages) && ($currentPage < $pages)) ? ("$targetPage?$uriPager=" . ($currentPage + 1)) : "$targetPage?$uriPager=$currentPage"),
                    //'link' => ((($currentPage !== $pages) && ($currentPage < $pages)) ? ("$targetPage&$uriPager=" . ($currentPage + 1)) : "$targetPage&$uriPager=$currentPage"),
                    'link' => ((($currentPage !== $pages) && ($currentPage < $pages)) ? $appendParam($targetPage, "$uriPager=" . ($currentPage + 1)) : $appendParam($targetPage, "$uriPager=$currentPage")),
                    'current' => FALSE,
                    'enabled' => ((($currentPage !== $pages) && ($currentPage < $pages)) ? TRUE : FALSE)
                ];

                // 'Last' link
                $pagination[] = [
                    'page' => 'Last',
                    //'link' => ((($currentPage !== $pages) && ($currentPage < $pages)) ? ("$targetPage?$uriPager=$pages") : ("$targetPage?$uriPager=$currentPage")),
                    //'link' => ((($currentPage !== $pages) && ($currentPage < $pages)) ? ("$targetPage&$uriPager=$pages") : ("$targetPage&$uriPager=$currentPage")),
                    'link' => ((($currentPage !== $pages) && ($currentPage < $pages)) ? $appendParam($targetPage, "$uriPager=" . ($currentPage + 1)) : $appendParam($targetPage, "$uriPager=$currentPage")),
                    'current' => FALSE,
                    'enabled' => ((($currentPage !== $pages) && ($currentPage < $pages)) ? TRUE : FALSE)
                ];


//                if ($currentPage !== $pages) {
//                    $pagination[] = [
//                        'page' => 'Next',
//                        'link' => "$targetPage?$uriPager=" . ($currentPage + 1),
//                        'active' => FALSE,
//                        'enabled'=>TRUE
//                    ];
//                }

                return $pagination;
            })();


    /**
     * Return pagination as unordered list
     * @var Closure Pager
     */
    $pager = (function() use($pagination, $pagerClass) {

                $links = '';

                foreach ($pagination as $item) {

                    if (is_numeric($item['page'])) {
                        $active = ($item['current'] == TRUE) ? 'active' : '';
                        $links .= '<li class="' . (($item['current'] == TRUE) ? 'active' : '') . '"><a href="' . $item['link'] . '">' . $item['page'] . '</a></li>' . PHP_EOL;
                    } else {
                        switch ($item['page']) {
                            case 'First':

                                $links .= '<li class="' . (($item['enabled'] == TRUE) ? '' : 'disabled') . '"><a href="' . $item['link'] . '"><i class="fa fa-angle-double-left"></i></a></li>' . PHP_EOL;
                                break;

                            case 'Previous':
                                $links .= '<li class="' . (($item['enabled'] == TRUE) ? '' : 'disabled') . '"><a href="' . $item['link'] . '"><i class="fa fa-angle-left"></i></a></li>' . PHP_EOL;

                                break;

                            case 'Next':
                                $links .= '<li class="' . (($item['enabled'] == TRUE) ? '' : 'disabled') . '"><a href="' . $item['link'] . '"><i class="fa fa-angle-right"></i></a></li>' . PHP_EOL;

                                break;

                            case 'Last':
                                $links .= '<li class="' . (($item['enabled'] == TRUE) ? '' : 'disabled') . '"><a href="' . $item['link'] . '"><i class="fa fa-angle-double-right"></i></a></li>' . PHP_EOL;

                                break;

                            default:
                                break;
                        }
                        //$links .= '<li><a href="">' . $item['page'] . '</a></li>' . PHP_EOL;
                    }
                }

                $pager = '<ul class="' . $pagerClass . '">' . PHP_EOL;
                $pager .= $links;
                $pager .= '</ul>' . PHP_EOL;

                return $pager;
            })();


    // get paginated query
    $paginatedQuery = rtrim($query) . " LIMIT {$resultItemsPerPage} OFFSET " . (($currentPage - 1) * $resultItemsPerPage);

    //$paginatedQuery = sprintf(rtrim($query) . ' LIMIT %d OFFSET %d', $resultItemsPerPage, (($currentPage - 1) * $resultItemsPerPage));
    $paginatedResult = $connection->query($paginatedQuery) or db_fail($connection);


    // prepare the paginated result if array is wanted
    if ($resultSetToArray === TRUE) {
        $resToArr = array();

        while ($row = mysqli_fetch_assoc($paginatedResult)) {
            $resToArr[] = $row;
        }

        // override the result
        $paginatedResult = $resToArr;
    }


    return [
        'pager_arr' => $pagination,
        'pager_html' => $pager,
        'pager_resultset' => $paginatedResult
    ];
}


function remove_querystring_var($url, $key) { 
	$url = preg_replace('/(.*)(?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&'); 
	$url = substr($url, 0, -1); 
	return $url; 
}

/**
 * 
 * @global type $conn
 * @param type $query
 * @param type $orderby
 * @param type $limit
 * @param type $targetpage
 * @return type
 */
function pagination($query, $orderby, $limit, $targetpage) {
    global $connection;
    $sql = $connection->query($query);
    $total = $sql->num_rows;
    $adjacents = 3;
    $targetpage = $targetpage;
    $limit = $limit;

    if (isset($_GET['page'])) {
        $page = filter_var($_GET['page'], FILTER_VALIDATE_INT);
        unset($_GET['page']);
    } else {
        $page = 0;
    }
    $alteoptiuni = "";

    foreach ($_GET as $key => $val) {
        $alteoptiuni .= "&$key=$val";
    }

    if ($page) {
        $start = ($page - 1) * $limit;
    } else {
        $start = 0;
    }
    /* Setup page vars for display. */
    if ($page == 0)
        $page = 1; //if no page var is given, default to 1.
    $prev = $page - 1; //previous page is current page - 1
    $next = $page + 1; //next page is current page + 1
    $lastpage = ceil($total / $limit); //lastpage.
    $lpm1 = $lastpage - 1; //last page minus 1    
    $sql2 = $query;
    $sql2 .= " ORDER BY $orderby LIMIT $start ,$limit ";
    $sql2_query = $connection->query($sql2);

    /* CREATE THE PAGINATION */

    $pagination = "";
    $counter=1;
    if ($lastpage > 1) {
        $pagination .= "<ul class='pagination'>";
        if ($page > $counter + 1) {
            $pagination .= "<li><a href=\"$targetpage?page=$prev$alteoptiuni\"><</a></li>";
        }

        if ($lastpage < 7 + ($adjacents * 2)) {
            for ($counter = 1; $counter <= $lastpage; $counter++) {
                if ($counter == $page)
                    $pagination .= "<li class='active'><a href='#'>$counter</a></li>";
                else
                    $pagination .= "<li><a href=\"$targetpage?page=$counter$alteoptiuni\">$counter</a></li>";
            }
        }
        elseif ($lastpage > 5 + ($adjacents * 2)) { //enough pages to hide some
            //close to beginning; only hide later pages
            if ($page < 1 + ($adjacents * 2)) {
                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                    if ($counter == $page)
                        $pagination .= "<li class='active'><a href='#'>$counter</a></li>";
                    else
                        $pagination .= "<li><a href=\"$targetpage?page=$counter$alteoptiuni\">$counter</a></li>";
                }
                $pagination .= "<li>...</li>";
                $pagination .= "<li><a href=\"$targetpage?page=$lpm1$alteoptiuni\">$lpm1</a></li>";
                $pagination .= "<li><a href=\"$targetpage?page=$lastpage$alteoptiuni\">$lastpage</a></li>";
            }
            //in middle; hide some front and some back
            elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                $pagination .= "<li><a href=\"$targetpage?page=1$alteoptiuni\">1</a></li>";
                $pagination .= "<li><a href=\"$targetpage?page=2$alteoptiuni\">2</a></li>";
                $pagination .= "<li>...</li>";
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                    if ($counter == $page)
                        $pagination .= "<li class='active'><a href='#'>$counter</a></li>";
                    else
                        $pagination .= "<li><a href=\"$targetpage?page=$counter$alteoptiuni\">$counter</a></li>";
                }
                $pagination .= "<li>...</li>";
                $pagination .= "<li><a href=\"$targetpage?page=$lpm1$alteoptiuni\">$lpm1</a></li>";
                $pagination .= "<li><a href=\"$targetpage?page=$lastpage$alteoptiuni\">$lastpage</a></li>";
            }
            //close to end; only hide early pages
            else {
                $pagination .= "<li><a href=\"$targetpage?page=1$alteoptiuni\">1</a></li>";
                $pagination .= "<li><a href=\"$targetpage?page=2$alteoptiuni\">2</a></li>";
                $pagination .= "<li>...</li>";
                for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                    if ($counter == $page)
                        $pagination .= "<li class='active'><a href='#'>$counter</a></li>";
                    else
                        $pagination .= "<li><a href=\"$targetpage?page=$counter$alteoptiuni\">$counter</a></li>";
                }
            }
        }

        //next button
        if ($page < $counter - 1)
            $pagination .= "<li><a href=\"$targetpage?page=$next$alteoptiuni\">></a></li>";
        else
            $pagination .= "";
        $pagination .= "</ul>\n";
    }
    return array('pag' => $pagination, 'start' => $start, 'limit' => $limit);
}

/**
 * 
 * @return string
 */
function getClientIP() {

    if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
        return $_SERVER["HTTP_X_FORWARDED_FOR"];
    } else if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
        return $_SERVER["REMOTE_ADDR"];
    } else if (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
        return $_SERVER["HTTP_CLIENT_IP"];
    }

    return '';
}

/**
 * Sent SMTP email
 * 
 * @global type $email_username
 * @global type $email_host
 * @global type $email_password
 * @param type $to
 * @param type $subject
 * @param string $body
 * @return type
 */
function sendSMTPEmail($to, $subject, $body) {
    global $email_username;
    global $email_host;
    global $email_password;

    $mail = new PHPMailer(TRUE); // enable exception

    try {
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = $email_host;
        if (!empty($email_username) && !empty($email_password)) {
            $mail->SMTPAuth = TRUE;
            $mail->Username = $email_username;
            $mail->Password = $email_password;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $body .= PHP_EOL . "Acest mesaj a folosit autentificare SMTP";
        }

        $mail->setFrom($email_username, 'Office Autoland');
        if (strcmp($to,'auto@autolandromania.ro') == 0) {$mail->addAddress('office@autolandromania.ro', '');}
        else {$mail->addAddress($to, '');}
        
        $mail->addReplyTo('office@autolandromania.ro', 'Office Autoland');

        // Set UTF-8 encoding to properly handle Romanian diacritics (ă, â, î, ș, ț)
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();

        return ['status' => 'success'];
    } catch (Exception $exc) {
        $err = str_replace(":", " ", $mail->ErrorInfo);
        return ['status' => 'error', 'error' => $err];
    }
}

/**
 * Remove generic sales phrases (rate, leasing, avans 0, garantie...) from the admin "tip" text.
 * Car-specific details (L2H2, 7 locuri, 4x4, automat, motor) are kept.
 */
function clean_car_tip($tip) {
    $t = ' ' . trim((string)$tip) . ' ';
    $patterns = array(
        '/\bsecond[ -]?hand\b/iu',
        '/\b(in|cu)\s+(leasing|rate)(\s+(sau|si)\s+(leasing|rate))?\b/iu',
        '/\bavans\s*(zero|0|z\w*)?\b/iu',
        '/\bfara\s+avans\b/iu',
        '/\bgarantie(\s*(12\s*luni|1\s*an|\d+\s*luni))?\b/iu',
        '/\bgar(an|a)?\s*$/iu',
        '/\brate\s+auto\b/iu',
        '/\bavan?s?\s*$/iu',
        '/\b(leasing|rate|credit|finantare|ieftin[a]?)\b/iu',
        '/\bpersoan[ae]\s+(fizic[ae]|juridic[ae])\b/iu',
    );
    $t = preg_replace($patterns, ' ', $t);
    // leftover connectors and separators
    $t = preg_replace('/\b(sau|si)\b(?=[\s,\-–]*($|[,\-–]))/iu', ' ', $t);
    $t = preg_replace('/\s*,\s*(,\s*)+/u', ', ', $t);
    $t = preg_replace('/\s+-\s+(-\s+)+/u', ' - ', $t);
    $t = preg_replace('/(\s*[,\-–]\s*)+$/u', '', $t);
    $t = preg_replace('/^(\s*[,\-–]\s*)+/u', '', $t);
    $t = preg_replace('/\s*-\s*,\s*/u', ' - ', $t);
    $t = preg_replace('/\s*,\s*-\s*/u', ' - ', $t);
    $t = preg_replace('/\s{2,}/u', ' ', $t);
    return trim($t, " ,-–");
}

/**
 * Page title for a car: "Marca Tip [automat] [110 CP] [7 locuri] An[, în rate, avans 0%] – Second Hand Iași"
 *
 * @param array $car   row from masini (marca, tip, an, cat, subcateg)
 * @param array|null $csv row from for_csv (combustibil, transmisie, nr_locuri)
 */
function car_page_title(array $car, $csv = null) {
    $is_utilaje = (isset($car['cat']) && $car['cat'] === 'Utilaje');
    $tip = clean_car_tip(isset($car['tip']) ? $car['tip'] : '');
    $marca = trim(isset($car['marca']) ? $car['marca'] : '');

    // Utilaje: tip conține deja marca + model
    $name = $is_utilaje ? $tip : trim($marca . ' ' . $tip);
    if ($name === '') {
        $name = trim($marca . ' ' . (isset($csv['model']) ? $csv['model'] : ''));
    }
    $lower = mb_strtolower($name, 'UTF-8');

    $extra = array();
    if (!$is_utilaje && $csv) {
        // Transmisie automată (manuala e implicită)
        $is_electric = (!empty($csv['combustibil']) && stripos($csv['combustibil'], 'electric') !== false) || strpos($lower, 'electr') !== false;
        if (!$is_electric && !empty($csv['transmisie']) && stripos($csv['transmisie'], 'autom') === 0
            && !preg_match('/autom|dsg|tiptronic|cvt/u', $lower)) {
            $extra[] = 'automat';
        }
        // Puterea motorului (for_csv.putere_motor, în CP), dacă nu apare deja în tip.
        // Valorile în afara 40–700 sunt greșeli de introducere (cilindree, wați) și se ignoră.
        $power = !empty($csv['putere_motor']) ? (int)$csv['putere_motor'] : 0;
        if ($power >= 40 && $power <= 700 && !preg_match('/\d\s*(cp|hp|ps|kw)\b/u', $lower)) {
            $extra[] = $power . ' CP';
        }
        // 6+ locuri (se caută: „7 locuri”, „vivaro 6 locuri”)
        if (!empty($csv['nr_locuri']) && (int)$csv['nr_locuri'] >= 6 && strpos($lower, 'locuri') === false) {
            $extra[] = (int)$csv['nr_locuri'] . ' locuri';
        }
    }
    if (!empty($car['an']) && strpos($name, (string)$car['an']) === false) {
        $extra[] = (int)$car['an'];
    }

    // Finanțarea rămâne în titlu doar dacă e trecută în admin (tip), scrisă unitar
    $finance = car_finance_label(isset($car['tip']) ? $car['tip'] : '');

    $suffix = $is_utilaje ? ' – Utilaje Second Hand Iași' : ' – Second Hand Iași';
    return trim($name . ($extra ? ' ' . implode(' ', $extra) : '')) . ($finance ? ', ' . $finance : '') . $suffix;
}

/**
 * Financing label from the admin "tip" text: only what the dealer already wrote there, normalized.
 * "rate, avans 0" / "avans zero" / "fara avans" -> "în rate, avans 0%"; rate + leasing -> "în rate sau leasing".
 */
function car_finance_label($tip) {
    $t = mb_strtolower((string)$tip, 'UTF-8');
    $avans0 = preg_match('/avans\s*(0|z)|fara\s+avans/u', $t); // "avans z" = "avans zero" tăiat în admin
    $rate = preg_match('/\b(rate|credit)\b/u', $t);
    $leasing = strpos($t, 'leasing') !== false;
    if ($avans0) {
        return 'în rate, avans 0%';
    }
    if ($rate && $leasing) {
        return 'în rate sau leasing';
    }
    if ($rate) {
        return 'în rate';
    }
    return $leasing ? 'în leasing' : '';
}
