@echo off
SETLOCAL
@echo on
@prompt EXEC: 
rm -rf src/depends
rm -rf bin
rm -rf src/phc-win/main.php src/phc-win/tmp.exe src/phc-win/tmp-c.exe src/phc-win/bin src/phc-win/app.evp 
rm -rf src/phc-win/node_modules
rm -rf nw.exe nw.pak icudtl.dat phc-win.exe
rm -rf locales
rm -rf main.zip
@echo off
ENDLOCAL