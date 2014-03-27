<?php

/**
 * wbObject.php5
 * 
 * Provides basic functionality for windows and controls
 *
 */

class wbObject extends wbPrimitive
{
   
   public $objects;
   public $parent;
   public $handler;
   public $name;
   public $size;
   public $position;
   
   
   public function __destruct()
   {  global $wbSystem;
      $wbSystem->registerControl($obj,false);
   }

   /**
    * Event handler (object side)
    *
    */     
   
   public function handle(&$params)
   {  
      $param1 = $params['param1'];
     $param2 = $params['param2'];
	   
      $onEvent = false;
      if (array_key_exists('event',$this->handler) && is_callable($this->handler['event']) 
            && $params['param2'] !== 'noEvent')
      { 
         $onEvent = call_user_func_array($this->handler['event'],array($params));
      }
        
      if ($param1 < WBC_DBLCLICK) 
      { $param1 = $params['id'] == IDDEFAULT ? 'mouseMove':'main';
        //$param1 = 'main';
      }    
      else if ($param1 & WBC_DBLCLICK) 
      { $param1 = WBC_DBLCLICK;
      }      
      else if ($param1 & WBC_MOUSEDOWN) 
      { $param1 = WBC_MOUSEDOWN;
      }
      else if ($param1 & WBC_MOUSEUP) 
      { $param1 = WBC_MOUSEUP;
      }
      else if ($param1 & WBC_KEYDOWN) 
      { $param1 = WBC_KEYDOWN;
      }
      else if ($param1 & WBC_KEYUP) 
      { $param1 = WBC_KEYUP;
      }
      

      if ($params['id'] == IDCLOSE && array_key_exists('onClose',$this->handler) && is_callable($this->handler['onClose'])) 
      { 
         return call_user_func_array($this->handler['onClose'],array($params));
      }
      else if ($param1 == WBC_RESIZE && array_key_exists($param1,$this->handler) && is_callable($this->handler[$param1]))
      {  
         $ret = call_user_func_array($this->handler[$param1],array($params));
           $this->size = $this->getSize();  
          return $ret;
      }
      else if ($param1 != 'event' && array_key_exists($param1,$this->handler) && is_callable($this->handler[$param1]))
      {  //echo "[$params[ctrl]->name:$param1]";
         return call_user_func_array($this->handler[$param1],array($params));
      }
      else 
      {
          return $onEvent;
      }
   }
 

   
   
   public function & __get($n)
   {  
      switch ($n)
      {  case 'text':
            $text = $this->getText();
            return $text;
                    
         case 'xpos':
            return $this->position[0];
         
         case 'ypos':
            return $this->position[1];
         
         case 'width':
            return $this->size[0];
         
         case 'height':
            return $this->size[1];

         
         case 'self':
         case 'wbObj':
            return $this->wbObj;
                
         case 'name':
            return $this->name;
            
         case 'id':
            $id = wb_get_id($this->wbObj);
            return $id;
            
         default:
          
            if (array_key_exists($n, $this->objects))
            {  return $this->objects[$n];
            }
      }
      $ret = false;
      return $ret;
   }     
   
   public function __set($n, $v)
   {  if (substr($n,0,2) == 'on' && is_callable(array($this,$n)))
      { return call_user_func_array(array($this,$n),array($v));
         
      }
   }
 
   public function getClass()
   {
      return wb_get_class($this->wbObj);
   }
      
   
   public function hasFocus()
   {
      return (wb_get_focus() == $this->id);
   }

   public function getID()
   {  return wb_get_id($this->wbObj);
   }

   public function getItemCount()
   {  return wb_get_item_count($this->wbObj);
   }

   public function getItemList()
   {  return wb_get_item_list($this->wbObj);
   }
   
   public function getParent($item = null)
   {  return $this->parent;
   }
   public function getPosition($clientarea = false, $use_internal_position = true)
   {   
      return $use_internal_position ? $this->position : wb_get_position($this->wbObj, $clientarea) ;
   }
   
   public function getSize($param = null)
   {  $ret = wb_get_size($this->wbObj, $param);
      if (is_array($ret))
      {  $ret['width'] = $ret[0];
         $ret['height'] = $ret[1];
      }
      return $ret;
   }

   public function isVisible()
   {  return wb_get_visible($this->wbObj);
   }

   public function refresh($now = false)
   {  return wb_refesh($now);
   }
      
   public function setArea($type , $x, $y, $width, $height)
   {  
       $this->size = array ($width, $height);
      return wb_set_area($this->wbObj, $type, $x, $y, $width, $height);
   }
   
   public function focus()
   {  return wb_set_focus($this->wbObj);
   }
  
   
   public function setSize($width, $height = null)
   {  if ($height === null)
      {
         $this->size[0] = $width;
           return wb_set_size($this->wbObj, $width);
      }
      else
      {  
         $this->size = array ($width, $height);
         return wb_set_size($this->wbObj, $width, $height);
      }
   } 

   public function resize($width = 0, $height = 0, $relative = true)
   {  if ($relative)
      {  $loc = $this->getSize();
         $width = $loc[0] + $width;
         $height = $loc[1] + $height;
      }
       $this->size = array ($width, $height);
      return wb_set_size($this->wbObj, $width, $height);
   }   
   
   public function setStyle($style, $set)
   {  return wb_set_style($this->wbObj, $style, $set);
   }
   
   public function setVisible($visible =true)
   {  return wb_set_visible($this->wbObj, $visible);
   } 
   
   public function hide()
   {  return wb_set_visible($this->wbObj, false);
   } 
   
   public function show()
   {  return wb_set_visible($this->wbObj, true);
   } 
   
   
   public function setPosition($xpos = WBC_CENTER, $ypos = WBC_CENTER)
   {  $this->position = array($xpos, $ypos);
      
      return wb_set_position($this->wbObj, $xpos, $ypos);
   } 

   public function move($xpos = 0, $ypos = 0, $relative = true)
   {  if ($relative)
      {  $loc = $this->getPosition();
         $xpos = $loc[0] + $xpos;
         $ypos = $loc[1] + $ypos;
      }
      $this->position = array($xpos, $ypos);
      return wb_set_position($this->wbObj, $xpos, $ypos);
   }
   



   /**
    * Events
    *
    */     


   public function onEvent($handler)
   {  
      $this->handler['event'] = $handler; 
   }
   
   public function onMouseMove($handler)
   {  $this->handler['mouseMove'] = $handler;
   }
   
   public function onMouseDown($handler)
   {  $this->handler[WBC_MOUSEDOWN] = $handler;
   }
   
   public function onMouseUp($handler)
   {  
      $this->handler[WBC_MOUSEUP] = $handler;
   }
   
   public function onDblClick($handler)
   {  $this->handler[WBC_DBLCLICK] = $handler;
   }
   
   public function onGetFocus($handler)
   {  $this->handler[WBC_GETFOCUS] = $handler;
   }
   public function onRedraw($handler)
   {  $this->handler[WBC_REDRAW] = $handler;
   }


}

