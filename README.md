# PHC-WIN

### *This branch is for active development, see branch [v2.0.1](https://github.com/jaredallard/phc-win/tree/v2.0.1) for current release.*

<br />

Create single file executables for your PHP applications.

## Install

Checkout the [releases](https://github.com/jaredallard/phc-win/releases)
for the GUI.

**CONSOLE**

Via [npm](https://npmjs.org):

```bash
# Console Version only.
npm install -g phc-win
```

Via Git:

```bash
git clone https://github.com/jaredallard/phc-win

node ./bin/phc-win
```

## Building

### Requirements

* [Node.js](https://nodejs.org) 4+

**Windows**:

  * Visual Studio 2015 (PHP 7)
  * Visual Studio 2012 (PHP 5)

**Linux**:

  * Internet, when downloading pre-builts.
  * build-essentials (Debian) when building PHP from source.

First clone the repository:

```bash
git clone https://github.com/jaredallard/phc-win
```

If from source, make sure a compiler is installed: (Windows just needs VS)

```bash
  # Debian / Ubuntu
  sudo apt install build-essentials

  # Arch Linux
  sudo pacman -Syu
```

Modify the config: `config.js`

Start the build:

```bash
# GUI
gulp build-gui

# Console
gulp build-console

# GUI & Console
gulp build-all
```
