<?php

/**
 * phc-functions.inc.php - Functions for phc-win.phw
 *
 * (c) 2006 by frantik
 * 
 * http://wiki.swiftlytilting.com/phc-win
 *
 */



function _exec($cmd)
{ 
   $WshShell = new COM("WScript.Shell");
   $cwd = getcwd();
   if (strpos($cwd,' '))
   {  if ($pos = strpos($cmd, ' '))
      {  $cmd = substr($cmd, 0, $pos) . '" ' . substr($cmd, $pos);
      }
      else
      {  $cmd .= '"';
      }
      $cwd = '"' . $cwd;
   }
   //echo "\nexec: cmd /C \" $cwd\\$cmd \"\n";
   $oExec = $WshShell->Run("cmd /C \" $cwd\\$cmd\"", 0,true);
   
   return $oExec == 0 ? true : false;
}


function bcompile($input, $output)
{ 
   return _exec("bcomp.exe \"$input\" \"$output\"");
}


function getfileparts($file)
{
   $dotparts=explode('.',$file);
   $ext = array_pop($dotparts);
   if (strpos($ext,'\\'))
   {  $dotparts[] = $ext;
      $ext = '';
   }
   $path = implode('.',$dotparts);
   
   $slashparts = explode('\\',$path);
   
   $basename = array_pop($slashparts);
   $path = implode('\\',$slashparts);     
   return array('path'=>$path, 'basename'=>$basename, 'ext'=>$ext);
}

function simpleEXE($mainfile)
{  global $wbSystem;   
   $dotparts=explode('.',$mainfile);
   $ext = array_pop($dotparts);
   if (strpos($ext,'\\'))
   {  $dotparts[] = $ext;
      $ext = '';
   }
   $path = implode('.',$dotparts);
   
   $slashparts = explode('\\',$path);
   
   $basename = array_pop($slashparts);
   $path = implode('\\',$slashparts);     
   
   $bcompfile =$path.'\\'.$basename . '.phb';
   echo "Compiling: $basename.$ext --> $basename.phb... ";
   $wbSystem->edit->text = ob_get_contents();
   $wait = $wbSystem->main->wait(50);
   if (bcompile($mainfile,$bcompfile) === false)
   {  echo "Unable to compile.\n";
   }
   else
   {  
      echo "done.\n";
      echo "Done compiling.\n\n";
      echo "Embedding compiled code EXE: $basename.phb --> $basename.exe\n";
      $wbSystem->edit->text = ob_get_contents();
      $wait = $wbSystem->main->wait(50);
      $exe = createEXE($basename);
      $exe->addMain($bcompfile);
      
      setEXEtype($path.'\\'.$basename.'.exe',$wbSystem->main);
      echo "\nDone creating EXE.\n";
   }
   $wbSystem->edit->text = ob_get_contents();
   
   
   
   return true;
}

function complexEXE($mainfile, $addfiles, $path)
{  global $wbSystem;
 
   
   array_push($addfiles, $mainfile);
   
   $mainparts = getfileparts($mainfile);
   $err = false;
   
   // Compile files
   foreach ($addfiles as $v)
   {  
      if (!$err)
      {  extract(getfileparts($v));
         
         if (stripos($ext,'php') !== false )
         {  $bcompfile ="$path\\$basename.phb";
         
            echo("Compiling: $basename.$ext --> $basename.phb... ");
            $wbSystem->edit->text = ob_get_contents();
            $wbSystem->main->wait(50);
            if (file_exists($bcompfile))
            {  echo "file already compiled.\n";
               if (($loc = array_search($bcompfile, $addfiles)) !== false)
               {  unset($addfiles[$loc]);
               }
            } else 
            {  if(bcompile($v,$bcompfile) !== false)
               {
                  echo "done\n";
               } else
               {  echo "\nUnable to compile.\n";
                  $err = true;
               }
            }
         }
         
         
         if (wb_wait() == 27)
         {  if($wbSystem->main("Are you sure you want to cancel the operation?","Cancel?", WBC_YESNO))
            {  $err = true;
               echo "User canceled operation\n";
               break;
            }
         }
      }            
   }  
   
   // Embed files
   if (!$err)
   {  array_pop($addfiles);
      $mainbasename = $mainparts['basename'];
      echo("Done compiling.\n\n");
      
      $wbSystem->edit->text = ob_get_contents();
      
      if (file_exists("$path\\$mainparts[basename].exe") !== false)
      {  
         if (!unlink("$path\\$mainparts[basename].exe"));
         {  echo "Error deleting $mainparts[basename].exe";
         }
      }
      
      $exe = createEXE($mainparts['basename'], $path);
      $wbSystem->edit->text = ob_get_contents();
      $wbSystem->main->wait(50); 
      $exe->addMain($mainparts['path'].'/'."$mainbasename.phb");
      $wbSystem->edit->text = ob_get_contents();
      

      $wbSystem->main->wait(50);  
      foreach($addfiles as $v)
      {  
         if (!$err)
         {  extract(getfileparts($v));
            
            if (strpos($ext,'php') !== false)
            {  $v2 = $path.'/'."$basename.phb";
               $exe->addFile($v2, $v);
            }
            else
            {
               $exe->addFile($v);
            }
            
             
            if (wb_wait() == 27)
            {  if($wbSystem->main->messageBox("Are you sure you want to cancel the operation?","Cancel?", WBC_YESNO))
               {  $err = true;
                  echo "User canceled operation\n";
                  break;
               }
            }
            $wbSystem->main->wait(50); 
            $wbSystem->edit->text = ob_get_contents();
         }
         
      }
       echo "\nDone creating EXE.\n";
       $wbSystem->main->wait(50); 
       setEXEtype($mainparts['path'].'\\'.$mainparts['basename'].'.exe',$wbSystem->main);
       $wbSystem->main->messageBox("Finished compiling sucessfully!","Done");
   }
   
   return $err; 
}

function getFiles($dir) 
{   
   $files = scandir($dir);
   
   if ($files === false)
   {  return false;
   }
   
   $thisfiles = array();
   
   foreach($files as $v)
   {  if ($v != '.' && $v != '..')
      {  $thisdir = $dir.'\\'.$v;
         if (is_dir($thisdir))
         {  $subfiles = getFiles($thisdir);
            $v = $v;
            foreach($subfiles as $v2)
            {  $thisfiles[] = $v2;
            }
         }
         else if(is_file($thisdir))
         {  $thisfiles[] = $thisdir;
         }
      }
   }
   
  return $thisfiles;
}

function setEXEtype($filename, &$window)
{
   if (file_exists($filename))
   {  clearstatcache();
      $file = file_get_contents($filename);
      $type = false;
      switch($file[348])
      {  case chr(03):
            $type = 'CONSOLE';
            $opp = 'WINDOWS';      
         break;
         case chr(02):
            $type = 'WINDOWS';
            $opp = 'CONSOLE';
         break;
         default:
            $type = false;
      }
      
      if($type) 
      {  extract(getfileparts($filename));
         if ($window->messageBox("Program '$basename.$ext' is currently set to type '$type'.\nChange to '$opp'?",'Change EXE type?', WBC_YESNO) )
         {  $newchr = false;
            if ($type == 'CONSOLE')
            {  $newchr= chr(02);
            }
            else
            { $newchr=  chr(03);
            }
            file_put_contents($filename,substr($file,0,348).$newchr.substr($file,349));
            
            return true;
         }
      }else
      { $window->messageBox("EXE is not a phc-win EXE",'Error');
         return false;
      }
   } 
   else  
   {$window->messageBox("$filename does not exist",'Error');
   }
}

