<?
class file_system{	static $files_array;
	private function __scan_dir($path = false, $path_name = false, $out_sub = false){       if(!file_exists($path)){       	  return false;
       }
       if ($handle = opendir($path)) {
	      while (false !== ($file = readdir($handle))) {
			  if ($file != "." && $file != "..") {
			  	   $pather = $path . "/" . $file;

			       if(is_dir($pather)){
	                    self::__scan_dir($pather, $file, $out_sub);
			       } else {			       	    $file = ($path_name) ? $path_name . "/" . $file : $file;

	                    self::$files_array[] = $out_sub . $file;
	               }
			  }
	      }

	      closedir($handle);
	   }
	}

	public function scan_dir($path = false, $out_sub = false){         self::__scan_dir($path, null, $out_sub);
         $file_array = self::$files_array;

         self::$files_array = null;

         return $file_array;
	}
}