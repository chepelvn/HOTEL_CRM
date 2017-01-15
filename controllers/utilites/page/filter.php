<?
namespace ul\page;
class filter{	protected  $current;
	protected $items = array();

	public function __construct($request = array()){		$this->request = $request;
    }
	public function getInstance(){		return new filter();
	}

	public function setRequest($data){		$this->request = (array)$data;
    }

    public function setUri($uri){
       parse_str($uri, $output);
       $this->request = (array)$output;
    }

    public function set($array_param, $method = null){    	if(!is_array($array_param)){    		$str = $array_param;
    		unset($array_param);    		$array_param[0] = $str;
    		$array_param[1] = null;
        }
    	$this->items[]['name'] = $array_param[0];
    	$this->current = &$this->items[(count($this->items) - 1)];
    	$this->current['bool'] = false;

    	if(isset($array_param[1])){    		 $this->current['param'] = $array_param[1];
        }

        $this->current['request_value'] = (isset($this->request[$array_param[0]]) ? $this->request[$array_param[0]] : null);
        if(is_array($this->current['request_value'])){        	$this->current['request_value'] = array_map('remove_extra_spaces_str', array_values($this->current['request_value']));
        } else {        	$this->current['request_value'] = remove_extra_spaces_str($this->current['request_value']);
        }

        switch($method){        	case 'inArray':
                if(in_array($array_param[0], $this->request)){                	$this->current['bool'] = true;
                }
        	break;

        	default:
               if($this->current['request_value']){
        	      $this->current['bool'] = true;
               }
        	break;
        }


        return $this;
    }

    public function setReplaceArgsSql(array $array){
       $this->set_ags_replace_sql_sql = $array;
    }

    public function sql($sql, $rep_arg = null){    	$this->current['sql_str'] = $sql;
    	$def_arg = "%k%";

        $arg_repl = $arg_v_repl = array();

        if(is_array($this->set_ags_replace_sql_sql)){        	foreach($this->set_ags_replace_sql_sql as $a => $vr){
              $arg_repl[$a] = "%$a%";
              $arg_v_repl[$a] = $vr;
            }
        }

        if(is_array($rep_arg)){
        	foreach($rep_arg as $a => $vr){              $arg_repl[$a] = "%$a%";
              $arg_v_repl[$a] = $vr;
            }
        }

        $replace_val = "";
        switch($this->current['param']){
    	   	 case 'enum':
    	   	    $repl = array();
    	   	    foreach((array) $this->current['request_value'] as $r){
    	   	    	if(is_numeric($r)){
    	   	    		$repl[] = $r;
    	   	    		continue;
    	   	        }

    	   	        $repl[] = "'$r'";
    	   	    }
    	   	    $replace_val = implode(", ", $repl);
    	   	 break;

    	   	 default:
    	   	    $replace_val = $this->current['request_value'];
    	   	    if(is_array($replace_val)){
    	   	    	$replace_val = implode(", ", $replace_val);
    	   	    }
    	   	 break;
    	}

    	$this->current['sql_request'] = str_replace($def_arg, mysql_escape_string($replace_val), $this->current['sql_str']);
    	$this->current['sql_request'] = str_replace($arg_repl, $arg_v_repl, $this->current['sql_request']);
    	return $this;
    }

    public function variation($value_mixed){    	 $this->current['bool'] = false;    	 if(is_array($value_mixed)){    	 	 if(in_array($this->current['request_value'], $value_mixed)){    	 	 	$this->current['bool'] = true;
    	     }

    	     return $this;
    	 }

         if($value_mixed == $this->current['request_value']){         	  $this->current['bool'] = true;
         }         return $this;
    }

    public function select($mix = null){
        $this->current['array_query'] = $mix;
    }

    public function getArrayQuery(){        $array = array();    	foreach($this->items as $item){    		if($item['bool'] == false || !isset($item['bool'])) continue;
    		$array[] = $item['sql_request'];
        }

        return $array;
    }

    public function setMode($mode_name){
    }

    public function getSql($separator = " and "){    	$str_arr = array();    	foreach($this->items as $k => $v){           if($v['bool'] == false || !isset($v['bool'])) continue;
    	   $str_arr[] = $v['sql_request'];
    	}

    	return implode($separator, $str_arr);
    }

    public function getBool($name){    	return $this->get_key_item($name, 'bool');
    }

    public function getItemSql($name){    	return $this->get_key_item($name, 'sql_request');
    }

    public function getResponceOfName($name){    	return $this->get_key_item($name, 'request_value');
    }

    public function getResponceArray($mod = null){    	$item = array();    	foreach($this->items as $v){    		if($mod != 'all' && ($v['bool'] == false || !isset($v['bool'])))continue;
            $item[$v['name']] = $v['request_value'];
        }
        return $item;
    }

    private function get_key_item($name, $key){    	foreach($this->items as $v){    		if($v['name'] == $name){    			return $v[$key];
            }
       }
    }

    public function issetFilter(){    	if(count($this->getResponceArray())){    		return true;
        }
        return false;
    }

    public function close(){    	$this->items = array();
    	$this->request = array();
    	$this->current = array();
    	$this->set_ags_replace_sql_sql = null;
    }
}
?>