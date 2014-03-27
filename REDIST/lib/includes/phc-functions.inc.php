<?php

/**
 * phc-functions.inc.php - Functions for phc-win.phw
 *
 * (c) 2006 by frantik
 * 
 * http://wiki.swiftlytilting.com/phc-win
 *
 */
 

function bcomp() {
	return "lib\\bin\\bcomp.exe";
}

/**
	Execute a command and get it's err code. If 0, true, if not false.
	@return bool
**/
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
   $oExec = $WshShell->Run("cmd /C \" $cwd\\$cmd\" >> ./compile.log", 0,true);
   
   return $oExec == 0 ? true : false;
}


function bcompile($input, $output)
{
	return _exec(bcomp()." \"$input\" \"$output\"");
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
{  
   global $wbSystem;   
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
   
   logger($path);
   
   $bcompfile =$path.'\\'.$basename . '.phb';
   $finalexe =$path.'\\'.$basename . '.exe';
   if(file_exists($finalexe)) {
		echo "Removing old EXE: $finalexe...";
		shell_exec("del /q /f \"$finalexe\"");
		echo "done\n";
   }
   if(file_exists($bcompfile)) {
		echo "Removing: $bcompfile...";
		shell_exec("del /q /f \"$bcompfile\"");
		echo "done\n";
   }
   echo "Compiling: $basename.$ext --> $basename.phb... ";
   $wbSystem->edit->text = ob_get_contents();
   //$wait = $wbSystem->main->wait(50);
   if (bcompile($mainfile,$bcompfile) === false) { 
		echo "Unable to compile.\n";
   } else {  
      echo "done.\n";
      echo "Done compiling.\n\n";
      echo "Embedding compiled code EXE: $basename.phb --> $basename.exe\n";
      $wbSystem->edit->text = ob_get_contents();
      //$wait = $wbSystem->main->wait(50);
      $exe = createEXE($basename);
      $exe->addMain($bcompfile);
      
      setEXEtype($path.'\\'.$basename.'.exe',$wbSystem->main);
      echo "\nDone creating EXE.\n";
   }
   $wbSystem->edit->text = ob_get_contents();
   
   
   
   return true;
}

function openingText() {
	$var = "phc-win $version (c) 2014 Andrew Fitzgerald, RainbowDashDC [RDashINC]\n";
	$var .= "PHP Version: ".phpversion()."\n";
	$var .= "\n";
    $var .= "* To compile a single file:\n";
    $var .= "   - Choose 'Compile single file' from the File menu.\n";
	$var .= "   - Then select the file to compile.\n";
	$var .= "\n";
    $var .= "* To build an EXE containing all files in a directory and all sub directories:\n";
    $var .= "	 - Choose 'Compile directory' from the File menu.\n";
    $var .= "	 - Select the project folder.\n   - Select the main program file.\n";
    $var .= "    - phc-win will then recursively scan the specified directory.\n";
    $var .= "	 - All files with 'php' anywhere in the extension will be compiled into .phb files.\n";
    $var .= "	 - These .phb files, along with all files in the directory tree will be added to the project EXE.\n";
	$var .= "\n";
    $var .= "* Once the EXE has been created, you will be asked about the EXE type:\n";
    $var .= "	 - CONSOLE (displays CMD Window box)\n";
    $var .= "	 - WINDOWS (no CMD Window box).\n\n";
    $var .= "* Place the EXE in the same directory with the required DLL file(s) and php-embed.ini file if needed.";
	return $var;
}

function logger($path) {
	shell_exec("cmd /C \"echo Logging Started. > $path\\compile.log\"");
	echo "Log started \"$path\\compile.log\"\n";
}

function complexEXE($mainfile, $addfiles, $path)
{ 
   logger($path); 
   global $wbSystem;
 
   
   array_push($addfiles, $mainfile);
   
   $mainparts = getfileparts($mainfile);
   $err = false;
   
   // Compile files
   foreach ($addfiles as $v)
   {  
      if (!$err)
      {  extract(getfileparts($v));
         
		 if(stripos($ext,'phb') !== false ) {
			// Remove .phbs untill we have a way too check if they've been changed or not.
			// TODO: Use sha256 too check if file has changed?
		    echo "Removing: $basename.$ext...";
			shell_exec("del /q /f \"$path\\$basename.phb\"");
			echo "done\n";
         } elseif (stripos($ext,'php') !== false ) {  
		    $bcompfile ="$path\\$basename.phb";
			$finalexe =$path.'\\'.$basename . '.exe';
         
            echo("Compiling: $basename.$ext --> $basename.phb... ");
            $wbSystem->edit->text = ob_get_contents();
            //$wbSystem->main->wait(50);
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
               {  echo "\nUnable to compile. - See compile.log in \"$path\"\n";
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

   if(file_exists($finalexe)) {
		echo "Removing old EXE: $finalexe...";
		shell_exec("del /q /f \"$finalexe\"");
		echo "done\n";
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
      //$wbSystem->main->wait(50); 
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
       $wbSystem->main->messageBox("Finished compiling successfully!","Done");
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

