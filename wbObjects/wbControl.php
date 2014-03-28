<?php


class wbControl extends wbObject
{  
   public $class;
   
   public function __construct($class, $parent, $name, $caption, $xpos=null, $ypos=null, $width=0, $height=0, $id=NULL, $style=null, $param1=null, $ntab = null)
   {  $this->parent =& $parent;
      $this->name = $name; 
      $this->objects = array();
      $this->handler = array();
      $this->class = $class;   
      $this->position = array($xpos, $ypos);
      $this->size = array ($width, $height);
           
   	switch($class) {

   		case Accel:
   		case Menu:
   	   	global $wbSystem;
   	   	foreach ($caption as $n=>$v)
            {  
               if (is_array($v))
               {  $id = $wbSystem->nextID();
                  $this->objects[$id] = new wbControl('MenuItem',$this->wbObj, NULL,$v[0]);
                  $wbSystem->registerControl($this->objects[$id], $id);
                  $caption[$n][0]= $id;
               }
            }
      
            if ($class==Accel)
            {  
               $this->wbObj= wbtemp_set_accel_table($parent, $caption);
         
            }else if ($class==Menu)
            {//printr($caption);
               $this->wbObj= wbtemp_create_menu($parent, $caption);  
            }
   		   		   			
         break;
         
         case 'MenuItem':
            $this->wbObj=& $parent;
            $this->onMainEvent = $caption;
         break;
         
   		case ToolBar:
   			$this->wbObj= wbtemp_create_toolbar($parent, $caption, $width, $height, $param1);
   
   		
   			$this->wbObj= wbtemp_create_menu($parent, $caption);
         break;
   		case HyperLink:
   			$this->wbObj= wbtemp_create_control($parent, $class, $caption, $xpos, $ypos, $width, $height, $id, $style,
   			  is_null($param1) ? NOCOLOR : $param1, $ntab);
         break;
         case 'Timer':
            $this->wbObj = wb_create_timer($parent, $id, $caption);
         break;
   		default:
   		   
   			$this->wbObj= wbtemp_create_control($parent, $class, $caption, $xpos, $ypos, $width, $height, $id, $style, $param1, $ntab);
   	}     
     
     
      
      
   }
   
      
   
   public function setItemImage($index, $item = null, $subitem = null)
   {  return wb_set_item_image($this->wbObj, $index, $item, $subitem);
   } 
   
   /*
   
   Creates one or more items in a control.
   
   */
   
   public function createItems($items, $clear=false, $param=null)
   {  $ctrl = $this->wbObj;
   	switch(wb_get_class($ctrl)) {
   
   		case ListView:
   
   			if($clear)
   				wb_send_message($ctrl, LVM_DELETEALLITEMS, 0, 0);
   
   			$last = -1;
   
   			// For each row
   
   			for($i = 0; $i < count($items); $i++) {
   				if(!is_scalar($items[$i]))
   					$last = wbtemp_create_listview_item(
   						$ctrl, -1, -1, (string)$items[$i][0]);
   				else
   					$last = wbtemp_create_listview_item(
   						$ctrl, -1, -1, (string)$items[$i]);
   				wbtemp_set_listview_item_text($ctrl, -1, 0, (string)$items[$i][0]);
   
   				// For each column except the first
   
   				for($sub = 0; $sub < count($items[$i]) - 1; $sub++) {
   					if($param) {
   						$result = call_user_func($param, 	// Callback function
   							$items[$i][$sub + 1],			// Item value
   							$i,								// Row
   							$sub							// Column
   						);
   						wbtemp_set_listview_item_text($ctrl, $last, $sub + 1, $result);
   					} else
   						wbtemp_set_listview_item_text($ctrl, $last, $sub + 1, (string)$items[$i][$sub + 1]);
   				}
   			}
   			return $last;
   			break;
   
   		case TreeView:
   
   			if($clear)
   				$handle = wb_delete_items($ctrl); // Empty the treeview
   
   			if(!$items)
   				break;
   			$ret = array();
   			for($i = 0; $i < count($items); $i++) {
   				$ret[] = wbtemp_create_treeview_item($ctrl,
   				  (string)$items[$i][0],	// Name
   				  isset($items[$i][1]) ? $items[$i][1] : 0,			// Value
   				  isset($items[$i][2]) ? $items[$i][2] : 0,			// Where
   				  isset($items[$i][3]) ? $items[$i][3] : -1,			// ImageIndex
   				  isset($items[$i][4]) ? $items[$i][4] : -1,			// SelectedImageIndex
   				  isset($items[$i][5]) ? $items[$i][5] : 0			// InsertionType
   				);
   			}
   			return (count($ret) > 1 ? $ret : $ret[0]);
   			break;
   
   /*		case ListBox:
   		case ComboBox:
   			return wb_set_text($ctrl, $items, false);*/
   
   		default:
   
   			if(is_array($items)) {
   				foreach($items as $item)
   					wbtemp_create_item($ctrl, $item);
   				return true;
   			} else
   				return wbtemp_create_item($ctrl, $items);
   			break;
   	}
   }
   
