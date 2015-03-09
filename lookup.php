<?php

# Kaldes med en parameter
#    isbn=              ISBN-nummer som slås op vha isbn.py

$isbnpy = '/Users/mortenb/Documents/bin/isbn.py';

// Sanity check
if (!file_exists($isbnpy)) {
    error_log ('Lookup.php: Mangler isbn.py');
    header("HTTP/1.0 410 Fucked");
    exit();
}
if ((!isset($_GET['isbn'])) or (!preg_match('/^[-0-9a-zA-Z]+$/', $_GET['isbn']))) {
    error_log ('Lookup.php: Bad input til lookup.php');
    header("HTTP/1.0 410 Fucked");
    exit();
}

exec ($isbnpy . ' ' . $_GET['isbn'], $svar, $errorcode);
if ($errorcode == 1) {
    error_log ('Lookup.php: Bad output fra isbn.py');
    header("HTTP/1.0 410 Fucked");
    exit();
} else {
    echo ($svar[0]);
}

?>