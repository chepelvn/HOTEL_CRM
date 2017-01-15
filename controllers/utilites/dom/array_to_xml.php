<?
namespace ul\dom;
class array_to_xml extends \DOMDocument{    public function setElements($data, $node = null){       $domElement = (isset($node) ? $node : $this);       if(is_array($data)){       	  foreach($data as $key => $value){              $node = $this->createElement($key);
              if(is_array($value)){              	  self::setElements($value, $node);
              }
              $domElement->appendChild($node);
       	  }
       }
    }
}

?>