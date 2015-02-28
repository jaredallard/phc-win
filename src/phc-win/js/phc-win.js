/**
 * phc-win.js
 *
 * Author:   RainbowDashDC <rainbowdashdc at mezgrman dot de>
 * License:  GNUGPLv3
 * Homepage: http://rdashinc.github.io/phc-win
 **/

function upd(prog) {
	$("#cmdprog").width(prog+"%");

	if(prog==100) {
		$("#cmdprog").removeClass("active");
	}
}

function startCompilier(output) {
	/* Progress Bar */
	var fs = require('fs');
	upd(0);

	/* Remove files, fixes #1
	   Todo: Make array */
	if(fs.existsSync("app.evb")) {
		out("Cleaning app.evb")
		fs.unlinkSync("app.evb");
	}
	if(fs.existsSync("tmp.exe")) {
		out("Cleaning tmp.exe")
		fs.unlinkSync("tmp.exe");
	}
	if(fs.existsSync("tmp-c.exe")) {
		out("Cleaning tmp-c.exe")
		fs.unlinkSync("tmp-c.exe");
	}
	if(fs.existsSync("main.php")) {
		out("Cleaning main.php")
		fs.unlinkSync("main.php");
	}

	/* Get file & set fs object */
	window.output=$("#fileInput").val().replace(".phpw", ".exe"); // patch for .phpw
	if(window.output === $("#fileInput").val()) {
		window.output=$("#fileInput").val().replace(".php", ".exe");
	}

	/* Output newline */
	$("#outputConsole").append("<span class='cmd'></span>");

	if($("#fileInput").val()==="") {
		return false;
	}

	out("Starting compile process.");
	upd(10);

	/* Copy to our dir */
	out("Copying file(s)");
	fs.writeFileSync("main.php", fs.readFileSync($("#fileInput").val()));
	upd(20);

	/* Create an Embeded EXE */
	embedExe("main.php", 'console');
}

function embedExe(main, type) {
	out("Embeding '"+main+"' to PHP/RUN");
	if(type!=="console") {
		if(type!=="window") {
			throw "not c/w err: 1"
		}
	}

	var exec = require('child_process').exec;

	/** Create EXE **/
	exec("bin\\embeder2.exe new tmp", function(error, stdout, stderr) {
		if(error) {
			throw error;
		}
		exec("bin\\embeder2.exe main tmp ./"+main, function(error) {
			if(error) {
				throw error;
			}
			exec("bin\\embeder2.exe type tmp "+type, function(error) {
				if(error) {
					throw error;
				}

				/* UPX compress if told too */
				if(window.doUpx===true) {
					upxExe("9");
				} else {
					if(window.doEvb===true) {
						upd(45);
						evbExe();
					} else {
						out("Reached Final Stage");
						upd(70);
						finishCompile();
					}
				}
			});
		});
	});
}

function upxExe(level) {
	out("UPX compressing EXE. (May Take Awhile)");
	upd(40);

	var exec = require('child_process').exec;

	exec("bin\\upx -"+level+" tmp.exe", function(error) {
		/* UPX compress if told too */
		if(window.doEvb===true) {
			evbExe();
		} else {
			out("Reached Final Stage");
			upd(60);
			finishCompile();
		}
	});
}

function finishCompile(exe) {
	upd(80);
	var fs = require('fs');

	if(exe===undefined) {
		var exe="tmp.exe";
	}

	/* Move file to given output location */
	out("Copying too '"+window.output+"'");
	fs.writeFileSync(window.output, fs.readFileSync(exe));

	/* Copy php5ts.dll if not using evb */
	if(window.doEvb===false) {
		var output_dir = window.output;
		var output_dir = output_dir.substring(0, output_dir.lastIndexOf("/"));

		out("Copying php5ts.dll to '"+output_dir+"/php5ts.dll'");
		fs.writeFileSync(output_dir+"\\php5ts.dll", fs.readFileSync("bin\\php5ts.dll"));
	}
	out("Done!");
	upd(100);
	return true;
}

function evbExe() {
	out("Generating evp");
	var evc = require('enigmavirtualbox');
	upd(50);
	evc.gen("app.evp", "tmp-c.exe", "tmp.exe", "bin\\php5ts.dll").then(function() {
		out("Combining exe+phpdll");
		upd(60);
		evc.cli("app.evp").then(function() {
			out("Reached Final Stage.");
			upd(70);
			finishCompile("tmp-c.exe");
		});
	});
}
