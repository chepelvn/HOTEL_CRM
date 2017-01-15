<?
namespace ul\page;
class filter_mysql{
	protected $id = 0;
	protected $item;
	protected $mode = "notEmpty";
	public $variation_query = false;
	public $variation_value = null;

	public function __construct(){

    }

    public function getInstance(){
    	return new filter_mysql();
    }

    public function input($data){
    	$this->input = $data;
    }

    public function mode($mode){
    	$this->item[$this->id]['mode'] = $mode;
    	return $this;
    }

    public function set($name, $method = null, $separ = ","){
       $this->id = $this->id + 1;
       $this->item[$this->id] = array(
          'name' => $name,
          'method' => $method,
          'separ' => $separ,
          'mode' => $this->mode
       );

       return $this;
    }

    public function set_value($param){    	if(!isset($this->item[$this->id]['name']) || isset($this->item[$this->id]['set_value'])){    	     $this->id = $this->id + 1;
        }
    	$this->item[$this->id]['set_value'] = $param;
    	return $this;
    }

    public function query($query){
    	$this->item[$this->id]['set_query'] = $query;
    	return $this;
    }

    protected function __sets_request(){
        foreach((array)$this->item as $key => $value){
        	$query = null;
        	$str = null;
        	$data = $do = $this->input;

        	if($this->variation_query == true){
        		$data = $this->variation_value;
        	}

        	if(isset($value['set_query'])){
        		$query = $value['set_query'];
            }

        	if(isset($value['name'])){        		$data = $do = $this->input[$value['name']];

        		if($this->variation_query == true){        			 $data = $this->variation_value;
        	    }

        		if(isset($this->input[$value['name']])){
        			if($value['method'] == 'enum'){
        			   if(is_array($data)){
                           $data = implode($value['separ'], array_map('mysql_real_escape_string', array_values($data)));
                       }
                    }

        			$str = str_replace('%k%', $data, $query);
                }
            }

            if(isset($value['set_value'])){
                $str = null;
        		if($data == $value['set_value']){
        			$str = str_replace('%k%', mysql_real_escape_string($data), $query);
        		}
        	}

            switch($value["mode"]){
            	case $this->mode:
            	case null:
            	   if($data && $do){
            	   	   $this->item[$key]['query'] = $str;
            	   }
            	break;
            }
        }
    }

    public function variations(array $array){
       $item = &$this->item[$this->id];
       $str = null;
       if(isset($this->input[$item['name']])){
       	  if(array_key_exists($this->input[$item['name']], $array)){
       	  	  $str = $array[$this->input[$item['name']]];
          }
       }

       switch($item['mode']){
       	  case $this->mode:
       	     if(isset($str)){
                $item['query'] = $str;
             }
          break;
       }

       return $this;
    }

    public function save_session(){

    }

    public function outResult($separ = " and "){    	$this->__sets_request();
        $query = array();
        if(is_array($this->item)){
           foreach($this->item as $key => $val){
              if(isset($val['query'])){              	  if(!$val['query']) continue;
              	  $query[] = $val['query'];
              }
           }
        }

        return implode($separ, $query);
    }

    public function get_request_value(){        $data = array();
        if(is_array($this->item)){        	foreach($this->item as $key){        		$value = null;        		 if(isset($this->input[$key['name']])){        		 	 $value = $this->input[$key['name']];
        	     }
        		 $data[$key['name']] = $value;
            }
        }

        return $data;
    }

    public function clear(){        $this->item = null;
        $this->input = null;
        $this->id = 0;
    }
}
?>