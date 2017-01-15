<?
namespace ul\files;
class upload{    public function setFile($input_name){        $this->input = $_FILES[$input_name];
        return $this;
    }

    public function dir($directory, $create_dir = false, $cmod = 0777){       $this->dir = $directory;
       $this->cmod = $cmod;       $this->dir_create = $create_dir;
    }

    public function run(){    	$base_dir = CURRENT_WORKING_DIR.'/'.$this->dir;
        if(is_uploaded_file($this->input['tmp_name'])){        	if($this->dir_create == true){        		if(!is_dir($base_dir)){        		     mkdir($base_dir, $this->cmod, true);
        	    }
            }

            $this->url = null;

            if(move_uploaded_file($this->input['tmp_name'], $base_dir.'/'.basename($this->input['name']))){            	$this->url = '/'.$this->dir.'/'.basename($this->input['name']);
            	return true;
            }

            trigger_error('fail upload file '.$path);
        }

        return false;
    }

    public function getUrl(){         return $this->url;
    }
}
?>