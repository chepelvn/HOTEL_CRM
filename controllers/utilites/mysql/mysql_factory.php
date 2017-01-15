<?
abstract class mysql_factory{	protected $__join = array(),
	          $__where = array(),
	          $__order = array(),
	          $__find = array(),
	          $__limit = array(),
	          $__params = array(),
	          $__what = array(),
	          $__query_db = array(),
	          $__insert_id = false,
	          $__query_string = false,
	          $__callback = array(),
	          $__primary_key = false,
	          $__insert_props = array(),
	          $__execute_params = array(),
	          $__statement_prepare = null,
	          $__responce = null,
	          $_qsc = null,
	          $conn = false,
	          $__set_props = array(),
	          $__main_from_table = null,
	          $__set_fetch_mode_params = array(\PDO::FETCH_ASSOC);
	protected function __create_object_call_func($method, $args){
		return call_user_func_array(array(new mysql(self::__get_system_pdo()), $method), $args);
    }

    //сливает массивы полученный через where и парсит из них строку
    public function __parse_params_where(){
		$data = array();
		foreach($this->__where as $vars){
			$data = array_merge($data, (array)$vars);
        }
		return $this->__parse_selector_where($data);
	}

   //сливает массивы полученный через select и парсит из них строку
	public function __parse_params_select(){        $data = array();
	    foreach($this->__query_db as $vars){
			$data = array_merge($data, (array)$vars);
        }
        return $this->__parse_selector_from_table($data);
	}

    //сливает массивы полученный через find и парсит из них строку
	public function __parse_params_find(){
        $data = array();
	    foreach($this->__find as $vars){
			$data = array_merge($data, (array)$vars);
        }
        return $this->__parse_selector_find($data);
	}

	public function __parse_params_what(){		$data = array();
	    foreach($this->__what as $vars){
			$data = array_merge($data, (array)$vars);
        }
        return $this->__preparse_selector_what($data);
    }

    public function __parse_params_insert_props(){
		$data = array();
	    foreach($this->__insert_props as $vars){
			$data = array_merge($data, (array)$vars);
        }
        return $this->__parse_selector_insert_props($data);
    }

    protected function __preparse_selector_what($data){    	$data = (array)$data;
		$_what = array();
		foreach($data as $prop => $val){
           if(is_array($val) && is_string($prop)){
               $w = $this->__parse_selector_what_set($prop, $val);
           } else {
           	   $w = $this->__parse_selector_what_set(null, array($prop => $val));
           }
           $_what[] = implode(",", $w);
		}
		return implode(', ', $_what);
    }

    //парсер строки для FROM TABLE
	protected function __parse_selector_from_table($table = null){		$mft = &$this->__main_from_table;        if(is_array($table)){
	       $pre_query_db = array();
	       foreach($table as $pref => $table_name){
	           if(is_numeric($pref)){
	               $pre_query_db[] = $table_name;
	           } else {
	               $pre_query_db[] = $table_name.' '.$pref;
	           }

	           if(!$mft){	           	   $exp = explode(',', trim(preg_replace("/ {2,}/"," ",$table_name)));
	           	   $pret = explode(' ', $exp[0]);
	           	   $prefx = (is_numeric($pref) ? $pret[1] : $pref);
	       	   	   $mft = array(
	       	   	     '_table' => $pret[0],
	       	   	     '_pref' => $prefx
	       	   	   );
	       	   }
	       }
           return implode(', ', $pre_query_db);
        }
        return false;
	}

	protected function get_main_from_table(){		$this->__parse_params_select();		return $this->__main_from_table;
    }
	protected function get_string_find($insert_pref = null, $separ = ','){
		$str = $this->__parse_params_find();
		return (($insert_pref && $str) ? " $insert_pref " : " ").$str." ";
    }

    protected function get_string_where($insert_pref = null, $separ = ' AND '){
    	$str = $this->__parse_params_where();
		return (($insert_pref && $str) ? " $insert_pref " : " ").$str." ";
    }

