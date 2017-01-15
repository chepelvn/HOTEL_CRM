<?
namespace ul\validator;
class validator{
    protected $FORMATSFILE = array(
        'image' => ['image/jpeg', 'image/jpg', 'image/gif', 'image/png', 'image/bmp'],
        'doc' => []
    );
    protected $__current_connect_name = false;

    private function mysql(){
        if(($this->getCurrentDB() instanceof \mysql)){
            return $this->getCurrentDB();
        }
        return new \mysql(\MCM::getInstance()->get_connect($this->getCurrentDB()));
    }

    private $METHODS = array(
        'is-empty'            => '__isEmpty',
        'is-phone'            => '__isPhone',
        'is-email'            => '__isEmail',
        'exists-data-db'      => '__existsDataDB',
        'is-empty-array'      => '__isEmptyArray',
        'exists-values-array' => '__existsValuesArray',
        'is-numeric'          => '__isNumeric',
        'just'                => '__just',
        'is-file-extension'   => '__isFileExtensions',
        'max-size-file'       => '__maxSizeFile',
        'valid-url'          => '__validUrl',
        'is-datetime'           => '__dateTime',
        'is-date'               => '__date',
        'is-date-dmy'         => '__dateDmy'
    );

    private $input, $curr, $item;

    public function getInstance(){
        return new validator();
    }

    public function setConnectDB($connect_name){
        $this->__current_connect_name = $connect_name;
        return $this;
    }

    public function getCurrentDB(){
        return $this->__current_connect_name;
    }

    public function getCurrent(){
        return $this->curr;
    }

    public function __construct(){
        $this->input = $_POST;
    }

    public function setInput($input = array()){
        $this->input = $input;
        return $this;
    }

    public function Input($data){
        $this->input = $data;
    }

    public function setField($sets = null, $input = null){
        $this->item[] = array();
        $this->curr = &$this->item[(count($this->item) - 1)];

        if(is_array($sets)){
            if(!isset($sets['field'])){
                trigger_error('not field name in array');
            } else {
                $this->curr['name'] = $sets['field'];
            }
            $this->curr = array_merge($this->curr, $sets);
        }

        $this->curr['properties'] = $sets;

        if(is_string($sets)){
            $this->curr['name'] = $sets;
        }

        if(!$sets){
            $this->curr['name'] = md5(count($this->item));
        }

        if(isset($input)){
            $this->input[(string)$this->curr['name']] = $input;
        }

        return $this;
    }

    public function method($method = null){
        $exp = explode(':', $method);

        if(count($exp) > 1){
            $method = $exp[1];
            switch($exp[0]){
                case 'no-empty':
                    if(!$this->__isEmpty($this->curr['name'])){
                        return $this->method('just', true);
                    }
                    break;
            }
        }

        if(!isset($method)){
            $method = "just";
        }

        if(!array_key_exists($method, $this->METHODS)){
            trigger_error("not validator method: $method");
        }

        $args = func_get_args();
        $args[0] = $this->curr['name'];

        $this->curr['bool'] = (bool)call_user_func_array(array($this, $this->METHODS[$method]), $args);
        $this->curr['method'] = $method;
        return $this;
    }

    public function boolField($field = null){
        $items = $this->item;
        $f = explode('.', $field);

        if($field){
            foreach($this->item as $k){
                if($k['name'] == $f[0] && $f[1] == $k['method']){
                    return $k['bool'];
                }
            }
        } else {
            return $this->curr['bool'];
        }

        return null;
    }

    public function setId($id){
        $this->curr['id'] = $id;
        return $this;
    }

    public function text($text){
        $this->curr['text'] = str_replace("%s", $this->input[$this->curr['name']], $text);
        return $this;
    }

    protected function __isEmpty($f){
        if(!empty($this->input[$f])){
            return true;
        }
        return false;
    }

    protected function __existsDataDB($f, $str_sql){
        $query = $this->mysql();
        $str = str_replace("%s", mysql_escape_string($this->input[$f]), $str_sql);
        $str = str_replace("%q", $query->__quote($this->input[$f]), $str);
        $query->query($str);
        if(!$query->count()){
            return true;
        }
        return false;
    }

