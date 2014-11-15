@echo off
SETLOCAL
pushd "src\phc-win"
zip -r main.zip *
move "main.zip" "..\..\main.zip"
popd
ENDLOCAL