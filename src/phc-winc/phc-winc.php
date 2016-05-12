<?php
	/** 
	 * phc-winc - Now in PHP!
	 *
	 * @author Jared Allard <jaredallard@outlook.com>
	 * @license MIT
	 * @link http://github.com/jaredallard/phc-win
	 *
	 **/

	// Havn't coded PHP in like 1.5 years, here we go.
	// using JSDoc in PHP, I guess that works?

	class phcwin {
		protected $binary;
		protected $version;
		private $queue;

		public function __construct() {
			/** Blank Construct **/
			$this->binary        = false;
			$this->version       = "1.0-release";
			$this->queue         = array();
			$this->queue["pre"]  = array();
			$this->queue["post"] = array();
			$this->pl            = new plugins(false);

			echo "PHC-WIN v".$this->version." by Jared Allard\n";
		}

		public function help() {
			global $pl;

			echo 'phc-winc.exe input output [--with]';
			echo "\n\n";
			echo "Options:\n";
			
			// get only plugins that support enable flags.
			foreach($this->pl->getAllWith() as $i => $v) {
				if($v["with"]==1) {
					echo '  --with-'.$v["name"].'=y/n'."\n";
				}
			}
			
			echo "\n";
			echo "post bug reports to http://github.com/jaredallard/phc-win\n";
			exit(0);
		}

		public function enable($plugin) {
			$tmp = new $plugin;
			if($tmp->event=='post') {
				array_push($this->queue["post"], $plugin);
			} elseif($tmp->event=='pre') {
				array_push($this->queue["pre"], $plugin);
			} 
		}

		public function disable($plugin) {

		}
	}

	/**
	 * @class plugins
	 * 
	 * Handles all plugin releated activites.
	 **/
	class plugins {
		public $version;
		private $ptable = array();

		/**
		 * Constructor, setups up the plugin tables.
		 *
		 * @constuctor
		 * @this plugins
		 * @return {null}
		 **/
		public function __construct($verbose = false) {
			if($verbose) {
				echo "Loading plugins...\n";
			}

			// Setup table
			$this->ptable["loaded"] = array();
			$this->ptable["info"] = array();

			$this->scan($verbose);
		}

		/**
		 * Scans for plugins
		 *
		 * @this plugins
		 * @return {null}
		 **/
		private function scan($verbose = false) {
			// Scan plugin directory
			if ($handle = opendir('./plugins')) {
			    /* This is the correct way to loop over the directory. */
			    while (false !== ($entry = readdir($handle))) {
			    	if($entry !== "."  AND ($entry !== "..")) {
			        	if($verbose) {
			        		echo "$entry\n";
			    		}

			    		$this->load($entry);
			    	}
			    }

			    if($verbose) {
			    	echo "\n";
			    }

			    // close handle
			    closedir($handle);
			}
		}

		/**
		 * Loads a plugin and adds it to the plugin list.
		 *
		 * @this plugins
		 * @return {bool} success
		 **/
		public function load($entry) {
			// Load plugin
			if(!file_exists('./plugins/'.$entry)) {
				echo "Failed to load plugin: ".$entry." ERNOTEXIST";
				return false;
			}

			require('./plugins/'.$entry);

			// Remove file ext for class name
			$name = preg_replace('/\\.[^.\\s]{3,4}$/', '', $entry);

			$tmp = new $name;

			// plugin info object
			$t["version"] = $tmp->version;
			$t["binary"]  = $tmp->binary;
			$t["with"]    = $tmp->with;
			$t["name"]    = $name;

			// add to ptable
			array_push($this->ptable["info"], $t);
			array_push($this->ptable["loaded"], $name);
		}

		/**
		 * Unloads a plugin and deregisters it from the plugin table.
		 *
		 * @this plugins
		 * @return {bool} succcess
		 **/
		public function unload($name) {
			// unload plugin
			foreach($this->ptable["info"] as $i => $v) {
				if($v["name"]==$name) {
					unset($pl->ptable["info"][$i]);
				}
			}

			foreach($this->ptable["loaded"] as $i => $v) {
				if($v==$name) {
					unset($pl->ptable["info"][$i]);
					return true;
				}
			}

			/** Soemthing went wrong, return false **/
			return false;
		}

		/**
		 * Check if a plugin is loaded.
		 *
		 * @this plugins
		 * @return {bool} isloaded
		 **/
		public function isLoaded() {
			// is this plugin loaded?
			foreach($this->ptable["loaded"] as $i => $v) {
				if($v==$name) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Gets plugin info for $name plugin.
		 *
		 * @this plugins
		 * @param {string} plugin
		 * @return {array} plugins info
		 **/
		public function getPluginInfo($name) {
			// get plugin info
			foreach($this->ptable["info"] as $i => $v) {
				if($v["name"]==$name) {
					return $v;
				}
			}

			return false;
		}

		/**
		 * Get's all plugins with option flags.
		 *
		 * @this plugins
		 * @return {array} plugins with with flags.
		 **/
		public function getAllWith() {
			$t = array();
			foreach($this->ptable["info"] as $i => $v) {
				if($v["with"]==1) {
					array_push($t, $v);
				}
			}

			return $t;
		}
	}

	/* Start phcwin class, and plugins */
	$pw = new phcwin();

	/* Check input */
	foreach($argv as $value) {
	  if($value=="--help") {
	  	$pw->help();
	  }

	  foreach($pw->pl->getAllWith() as $i => $v) {
	  	if($v["with"]==1) {
	  		if($value=='--with-'.$v["name"].'=Y' OR ($value=='--with-'.$v["name"].'=y')) {
	  			$pw->enable($v["name"]);
	  			$tmp = new $v["name"];
	  			$tmp->enabled();
	  		} else {
	  			$pw->disable($v["name"]);
	  		}
	  	}
	  }
	}

	if(!isset($argv[1])) {
		$pw->help();
	}
