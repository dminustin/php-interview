<?php

namespace Danila\PhpCoder;

use Michelf\Markdown;

class HtmlWriter extends BaseWriter
{
    protected string $document = './output/output.html';

    public function __construct(protected array $files)
    {
        //Do something
    }

    public function makeDocument(): static
    {
        $this->startDocument();
        foreach ($this->files as $filename) {

            $this->writeAppend($this->preprocessCode($filename));
        }
        $this->finishDocument();
        return $this;
    }

    public function getDocument(): string
    {
        return file_get_contents($this->document);
    }

    protected function preprocessCode(string $filename): string
    {
        $text = file_get_contents('./docs/' . $filename . '.MD');
        $text = preg_replace('#````php(.*?)````#si','<pre>$1</pre>', $text);
        $text = Markdown::defaultTransform($text) . PHP_EOL;
        $text = preg_replace('#<em>(.*?)</em>#s',"_$1_", $text);

        return $text;
    }

    protected function startDocument()
    {
        file_put_contents($this->document, file_get_contents('./template/header.html'));
    }

    protected function writeAppend(string $text)
    {
        file_put_contents($this->document, $text . PHP_EOL, FILE_APPEND);
    }

    protected function finishDocument()
    {
        file_put_contents($this->document, file_get_contents('./template/footer.html'), FILE_APPEND);
    }
}