<?php
namespace PHTML;

use PHTML\DOM\Model;
use PHTML\DOM\Accessor;
use PHTML\DOM\Element;

class PHtml extends Accessor
{

    public function __construct(Model $dom, Element $element)
    {
        $this->dom = $dom;
        $this->element = $element;
    }
}
