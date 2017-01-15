<?
namespace ul\xml;
class array_to_xml extends \XMLWriter{

    public $prm_rootElementName = 'result';
    private $data, $key = "item", $value;

    public function __construct($prm_rootElementName = false, $prm_xsltFilePath='', $page = false){
        $this->openMemory();
        $this->setIndent(true);
        $this->setIndentString(' ');
        $this->startDocument('1.0', 'UTF-8');

        if($prm_xsltFilePath){
            $this->writePi('xml-stylesheet', 'type="text/xsl" href="'.$prm_xsltFilePath.'"');
        }

        $this->startElement($prm_rootElementName ? $prm_rootElementName : $this->prm_rootElementName );
        $this->writeAttribute('xmlns:xlink', "http://www.w3.org/TR/xlink");
    }

    public function setElement($data, $key_node = null){
              	   $key = $this->key;
              }

              if(isset($key_node)){
              }

              if(!$key) continue;

              preg_match("/^\@(.*)$/", $key, $attributes); // поис атрибутов для ветки
              preg_match("/^nodes:(.*)$/", $key, $param_nodes); // параметры...
              preg_match("/^(.*)\:(.*)$/", $key, $space_name);  // параметры для name space node
              preg_match("/^node:(.*)$/", $key, $node_text); //параметры...

              $key = preg_replace("~[^a-zA-Z0-9\-_\x80-\xFF]+~", "", $key);

              if(isset($node_text[0])){
	              if($node_text[1] == "text"){
	                  continue;
	              }

	              if($node_text[1] == "cdata" && !$this->__start_cdata){
	              	    continue;
	              	  }


	                  $this->startCData();
	                  $this->setElement($value);
	                  $this->endCData();

	                  unset($this->__start_cdata);
	                  continue;
	              }

	              if($node_text[1] == "void"){
	                  $this->setElement($value);
	                  continue;
	              }
	          }

              if($attributes[1]){
              	  continue;
              }

              if($param_nodes[0]){
              	  continue;
              }

              if(isset($space_name[1]) && !isset($param_nodes[0]) && !isset($node_text[0])){

                  $prefix = $space_name[1];
                  $name = $space_name[2];
                  $uri = null;

                      $name = $space_uri[1];
                      $uri = str_replace($prefix.":".$name.":", "", $space_name[0]);
                  }

              	  $this->startElementNS($prefix, $name, $uri);
              } else{
                 $this->startElement($key);
              }

              $this->__start_element = true;

              $this->setAttributes($value);

              if(is_array($value)){
              } else {
              	  $this->text($value);
              }

              $this->endElement();
           }
       } else {
           $this->endElement();
       }
    }

    private function setAttributes($attributes = false){
              preg_match("/^\@(.*)$/", $key, $attribute);
              $attribute[1] = preg_replace("~[^a-zA-Z0-9\-_\x80-\xFF]+~", "", $attribute[1]);

              if($attribute[1] == "attributes"){
              	        }
              	    }
              	 } else if(is_string($val)){
              	 }
              } else if($attribute[1]){
              }
           }
        }
    }

    public function getDocument(){
        $this->endElement();
        $this->endDocument();
        return $this->outputMemory();
    }

    public function out(){
        return $this->getDocument();
    }

}
?>