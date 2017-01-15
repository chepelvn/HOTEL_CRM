<?

/**
* version 1.0.0
*/

class mysql extends mysql_factory{
	static $instance = null;
	static $instances_connects = null;
	protected $PDO, $exampleMCM;

	public function __construct(\MCM $connect, $connect_name = 'system'){		$this->exampleMCM = $connect;		$this->PDO = $connect->getPDO();
	}

	public function getInstance(\MCM $connectExample = null){       $connect_name = $connectExample->getConnectName();       $connect_instance = &mysql::$instances_connects[$connect_name];       if(!($connect_instance instanceof mysql)){       	  return $connect_instance = new mysql($connectExample->getPDO());
       }
       return $connect_instance->close();
	}

    //создает новый объект класса с дефолтным подключемние к серверу
    public function create(){    	return new mysql(self::__get_system_pdo());
    }

    /**
     *@param string $query сроковой запрос mysql
    */
    public function query($query, $args = array()){    	if(!self::__is_instance()){
			return self::__create_object_call_func(__METHOD__, func_get_args());
	    }
    	$this->_qsc = $query;
    	return $this;
    }

    public function getLine($table = null, $selection = null, $where = null, $callback = null){
       if(!self::__is_instance()){
			return self::__create_object_call_func(__METHOD__, func_get_args());
	   }

	   $this->select($table, $selection);
	   $this->where($where);
	   $this->callback($callback);

       $query = $this->__get_parse_query_str('select');
       $this->__responce = $this->__pdo_execute($query);
       $this->__execFetchMode($this->__responce);
       $responce = $this->__responce->fetch();
       $this->__apply_callback($responce);
       return $responce;
    }

	public function leftJoin($select_mix = null, $on = null, $pref_find = false, array $find = null, array $not_find = null){		if(!self::__is_instance()){
			 return self::__create_object_call_func(__METHOD__, func_get_args());
	    }		$query = $select_mix;
		if(array($select_mix)){
			$query = ' LEFT OUTER JOIN ';
			foreach($select_mix as $pref => $sel){	           $query .= $sel.' '.$pref;
	           if($pref_find){	           	    $this->__parse_join_find_prefix($sel, $pref_find, $pref, $find, $not_find);
	           }
			}
			$query .= ' ON '.$on.' ';
        }

        if($query){        	$this->__join[] = $query;
        }

        return $this;
	}

	public function join($select_mix = null, $on = null, $pref_find = false, array $find = null, array $not_find = null){		if(!self::__is_instance()){
			 return self::__create_object_call_func(__METHOD__, func_get_args());
	    }
		$query = $select_mix;
		if(is_array($select_mix)){
			$query = ' JOIN ';
			foreach($select_mix as $pref => $sel){
	           $query .= $sel.' '.$pref;
	           if($pref_find){
	           	    $this->__parse_join_find_prefix($sel, $pref_find, $pref, $find, $not_find);
	           }
			}

			if($on){				$query .= ' ON '.$on.' ';
	        }
        }

        if($query){
        	$this->__join[] = $query;
        }

        return $this;
	}

	public function getTableColumns($table_name = null, $flag = \PDO::FETCH_COLUMN){        if(!$table_name){        	$mt = $this->get_main_from_table();
        	$table_name = $mt['_table'];
        }
        return $this->__pdo_execute('DESCRIBE '.$table_name)->fetchAll($flag);
	}

	public function order($select_mix = null){		if(!self::__is_instance()){
			 return self::__create_object_call_func(__METHOD__, func_get_args());
	    }
		$query = $select_mix;
		if(is_array($select_mix)){            $query = null;
			foreach($select_mix as $pref => $sel){				switch(strtolower($pref)){					case 'order':
					   $sel = mysql_escape_string((string)$sel);
	                   $query .= ' ORDER BY '.$sel;
					break;

					case 'group':
					   $sel = array_map('mysql_escape_string', array_values((array)$sel));
	                   $query .= ' GROUP BY '.implode(',', $sel);
					break;

					default:
	                   $query .= $sel;
					break;
		        }
		    }
	    }
        if($query){        	$this->__order[] = $query;
        }

        return $this;
	}

