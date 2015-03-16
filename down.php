<?php

# Kaldes med to parametre
#    md5=              Som skal hentes
#    dest=             Filnavn som skal skrives

$downloaddir = '/Users/mortenb/Downloads/libgennews';
$logdir = dirname(__FILE__) . '/logs';

// Sanity check
if (strpos(getenv('PATH'), '/usr/local/bin:') === false) putenv("PATH=/usr/local/bin:" . getenv('PATH'));     // hvis Apache ikke startes fra mit env kender den ikke mine paths (fx Brew)
if (empty(shell_exec("which wget"))) {
    error_log ('Down.php: Mangler wget');
    header("HTTP/1.0 410 Fucked");
    exit();
}
if (!is_dir($downloaddir)) {
    mkdir($downloaddir);
    if (!is_dir($downloaddir)) {
        error_log ('Down.php: Intet downloaddir: ' . $downloaddir);
        header("HTTP/1.0 410 Fucked");
        exit();
    }
}
if ((!isset($_GET['md5'])) or (!isset($_GET['dest']))) {
    error_log ('Down.php: Kaldet uden sunde argumenter');
    header("HTTP/1.0 410 Fucked");
    exit();
}
$md5 = $_GET['md5'];
$dest = preg_replace('/[\/\?":\n\t]/', "", $_GET['dest']);
if (!is_dir($logdir)) {
    mkdir ($logdir);
    if (!is_writable($logdir)) {
        error_log ('Down.php: Ballade med logdir: ' . $logdir);
        header("HTTP/1.0 410 Fucked");
        exit();
    }
}

$destfile = $downloaddir.'/'.$dest;
$logfile = $logdir.'/'.$md5.'.log';
shell_exec("wget --continue --wait=60 --random-wait --retry-connrefused --background -o \"$logfile\" -O \"$destfile\" \"http://libgen.org/get.php?md5=$md5\"");

?>