<?php
namespace PHTML\DOM;

class Element
{

    public $id, $type, $name, $value, $html, $properties, $childrens, $parentID;

    public function __construct(string $type, string $name, string $value, array $html, array $properties, int $parentID)
    {
        $this->type = $type;
        $this->name = $name;
        $this->value = $value;
        $this->html = $html;
        $this->properties = $properties;
        $this->parentID = $parentID;
    }

    public function setID(int $id)
    {
        $this->id = $id;
    }
}
