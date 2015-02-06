<?php

class upx {
	public $binary;
	public $version;

	public function __construct() {
		$this->binary  = "bin\\upx.exe";
		$this->version = "1.0.0";
		$this->with    = true;
		$this->event   = "pre";
		$this->after   = "embeder";
	}

	public function enabled() {
		echo 'UPX plugin version '.$this->version." (C) UPX Team\n";
	}

	public function action($exe) {

	}
}
