<?php

dataset('sticker', function () {
    $file = file_get_contents(__DIR__.'/../Fixtures/Updates/sticker.json');

    return [json_decode($file)];
});