    protected function get_string_what($insert_pref = null, $separ = ','){
        $str = $this->__parse_params_what();
		return (($insert_pref && $str) ? " $insert_pref " : " ").$str." ";
    }

    protected function get_string_from_table($insert_pref = null, $separ = ','){
        $str = $this->__parse_params_select();
		return (($insert_pref && $str) ? " $insert_pref " : " ").$str." ";
    }

    protected function get_string_join($insert_pref = null, $separ = ' '){
        $str = implode($separ, $this->__join);
		return (($insert_pref && $str) ? " $insert_pref " : " ").$str." ";
    }

    protected function get_string_order($insert_pref = null, $separ = ' '){
        $str = implode($separ, $this->__order);
		return (($insert_pref && $str) ? " $insert_pref " : " ").$str." ";
    }

    protected function get_string_limit($insert_pref = null, $separ = ', '){
        $str = implode($separ, $this->__limit);
		return (($insert_pref && $str) ? " $insert_pref " : " ").$str." ";
    }

    protected function get_string_insert_props($insert_pref = null, $separ = ', '){
        $str = $this->__parse_params_insert_props();
		return (($insert_pref && $str) ? " $insert_pref " : " ").$str." ";
    }


    protected function get_string_params($insert_pref = null, $separ = ' '){
        $str = implode($separ, $this->__params);
		return (($insert_pref && $str) ? " $insert_pref " : " ").$str." ";
    }

    public function __get_primary_data($param = null){    	$mt = $this->get_main_from_table();    	$responce = $this->__pdo_execute('SHOW KEYS FROM '.@$mt['_table'].' WHERE Key_name = "PRIMARY"')->fetch(\PDO::FETCH_ASSOC);
    	$responce = array_merge($responce, $mt);    	if($param) return @$responce[$param];    	return $responce;
    }

    protected function __set_selection_where_primary_key(){        if($this->__primary_key){         	$primary = $this->__get_primary_data();
         	$col = $primary['Column_name'];
         	if($primary['_pref']) $col = $primary['_pref'].'.'.$col;
         	$this->where(array($col => $this->__primary_key));
        }
    }

	protected function __parse_selector_what_set($table = "", $values){
        if($table) $table = $table.".";
        $values = (array)$values;
        $__what = array();
        foreach($values as $prop => $value){
            $__what[] = $table.$prop."=".$this->__quote((string)$value);
        }
        return $__what;
	}

	public function __get_parse_query_str($method){		$str = null;
		if(!($this->__statement_prepare instanceof \PDOStatement)){           switch($method){           	  case 'count': break;
           	  default:      	        $str = $this->__parse_query__query();
                break;
           }
		   if(!isset($str)){		   	   if(!method_exists($this, '__parse_query__'.$method)){		   	   	  trigger_error('not exists praser method '.$method);
		   	   	  exit();
		   	   }		       $str = call_user_func(array($this, '__parse_query__'.$method));
		   }
	    }
		return $str;
	}

	public function __parse_query__select(){
       $find = $this->get_string_find();
       if(!str_replace(' ', '', $find)) $find = "*";

       $this->__set_selection_where_primary_key(); //выборка по primary_key

       $query = "SELECT ".$find." FROM ".$this->get_string_from_table()
                  .$this->get_string_join().$this->get_string_where('WHERE')
                  .$this->get_string_order().$this->get_string_limit('LIMIT')
                  .$this->get_string_params();
       return $query;
	}

	public function __parse_query__insert(){        return "INSERT INTO ".$this->get_string_from_table()." ".$this->get_string_insert_props();
	}

	public function __parse_query__update(){        return "UPDATE ".$this->get_string_from_table()." SET ".
	             $this->get_string_what().
	             $this->get_string_where('WHERE');
	}

	public function __parse_query__delete(){         return "DELETE FROM ".$this->get_string_from_table()." WHERE ".$this->get_string_where();
	}

