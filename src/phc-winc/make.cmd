@echo off

echo Compiling PHC-WIN cmd-line...
echo.

echo [NOTICE]: Copying dependencies
copy "..\..\bin\upx.exe" "bin\upx.exe"
copy "..\..\bin\embeder2.exe" "bin\embeder2.exe"
copy "..\..\bin\php5ts.dll" "bin\php5ts.dll"
copy "bin\php5ts.dll" "php5ts.dll"
echo [NOTICE]: Done.

echo [NOTICE]: Building phc-win.php as an exe manually.
bin\embeder2.exe new final
bin\embeder2.exe main final phc-winc.php
echo [NOTICE]: Done.

echo.
echo Finished!
