<?php

class wbPrimitive
{
   protected $wbObj;
   
   function drawImage ($bitmap, $xpos=0, $ypos=0 , $width=0 , $height=0, $transparentcolor=NOCOLOR)
   {  return wb_draw_image ($this->wbObj, $bitmap, $xpos, $ypos , $width , $height, $transparentcolor);      
   }
   
   function drawLine ($x0, $y0, $x1, $y1, $color=0, $linewidth=0)
   {  return wb_draw_line ($this->wbObj, $x0, $y0, $x1, $y1, $color, $linewidth);
   }
   function drawPoint ($xpos, $ypos, $color)
   {
      return wb_draw_point ($this->wbObj, $xpos, $ypos, $color);
   }
   function drawRect ($xpos, $ypos, $width, $height, $color, $filled = false, $linewidth=0)
   {  return wb_draw_rect ($this->wbObj, $xpos, $ypos, $width, $height, $color, $filled, $linewidth);
   }
   
   function drawText ($text, $xpos, $ypos , $width, $height=null, $font = null, $flags = null)
   {  if ($height === null && $font === null && $flags === null)
      {  $font = $width;
         return wb_draw_text ($this->wbObj, $text, $xpos, $ypos , $font);
      }else
      {  return wb_draw_text ($this->wbObj, $text, $xpos, $ypos , $width, $height, $font, $flags);
      }
   }
   function getPixel ($xpos, $ypos)
   {  return wb_get_pixel ($this->wbObj, $xpos, $ypos);
   }  
   
   function getSize($param = null)
   {  return wb_get_size($this->wbObj, $param);
   }
   
   function setImage($source, $transparentcolor = null, $index = null, $param = null)
   {  return wb_set_image($this->wbObj, $source, $transparentcolor, $index, $param);
   }
   
   function setItemImage($wbobject, $index, $item = null ,$subitem = null)
   {
      return wb_set_item_image($this->wbObj, $index, $item, $subitem);
   }
}