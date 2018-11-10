<?php
namespace PHTML\DOM;

class Model
{

    public $elements, $index;

    public function __construct($elements, $index)
    {
        $this->elements = $elements;
        $this->index = $index;
    }
}
