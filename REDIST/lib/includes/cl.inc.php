<?php

	/**
		CLI Friendly library, rewrites most of the libs too be terminal friendly.
	**/
	
	// Grab stdin input without using php://stdin, (safer method).
	parse_str(implode('&', array_slice($argv, 1)), $_GET);
	
	echo "\n";
	echo 'PHC-WIN VERSION '.$version;
	echo "\n";
	if(!empty($_GET)) {
		echo "\n";
		print_r($_GET);
		echo "\n";
	
		if(isset($_GET["--help"])) {
			echo "\nPHC-WIN CLI\n(C) 2014 RDashINC, RainbowDashDC GNUGPLv3\n\nSYNOPSIS phc-win [OPTION]... [ETC]\n\nOPTIONS:\n   --help Show this help page.";
			exit(0);
		}
	}