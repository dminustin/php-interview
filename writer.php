<?php

use Danila\PhpCoder\HtmlWriter;

$files = [
    'README',
    '250JUN',
    '250MID',
    '250SEN',
    'PHP82',
    'PHP81',
    'PHP80',
    'PHP74',
    'PHP73',
    'PHP72',
    'PHP71',
];

require 'vendor/autoload.php';

(new HtmlWriter($files))->makeDocument();