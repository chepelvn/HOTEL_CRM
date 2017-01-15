<?
namespace ul\json;

class json_transform{	public function getInstance(){		return \singletone::getInstance(__CLASS__);
    }

    public function encode($json, $flags = null, $indent = "\t"){
        if (!self::isJson($json)) {
        	//$json = self::parseString((array)$json);
            return self::process(json_encode($json, $flags), $indent);
        }

        return self::process($json, $indent);
    }

    function process( $json )
	{
	    $result = '';
	    $level = 0;
	    $in_quotes = false;
	    $in_escape = false;
	    $ends_line_level = NULL;
	    $json_length = strlen( $json );

	    for( $i = 0; $i < $json_length; $i++ ) {
	        $char = $json[$i];
	        $new_line_level = NULL;
	        $post = "";
	        if( $ends_line_level !== NULL ) {
	            $new_line_level = $ends_line_level;
	            $ends_line_level = NULL;
	        }
	        if ( $in_escape ) {
	            $in_escape = false;
	        } else if( $char === '"' ) {
	            $in_quotes = !$in_quotes;
	        } else if( ! $in_quotes ) {
	            switch( $char ) {
	                case '}': case ']':
	                    $level--;
	                    $ends_line_level = NULL;
	                    $new_line_level = $level;
	                    break;

	                case '{': case '[':
	                    $level++;
	                case ',':
	                    $ends_line_level = $level;
	                    break;

	                case ':':
	                    $post = " ";
	                    break;

	                case " ": case "\t": case "\n": case "\r":
	                    $char = "";
	                    $ends_line_level = $new_line_level;
	                    $new_line_level = NULL;
	                    break;
	            }
	        } else if ( $char === '\\' ) {
	            $in_escape = true;
	        }
	        if( $new_line_level !== NULL ) {
	            $result .= "\n".str_repeat( "\t", $new_line_level );
	        }
	        $result .= $char.$post;
	    }

	    return $result;
	}


    protected function __process_old($json, $indent = "\t"){
        $result = '';
        $indentCount = 0;
        $inString = false;
        $len = strlen($json);
        for ($c = 0; $c < $len; $c++) {
            $char = $json[$c];
            if ($char === '{' || $char === '[') {
                if (!$inString) {
                    $indentCount++;
                    if ($char === '[' && $json[$c+1]  == "]") {
                        $result .= $char . PHP_EOL;
                    } elseif ($char === '{' && $json[$c+1]  == "}") {
                        $result .= $char . PHP_EOL;
                    } else {
                        $result .= $char . PHP_EOL . str_repeat($indent, $indentCount);
                    }
                } else {
                    $result .= $char;
                }
            } elseif ($char === '}' || $char === ']') {
                if (!$inString) {
                    $indentCount--;
                    $result .= PHP_EOL . str_repeat($indent, $indentCount) . $char;
                } else {
                    $result .= $char;
                }
            } elseif ($char === ',') {
                if (!$inString) {
                    $result .= ',' . PHP_EOL . str_repeat($indent, $indentCount);
                } else {
                    $result .= $char;
                }
            } elseif ($char === ':') {
                if (!$inString) {
                    $result .= ': ';
                } else {
                    $result .= $char;
                }
            } elseif ($char === '"') {
                if (
                    // A String is ending, when there is a not escaped quote ...
                    ($c > 0 && $json[$c - 1] !== '\\')
                    ||
                    // or a String is ending, when there is a quote prepended with a escaped slash.
                    ($c > 1 && $json[$c - 2].$json[$c - 1] === '\\\\')
                ) {
                    $inString = !$inString;
                }
                $result .= $char;
            } else {
                $result .= $char;
            }
        }
        return $result;
    }

    protected function isJson($string){
        if(!is_string($string)) {
            return false;
        }
        json_decode($string);
        return json_last_error() == JSON_ERROR_NONE;
    }

    protected function parseString($json = false){    	if(is_array($json)){    		foreach($json as $key => $value){               if(is_int($key)){                   $key = 'result';
               }
               $data[$key] = $value;
            }
        }

        return $data;

    }
}
?>