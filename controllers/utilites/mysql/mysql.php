<?

/**
* version 1.0.0
*/

class mysql extends mysql_factory{
	static $instance = null;
	static $instances_connects = null;
	protected $PDO, $exampleMCM;

	public function __construct(\MCM $connect, $connect_name = 'system'){
	}

	public function getInstance(\MCM $connectExample = null){
       }
       return $connect_instance->close();
	}

    //������� ����� ������ ������ � ��������� ������������ � �������
    public function create(){
    }

    /**
     *@param string $query �������� ������ mysql
    */
    public function query($query, $args = array()){
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

	public function leftJoin($select_mix = null, $on = null, $pref_find = false, array $find = null, array $not_find = null){
			 return self::__create_object_call_func(__METHOD__, func_get_args());
	    }

			$query = ' LEFT OUTER JOIN ';
			foreach($select_mix as $pref => $sel){
	           if($pref_find){
	           }
			}
			$query .= ' ON '.$on.' ';
        }

        if($query){
        }

        return $this;
	}

	public function join($select_mix = null, $on = null, $pref_find = false, array $find = null, array $not_find = null){
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

			if($on){
	        }
        }

        if($query){
        	$this->__join[] = $query;
        }

        return $this;
	}

	public function getTableColumns($table_name = null, $flag = \PDO::FETCH_COLUMN){
        	$table_name = $mt['_table'];
        }
        return $this->__pdo_execute('DESCRIBE '.$table_name)->fetchAll($flag);
	}

	public function order($select_mix = null){
			 return self::__create_object_call_func(__METHOD__, func_get_args());
	    }
		$query = $select_mix;
		if(is_array($select_mix)){
			foreach($select_mix as $pref => $sel){
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

        }

        return $this;
	}

    /**
      *@var $order = mixed
    */
	public function orderBy($order = null){
			return self::__create_object_call_func(__METHOD__, func_get_args());
	    }

	    $order = (array)$order;

	    if(is_array($order)){
	    	$quers = array();

	    	}
	    	$order = ' ORDER BY '.implode(', ',$quers);
	    }

	    if($order){
        	$this->__order = (array)$order;
        }

        return $this;
	}

	public function limit($start = null, $end = null){
		if(isset($end)) array_push($start, $end);
			 return self::__create_object_call_func(__METHOD__, func_get_args());
	    }
		$limit = array_map('intval', $limit);
		return $this;
	}

	public function group(){
	}

	public function select($table = null, $find = null){
		   return self::__create_object_call_func(__METHOD__, func_get_args());
	   }

	   $this->find($find);
	   if($table)$this->__query_db[] = $table;
       return $this;
	}

	public function find($find = null){
		   return self::__create_object_call_func(__METHOD__, func_get_args());
	   }

       if($find) $this->__find[] = $find;
       return $this;
	}

	public function find_not($find_not = null){
    }

	public function where($select_mix = null){
			 return self::__create_object_call_func(__METHOD__, func_get_args());
	    }

	       	   	$this->__params[] = $select_mix['_params'];
	       	   	unset($select_mix['_params']);
            }
       	}

        if($select_mix)$this->__where[] = $select_mix;
        return $this;
	}

	public function order_param($str = null, $callback_parser = null){
            }
            $this->__params[] = $str;
        }
        return $this;
	}

	public function update($table = null, $params_sets = null, $where = null){
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

    public function responce(){
    }

    public function what($params_sets = null){
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

	public function insert_id(){
	}

	public function fetch_assoc($table = null, $selection = null, $where = null, $callback = null){
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

	public function fetch(){
			 return self::__create_object_call_func(__METHOD__, func_get_args());
	    }
    }

	public function setFetchMode(){
        return $this;
	}

	public function callback($mixed = null){
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

	public function delete($table = null, $where = null){
			 return self::__create_object_call_func(__METHOD__, func_get_args());
	   }

	   $this->select($table);
	   $this->where($where);

       $query = $this->__get_parse_query_str('delete');
       $this->__responce = $this->__pdo_execute($query);
       return $this;
	}

	public function primary($param = null){
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

    public function alterTable($table_name, $alter_set){
    	if(is_array($alter_set)){
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
           	   }
           }
           $str_alter = implode(', ', $sets);
        }
    	return $this->__pdo_execute($query_str);
    }

    public function exec($params = array()){
        if($params)$this->__execute_params = $params;
        return $this;
    }

    public function prepare($parse_m = 'query', $execute = null){
    	$parserStr = "__parse_query__".$parse_m;
    	if(!method_exists($this, $parserStr)){
    		exit();
        }
        $query = call_user_func(array($this, $parserStr));
        $query = $this->__parse_query_str_for_prepare($query);
        $this->__statement_prepare = $this->PDO->prepare($query);
        $this->exec($execute);
    }
}
?>