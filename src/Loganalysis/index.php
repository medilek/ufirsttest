<?php
include('loganalysis.php');

$loganalyis = new Loganalysis();
return $loganalyis->loadLog();