   public function deleteItems($items)
   {
      return wb_delete_items($this->wbObj, $items);
   }
   
   public function destroy()
   {
      
      global $wbSystem;
      $wbSystem->registerControl($this,false);
      return wb_destroy_control($this->wbObj);
      
   }	

   public function getControl($id)
   {  return wb_get_control($this->wbObj, $id);
   }
	
	public function isEnabled()
   {
      return wb_get_enabled($this->wbObj);
   }
   
   public function setEnabled($enabled = true)
   {  return wb_set_enabled($this->wbObj, $enabled);
   }

   public function getLevel($item = null)
   {  return wb_get_level($this->wbObj,$item);
   }
   
   public function getSelected()
   {  return wb_get_selected($this->wbObj);
   }


   public function getState($item = null)
   {  return wb_get_state($this->wbObj, $item);
   }

   public function setState($item, $state)
   {  return wb_set_state($this->wbObj, $item, $state);
   } 
   
   
   public function getValue($item = null, $subitem = null)
   {  return wb_get_visible($this->wbObj, $item, $subitem);
   }

   public function setLocation($location)
   {  return wb_set_location($this->wbObj, $location);
   } 

   public function setFont($font, $redraw = true)
   {  return wb_set_font($this->wbObj, $font, $redraw);
   }
   
   /*
   
   Selects one or more items. Compare with wb_set_value() which checks items instead.
   
   */
   
   public function setSelected($selitems, $selected=TRUE)
   {  $ctrl = $this->$item;
   	switch(wb_get_class($ctrl)) {
   
   		case ComboBox:
   			wb_send_message($ctrl, CB_SETCURSEL, (int)$selitems, 0);
   			break;
   
   		case ListBox:
   			wb_send_message($ctrl, LB_SETCURSEL, (int)$selitems, 0);
   			break;
   
   		case ListView:
   
   			if(is_null($selitems)) {
   				return wbtemp_select_all_listview_items($ctrl, false);
   			} elseif(is_array($selitems)) {
   				foreach($selitems as $item)
   					wbtemp_select_listview_item($ctrl, $item, $selected);
   				return TRUE;
   			} else
   				return wbtemp_select_listview_item($ctrl, $selitems, $selected);
   			break;
   
   		case Menu:
   			return wbtemp_set_menu_item_selected($ctrl, $selected);
   
   		case TabControl:
   			wbtemp_select_tab($ctrl, (int)$selitems);
   			break;
   
   		case TreeView:
   			wbtemp_set_treeview_item_selected($ctrl, $selitems);
   			break;
   
   		default:
   			return false;
   	}
   	return true;
   }



   /*
   
   Sets the value of a control or control item
   
   */
   
   public function setValue($value)
   {  
      $ctrl = $this->$item;
   	if(!$ctrl)
   		return null;
   
   	$class = wb_get_class($ctrl);
   	switch($class) {
   
   		case ListView:		// Array with items to be checked
   
   			if($value === null)
   				break;
   			elseif(is_string($value) && strstr($value, ","))
   				$values = explode(",", $value);
   			elseif(!is_array($value))
   				$values = array($value);
   			else
   				$values = $value;
   			foreach($values as $index)
   				wbtemp_set_listview_item_checked($ctrl, $index, 1);
   			break;
   
   		case TreeView:		// Array with items to be checked
   
   			if($item === null)
   				$item = wb_get_selected($ctrl);
   			return wbtemp_set_treeview_item_value($ctrl, $item, $value);
   
   		default:
   
   			if($value !== null) {
   				return wbtemp_set_value($ctrl, $value, $item);
   			}
   	}
   }

   /*
   
   Gets the text from a control, a control item, or a control sub-item.
   
   */
   
   public function getText($item=null, $subitem=null)
   {  $ctrl = $this->wbObj;
   	if(!$ctrl)
   		return null;
   
   	if(wb_get_class($ctrl) == ListView) {
   
   		if($item !== null) {		// Valid item
   
   			$line = wbtemp_get_listview_text($ctrl, $item);
   			if($subitem === null)
   				return $line;
   			else
   				return $line[$subitem];
   
   		} else {					// NULL item
   
   			$sel = wb_get_selected($ctrl);
   			if($sel === null)
   				return null;
   			else {
   				$items = array();
   				foreach($sel as $row)
   					$items[] = wbtemp_get_listview_text($ctrl, $row);
   				return $items ? $items : null;
   			}
   		}
   
   	} elseif(wb_get_class($ctrl) == TreeView) {
   
   		if($item) {
   			return wbtemp_get_treeview_item_text($ctrl, $item);
   		} else {
   			$sel = wb_get_selected($ctrl);
   			if($sel === null)
   				return null;
   			else {
   				return wbtemp_get_text($ctrl);
   			}
   		}
   
   	} else
   
   		return wbtemp_get_text($ctrl);
   }
   
   /*
   
   Sets the text of a control.
   In a ListView, it creates columns: each element of the array text is a column.
   In a tab control, it renames the tabs.
   Sets the text of a control item.
   
   */
   
