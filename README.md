# PHTML

Simple HTML Dom Parser

## Install
 *Please note that PHP 7.4 or higher is required.*

Via Composer

``` bash
$ composer require ataur/phtml
```

## Usage
``` php
<?php
use PHTML\Html;
use PHTML\PHtml;
use PHTML\Parser;
use PHTML\DOM\Index;

// our HTML content
$htmlText = '<html><head>....</head><body>....</body></html>';

// HTML container
$html = new Html($htmlText);

// define which html attributes we want to index
// indexing makes searching in HTML tree fast
$index = new Index(['a', 'id', 'class']);

//build parser
$parser = new Parser($index);


// now we can start parsing our html
$phtml = $parser->parse($html);

$items = $phtml->getByClass('item');
// will return an array with all class item

foreach($items as $item){
    // can access inner property
    $url = $item->getByTag('a')[0]->property('href');

    $title = $item->getByTag('a')[0]->value();

    // as we used index on 'a',
    // fetching attributes with a tag is much more efficient
}

```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
