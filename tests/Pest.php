<?php

$TEST_AUTHS = [];

$filePath = dirname(__DIR__) . '/.auth.json';
if (file_exists($filePath)) {
    $TEST_AUTHS = json_decode(file_get_contents($filePath));
}
