<?php
namespace PHTML;

use PHTML\DOM\Index;

class Parser
{

    const SELF_CLOSE_TAGS = [
        'area' => true,
        'base' => true,
        'basefont' => true,
        'br' => true,
        'col' => true,
        'embed' => true,
        'frame' => true,
        'hr' => true,
        'img' => true,
        'input' => true,
        'ins' => true,
        'link' => true,
        'meta' => true,
        'param' => true,
        'source' => true,
        'track' => true,
        'wbr' => true
    ];
    const OPTIONAL_CLOSE_TAGS = [
        'tr' => ['tr' => true, 'td' => true, 'th' => true],
        'th' => ['th' => true],
        'td' => ['td' => true],
        'li' => ['li' => true],
        'dt' => ['dt' => true, 'dd' => true],
        'dd' => ['dd' => true, 'dt' => true],
        'dl' => ['dd' => true, 'dt' => true],
        'p' => ['p' => true],
        'nobr' => ['nobr' => true],
        'b' => ['b' => true],
        'option' => ['option' => true],
    ];

    protected $elements, $index;

    public function __construct(Index $index)
    {
        $this->index = $index;
    }

    public function parse(Html $html)
    {

        $elements = $html->breakDown();

        if (empty($elements)) {
            return false;
        }

        //pr($elements);die;
        //Regex List
        $tagRegex = '[a-zA-Z0-9]+';
        $propertyNameRegex = '[a-zA-Z0-9\-]+';

        // Local variables
        // To Do : manage it as oop
        $parents = [];

        foreach ($elements as $key => $element) {

            $elementKey = (!empty($this->elements)) ? count($this->elements) : 0;
            if (!empty($parents)) {
                end($parents);
                $myParent = current($parents);
                reset($parents);
            } else {
                $myParent = -1;
            }

            //pr($myParent);htmlpr($element);

            if (preg_match('#<(?P<tag>' . $tagRegex . ')(?P<properties>.*?)>#', $element, $matches) == false) {

                // This is not a starting html tag
                if (preg_match('#</(?P<tag>' . $tagRegex . ')>#', $element, $endTagMatch) == false) {
                    // This is not a valid html tag

                    if (isset($this->elements[$myParent]) === false) {
                        // Invalid html (comments
                        continue;
                    } else {
                        $this->elements[$myParent]->value .= $element;
                    }
                } else {
                    // Ending tag
                    $reverseParent = array_reverse($parents, true);

                    // We will try to search through html
                    // to find a valid parent for this closing tag
                    foreach ($reverseParent as $kp => $rp) {
                        if (isset($this->elements[$rp])) {
                            if ($this->elements[$rp]->name === strtolower($endTagMatch['tag'])) {
                                $this->elements[$rp]->html['end'] = $element;

//                                pr($parents);
                                unset($parents[$kp]);
                                //pr($endTagMatch['tag'].' removed from parent');
//                                pr($this->elements[$rp]);pr($rp);
//                                pr($parents);
                                break;
                            }
                        } else {
                            unset($parents[$kp]);
                        }
                    }
                }
                continue;
            }

            // valid HTML tag (start)
            $tag = strtolower($matches['tag']);

            if (!empty($tag)) {

                //check if it should force close parents
                /**
                  if (isset(self::OPTIONAL_CLOSE_TAGS[$tag])) {
                  if (!empty($parents)) {
                  $reverseParent = array_reverse($parents, true);
                  foreach ($reverseParent as $rk => $rv) {
                  if (isset(self::OPTIONAL_CLOSE_TAGS[$tag][$this->elements[$rv]->name])) {
                  $this->elements[$rv]->html['end'] = '</' . $this->elements[$rv]->name . '>';
                  unset($parents[$rk]);
                  }
                  }
                  }
                  }
                 * 
                 */
                // check if Self closing tags
                // otherwise push to parents stack
                if (isset(self::SELF_CLOSE_TAGS[$tag]) === false) {
                    //pr($tag.' added to parent');
                    $parents[] = $elementKey;
                }


                // Keep track of tag mapping
                $this->index->setPropertyIndex('tag', $tag, $elementKey);
            }


            $properties = [];
            if (!empty($matches['properties'])) {
                preg_match_all('#(?P<key>' . $propertyNameRegex . ')=["|\'](?P<value>.*?)["|\']#', $matches['properties'], $propertyList);

                if (!empty($propertyList['key'])) {
                    $propertyKeys = $propertyList['key'];
                    foreach ($propertyKeys as $k => $propName) {
                        $pValues = array_filter(explode(' ', ($propertyList['value'][$k] ?? '')));

                        if (!empty($pValues)) {
                            foreach ($pValues as $pVal) {
                                $this->index->setPropertyIndex($propName, $pVal, $elementKey);
                            }
                        }


                        $properties[$propName] = $pValues;
                    }
                }
            }

            //$this->elements[] = array('type' => 'tag', 'name' => $tag, 'properies' => $properties, 'value' => '', 'html' => ['start' => $element, 'end' => ''], 'parent_id' => $myParent);
            $this->elements[] = new DOM\Element('tag', $tag, '', ['start' => $element, 'end' => ''], $properties, $myParent);

            // Maintain Parent Map
            $this->index->setParentIndex($myParent, $elementKey);
        }

        //pr($parents);pr($this->elements);die;
        //pr($this->elements);die;
        $rootElement = clone $this->elements[0];
        $rootElement->setID(0);

        return new PHtml(new DOM\Model($this->elements, $this->index), $rootElement);
    }
}
