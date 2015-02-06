# PHC-WIN

A Windows PHP EXE embeder

* Supports UPX compression.
* Supports Enigma Virtual Box EXE wrapping.
* WINBINDER and WIN32STD statically compilied into PHP dll.

## Download
Check out Github release for prebuilt binaries.

## From Source

### Needed to Compile

 * Microsoft Visual Studio 2013/2010 (Express should be fine).
 * Mysysgit/Cygwin tar (xvf flags), wget, unzip, zip, and rm in path.
 * Git in path
 * Node.js

**Notice: Using anything other than Visual Studio 2013 will require a `fetch_src.cmd` modification.**

### Editable Options in `fetch_src.cmd`:

 * `PHP_VER`    = Version to download of PHP
 * `PHP_ARGS`   = PHP Compiliation Arguments (notice adding extensions will likely require headers)
 * `NW_VER`     = NW.js version
 * `NW_VERSION` = NW.js name (used too be called node-webkit)
 * `NW_ARCH`    = ARCH for NW.js
 * `VCPATH`     = Path to VC/ (that contains vcvarsall.bat)

### Compiliation:
    Run `make.cmd`

Manual Compiliation:
    Reverse engineer `make.cmd/fetch_src.cmd`