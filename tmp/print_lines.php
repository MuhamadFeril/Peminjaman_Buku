<?php
$lines = file(__DIR__ . '/../app/Providers/RouteServiceProvider.php');
foreach ($lines as $i => $line) {
    printf("%4d: %s", $i+1, $line);
}
