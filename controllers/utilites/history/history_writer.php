<?
namespace ul\history;
class history_writer{
	const DB = 'change_history';

	protected $methods = array(
	  'compare' => '__compare',
	  'just' => '__just'

    ), $__dataId, $__query = array();

    protected $items = array(), $current = array();

	public function __construct($dataIn = null){
        $this->__setQuery($dataIn);
	}

	public function mysql(){
        return \def_class::mysql();
	}

	public function set($dataIn = false, $fieldName = false, $textChange = ""){
        if($dataIn){
        	$this->__setQuery($dataIn);
        }

        $this->items[] = array();
        $this->current = &$this->items[(count($this->items) - 1)];
        $this->current['name'] = $textChange;
        $this->current['field'] = $fieldName;

        return $this;
	}

    //выбор метода обработки
	public function method($method = null, $currentId = null, $lastId = null){
		$args = func_get_args();
		$method = array_shift($args);
        $this->current['used_method'] = $method;
        $this->current['bool'] = call_user_func_array(array($this, $this->methods[$method]), $args);
        $this->current['lastId'] = $lastId;
        $this->current['currentId'] = $currentId;

        return $this;
	}

	protected function __compare($current = null, $last = null){
        if($current != $last) return true;
        return false;
	}

	protected function __just(){
		return true;
    }

	public function reverse(){
        $cBool = &$this->current['bool'];
        if($cBool == true) $cBool = false;
        else if ($cBool == false) $cBool = true;
        return $this;
	}

	public function current($text = null, $id = null){
        $this->current['currentText'] = $text;
        if($id) $this->current['currentId'] = $id;
        return $this;
	}

	public function last($text = null, $id = null){
        $this->current['lastText'] = $text;
        if($id) $this->current['lastId'] = $id;
        return $this;
	}

    //Получаения данных для записи изминений из запроса mysql
	public function ofDB($query = ""){
        $this->current['DBquery'] = $query;
        return $this;
	}

    //усли был отправлен запрос mysql(ofDB), перем данные оттуда
	public function text($current = null, $last = null){
        $this->current($current);
        $this->last($last);
        return $this;
	}

	public function get($module, $where = null){
		$query = $this->mysql()->select(self::DB);
		$query->where(['module' => $module]);
		if($where) $query->where($where);
        return $query;
	}

    //Парсим current_text и переданные в нем аргументы
	private function parseTextOfBDResponce($text = null, $id = ""){
		$mysql = $this->mysql();
		$parseQuery = str_replace('%q', $mysql->__quote($id), $this->current['DBquery']);
		$parseQuery = str_replace('%s', mysql_escape_string($id), $parseQuery);

        $this->current['DBResponce'] = $mysql->query($parseQuery)->getLine();

        preg_match_all('|\%db\.(.*)\%|Uis', $text, $pregText);

        $toReplace = array();
        foreach($pregText[1] as $a){
        	$toReplace[] = @$this->current['DBResponce'][$a];
        }

        return str_replace($pregText[0], $toReplace, $text);
	}

	private function parseText(&$data){
        preg_match_all('|\%(.*)\%|Uis', $data['currentText'], $pregCurrentText);
        preg_match_all('|\%(.*)\%|Uis', $data['lastText'], $pregLastText);

        $toReplaceCurrent = $toReplaceLast = array();
        foreach($pregCurrentText[1] as $a){
        	$toReplaceCurrent[] = @$data[$a];
        }

        foreach($pregLastText[1] as $a){
        	$toReplaceLast[] = @$data[$a];
        }

        $data['currentText'] = str_replace($pregCurrentText[0], $toReplaceCurrent, $data['currentText']);
        $data['lastText'] = str_replace($pregLastText[0], $toReplaceLast, $data['lastText']);
	}

	public function writeIds(){
		$this->current['isWriteId'] = true;
		return $this;
    }

	public function goWrite($flag_options = 0){
        foreach($this->items as $key => &$data){
            if($data['bool'] == true){

                if($data['DBquery']){
                	$data['currentText'] = $this->parseTextOfBDResponce($data['currentText'], $data['currentId']);
                	$data['lastText'] = $this->parseTextOfBDResponce($data['lastText'], $data['lastId']);
                }

                //Парсим current|last..Text
            	$this->parseText($data);

                if($data['isWriteId']){
               		$this->__setQuery('current_id', $data['currentId']);
               		$this->__setQuery('last_id', $data['lastId']);
               	}

               	$this->__setQuery('current_text', $data['currentText']);
               	$this->__setQuery('last_text', $data['lastText']);
               	$this->__setQuery('name', $data['name']);
               	$this->__setQuery('field', $data['field']);
               	$this->__setQuery('date_create', now());

               	if($flag_options == 0){
               	    $this->mysql()->insert(self::DB, $this->__getQuery())->insert_id();
                }
            }
        }
	}

	public function __setQuery($name, $value = null){
		if(is_array($name)){
			$this->__query = array_merge($this->__getQuery(), $name);
			return true;
        }
        $this->__query[$name] = $value;
        return true;
	}

	public function __getQuery(){
		return $this->__query;
    }
}
?>