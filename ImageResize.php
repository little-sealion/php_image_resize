<?php 

namespace tc\bulkimageresize;

class ImageResize{

    public $imagine;
	private $width = 200;
	private $extensions = ['jpg','jpeg','png','gif'];
	private $beforeResizeCallable = null;
	private $afterResizeCallable = null;

	public function onBeforeResize($callable){
		$this->beforeResizeCallable = $callable;

	}
	public function onAfterResize($callable){
		$this->afterResizeCallable = $callable;

	}

// define calss construct function
	public function __construct($width=null,$extensions=null){
		// assign a new created imagine object to $this->imagine
		$this->imagine = new \Imagine\Gd\Imagine();
		// if the passed in parameter $width is not null, assign its value to override $this->width
		if($width !== null){
			$this->width = $width;
		}
		// if the passed in parameter $extensions is not null, assign its value to override $this->extensions
		if($extensions !== null){
			$this->extensions = $extensions;
		}

	}

	public function resizeAllImages($dir){
		// scan all files under $dir
		$files = scandir($dir);
		// iterate through all files
		foreach ($files as $key => $value) {
			// get the absolute path of each file
			$path = realpath($dir . DIRECTORY_SEPARATOR .$value); 
			// if the file itself is not a directory
			if(!is_dir($path)){
				// get the extension of the file
				$ext = pathinfo($path,PATHINFO_EXTENSION);
				// if the file extension is one of the allowed image extensions
				if(in_array($ext,$this->extensions)){
					// call the before resize function
                    if(is_callable($this->beforeResizeCallable)){
                    	call_user_func($this->beforeResizeCallable,$path);
                    }

                    // resize image and save it back to the original path
					$this->imagine->open($path)
					->thumbnail(new \Imagine\Image\Box($this->width,$this->width))
					->rotate(90)
					->save($path);

                    // call the afterResize function
                    if(is_callable($this->afterResizeCallable)){
                    	call_user_func($this->afterResizeCallable,$path);
                    }
				
			   }
			}
			// if the file itself is also a directory and not '.' not '..'
			elseif ($value != '.' && $value != '..') {
				// call resizeAllImages function to resize files under this directory recursively
				$this->resizeAllImages($path);
			}
		}
	}

}


 ?>