   public function setText($text, $item=null, $subitem=null)
   {  $ctrl = $this->wbObj;
   	if(!$ctrl)
   		return null;
   
   	switch($this->getClass()) {
   
   		case ListView:
   
   			if($item !== null) {
   
   				if(!is_array($text) && $subitem !== null) {
   
   					// Set text of a ListView cell according to $item and $subitem
   
   					wbtemp_set_listview_item_text($ctrl, $item, $subitem, $text);
   
   				} else {
   
   					// Set text of several ListView cells, ignoring $subitem
   
   					for($sub = 0; $sub < count($text); $sub++) {
   						if($text) {
   							if(($text[$sub] !== null)) {
   								wbtemp_set_listview_item_text($ctrl, $item, $sub, (string)$text[$sub]);
   							}
   						} else {
   							wbtemp_set_listview_item_text($ctrl, $item, $sub, "");
   						}
   					}
   				}
   
   			} else {
   
   				if(!is_array($text))
   					$text = explode(",", $text);
   
   				wb_delete_items($ctrl, null);
   
   				if(!$item) {
   					wbtemp_clear_listview_columns($ctrl);
   
   					// Create column headers
   					// In the loop below, passing -1 as the last argument of wbtemp_create_listview_column()
   					// makes it calculate the column width automatically
   
   					for($i = 0; $i < count($text); $i++) {
   						if(is_array($text[$i]))
   							wbtemp_create_listview_column($ctrl, $i,
   							  (string)$text[$i][0],
   							  isset($text[$i][1]) ? (int)$text[$i][1] : -1);
   						else
   							wbtemp_create_listview_column($ctrl, $i,
   							  (string)$text[$i], -1);
   					}
   				}
   			}
   			break;
   
   		case ListBox:
   
   			if(!$text) {
   				wb_delete_items($ctrl);
   			} elseif(is_string($text)) {
   				if(strchr($text, "\r") || strchr($text, "\n")) {
   					$text = preg_split("/[\r\n,]/", $text);
   					wb_delete_items($ctrl);
   					foreach($text as $str)
   						wbtemp_create_item($ctrl, (string)$str);
   				} else {
   					$index = wb_send_message($ctrl, LB_FINDSTRINGEXACT, -1, wb_get_address($text));
   					wb_send_message($ctrl, LB_SETCURSEL, $index, 0);
   				}
   			} elseif(is_array($text)) {
   				wb_delete_items($ctrl);
   				foreach($text as $str)
   					wbtemp_create_item($ctrl, (string)$str);
   			}
   			return;
   
   		case ComboBox:
   
   			if(!$text)
   				wb_delete_items($ctrl);
   			elseif(is_string($text)) {
   				if(strchr($text, "\r") || strchr($text, "\n")) {
   					$text = preg_split("/[\r\n,]/", $text);
   					wb_delete_items($ctrl);
   					foreach($text as $str)
   						wbtemp_create_item($ctrl, (string)$str);
   				} else {
   					$index = wb_send_message($ctrl, CB_FINDSTRINGEXACT, -1, wb_get_address($text));
   					wb_send_message($ctrl, CB_SETCURSEL, $index, 0);
   					if($index == -1)
   						wb_send_message($ctrl, WM_SETTEXT, 0, wb_get_address($text));
   				}
   			} elseif(is_array($text)) {
   				wb_delete_items($ctrl);
   				foreach($text as $str)
   					wbtemp_create_item($ctrl, (string)$str);
   			}
   			return;
   
   		case TreeView:
   
   			if($item)
   				return wbtemp_set_treeview_item_text($ctrl, $item, $text);
   			else
   				return $this->createItems($ctrl, $text, true);
   
   		default:
   			if(is_array($text))
   				return null;
   			$text = str_replace("\r", "", (string)$text);
   			$text = str_replace("\n", "\r\n", $text);
   			return wbtemp_set_text($ctrl, $text, $item);
   	}
   }

   public function sort($asc = true, $item = null)
   {  return wb_sort($this->wbObj, $asc, $item);
   } 

   public function __set($n, $val)
   {  switch ($n)
      {  case 'text':
            $this->setText($val);
            return $this->getText();
         
         case 'value':
            $this->setValue($val);
            return $this->getValue();
         
         default:
           return parent::__set($n,$val);
      }
   }


   public function & __get($n)
   {  switch ($n)
      {  case 'text':
            $ret = $this->getText();
            return $ret; return $this->getText();
         
         case 'value':
            $ret = $this->getValue();
            return $this->getValue();
            
         default:
           return parent::__get($n);
      }
   }


   public function onKeyDown($handler)
   {  $this->handler[WBC_KEYDOWN] = $handler;
   }
   public function onKeyUp($handler)
   {  $this->handler[WBC_KEYUP] = $handler;
   }
     
   public function onHeaderSelect($handler)
   {  $this->handler[WBC_HEADERSEL] = $handler;
   }
   
   public function onMainEvent($handler)
   {  
      $this->handler['main'] = $handler; 
   }
}


