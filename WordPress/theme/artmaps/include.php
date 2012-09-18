<?php
$trace = debug_backtrace();
error_log("ArtMaps: File " . $trace[0]['file']
        . " is referencing include.php");
require_once("common.php");
?>