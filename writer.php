<?php

use Danila\PhpCoder\HtmlWriter;
use Danila\PhpCoder\PDFWriter;

$files = [
    'README',
    '250JUN',
    '250MID',
    '250SEN',
    'LARAVEL',
    'PHP82',
    'PHP81',
    'PHP80',
    'PHP74',
    'PHP73',
    'PHP72',
    'PHP71',
];

require 'vendor/autoload.php';

$html = (new HtmlWriter($files))->makeDocument()->getDocument();
//(new PDFWriter($html))->makeDocument();