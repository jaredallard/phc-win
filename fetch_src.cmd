@echo off
SETLOCAL ENABLEDELAYEDEXPANSION
echo Retreiving dependencies

PROMPT EXEC: 
:: Load configuration file
SET CONF_FILE=config.ini

IF NOT EXIST "%CONF_FILE%" @echo ERR: Configuration file %CONF_FILE% missing. & @exit /b
for /f "delims=" %%a in ('find "=" ^< "%CONF_FILE%"') do call set "%%a"



:: Continue
@echo on

:: Check minimal requirements are here.
@echo Checking for prerequisites.
@unzip --help >nul || @echo ERR: unzip not found. & @exit /b
@zip --help >nul   || @echo ERR: zip not found. & @exit /b
@tar --help >nul   || @echo ERR: tar not found & @exit /b
@wget --help >nul 2>nul  || @echo ERR: wget not found & @exit /b
@call "!VCPATH!\vcvarsall.bat" || @echo ERR: vcvarsall.bat not found. Check MS Visual Studio Setup. & exit /b

IF NOT EXIST "src\" MD "src"
IF NOT EXIST "src\depends" MD "src\depends"

:: Dependencies time.
pushd "src\depends"
IF NOT EXIST ".\php-sdk" (
	wget --no-check-certificate "!PHP_SDK!" -O "php-sdk-binary-tools-20110915.zip" || exit /b
	unzip "php-sdk-binary-tools-20110915.zip" -d "php-sdk/"
	del /q /f "php-sdk-binary-tools-20110915.zip"
) else (
	@echo PHP-sdk Found
)

IF NOT EXIST ".\upx" (
	wget "http://upx.sourceforge.net/download/upx391w.zip"
	unzip "upx391w.zip"
	move "upx391w" "upx"
	del /q /f "upx391w.zip"
) else (
	@echo UPX found
)

:: Embeder2
IF NOT EXIST ".\embeder2" (
	@echo Fetching embeder2
	git clone http://github.com/RDashINC/embeder2 embeder2
) ELSE ( 
	@echo embeder2 found.
	@echo Attempting git pull on embeder2 
	pushd "embeder2"
		git pull
	popd
)

IF NOT EXIST ".\php-src" (
	wget "http://php.net/distributions/php-!PHP_VER!.tar.xz" || @echo ERR: Failed to download PHP !PHP_VER!. & @exit /b
	tar xvf php-!PHP_VER!.tar.xz
	del /q /f "php-!PHP_VER!.tar.xz"
	move "php-!PHP_VER!" "php-src"

	:: PHP Windows Patch
	echo "" >> php-src\ext\standard\winver.h
) else (
	@echo PHP source found
)

IF NOT EXIST ".\nw" (
	wget "http://dl.nwjs.io/v!NW_VER!/!NW_NAME!-v!NW_VER!-win-!NW_ARCH!.zip"
	unzip "!NW_NAME!-v!NW_VER!-win-!NW_ARCH!.zip"
	move "!NW_NAME!-v!NW_VER!-win-!NW_ARCH!" "nw"
	del /q /f "!NW_NAME!-v!NW_VER!-win-!NW_ARCH!.zip"
) else (
	@echo NW.js found
)

pushd "php-src/ext"
@echo Fetching extensions

:: Download extensions, you could add you own here.
git clone http://github.com/RDashINC/win32std win32std 2>nul || @echo Attempting git pull on win32std & pushd "win32std" & git pull & popd
git clone https://github.com/stefan-loewe/WinBinder winbinder 2>nul || @echo Attempting git pull on winbinder & pushd "winbinder" & git pull & popd

popd

@echo Setting up compilier
call "%VCPATH%\vcvarsall.bat" || exit /b

@echo Setting up PHP depends
pushd "php-sdk"
call bin\phpsdk_setvars.bat || exit /b
popd

@echo Building targets (this will take awhile)
IF NOT EXIST "..\..\bin" MD "..\..\bin"
pushd "php-src"
call buildconf.bat
call configure.bat !PHP_ARGS! || exit /b
call nmake || exit /b
copy "Release_TS\php.exe" "..\embeder2\php.exe" || exit /b
copy "Release_TS\php5ts.dll" "..\embeder2\php5ts.dll" || exit /b
popd

pushd "upx"
copy "upx.exe" "..\..\..\bin\upx.exe" || exit /b
popd

pushd "embeder2/src"
	pushd "../"
		IF NOT EXIST "php.exe" @echo PHP exe not found. && exit /b
		@echo Compiling embeder2...
		call make_embeder.cmd  1>nul || exit /b
		@echo Done.
		copy "embeder2.exe" "..\..\..\bin\embeder2.exe"
		copy "php5ts.dll" "..\..\..\bin\php5ts.dll"
	popd
popd

pushd "nw"
	copy "nw.exe" "..\..\..\nw.exe"
	copy "nw.pak" "..\..\..\nw.pak"
	copy "icudtl.dat" "..\..\..\icudtl.dat"
popd
popd

pushd "src/phc-win"
@echo Running NPM
call npm install || exit /b
popd

echo Using PHP: !PHP_VER!
echo Using !NW_NAME!: !NW_VER! !NW_ARCH!

:: Gen build.csv
echo !PHP_VER!,!NW_VER!,!NW_ARCH! > build.csv

ENDLOCAL