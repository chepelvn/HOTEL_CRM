<?
namespace ul\page;
class filterAdapter{	static $instance = null;	public function getInstance(){        if((filterAdapter::$instance instanceof filterAdapter)){        	return filterAdapter::$instance;
        }

        return filterAdapter::$instance = new filterAdapter();
	}
	public function __construct($config = array()){
	}

	public function getOF(){		return $this;
    }

    public function getRequestArray(){
    }

	public function set($config){
	}
	public function setQuery(){
	}

	public function setFilterName(){
	}

	public function close(){
	}
}
?>