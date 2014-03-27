<?php
//========================================================================
//       Embeder - Make an executable Windows-binary file from a PHP script
//
//       License : PHP License (http://www.php.net/license/3_0.txt)
//       Author : Eric Colinet <e dot colinet at laposte dot net>
//       http://wildphp.free.fr/wiki/doku?id=win32std:embeder
//==========================================================


/**
 * embeder2 - add file function has third parameter defining resource path
 *
 * (c) 2006 by frantik
 * 
 * http://wiki.swiftlytilting.com/phc-win
 *
 */


/* Exit function */
function err($txt) {
	echo ucfirst(basename($_SERVER['argv'][0]))." ".(defined('EMBEDED')?'(embeded) ':'').'- Powered by PHP version '.phpversion()."\n";
	die($txt."\n");
}

/* File transformation function */
function _f($file, $force=false) { return $force||defined('EMBEDED')?'res:///PHP/'.md5($file):$file; }

if( !extension_loaded('win32std') ) err("I need win32std !");

/* Conf */
define('EMBEDER_BASE_EXE_PATH', 'out/');

/* Action list */
$actions= array(
	'new' => array( 'new_file', array('name'/*, 'type'*/) ),
	'main' => array( 'add_main', array('name', 'file') ),
	'add' => array( 'add_file', array('name', 'file','link') ),

	'list' => array( 'display_list', array('name') ),
	'view' => array( 'display_resource', array('name', 'section', 'value') ),
);

/* Action functions */
function new_file($name, $type= 'console') {
	$base_exe= EMBEDER_BASE_EXE_PATH.$type.'.exe';
	$exe= ".\\{$name}.exe";
	if( file_exists($exe) ) err("'$exe' already exists.");
	if( !copy(_f(EMBEDER_BASE_EXE_PATH.$type.'.exe'), $exe) ) err("Can't create '$exe'");
	echo "'$exe' created\n";
}

function add_main($name, $file) { 
	$exe= ".\\{$name}.exe";
	if( !file_exists($exe) ) err("'$exe' doesn't exists.");
	update_resource($exe, 'PHP', 'RUN', file_get_contents($file));
}

function add_file($name, $file, $link) {
	$exe= ".\\{$name}.exe";
	if( !file_exists($exe) ) err("'$exe' doesn't exists.");
	update_resource($exe, 'PHP', md5($link), file_get_contents($file));
}

function update_resource($file, $section, $name, $data) {
	$res= "res://$file/$section/$name";
	if( !res_set( $file, $section, $name, $data) ) err("Can't update '$res'\n");
	echo "Updated '$res' with ".strlen($data)." bytes\n";
}

function display_list($name) {
	$exe= ".\\{$name}.exe";

	$h= res_open( $exe );
	if( !$h ) err( "can't open '$exe'" );

	echo "Res list of '$exe': \n";
	$list= res_list_type($h, true);
	if( $list===FALSE ) err( "Can't list type" );
	
	for( $i= 0; $i<count($list); $i++ ) {
		echo $list[$i]."\n";
		$res= res_list($h, $list[$i]);
		for( $j= 0; $j<count($res); $j++ ) {
			echo "\t".$res[$j]."\n";
		}
	}
	res_close( $h );
}

function display_resource($name, $section, $value) {
	$exe= ".\\{$name}.exe";
	$res= "res://{$exe}/{$section}/{$value}";
	echo "-Displaying '$res'\n";
	echo file_get_contents("res://{$exe}/{$section}/{$value}");
	echo "\n-End\n";
}

/* Run specified action */
if( !isset($argv[1]) ) err( "Please specify something to do.\nUsage: {$argv[0]} action [params...]\nWhere action can be: ".implode(', ', array_keys($actions))."\n");
foreach( $actions as $k => $v ) {
	if( $k==$argv[1] ) {
		$params= $argv;
		array_shift($params);
		array_shift($params);
		if( count($params) != count($v[1]) ) err("Bad number of parameters, '$k' needs: ".implode(", ", $v[1]));
		call_user_func_array($v[0], $params);
		exit(0);
	}
}
err("Unknown action '{$argv[1]}'");
?>