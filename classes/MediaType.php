<?php
    abstract class MediaType {
        protected static $extensions = array();
        protected $file;

        public function __construct($file=null) {
            if($file!==null){
                $this->file = $file;
            }
        }

        public static function addExtension($ext){
            if(!in_array($ext,static::$extensions)){
                static::$extensions[] = $ext;
            }
        }

        public static function getExtensions(){
            return static::$extensions;
        }

        public function getType(){
            $class = get_class($this);

            return substr(strrchr($class, "_"), 1);
        }

        public function isValidType($file){
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            if(in_array($ext,static::$extensions)){
                return true;
            }else{
                return false;
            }
        }

    }
?>