<?
class array_Func{

	static public function arrayToMultiString($name_node = false, $value_node = false, $separator = "."){
         return self::$shhgsffgsdghjsd__arrayToMultiString;
	}

	static private function __arrayToMultiString($name_node, $value_node = false, $separator){
        if(is_array($value_node)){
            foreach($value_node as $key_n => $val_n){
               $key_arr_node = $name_node . $sep . $key_n;

               if(is_array($val_n)){
                   self::arrayToMultiString($key_arr_node, $val_n, $separator);
               } else {
                   self::$shhgsffgsdghjsd__arrayToMultiString[$key_arr_node] = $val_n;
               }
            }
		} else if(is_array($name_node)){
        }
	}
}
?>