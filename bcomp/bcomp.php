<?php

/**
 * bcomp - bcompiles a php file into php bytecode.  
 *
 * (c) 2006 by frantik
 * 
 * http://wiki.swiftlytilting.com/phc-win
 *
 */

if ($argc == 2 || $argc == 3)
{  
   $dotparts=explode('.',$argv[1]);
   $ext = array_pop($dotparts);
   if (strpos($ext,'\\'))
   {  $dotparts[] = $ext;
      $ext = '';
   }
   $path = implode('.',$dotparts);
   
   $slashparts = explode('\\',$path);
   
   $basename = array_pop($slashparts);
   $path = implode('\\',$slashparts);     
   
   if ($argc == 2)
   {  
      $argv[2] =$path . $basename . '.phb';
   }
   
   if (file_exists($argv[1]))
   {  echo "Compiling $argv[1]...";
      $fh = fopen($argv[2], "w");
      bcompiler_write_header($fh);
      bcompiler_write_file($fh, $argv[1]);
      bcompiler_write_footer($fh);
      fclose($fh);
      
      echo "done.";
 
   }
   else
   {  echo "File '$argv[1]' does not exist";
   }
  
}
else
{  echo "bcomp inputfile [outputfile]\n";
}

echo "\n";
exit;

