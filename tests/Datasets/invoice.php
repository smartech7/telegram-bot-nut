<?php

dataset('invoice', function () {
    $file = file_get_contents(__DIR__.'/../Fixtures/Updates/invoice.json');

    return [json_decode($file)];
});
