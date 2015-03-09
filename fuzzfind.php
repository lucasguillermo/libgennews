<?php

# Kaldes med en parameter
#    term=              søgefeltet

setlocale(LC_ALL, 'da_DK.utf-8'); putenv('LC_ALL=da_DK.utf-8');      # sikrer at shell_exec kan modtage non-ASCII-tegn fra Python-script

$fuzzfindpy = '/Users/mortenb/Documents/bin/fuzzfind.py';
$ebooksdir = '/Users/mortenb/Documents/eBooks';

// Sanity check
if (!file_exists($fuzzfindpy)) {
    error_log ('fuzzfind.php: Mangler fuzzfind.py');
    header("HTTP/1.0 410 Fucked");
    exit();
}
if ((!isset($_GET['term'])) or (preg_match('/[\n\t"]/', $_GET['term']))) {
    error_log ('fuzzfind.php: Bad input til fuzzfind.php');
    header("HTTP/1.0 410 Fucked");
    exit();
}

$exec_line = $fuzzfindpy . ' "' . $_GET['term'] . '" -s -d "' . $ebooksdir . '"';
error_log ('fuzzfind.php: ' . $exec_line);
$svar = shell_exec($exec_line);
if (!$svar) {
    error_log ('fuzzfind.php: Bad output fra isbn.py');
    header("HTTP/1.0 410 Fucked");
    exit();
} else {
    echo ($svar);
}

?>