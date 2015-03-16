<?php

# Kaldes med een parameter
#    md5=              Som skal hentes

$downloaddir = '/Users/mortenb/Downloads';
$logdir = dirname(__FILE__) . '/logs';

// Sanity check
if (!is_dir($logdir)) {
    error_log ('Getstatus.php: Intet logdir: ' . $logdir);
    header("HTTP/1.0 410 Fucked");
    exit();
}
if (!isset($_GET['md5'])) {
    error_log ('Getstatus.php: Kaldet uden sunde argumenter');
    header("HTTP/1.0 410 Fucked");
    exit();
}
$md5 = $_GET['md5'];
$logfile = $logdir.'/'.$md5.'.log';

if (!file_exists($logfile)) {
    echo 'Log findes ikke: ' . $logfile;
    exit();
}

$logfiletxt = file_get_contents($logfile);

if (preg_match('/ saved \[\d+\/\d+\]/', $logfiletxt)) {
    echo 'Saved!';
} elseif (preg_match('/ERROR \d+/', $logfiletxt, $m)) {
    echo $m[0];
} elseif (preg_match('/(\d+%)[^%]+$/', $logfiletxt, $m)) {
    echo 'Downloading ' . $m[1];
} elseif (preg_match('/Saving to/', $logfiletxt)) {
    echo "Downloading 0%";
}

?>