	protected function __parse_query__count(){		$str = $this->__get_query_method_or_qsc('select');
        return preg_replace('/SELECT\s(.*)\sFROM/is', "SELECT COUNT(*) AS count FROM ", $str);
	}

	public function __parse_query__query(){         return $this->_qsc;
	}

	public function __get_query_method_or_qsc($to_method = 'select'){
         return ($this->_qsc ? $this->_qsc : $this->__get_parse_query_str($to_method));
	}

	protected function __is_instance(){
		return ($this instanceof mysql);
    }

    protected function __parse_selector_find($mixed = null, $prefix = null){    	 $think = $this;    	 $data = $mixed;    	 if(is_array($mixed)){
		     $sel_arrp = array();
		     foreach($mixed as $s_k => $s_v){		     	if(is_array($s_v)){		            $sel_arrp[] = $this->__parse_selector_find($s_v, $s_k.'.');
                    continue;
		        } else if(!is_numeric($s_k)){
		       	    $sel_arrp[] = $prefix.$s_k . " AS " . $s_v;
		       	    continue;
		       	}
		       	$sel_arrp[] = $prefix.$s_v;
		     }
	         $data = implode(", ", $sel_arrp);
	     }
	     return $data;
	}

    protected function __parse_selector_where($w_selection = null, $separator = "AND"){
       $separator = (!$separator) ? 'AND' : $separator;
       $where = null;
       $w = $w_selection;

       if(is_array($w_selection)){
          $array = array();
       	  foreach($w_selection as $name => $val){
       	  	  if(is_array($val) && empty($val)) continue;
              $exp = explode(':', $name);

              $col = $exp[0]; //имя столбца
              $met = (!mb_strtoupper(@$exp[1]) ? '=' : mb_strtoupper(@$exp[1])); //метод выборки(указатель, функция)
              $row_s = mb_strtoupper((!$exp[2] ? $separator : $exp[2])); //указывает какой сепаратор будет перед выборкой
              $where = null;

              /*Вспомогательные функции */
              $__func_parse_INARG = function($v){
              	  if(is_int($v)) return $v; else return $this->__quote($v);
              };

              /* Групповая выборка*/
              if($col == '_group' && $val){
              	 $where = "(".self::__parse_selector_where($val, $exp[1]).")";
              	 $array[] = array('separator' =>  $row_s, 'where' => $where);
              	 continue;
              }

              switch($met){
              	 case 'LIKE':
                   $where = $col." LIKE ".$this->__quote("%".trim($val, '%')."%")." ";
              	 break;

              	 case 'MATCH_EVERY_WORD':
              	 case 'LIKE_WORDS':
                   $like_a = array();
                   foreach(explode(" ", remove_extra_spaces_str($val)) as $s){
                      $like_a[] = "(CONCAT($col) LIKE ".$this->__quote("%".trim($s, '%')."%").")";
                   }
                   $where = ($like_a ? "(".implode(" AND ", $like_a).")" : "");
              	 break;

              	 case 'MATCH':
              	    $where = "MATCH ({$col}) AGAINST (".$this->__quote("%".trim($s, '%')."%*")." IN BOOLEAN MODE)";
              	    if(mb_strlen($val, 'utf-8') <= 3){
              	    	$where = $this->__parse_selector_where(array($col.':like' => $val));
              	    }
              	 break;

              	 case 'IN': case 'NOT':
                   $where = $col." ".($met =! 'IN' ? $met : null)." IN (".implode(",", array_map($__func_parse_INARG, (array)$val)).")";
                 break;

                 default:
                   if(is_int($name) && is_array($val)){
                   	  $where = self::__parse_selector_where($val); //есди в качестве аргумента передан массив
                   } else if(is_int($name)){
                   	  $where = $val; //Если был введен простой запрос вида order_id='dddd' AND p_er='sret'
                   } else if(!is_array($val)){
                   	  $val_sr = $this->__quote($val);
                   	  $where = $col.$met.$val_sr; //Если в качестве ключа переданно наименование столбца
                   } else {
                      $where = $col.$met."'(".self::__parse_selector_where($val)."')"; //Если в качестве ключа переданно наименование столбца, а значение является массивом выборки
                   }
                 break;
              }

              if(isset($where)){
              	  $array[] = array('separator' =>  $row_s, 'where' => $where);
              }
       	  }

          $w = "";
       	  for($i = 0; $i < count($array); $i++){
             $item = $array[$i];
             $sep = "";
             if($i > 0 && $i < count($array)){
               	$sep = $item['separator'];
             }

             $w .= " ".$sep." " .$item['where'];
       	  }
       }

      return $w;
	}

