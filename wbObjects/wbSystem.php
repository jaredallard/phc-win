<?php

/**
 * wbSystem.php5
 * 
 * wbSystem contains a reference to every created wbObject as well as
 * various non-window specific commands
 *
 */
 



class wbSystem
{
   
   public $globals; 
   
   public $objectsByName;
   public $objects;
   public $windows;
   public $controls;
   public $handler;
   
   private $IDnum;
   
   

   function __construct()
   { $this->IDnum = 101;
     $this->windows = array();
     $this->controls = array();
     $this->globals = array();
     $this->handler = array();
   }
   
   public function start()
   {  if (array_key_exists('start',$this->handler) && is_callable($this->handler['start']))      
      {  $params = array('wbSystem'=>$this);
         call_user_func_array($this->handler['start'],array($params));
      }
      wb_main_loop();  
   }

   public function & createWindow($class, $name=null, $caption=null, $xpos=0x800, $ypos=0x800, $width=null, $height=null, $style=0x10, $param1=0xffc0)
   {  if (is_array($class) && $name === null && $caption === null)
      {  extract($class);
      }     
      
            
      $id = $this->nextID();
      $this->windows[$name] = new wbWindow($class, NULL,$name, $caption, $xpos, $ypos, $width, $height, $id, $style, $param1);
      
      $this->objects[$this->windows[$name]->self]=$this->windows[$name];
      if (isset($controls))
      {  $this->windows[$name]->createControl($controls);
         
      }
      return $this->windows[$name];
      
   }
   
   public function nextID()
   {
        return $this->IDnum++;
   }
   
   public function registerControl(&$object, $id = NULL)
   {  
      if ($id === NULL)
      {  $id = $object->getID();
      }
      else if ($id === false)
      {  unset($this->controls[$id]);
      }
      else
      {  $this->controls[$id] =& $object;
         $this->objects[$object->self]=& $object;
         $this->objectsByName[$object->name] =& $object;
      }
      
      
   }
   
   
   public function registerWindow(&$object, $id = NULL)
   {  if ($id === NULL)
      {  $id = $this->nextID;
      }
      else if ($id === false)
      {  unset($this->windows[$id]);
      }
      else
      {  $this->windows[$id] =& $object;
         $this->objects[$object->self]=& $object;
         $this->objectsByName[$object->name] =& $object;
      }
      
   }
  
  
   public function & __get($n)
   {  
            if (array_key_exists($n, $this->windows))
            {  return $this->windows[$n];
            }
            else if (array_key_exists($n, $this->objectsByName))
            {  return $this->objectsByName[$n];
            }
      
      $ret = false;
      return $ret;
   } 
   
   public function __set($n, $v)
   {  if (substr($n,0,2) == 'on' && is_callable(array($this,$n)))
      { return call_user_func_array(array($this,$n),array($v));
         
      }
   }
   
   function setFocus($object)
   {  return wb_set_focus($object->self);
   }
   
   
   /**
    * Events
    *
    */     

   public function onEvent($handler)
   {  
      $this->handler['event'] = $handler; 
   }
   
   public function onStart($handler)
   {  $this->handler['start'] =$handler;
   }

   
   /**
    * Event handler (system side)
    *
    */     
   
   
   
   public function handle($params)
   {  if (array_key_exists('event',$this->handler))
      {
         return call_user_func_array($this->handler['event'],array($params));
      }
   }
   
   
    
   public function handler($window, $id, $ctrl, $param1, $param2)
   {  $window = $this->objects[$window];
      $params = array('window'=>$window, 'id'=>$id, 'ctrl'=>$ctrl, 'param1'=>$param1, 'param2'=>$param2, 'wbSystem'=>$this, 'mouse'=>false);
      
       
		   
      // this only is true when a mouse event occurs
      if ($params['id'] == IDDEFAULT)      
      {  
	      
         if($param1 & WBC_LBUTTON)
			{ 	$params['mouse']['buttons'][] = 'LBUTTON';
		   }
			if($param1 & WBC_RBUTTON)
			{ 	$params['mouse']['buttons'][] = 'RBUTTON';
		   }
		   if($param1 & WBC_MBUTTON)
			{ 	$params['mouse']['buttons'][] = 'MBUTTON';
		   }
			if($param1 & WBC_ALT)
			{ 	$params['mouse']['buttons'][] = 'ALT';
		   }
			if($param1 & WBC_CONTROL)
			{ 	$params['mouse']['buttons'][] = 'CONTROL';
		   }
			if($param1 & WBC_SHIFT)
			{ 	$params['mouse']['buttons'][] = 'SHIFT';
		   }
		   
		   $xpos = $param2 & 0xFFFF;
	      $ypos =($param2 & 0xFFFF0000) >> 16;
         
         
         foreach ($window->objects as $n=>$v)
         {
            if ($xpos >= $v->xpos && $xpos <= $v->xpos + $v->width - 1)
            {  if ($ypos >= $v->ypos && $ypos <= $v->ypos + $v->height - 1)
               {  
                  $params['ctrl'] =& $v;
                  $temp_params = $params;
                  $temp_params['mouse']['xpos'] = $xpos - $v->xpos;
	               $temp_params['mouse']['ypos'] = $xpos - $v->ypos;
                  
                  $v->handle($temp_params);
               }
            }
         }
         
	   }

      
            
      if (array_key_exists($id, $this->controls))
      {  $params['ctrl'] =& $this->controls[$id];
         $this->controls[$id]->handle($params);
      } 
      else if (array_key_exists($id, $this->windows))
      {  $this->windows[$id]->checkSize();
         
      }      
      else 
      {  $handled = false;    
       
         $handled = $window->handle($params);
         
         if ($id == IDCLOSE && $handled == false)
         {
                                      
            $window->destroy();     
         }
      }      
   } 
   
   
    
}

function wbSystem_handler($window, $id, $ctrl=0, $param1=0, $param2=0)
{  
   global $wbSystem;
   //echo 'event';
   $wbSystem->handler($window, $id, $ctrl, $param1, $param2);
   
}

$wbSystem = new wbSystem();
