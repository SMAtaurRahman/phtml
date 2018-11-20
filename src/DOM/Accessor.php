<?php
namespace PHTML\DOM;

use PHTML\PHtml;

abstract class Accessor
{

    /**
     * @var PHTML\DOM\Model
     */
    protected $dom;

    /**
     * @var PHTML\DOM\Element
     */
    protected $element;

    public function value()
    {
        return $this->element->value;
    }

    public function property(string $property)
    {
        if (isset($this->element->properties[$property])) {
            return (is_array($this->element->properties[$property]) ? implode(' ', $this->element->properties[$property]) : $this->element->properties[$property]);
        }

        throw new \Exception($property . ' property not found');
    }

    public function nextSibling()
    {
        if (empty($this->dom->index->parent[$this->element->parentID])) {
            return false;
        }

        $sibling = false;
        foreach ($this->dom->index->parent[$this->element->parentID] as $child) {

            if ($sibling === false && $child !== $this->element->id) {
                continue;
            } elseif ($child === $this->element->id) {
                $sibling = true;
                continue;
            } elseif ($sibling === true) {
                $sibling = $child;
                break;
            }
        }

        return is_int($sibling) ? $this->returnByID($sibling) : false;
    }

    public function html()
    {
        if (isset($this->dom->elements[$this->element->id]) === false) {
            return '';
        }

        $element = $this->dom->elements[$this->element->id];

        return $element->html['start'] . $element->value . $this->innerHtml() . $element->html['end'];
    }

    protected function recursiveElementHtml(int $elementID)
    {
        if (isset($this->dom->elements[$elementID]) === false) {
            return '';
        }

        $element = $this->dom->elements[$elementID];

        $html = $element->html['start'] . $element->value;

        if (!empty($this->dom->index->parent[$elementID])) {
            foreach ($this->dom->index->parent[$elementID] as $childID) {
                $html .= $this->recursiveElementHtml($childID);
            }
        }

        return $html . $element->html['end'];
    }

    public function innerHtml()
    {
        $childElements = $this->dom->index->parent[$this->element->id] ?? [];

        if (empty($childElements)) {
            /**
             * Need to return plain text
             */
            return '';
        }

        $html = '';
        foreach ($childElements as $elementID) {
            $html .= $this->recursiveElementHtml($elementID);
        }

        return $html;
    }

    public function get()
    {
        // .class
    }

    public function getByClass(string $selector)
    {
        return $this->getByThat('class', $selector);
    }

    public function getByID(string $selector)
    {
        return $this->getByThat('id', $selector);
    }

    public function getByTag(string $selector)
    {
        return $this->getByThat('tag', $selector);
    }

    public function getByMultiClass(string $class)
    {
        // class1 class2 class3
        $classes = explode(' ', $class);

        if (empty($classes)) {
            return false;
        }

        $candidates = [];

        foreach ($classes as $className) {
            if (!empty($this->dom->index->property['class'][$className])) {
                $candidates[] = $this->dom->index->property['class'][$className];
            }
        }


        if (count($candidates) === 1) {
            return $this->getByClass($class);
        }


        $elements = array_intersect(...$candidates);

        return array_map(function($elementID) {
            return $this->returnByID($elementID);
        }, $elements);
    }

    public function getByTree()
    {
        // tag > class > id
    }

    public function hasClass($class)
    {
        return $this->hasThat('class', $class);
    }

    public function hasID($id)
    {
        return $this->hasThat('id', $id);
    }

    public function hasTag($tag)
    {
        return $this->hasThat('tag', $tag);
    }

    public function hasThat($type, $selector)
    {
        return isset($this->dom->index->property[$type][$selector]);
    }

    protected function getByThat($type, $selector, $order = false, $sort = false, $offset = false, $limit = false)
    {
        if (empty($this->dom->index->property[$type][$selector])) {
            return false;
        }
        $elements = [];

//        pr($this->element);
//        pr($this->dom->index->property[$type][$selector]);
        foreach ($this->dom->index->property[$type][$selector] as $elementID) {

            if (is_null($this->element->childrens) === false && in_array($elementID, $this->element->childrens ?? [], true) === false) {
                continue;
            }
            $elements[] = $this->returnByID($elementID);
        }
        return $elements;
    }

    protected function returnByID(int $elementID)
    {
        if (is_int($elementID) === false) {
            return false;
        }

        $newElement = clone $this->dom->elements[$elementID];
        $newElement->setID($elementID);

        $newElement->childrens = [];
        $this->recursiveChildrenList($elementID, $this->dom->index->parent, $newElement->childrens);

        return new PHtml($this->dom, $newElement);
    }

    public function recursiveChildrenList(int $parentID, $parentIndex, &$childrenList)
    {
        $childrens = $parentIndex[$parentID] ?? [];

        if (!empty($childrens)) {
            foreach ($childrens as $child) {
                $childrenList[] = $child;

                $this->recursiveChildrenList($child, $parentIndex, $childrenList);
            }
        }

        return true;
    }

    public function getNonIndex($selector)
    {
        if (empty($this->dom->elements)) {
            return false;
        }

        $elements = array_filter($this->dom->elements, function($element)use($selector) {
            if (empty($element->properties['class'])) {
                return false;
            }

            return in_array($selector, $element->properties['class'], true);
        });

        return $elements;
    }
}
