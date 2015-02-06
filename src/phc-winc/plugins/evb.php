<?php 

class evb {
	public $binary;
	public $version;

	public function __construct() {
		$this->binary  = "bin\\evb.exe";
		$this->version = "1.0.1";
		$this->with    = true;
		$this->event   = "post";
	}

	public function enabled() {
		/* enabled event */
		echo 'EVB plugin version '.$this->version." (C) Vladimir Sukhov\n";
	}

	public function action() {
		/* Public class for all actions */
	}
}