    /**
      *@var $order = mixed
    */
	public function orderBy($order = null){        if(!self::__is_instance()){
			return self::__create_object_call_func(__METHOD__, func_get_args());
	    }

	    $order = (array)$order;

	    if(is_array($order)){
	    	$quers = array();
	    	foreach($order as $of => $met){	    		if(is_numeric($of)) $of = '';                $quers[] = $of.' '.$met;
	    	}
	    	$order = ' ORDER BY '.implode(', ',$quers);
	    }

	    if($order){
        	$this->__order = (array)$order;
        }

        return $this;
	}

	public function limit($start = null, $end = null){		$start = (array)$start;
		if(isset($end)) array_push($start, $end);		if(!self::__is_instance()){
			 return self::__create_object_call_func(__METHOD__, func_get_args());
	    }		$limit = array_map('mysql_escape_string', $start);
		$limit = array_map('intval', $limit);		$this->__limit = $limit;
		return $this;
	}

	public function group(){
	}

	public function select($table = null, $find = null){	   if(!self::__is_instance()){
		   return self::__create_object_call_func(__METHOD__, func_get_args());
	   }

	   $this->find($find);
	   if($table)$this->__query_db[] = $table;
       return $this;
	}

	public function find($find = null){	   if(!self::__is_instance()){
		   return self::__create_object_call_func(__METHOD__, func_get_args());
	   }

       if($find) $this->__find[] = $find;
       return $this;
	}

	public function find_not($find_not = null){		return $this;
    }

	public function where($select_mix = null){		if(!self::__is_instance()){
			 return self::__create_object_call_func(__METHOD__, func_get_args());
	    }
		if(is_array($select_mix)){			if(isset($select_mix['_params'])){
	       	   	$this->__params[] = $select_mix['_params'];
	       	   	unset($select_mix['_params']);
            }
       	}

        if($select_mix)$this->__where[] = $select_mix;
        return $this;
	}

	public function order_param($str = null, $callback_parser = null){        if($str){        	if($callback_parser){        		$str = call_user_func_array(array($this, $callback_parser), array($str));
            }
            $this->__params[] = $str;
        }
        return $this;
	}

	public function update($table = null, $params_sets = null, $where = null){		if(!self::__is_instance()){			 return self::__create_object_call_func(__METHOD__, func_get_args());
	    }

		$this->select($table);
		$this->where($where);
		$this->__set_selection_where_primary_key();
		$this->what($params_sets);
		$this->what($this->__set_props);

	    $query = $this->__get_parse_query_str('update');
        $this->__responce = $this->__pdo_execute($query);
        return $this;
    }

    public function responce(){    	return $this->__responce;
    }

    public function what($params_sets = null){    	if(!self::__is_instance()){
			return self::__create_object_call_func(__METHOD__, func_get_args());
	    }
		if($params_sets) $this->__what[] = $params_sets;
		return $this;
    }

    public function insertProp($values = null){
         if(!self::__is_instance()){
			 return self::__create_object_call_func(__METHOD__, func_get_args());
	    }

	    if($values) $this->__insert_props[] = $values;

	    return $this;
	}

	public function insert($table = null, $selection = null){
        if(!self::__is_instance()){
			 return self::__create_object_call_func(__METHOD__, func_get_args());
	    }

	    $this->select($table);
	    $this->insertProp($selection);
	    $this->insertProp($this->__set_props);

        $query = $this->__get_parse_query_str('insert');
        $this->__responce = $this->__pdo_execute($query);

        $this->__insert_id = false;
        if($__insert_id = $this->PDO->lastInsertId()){
       	   $this->__insert_id = $__insert_id;
        }

        if($this->__insert_id){
	         $primary = $this->__get_primary_data();

	         $where = $this->__parse_selector_where(array($primary['Column_name'] => $this->__insert_id));
	         $find = $this->__parse_selector_find("*");

             $this->__where = array($where);
             $this->__find = array($find);
        }

        return $this;
	}

