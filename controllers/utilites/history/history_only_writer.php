<?php
namespace ul\history;
class history_only_writer{
    protected $current = array(), $items = array(), $__input = array(), $_new, $_old, $__array, $__query, $__insD = array();
    protected $__insert_query;
    protected $methods = array(
        'compare' => '__compare',
        'compare-db' => '__compare_of_db',
        'exists-array-value' => '__exists-array-value',
        'just' => '__just'
    );

    public function mysql(){
        return \def_class::mysql();
    }

    public function __construct(){

    }

    public function setInput($new, $old){
        $this->__input = array(
            'new' => $new,
            'old' => $old
        );
    }

    private function __setCurrent(){
        $this->items[] = array();
        $this->current = &$this->items[count($this->items) - 1];
        return $this;
    }

    private function __setNewOld(){
        $this->_new = $this->current['input_data']['new'];
        $this->_old = $this->current['input_data']['old'];
    }

    public function setData($new = null, $old = null){
        $this->__setCurrent();
        $this->current['input_data'] = array(
            'new' => $new,
            'old' => $old
        );
        $this->__setNewOld();
        return $this;
    }

    public function setField($field = null){
        $this->__setCurrent();
        $this->current['field'] = $field;
        $this->current['input_data'] = array(
            'new' => @$this->__input['new'][$field],
            'old' => @$this->__input['old'][$field]
        );
        $this->__setNewOld();
        return $this;
    }

    public function method($method){
        $args = $oArgs = func_get_args();
        $args[1] = $this->_new;
        $args[2] = $this->_old;
        $args = array_merge($args, array_splice($oArgs, 1));

        $this->current['method_info'] = array(
            'method' => $method,
            'args' => array_splice($args, 1)
        );
        return $this;
    }

    public function text($text){
        $this->current['text'] = $text;
        return $this;
    }

    private function __compare($new, $old){
        return !($new == $old);
    }

    private function __compare_of_db($new, $old, $query, $wCheck = null){
        $this->__query = $query;
    }

    private function __just(){
        return true;
    }

    public function valuesArray($array){
         $this->__array = $array;
         return $this;
    }

    public function valuesQuery($query){
        $this->__query = $query;
        return $this;
    }

    protected function __parseText(&$item){
       $new = $item['input_data']['new'];
       $old = $item['input_data']['old'];
       if($text = $item['text']){
           $text = str_replace(array('%new%', '%old%'), array($new, $old), $text);
           if($this->__array){
               $text = str_replace(array('%new.val%', '%old.val%'), array($this->__array[$new], $this->__array[$old]), $text);
           }

           if($this->__query){
               $qsN = $this->mysql()->query(str_replace('%q', $this->mysql()->__quote($new), $this->__query))->getLine();
               $qsO = $this->mysql()->query(str_replace('%q', $this->mysql()->__quote($old), $this->__query))->getLine();

               preg_match_all('|%new\.q\.(.*)%|Uis', $text, $pregNtext);
               preg_match_all('|%old\.q\.(.*)%|Uis', $text, $pregOtext);

               $replace_to = array();
               foreach($pregNtext[1] as $it){
                   $replace_to[] = @$qsN[$it];
               }

               foreach($pregOtext[1] as $it){
                   $replace_to[] = @$qsO[$it];
               }

               $text = str_replace(array_merge($pregNtext[0], $pregOtext[0]), $replace_to, $text);
           }

           return $text;
       }
    }

    public function setInsertQuery($qb, $what){
        $this->__insert_query['db'] = $qb;
        $this->__insert_query['what'] = $what;
        return $this;
    }

    public function getChangesText(){
        foreach ($this->items as $item){
            $method = $this->methods[$item['method_info']['method']];
            $args = $item['method_info']['args'];
            $bool = false;
            if(!method_exists($this, $method)){
                 trigger_error('method '.$item['method_info']['method'].' not found');
            } else {
                $bool = call_user_func_array(array($this, $method), $args);
            }

            if($bool){
                $this->__insD[] = $this->__parseText($item);
            }
        }
        return $this->__insD;
    }
}