<?
class file_system{

       }

	      while (false !== ($file = readdir($handle))) {
			  if ($file != "." && $file != "..") {
			  	   $pather = $path . "/" . $file;

			       if(is_dir($pather)){
	                    self::__scan_dir($pather, $file, $out_sub);
			       } else {

	                    self::$files_array[] = $out_sub . $file;
	               }
			  }
	      }

	      closedir($handle);
	   }
	}

	public function scan_dir($path = false, $out_sub = false){
         $file_array = self::$files_array;

         self::$files_array = null;

         return $file_array;
	}
}