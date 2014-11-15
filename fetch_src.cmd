@echo off
SETLOCAL ENABLEDELAYEDEXPANSION
echo Retreiving dependencies

PROMPT EXEC: 
@echo on

@REM VC Config
@SET VCPATH=C:\Program Files (x86)\Microsoft Visual Studio 12.0\VC
@SET PHP_ARGS=--disable-all --enable-embed --enable-cli --with-winbinder --with-win32std
@SET NW_VER=0.11.0
@SET PHP_VER=5.6.3

IF NOT EXIST "src\" MD "src"
IF NOT EXIST "src\depends" MD "src\depends"

pushd "src\depends"
IF NOT EXIST ".\upx" (
	wget "http://upx.sourceforge.net/download/upx391w.zip"
	unzip "upx391w.zip"
	move "upx391w" "upx"
	del /q /f "upx391w.zip"
)
git clone http://github.com/RDashINC/embeder2 embeder2
IF NOT EXIST ".\php-src" (
	wget "http://us1.php.net/distributions/php-!PHP_VER!.tar.xz"
	tar xvf php-5.6.3.tar.xz
	del /q /f "php-5.6.3.tar.xz"
	move "php-5.6.3" "php-src"
	echo "" >> php-src\ext\standard\winver.h
)

IF NOT EXIST ".\nw" (
	wget "http://dl.node-webkit.org/v0.11.0/node-webkit-v0.11.0-win-ia32.zip"
	unzip "node-webkit-v0.11.0-win-ia32.zip"
	move "node-webkit-v0.11.0-win-ia32" "nw"
	del /q /f "node-webkit-v0.11.0-win-ia32.zip"
)

pushd "php-src/ext"
git clone http://github.com/RDashINC/win32std win32std
git clone https://github.com/stefan-loewe/WinBinder winbinder
popd

@echo Setting up compilier
call "!VCPATH!\vcvarsall.bat" || exit /b

@echo Building targets
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

pushd "embeder2/embed"
	msbuild
	call post.cmd
	pushd "../"
		IF NOT EXIST "php.exe" @echo PHP exe not found. && exit /b
		call make_embeder.cmd || exit /b
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

ENDLOCAL