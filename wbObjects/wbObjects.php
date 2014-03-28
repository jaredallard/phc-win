<?php

/**
 * wbObjects (c) 2009 by frantik
 *
 *
 *
 */


// Constants

define("BM_SETCHECK",            241);
define("LVM_FIRST",              0x1000);
define("LVM_DELETEALLITEMS",     (LVM_FIRST+9));
define("LVM_GETITEMCOUNT",       (LVM_FIRST+4));
define("LVM_GETITEMSTATE",       (LVM_FIRST+44));
define("LVM_GETSELECTEDCOUNT",   (LVM_FIRST+50));
define("LVIS_SELECTED",          2);
define("TCM_GETCURSEL",          4875);
define("CB_FINDSTRINGEXACT",     344);
define("CB_SETCURSEL",           334);
define("LB_FINDSTRINGEXACT",     418);
define("LB_SETCURSEL",           390);
define("TCM_SETCURSEL",          4876);
define("WM_SETTEXT",             12);

define('WBC_ALL',                (WBC_DBLCLICK | WBC_MOUSEMOVE | WBC_MOUSEDOWN
                                 | WBC_MOUSEUP | WBC_KEYDOWN | WBC_KEYUP
                                 | WBC_GETFOCUS | WBC_REDRAW | WBC_RESIZE
                                 | WBC_HEADERSEL));

define('Timer',                  'Timer');

include_once(exe_resource('./wbObjects/wbSystem.php'));
include_once(exe_resource('./wbObjects/wbPrimitive.php'));
include_once(exe_resource('./wbObjects/wbObject.php'));
include_once(exe_resource('./wbObjects/wbWindow.php'));
include_once(exe_resource('./wbObjects/wbControl.php'));