	public function insert_id(){        return $this->__insert_id;
	}

	public function fetch_assoc($table = null, $selection = null, $where = null, $callback = null){		if(!self::__is_instance()){			 return self::__create_object_call_func(__METHOD__, func_get_args());
	    }

		$this->select($table);
		$this->find($selection);
		$this->where($where);
		$this->callback($callback);

        $query = $this->__get_parse_query_str('select');
	    $this->__responce = $this->__pdo_execute($query);
	    $this->__execFetchMode($this->__responce);
	    return $this->__responce->fetchAll();
	}

	public function fetch(){		if(!self::__is_instance()){
			 return self::__create_object_call_func(__METHOD__, func_get_args());
	    }		return call_user_func(array($this, 'fetch_assoc'), func_get_args());
    }

	public function setFetchMode(){        $this->__set_fetch_mode_params = func_get_args();
        return $this;
	}

	public function callback($mixed = null){		if($mixed) $this->__callback[] = $mixed;        return $this;
	}

	public function num_rows($table = null, $selection = null, $where = null){
	    if(!self::__is_instance()){
			 return self::__create_object_call_func(__METHOD__, func_get_args());
	    }

	    $this->select($table, $selection);
		$this->where($where);

		$query = $this->__get_parse_query_str('count');
	    $this->__responce = $this->__pdo_execute($query);

        $row = $this->__responce->fetch(\PDO::FETCH_ASSOC);
        return $row['count'];
	}

	public function delete($table = null, $where = null){       if(!self::__is_instance()){
			 return self::__create_object_call_func(__METHOD__, func_get_args());
	   }

	   $this->select($table);
	   $this->where($where);

       $query = $this->__get_parse_query_str('delete');
       $this->__responce = $this->__pdo_execute($query);
       return $this;
	}

	public function primary($param = null){         $this->__primary_key = $param;
         return $this;
	}

	public function count(){
	    $query = $this->__get_parse_query_str('count');
	    $this->__responce = $this->__pdo_execute($query);
        $row = $this->__responce->fetch(\PDO::FETCH_ASSOC);
        return (int)$row['count'];
	}

	public function getId(){
	}

    public function alterTable($table_name, $alter_set){    	$str_alter = "ADD $alter_set";
    	if(is_array($alter_set)){           $sets = array();           foreach($alter_set as $col => $add_set){           	   if(is_array($add_set)){           	   	  foreach($add_set as $p => $v){           	   	  	switch(strtolower($p)){           	   	  		case 'enum':
           	   	  		   $add_set = "ENUM('".implode("','", (array)$v)."') DEFAULT '".$v[0]."'";
           	   	  	    break;

           	   	  	    case 'varchar':
           	   	  	       $add_set = "varchar($v)";
           	   	  	    break;

           	   	  	    case 'decimal':
           	   	  	       $add_set = "DECIMAL(".implode(',', (array)$v).")";
           	   	  	    break;

           	   	  	    default:
           	   	  	       trigger_error('mysql: not found parameter column');
           	   	  	    break;
           	   	    }
           	   	  }
           	   }               $sets[] = "ADD $col $add_set";
           }
           $str_alter = implode(', ', $sets);
        }    	$query_str = "ALTER TABLE $table_name $str_alter";
    	return $this->__pdo_execute($query_str);
    }

    public function exec($params = array()){
        if($params)$this->__execute_params = $params;
        return $this;
    }

    public function prepare($parse_m = 'query', $execute = null){
    	$parserStr = "__parse_query__".$parse_m;
    	if(!method_exists($this, $parserStr)){    		trigger_error('prepare parser method not found');
    		exit();
        }
        $query = call_user_func(array($this, $parserStr));
        $query = $this->__parse_query_str_for_prepare($query);
        $this->__statement_prepare = $this->PDO->prepare($query);
        $this->exec($execute);        return $this;
    }
}
?>