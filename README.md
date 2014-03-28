# PHC-WIN #

## What is it? ##

PHC-WIN is a php complier/embeder. It takes [multiple] files and combines them into a exe, and embeds php into the exe allowing resdistributable PHP applications.

## Supported OS's ##
  - Windows XP
  - Windows Server 2003
  - Windows Vista
  - Windows 7
  - Windows 8

## Authors ##
  - [frantik] (http://swiftlytilting.com) Original Maintainer
  - [RainbowDashDC] (http://rainbowdashdc.com) Current Maintainer.
  
## License ##
GNUGPLv3

## Compiling from source ##
Download the a release of the branch dependencies, or get the current files there.

Compile master with phc-win from (http://wiki.swiftlytilting.com/phc-win).

Place executable into top level of the dependencies folder. 

Soon dependencies will be merged into master.

## Notice ##
All source builds will have required .inc.php files in ./lib/includes/ for easy coding, and not having too recompile most of the time.
Main builds will integrate them.

## Decompilation ##
As this is turned into byte-code, decompiling is not possible, except ASM.