    protected function __existsValuesArray($f, $array_template = array()){
        $data = $this->input[$f];
        if(!is_array($data)){
            $data = array($data);
        }

        $bool = array();
        foreach($data as $k){
            if(in_array($k, $array_template)){
                $bool[] = true;
            }
        }

        if(count($bool) == count($data)){
            return true;
        }

        return false;
    }

    protected function __isNumeric($f){
        if(is_numeric($this->input[$f])){
            return true;
        }

        return false;
    }

    public function __isEmail($f){
        if($this->__pattern($f, '~^([a-z0-9_\-\.])+@([a-z0-9_\-\.])+\.([a-z0-9])+$~i')){
            return true;
        }

        return false;
    }

    public function __isPhone($f, $strlen = 11){
        if($this->__pattern($f, '/^8\d{'.($strlen - 1).'}$/') && strlen($this->input[$f]) == $strlen){
            return true;
        }

        return false;
    }

    public function __date($f){
        $pattern = '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/';
        if($this->__pattern($f, $pattern)){
            return true;
        }
        return false;
    }

    public function __dateDmy($f){
        $pattern = '/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/';
        if($this->__pattern($f, $pattern)){
            return true;
        }
        return false;
    }

    public function __dateTime($f){
        $pattern = '/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/';
        if($this->__pattern($f, $pattern)){
            return true;
        }
        return false;
    }

    public function __pattern($f, $pattern){
        if(preg_match($pattern, $this->input[$f])){
            return true;
        }

        return false;
    }

    public function __just($f, $boolean = false){
        if(!is_bool($boolean)){
            $boolean = false;
            trigger_error('method just 1 arqument not boolean');
        }
        return $boolean;
    }

    public function __isFileExtensions($f, $extensions = array(), $type = false){
        $file_type = $_FILES[$f]['type'];

        if($type){
            switch($type){
                case 'image':
                    $extensions = array_merge($extensions, $this->FORMATSFILE['image']);
                    break;
            }
        }

        if(in_array($file_type, $extensions)){
            return true;
        }

        return false;
    }

    public function __maxSizeFile($f, $size_max = 0, $size_min = 0){
        $file_size = (int)$_FILES[$f]['size'];
        $size_max = ($size_max * 1024);
        $size_min = ($size_min * 1024);

        if($size_min){
            if($file_size >= $size_max && $file_size <= $size_min){
                return true;
            }

            return false;
        }


        if($file_size <= $size_max){
            return true;
        }

        return false;
    }

    public function __validUrl($f){
        if(preg_match("~^(?:(?:https?|ftp|telnet)://(?:[a-z0-9_-]{1,32}".
            "(?::[a-z0-9_-]{1,32})?@)?)?(?:(?:[a-z0-9-]{1,128}\.)+(?:com|net|".
            "org|mil|mobi|edu|arpa|gov|biz|info|aero|inc|name|[a-z]{2})|(?!0)(?:(?".
            "!0[^.]|255)[0-9]{1,3}\.){3}(?!0|255)[0-9]{1,3})(:[0-9]{1,5})?(?:/[а-яa-z0-9.,_@%\(\)\*&".
            "?+=\~/-]*)?(?:#[^ '\"&<>]*)?$~i", $this->input[$f])){
            return true;
        }

        return false;
    }

    public function getMessages(){
        $mess = array();
        foreach((array)$this->item as $item){
            if($item['bool'] != true){
                $mess[] = $item['text'];
            }
        }
        return $mess;
    }

    public function getMessagesXML(){
        $items = array();
        foreach((array)$this->item as $item){
            $elem = array();
            if($item['bool'] != true){
                $elem['@attributes'] = $item['properties'];

                $elem['@method'] = $item['method'];
                $elem['@name'] = $item['name'];
                $elem['node:text'] = $item['text'];
                $items[] = $elem;
            }
        }

        return $items;
    }

    public function boolReverse(){
        if($this->curr['bool'] == true){
            $this->curr['bool'] = false;
        } else {
            $this->curr['bool'] = true;
        }
        return $this;
    }

    public function reverse(){
        return call_user_func_array(array($this, 'boolReverse'), func_get_args());
    }

    public function getException(){
        if(count($messages = $this->getMessagesXML()) > 0){
            throw new validator_exception($messages);
        }
    }

    public function exception($message = array()){
        throw new validator_exception($message);
    }
}
?>