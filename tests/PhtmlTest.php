<?php
declare(strict_types=1);

namespace PHTML\Tests;

use PHTML\Html;
use PHTML\PHtml;
use PHTML\Parser;
use PHTML\DOM\Index;
use PHPUnit\Framework\TestCase;

class PhtmlTest extends TestCase
{

    public function testBasicUsage()
    {
        $htmlText = file_get_contents(__DIR__ . '/stubs/basic.html');

        // HTML container
        $html = new Html($htmlText);

        // define which html attributes we want to index
        // indexing makes searching in HTML tree fast
        $index = new Index(['a', 'id', 'class']);

        //build parser
        $parser = new Parser($index);


        // now we can start parsing our html
        /* @var PHTML $phtml */
        $phtml = $parser->parse($html);

        $url = $phtml->getByTag('a')[0]->property('href');
        $title = $phtml->getByTag('a')[0]->value();

        $this->assertEquals('https://google.com/', $url);
        $this->assertEquals('Google.com', $title);
    }
}
