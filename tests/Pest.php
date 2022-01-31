<?php

$adaptersWithTestCredentials = [
    'Acube',
    'Aruba',
    'EFFatta',
];

$adaptersWithTestCredentials = array_map(fn ($d) => dirname(__DIR__). "/src/Adapter/$d/tests/", $adaptersWithTestCredentials);

uses()->beforeEach(function () {
    $this->credentials = [];
    $this->hasCredentials = false;

    $filePath = dirname(__DIR__) . '/.auth.json';
    if (file_exists($filePath)) {
        $this->credentials = json_decode(file_get_contents($filePath));
        $this->hasCredentials = true;
    }


})->in(...$adaptersWithTestCredentials);
