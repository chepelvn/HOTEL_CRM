<?
namespace ul\files;
class upload{
        return $this;
    }

    public function dir($directory, $create_dir = false, $cmod = 0777){
       $this->cmod = $cmod;
    }

    public function run(){

        	    }
            }

            $this->url = null;

            if(move_uploaded_file($this->input['tmp_name'], $base_dir.'/'.basename($this->input['name']))){
            	return true;
            }

            trigger_error('fail upload file '.$path);
        }

        return false;
    }

    public function getUrl(){
    }
}
?>