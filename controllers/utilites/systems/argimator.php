<?
namespace ul\systems;
/*
   ver 1.0;
   ������� ��� �������� ����� � �������� ������������ ��������
   ��������
      set(bool mixed, bool string, bool int,string)
         1 - ������� ������, ������� ����� ���������������.
             ���������� ����� �������� ������, ������������ �����(param=val&param1=val1), json ������({"param": "val"})
         2 - ���� ������������� ������� ������, ������: array,str,json
         3 - ������������ ���������. ����� ��� ��������� �������� ������ ��� getParams(ID)

      variationVariables(bool array) - ������ �� ��������� ����������.
      getParams(bool string,int) - ������� ��������������� ������.(�������� ����� �� Id)
*/


class argimator{
	private $METHODS = array('array', 'str', 'json');
	private $__variation = array();
	private $__id = 0;
	public $items = array();
	private $__def = 0;

	}

	private function __getMethods($param){
        $var = array();
        }
        return $var;
    }

    private function __setId($id = null){
        $c = &$this->getCurrent();
        $c['id'] = $id;
    }

    private function &getCurrent(){
    }

    private function __parseMethods(){
    }

    private function __parse__array($param = null){
        }
        return null;
    }

    private function __parse__json($param = null){
        if(is_string($param)){
        	    return $decode;
            }
        }
        return null;
    }

    private function __parse__str($param){
	        }
        }
        return null;
    }
	public function set($mixed, $method = 'array', $id = null){
          'method' => $this->__getMethods($method),
          'mixed' => $mixed,
        );
        $this->__parseMethods();
        $this->__setId($id);
	}

	public function def($mode = null){
	}

	public function getParams($id = null, $all = false){
               	if(isset($type_vars)){
                      }
                   }
               	   return $type_vars;
               	}
            }
         }

         return null;
	}
}
?>