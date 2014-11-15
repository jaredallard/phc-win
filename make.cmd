@echo off
SETLOCAL
prompt EXEC:
@echo on
call fetch_src.cmd || @exit /b
IF NOT EXIST "src\phc-win\bin" MD "src\phc-win\bin"
copy bin\* src\phc-win\bin
call make_zip.cmd || @exit /b
IF "%1" == "release" (
	@echo creating release
	copy /b nw.exe+main.zip "phc-win.exe"
	bin\upx.exe -9 phc-win.exe
	zip phc-win_release.zip nw.pak phc-win.exe icudtl.dat
	@echo created phc-win_release.zip
) ELSE (
	@echo.
	@echo Please run 'nw.exe main.zip' for debug. && @exit /b
)
exit /b
ENDLOCAL