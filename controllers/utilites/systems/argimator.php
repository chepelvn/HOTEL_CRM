<?
namespace ul\systems;
/*
   ver 1.0;
   Утилита для парсинга строк и массивов возвращаемых функцией
   Описание
      set(bool mixed, bool string, bool int,string)
         1 - входные данные, которые будем преобразовывать.
             Аргументом может являться массив, кодированная срока(param=val&param1=val1), json строка({"param": "val"})
         2 - типы преобразовани входный данных, пример: array,str,json
         3 - идетификатор установки. Нужен для получения выходных данных для getParams(ID)

      variationVariables(bool array) - фильтр по возможным параметрам.
      getParams(bool string,int) - получам преобразованные данные.(Возможен вывод по Id)
*/


class argimator{	private $mixed = "";
	private $METHODS = array('array', 'str', 'json');
	private $__variation = array();
	private $__id = 0;
	public $items = array();
	private $__def = 0;
	public function variationVariables($variables){        $this->__variation = $variables;
	}

	private function __getMethods($param){        $param = (!is_array($param) ? explode(',', preg_replace('~[^a-zA-z\,]+~', '', $param)) : $param);
        $var = array();		foreach($param as $key){			if(in_array($key, $this->METHODS)) $var[] = $key;
        }
        return $var;
    }

    private function __setId($id = null){        if(!$id) $id = $this->__id++;
        $c = &$this->getCurrent();
        $c['id'] = $id;
    }

    private function &getCurrent(){        return $this->items[(count($this->items) - 1)];
    }

    private function __parseMethods(){    	 $c = &$this->getCurrent();         foreach($c['method'] as $key){             $c['varibles'][$key] = call_user_func_array(array($this, '__parse__'.$key), array($c['mixed']));         }
    }

    private function __parse__array($param = null){        if(is_array($param)){        	 return $param;
        }
        return null;
    }

    private function __parse__json($param = null){
        if(is_string($param)){        	$decode = json_decode(str_replace("'", "\"", $param), true);        	if((json_last_error() == JSON_ERROR_NONE)){
        	    return $decode;
            }
        }
        return null;
    }

    private function __parse__str($param){        if(is_string($param)){        	json_decode(str_replace("'", "\"", $param));        	if(!(json_last_error() == JSON_ERROR_NONE)){	        	parse_str(rtrim(trim($param, '?')), $out);	        	return $out;
	        }
        }
        return null;
    }
	public function set($mixed, $method = 'array', $id = null){        $this->items[] = array(
          'method' => $this->__getMethods($method),
          'mixed' => $mixed,
        );
        $this->__parseMethods();
        $this->__setId($id);
	}

	public function def($mode = null){        $this->__def = $mode;
	}

	public function getParams($id = null, $all = false){         foreach($this->items as $prop){         	if($id) if($id != $prop['id']) continue;           	foreach($prop['varibles'] as $type_vars){
               	if(isset($type_vars)){                   if($this->__variation && $all == false){                      foreach($type_vars as $var => $val){                      	 if(!in_array($var, $this->__variation)) unset($type_vars[$var]);
                      }
                   }
               	   return $type_vars;
               	}
            }
         }

         return null;
	}
}
?>