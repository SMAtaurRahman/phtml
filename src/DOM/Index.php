<?php
namespace PHTML\DOM;

class Index
{

    const IGNORE_TO_INDEX_PROPERTIES = [
        'href' => true,
        'alt' => true,
        'src' => true,
        'placeholder' => true,
        'value' => true,
        'image' => true,
        'script' => true,
        'noscript' => true,
        'style' => true
    ];

    protected $toBeIndex;
    public $parent, $property;

    public function __construct($toBeIndex = [])
    {
        $this->toBeIndex = array_combine(array_values($toBeIndex), array_fill(0, count($toBeIndex), true));
        $this->parent = [];
        $this->property = [];
    }

    public function setParentIndex($parent, $elementID)
    {
        $this->parent[$parent][] = $elementID;
    }

    public function setPropertyIndex($type, $property, $elementID)
    {

        // Check if we should ignore this tag
        if (isset(self::IGNORE_TO_INDEX_PROPERTIES[$type]) || isset(self::IGNORE_TO_INDEX_PROPERTIES[$property]) || (isset($this->toBeIndex['*']) === false && (isset($this->toBeIndex[$type]) === false) && (isset($this->toBeIndex[$property]) === false))) {
            return false;
        }

        $this->property[$type][$property][] = $elementID;
    }
}
