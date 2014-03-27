<?php

/**
 * phc-embed.inc.php - object oriented interface for embeder application
 *
 * (c) 2006 by frantik
 * 
 * http://wiki.swiftlytilting.com/phc-win
 *
 */

class embeder
{
   private $name;
   private $main;
   private $add;
   private $path;
   private $exe; 

   function __construct($name, $path = null)
   {  $this->name = $name;
      $this->path = str_replace('\\','/',$path);
   }

   function newEXE()
   {  echo "Creating new exe: $this->name\n";
      
      return _exec("embeder2 new $this->name");
   }

   function addMain($filename)
   {
      $this->main = $filename;
      echo "Setting $filename as main file in $this->name\n";
      return _exec("embeder2 main $this->name \"$filename\"");
   }

   function addFile($filename, $link = null)
   {
     $link = $this->trimpath($link === null ? $filename : $link);
    
      echo "Adding $filename as $link\n";
      return _exec("embeder2 add $this->name \"$filename\" \"$link\"");
   }
   
   private function trimpath($filename)
   {  $filename = str_replace('\\','/',$filename);
      if ($filename[strlen($filename-1)] != '/')
      {  $filename .= '/';
      }
      if (strpos($filename, $this->path) !== false)
      {  $filename = './'.substr($filename,strlen($this->path)+1);
      } 
      return $filename;
   }
}

function & createEXE($name, $path = null)
{

 $ret = new embeder($name, $path);
 $ret->newEXE();
   return $ret;
}

?>