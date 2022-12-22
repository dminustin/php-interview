<?php

namespace Danila\PhpCoder;

use Dompdf\Dompdf;
use Dompdf\Options;
use Michelf\Markdown;

class PDFWriter extends BaseWriter
{
    protected string $document = './output/output.pdf';

    public function __construct(protected string $html)
    {
        //Do something
    }

    public function makeDocument(): static
    {
        $options = new Options();
        $options->set('defaultFont', 'Arial Cyr');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($this->html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        ob_start();
        $dompdf->stream();
        file_put_contents($this->document, ob_get_contents());
        ob_end_clean();
        return $this;
    }
}