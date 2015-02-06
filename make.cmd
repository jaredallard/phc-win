@echo off
SETLOCAL
prompt EXEC:
@echo on

:: Preliminary check for clean
@IF "%1" == "clean" ( 
	@echo Cleaning repo... ^(this may take awhile^)
	rm -rf src/depends
	rm -rf bin
	rm -rf src/phc-win/main.php src/phc-win/tmp.exe src/phc-win/tmp-c.exe src/phc-win/bin src/phc-win/app.evp 
	rm -rf src/phc-win/node_modules
	rm -rf nw.exe nw.pak icudtl.dat phc-win.exe
	rm -rf php5ts.dll
	rm -rf locales
	rm -rf main.zip
	rm -rf build.csv
	pushd "src\phc-winc"
	call clean.cmd
	popd
	@echo OK
	@exit /b
) ELSE (
	@IF "%1" == "help" (
		@echo make.cmd ^[action^]
		@echo.
		@echo clean - for commits
		@echo release - github release
	) ELSE (
		@IF "%1" == "source" (
			call fetch_src.cmd
			@exit /b
		)
	)
)

:: check source tree
@echo EXEC: call fetch_src.cmd
@call fetch_src.cmd || @exit /b

:: Create binaries location.
IF NOT EXIST "src\phc-win\bin" MD "src\phc-win\bin"
copy bin\* src\phc-win\bin

:: Make nw zip file.
pushd "src\phc-win"
echo Compressing files... ^(this may take awhile^)
zip.exe -r -9 -q main.zip *
echo main.zip =^> ..\..\main.zip
move "main.zip" "..\..\main.zip"
popd

:: Final options check.
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

:: This is a clean exit.
ENDLOCAL