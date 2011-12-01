<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Scanner
 *
 * @author branden
 */
    class Scanner {
        
        private $validExtensions = array();
        private $mediaTypes = array();
        
        public function __construct($scandir = false) {
            $this->loadMediaTypes();
            $this->updateExtensions();
            if($scandir !== false){
                echo "Scanning Directory: ".realpath($scandir)."\n";
                
                $files = $this->scan($scandir, true);
                if(empty($files)){
                    echo "No Valid Media Files Found\n";
                }else{
                    $this->processResults(array(realpath($scandir)=>$files));
                }
            }
            
        }
        
        public function scan($dir='.', $recursive = false, $fullPathKeys=true){
            $tree = array();
            $fullFile;
            $t;
            if($fullPathKeys){
                $dir = realpath($dir);
            }
            
            if (($handle = opendir($dir))) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != "..") {
                        $fullFile = $dir.DIRECTORY_SEPARATOR.$file;
                        if(is_dir($dir.DIRECTORY_SEPARATOR.$file)){
                            if($recursive){
                                $t = $this->scan($fullFile, $recursive, $fullPathKeys);
                                if(!empty($t)){
                                    if($fullPathKeys){
                                        $tree[$fullFile] = $t;
                                    }else{
                                        $dirname = substr(strrchr($fullFile, DIRECTORY_SEPARATOR), 1);
                                        $tree[$dirname] = $t;
                                    }
                                }
                            }
                        }else if(is_file($fullFile)){
                            if($this->isValidMediaType($fullFile)){
                                $tree[] = $file;
                            }
                        }
                    }
                }
                closedir($handle);
            }
            return $tree;
        }
        
        private function processResults($results){
            print_r($results);
        }
        
        public function updateExtensions(){
            $ext = array();
            foreach($this->mediaTypes as $c){
                $ext = array_merge($ext,$c::getExtensions());
            }
            $this->validExtensions = $ext;
            return $this->validExtensions;
        }
        
        public function loadMediaTypes(){
            $this->mediaTypes=array();
            $classes = get_declared_classes();
            foreach($classes as $c){
                if(preg_match('/^MediaType_/',$c) && is_subclass_of($c, 'MediaType')){
                    /*
                    $parts = explode("_",$c);
                    $m = $this->mediaTypes;
                    foreach($parts as $p){
                        if($p=="MediaType") continue;
                        $m[$p] = array();
                        $m = $m[$p];
                        //echo $p."\n";
                    }
                    print_r($m);
                    */
                    $this->mediaTypes[] = $c;
                }
            }
        }
        
        public function isValidMediaType($file){
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            if(in_array($ext,$this->validExtensions)){
                return true;
            }else{
                return false;
            }
        }
    }

?>
