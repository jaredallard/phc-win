<?php

/**
 * wbWindow.php5
 * 
 * Describes the window object
 *
 */

class wbWindow extends wbObject
{
      public $size;
      
      private $caption;
      private $timer;


   public function __construct($class, $parent, $name, $caption, $xpos=null, $ypos=null, $width=null, $height=null, $id, $style=0x10, $param = 0xFFC0)
   {  $this->name = $name;
      $this->parent = $parent;
      $this->handler=array();
      $this->objects=array();
      $this->caption = $caption;
      $this->position = array($xpos, $ypos);
      $this->size = array ($width, $height);

      if ($width ==null && $height==null && $style==WBC_NOTIFY && $param == WBC_ALL)
      {
           $this->wbObj =& wb_create_window($parent, $class, $caption, $xpos, $ypos);
      }
      else
      {
         $this->wbObj =& wb_create_window($parent, $class, $caption, $xpos, $ypos, $width, $height, $style  ,$param );
      }

      // create timer to check to see if the window has maximized.  if so, throw onResize
      if ($class == ResizableWindow)
      {  $this->timer = $this->createControl('Timer',$id,100);
         $this->timer->onMainEvent = array('wbWindow','checkSize');
      }

      wb_set_handler($this->wbObj, 'wbSystem_handler' );
      return $this->wbObj;

   }

   public function & createControl($class, $name=null, $caption=null, $xpos=null, $ypos=null, $width=0, $height=0, $style=null, $param1=null, $ntab = null)
   {  if (is_array($class) && $name=== null && $caption === null)
      {  if (array_key_exists(0,$class) && is_array($class[0]))
         {  $ret = array();
            foreach($class as $v)
            {  $ret[$v[0]] = call_user_func_array(array($this,'createControl'),$v);
            }
            return $ret;
         } else
         {
            extract($class);
         }
      }

      global $wbSystem;
      $id = $wbSystem->nextID();
      $this->objects[$name] = new wbControl($class, $this->wbObj,$name, $caption, $xpos, $ypos, $width, $height, $id, $style, $param1, $ntab);
      $wbSystem->registerControl($this->objects[$name], $id);
      return $this->objects[$name];
   }



   public function & createWindow($class, $name=null, $caption=null, $xpos=0x800, $ypos=0x800,  $width=null, $height=null, $style=0x10, $param1=0xffc0)
   {  if (is_array($class) && $name === null && $caption === null)
      {
         extract($class);
      }
      global $wbSystem;
      $id = $wbSystem->nextID();
      $this->objects[$name] = new wbWindow($class, $this->wbObj,$name, $caption, $xpos, $ypos, $width, $height, $id, $style, $param1);
      $wbSystem->registerWindow($this->objects[$name], $id);
      if (isset($controls))
      {  $this->objects[$name]->createControl($controls);

      }
      return $this->objects[$name];
   }

   public function destroy()
   {

      global $wbSystem;
      $wbSystem->registerControl($this,false);
      return wb_destroy_window($this->wbObj);
   }

   public function wait($pause = 0, $flags = null)
   {
      return wb_wait($this->wbObj, $pause, $flags);
   }


   public function messageBox($message, $title = null, $style=null)
   {  if ($title == null)
      {  $title = $this->caption;
      }
     return wb_message_box($this->wbObj,$message,$title,$style);
   }


   public function dialogColor($title=null, &$color=null)
   {  return wb_sys_dlg_color($this->wbObj, $title, $color);
   }

   public function dialogOpen($title=null, &$filter, &$path, &$filename = null)
   {  $filter = $this->_make_file_filter($filter ? $filter : $filename);
      return wbtemp_sys_dlg_open($this->wbObj, $title, $filter, $path);
   }

   public function dialogPath($title=null, &$path=null)
   {  return wb_sys_dlg_path($this->wbObj, $title, $path);
   }

   public function dialogSave($title=null, &$filter, &$path, &$filename)
   {  $filter = $this->_make_file_filter($filter ? $filter : $filename);

	   return wbtemp_sys_dlg_save($this->wbObj, $title, $filter, $path, $filename, $defext);
   }


   /*

   Creates a file filter for Open/Save dialog boxes based on an array.

   */

   function _make_file_filter($filter)
   {
   	if(!$filter)
	   {	return "All Files (*.*)\0*.*\0\0";
	   }

	   if(is_array($filter))
	   {  $result = "";
		   foreach($filter as $line)
   			$result .= "$line[0] ($line[1])\0$line[1]\0";
		   $result .= "\0";
		   return $result;
	   } else
		{  return $filter;
	   }
   }

   public function setIcon($icon)
   {

      return $this->setImage($icon);
   }

   public function checkSize($params)
   {  //print_r($params);
      $wbSystem =& $params['wbSystem'];
      $__this =& $params['window'];

      if ($__this->size != $__this->getSize())
      {  $params['param1'] = WBC_RESIZE;
         $params['param2'] = 'noEvent';
          $__this->handle($params);
          $__this->size = $__this->getSize();
          //echo 'resize!';

      }
      return;
   }

   /**
    * Events
    *
    */

   public function onResize($handler)
   {  $this->handler[WBC_RESIZE] = $handler;
   }

   public function onClose($handler)
   {  $this->handler['onClose'] = $handler;
   }
}