	protected function __parse_selector_insert_props($selection = null){
    	 $selection = (array)$selection;
         $col_insert = array_keys($selection);
       	 $val_insert = array_map(array($this, '__quote'), array_values($selection));
         return "(".implode(", ", $col_insert).") VALUES (".implode(", ", $val_insert).")";
    }

	protected function __parse_join_find_prefix($db, $pref, $db_pref, $find, $not_find){        if($table_columns = (array)$this->getTableColumns($db)){       	   $finds = array();           foreach($table_columns as $name){              $fromAs = $pref.$name;
              $toAs = $db_pref.'.'.$name;

              if($find){              	  if(array_key_exists($name, $find)){              	  	  $fromAs = $find[$name];
              	  } else {              	      if(!in_array($name, $find)) continue;
              	  }
              }

              if($not_find){
              	  if(in_array($name, $not_find)) continue;
              }
              $finds[] = "$toAs AS $fromAs";
           }
           $this->find($finds);
        }
	}

	protected function __get_system_pdo(){
        return \MCM::getInstance()->get_connect(\MCM::SYSTEM_CONNECT_NAME);
    }

    public function __pdo_execute($str){    	try{
    	    if(isset($this->__statement_prepare) && !($this->__statement_prepare instanceof \PDOStatement)){    	    	trigger_error('request PREPARE FALSE!');
    	    	exit();
    	    }
    		switch(($this->__statement_prepare instanceof \PDOStatement)){    			case true:
    			   $resp = $this->__statement_prepare;
    	           break;

    	        default:		    		$resp = $this->PDO->prepare($str);
		            break;
		    }

		    $resp->execute((array)$this->__execute_params);
        } catch(\PDOException $e){        	die('Ошибка запроса: ' . $e->getMessage());
        }
        return $resp;
    }

    protected function __apply_callback(&$data = null){       if($this->__callback){       	  foreach($this->__callback as $func){              call_user_func_array($func, array(&$data));
       	  }
       }
    }

    protected function __parse_query_str_for_prepare($str = ""){        preg_match_all("|[\'\"](\:[a-zA-z]*)[\'\"]|Uis", $str, $preg);
        return str_replace($preg[0], $preg[1], $str);
    }

    protected function __setORMProps($props, $val = null){    	$this->__set_props = $props;
    }

    protected function __execFetchMode(\PDOStatement $pdo){
        return call_user_func_array(array($pdo, 'setFetchMode'), $this->__set_fetch_mode_params);
    }

    public function __quote($str = "", $notisQuoteIdetifier = false){    	$quote = $this->PDO->quote($str);
    	if($notisQuoteIdetifier == true){    		if(preg_match("/^\'(.*)\'$/Uis", $quote, $preg_quote)){    			$quote = $preg_quote[1];
            }
        }
        return $quote;
    }

    public function __get($prop){        return $this->__set_props[$prop];
    }

    public function __set($prop, $val){        $this->__set_props[$prop] = $val;
    }

    public function __unset($prop){    	unset($this->__set_props[$prop]);
    }

    public function __isset($prop){    	return isset($this->__set_props[$prop]);
    }
}
?>