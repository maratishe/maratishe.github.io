<?php
$CLASS = 'gcalapi'; class gcalapi { // USER code 
	public $silent = false;
	public function __construct( $silent = false) { $this->silent = $silent; }
	// SECTION: overall functionality
	public function maketodos( $A, $files = 'todo.current.md=0,todo.midterm.md=10,todo.longterm.md=30') { foreach ( tth( $files) as $f => $r) { 
		extract( tsburst( tsystem()));  $now = "$yyyy-$mm-$dd"; extract( fpathparse( $f)); 
		extract( tsburst( tsystem() + $r * 24 * 60 * 60)); $then = "$yyyy-$mm-$dd";
		foreach ( file( $f) as $v) { if ( trim( $v)) $A[ "due:$then $now " . trim( $v) . " #$fileroot"] = true; }
	}; ksort( $A); $out = fopen( 'todo.txt', 'w'); foreach ( $A as $v => $t) { $L = ttl( $v, ' '); $due = lshift( $L); lpush( $L, $due); fwrite( $out, ltt( $L, ' ') . "\n"); }; fclose( $out); }
	public function make( $calendars = 'deadlines=ishort.ink/c1vK,jobhunt=ishort.ink/kSNB') { $A = array(); foreach ( tth( $calendars) as $calendar => $shorturl) { // makes  .md, .html
		// .md part
		echo "making $calendar.[md,md.txt,html]..."; $out = fopen( "$calendar.md", 'w'); $keymap = array();
		fwrite( $out, "# $calendar <span id=" . strdblquote( 'top') . "></span>\n\n"); 
		fwrite( $out, '<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF8">' . "\n\n");
		fwrite( $out, "this file is generated automatically, do not make manual changes to it!\n\n"); 
		$bywhen = array(); $H = jsonload( "$calendar.json"); 
		foreach ( $H as $k => $h) { extract( $h); $bywhen[ "$k"] = $when2; }
		asort( $bywhen); foreach ( $bywhen as $k => $when) { extract( $H[ "$k"]); $keymap[ "$k"] = substr( md5( $k), 0, 10); fwrite( $out, "[$title](#" . $keymap[ "$k"] . ") $when  \n"); }
		fwrite( $out, "\n\n"); foreach ( $H as $k => $h) { 
			extract( $h); // when, when2, url1, url2, title, duration, description
			fwrite( $out, "## $title  ($when) <span id=" . strdblquote( $keymap[ "$k"]) . "></span> <span style=" . strdblquote( 'color:#666;') . ">[→top](#top)</span>\n\n");
			$files = flget( '.', $calendar, $title, 'txt'); if ( ! $files) continue; 
			foreach ( file( lshift( $files)) as $v) { $v = trim( $v); if ( ! $v) fwrite( $out, "\n\n"); else fwrite( $out, $v . '  ' . "\n"); }
			fwrite( $out, "<span style=" . strdblquote( 'color:#666;') . ">[→top](#top)</span>");
			fwrite( $out, "\n\n\n"); extract( tsburst( tsystem())); 
			$A[ "due:" . lshift( ttl( $when, ' ')) . " $yyyy-$mm-$dd $title #$calendar $shorturl" . '#' . $keymap[ "$k"]] = true; 
		}
		fclose( $out); `cat $calendar.md > $calendar.md.txt`; echo " OK\n"; 
		$c = "pandoc -f markdown -t html $calendar.md > $calendar.html"; echo "$c ... "; procpipe( $c); echo " OK\n";
	}; $this->maketodos( $A); }
	// SECTION: gcalcli (python) api, also uses own  /code/gcal interface
	public function list2emptyfiles( $calendar = 'deadlines', $forceupdate = false) { // will not touch existing files
		if ( $forceupdate) `rm -Rf list.$calendar.json`; 
		if ( ! is_file( "list.$calendar.json")) { $c = "php /code/gcal/gcal.php list $calendar auto auto list.$calendar.json";  echo "$c .."; echopipee( $c); echo " OK\n"; }
		if ( ! is_file( "list.$calendar.json")) die( " ERROR!, no list.$calendar.json\n"); 
		foreach ( jsonload( "list.$calendar.json") as $v) {
			$v = trim( $v); if ( ! $v) continue; 
			$L = ttl( $v, ' '); $date = lshift( $L); extract( tsburst( tsste( "$date 00:00:00"))); 
			$name = ltt( $L, '.'); $file= $calendar . '.' . substr( '' . $yyyy . $mm . $dd, 2) . '.' . "$name" . ".txt"; 
			if ( is_file( "$file")) { echo "$file   (already file)\n"; continue; }  // do not overwrite an already existing file
			$out = fopen( "$file", 'w'); fclose( $out); echo "$file  (NEW!)\n";
		}
		`chmod -R 777 *`; 
	}
	public function add( $config = 'file=deadlines.200704.tale-takamatsu.txt,calendar=deadlines,when=2020-07-04,duration=allday', $noapicalls = false) { 
		$config = hm( tth( 'file=200704.tale-takamatsu.deadlines.txt,calendar=deadlines,when=2020-07-04,duration=allday'), tth( $config)); 
		extract( $config); // file, calendar, when, duration
		extract( fpathparse( $file)); $L = ttl( $file, '.'); lpop( $L); lshift( $L); lshift( $L); 
		if ( count( ttl( $when, ' ')) == 1) $when .= ' 00:00:00'; extract( tsburst( tsste( $when))); 
		$when2 = round( $mm) . '/' . round( $dd) . "/$yyyy"; if ( $duration != 'allday') $when2 .= ' ' . round( $hh) . ":$mm2"; 
		$name = ltt( $L, '.'); $H = is_file( "$calendar.json") ? jsonload( "$calendar.json") : array();
		$url1 = "http://maratishe.github.io/gcal/$calendar.md"; 
		$url2 = "http://maratishe.github.io/gcal/$calendar.md.txt"; 
		$title = $name; $description = $url1 . ' ' . $url2;
		$H[ "$name"] = compact( ttl( 'calendar,title,when,url1,url2,when2,duration,description'));
		$c = "php /code/gcal/gcal.php delete $calendar " . strdblquote( $name); 
		echo "DELETE  $c\n"; if ( $noapicalls) echo "no ap calls, skip\n"; else system( $c);  
		$c = "php /code/gcal/gcal.php add " . strdblquote( htt( $H[ "$name"]));
		echo "ADD  $c\n"; if ( $noapicalls) echo "no ap calls, skip\n"; else system( $c);  
		jsondump( $H, "$calendar.json"); echo "DONE > $calendar.json\n";
	}
	public function addall( $calendar = 'deadlines', $noapicalls = false) { `rm -Rf $calendar.json`; foreach ( flget( '.', $calendar, '', 'txt') as $f) {  // filenames should start from  yymmdd.  or  yymmddhhmm
		echo "\n\n"; $L = ttl( $f, '.'); lshift( $L); $time = lshift( $L); if ( ! is_numeric( $time)) continue; 
		$yyyy = '20' . substr( $time, 0, 2); $mm = substr( $time, 2, 2); $dd = substr( $time, 4); $when = "$yyyy-$mm-$dd";
		if ( strlen( $time) > 6) { $mm2 = substr( $time, 6, 2); $dd = substr( $time, 8, 2); $when .= " $mm2:$dd"; }
		echo "$f   $time   > $when\n";
		$this->add( "calendar=$calendar,file=$f,when=$when,duration=allday", $noapicalls);
	}}
	// web API -- if [webkeys.php] is found in the same folder, 'webkey' parameter is expected in all requests -- just put keys in comments in webkeys.php
}
if ( isset( $argv) && count( $argv) && strpos( $argv[ 0], "$CLASS.php") !== false) { // direct CLI execution, redirect to one of the functions 
	// this is a standalone script, put the header
	set_time_limit( 0);
	ob_implicit_flush( 1);
	//ini_set( 'memory_limit', '4000M');
	for ( $prefix = is_dir( 'ajaxkit') ? 'ajaxkit/' : ''; ! is_dir( $prefix) && count( explode( '/', $prefix)) < 4; $prefix .= '../'); if ( ! is_file( $prefix . "env.php")) $prefix = '/web/ajaxkit/'; 
	if ( ! is_file( $prefix . "env.php") && ! is_file( 'requireme.php')) die( "\nERROR! Cannot find env.php in [$prefix] or requireme.php in [.], check your environment! (maybe you need to go to ajaxkit first?)\n\n");
	if ( is_file( 'requireme.php')) require_once( 'requireme.php'); else foreach ( explode( ',', ".,$prefix") as $p) foreach ( array( 'functions', 'env') as $k) if ( is_dir( $p) && is_file( "$p/$k.php")) require_once( "$p/$k.php");
	$CLDIR = clgetdir(); //chdir( clgetdir());
	clparse(); $JSONENCODER = 'jsonraw'; // jsonraw | jsonencode    -- jump to lib dir
	// help
	clhelp( "FORMAT: php$CLASS WDIR COMMAND param1 param2 param3...     ($CLNAME)");
	foreach ( file( "$CLDIR/$CLNAME") as $line) if ( ( strpos( trim( $line), '// SECTION:') === 0 || strpos( trim( $line), 'public function') === 0) && strpos( $line, '__construct') === false) clhelp( lshift( ttl( trim( str_replace( 'public function', '', $line)), '{'))); // }
	// parse command line
	lshift( $argv); if ( ! count( $argv)) die( clshowhelp()); 
	//$wdir = lshift( $argv); if ( ! is_dir( $wdir)) { echo "ERROR! wdir#$wdir is not a directory\n\n"; clshowhelp(); die( ''); }
	//echo "wdir#$wdir\n"; if ( ! count( $argv)) { echo "ERROR! no action after wdir!\n\n"; clshowhelp(); die( ''); }
	$f = lshift( $argv); $C = new $CLASS(); chdir( $CWD); 
	switch ( count( $argv)) { case 0: $C->$f(); break; case 1: $C->$f( $argv[ 0]); break; case 2: $C->$f( $argv[ 0], $argv[ 1]); break; case 3: $C->$f( $argv[ 0], $argv[ 1], $argv[ 2]); break; case 4: $C->$f( $argv[ 0], $argv[ 1], $argv[ 2], $argv[ 3]); break; case 5: $C->$f( $argv[ 0], $argv[ 1], $argv[ 2], $argv[ 3], $argv[ 4]); break; case 6: $C->$f( $argv[ 0], $argv[ 1], $argv[ 2], $argv[ 3], $argv[ 4], $argv[ 5]); break; }
 	//switch ( count( $argv)) { case 0: $C->$f( $wdir); break; case 1: $C->$f( $wdir, $argv[ 0]); break; case 2: $C->$f( $wdir, $argv[ 0], $argv[ 1]); break; case 3: $C->$f( $wdir, $argv[ 0], $argv[ 1], $argv[ 2]); break; case 4: $C->$f( $wdir, $argv[ 0], $argv[ 1], $argv[ 2], $argv[ 3]); break; case 5: $C->$f( $wdir, $argv[ 0], $argv[ 1], $argv[ 2], $argv[ 3], $argv[ 4]); break; case 6: $C->$f( $wdir, $argv[ 0], $argv[ 1], $argv[ 2], $argv[ 3], $argv[ 4], $argv[ 5]); break; }
 	die();
}
if ( ! isset( $argv) && ( isset( $_GET) || isset( $_POST)) && ( $_GET || $_POST)) { // web API 
	set_time_limit( 0);
	ob_implicit_flush( 1);
	for ( $prefix = is_dir( 'ajaxkit') ? 'ajaxkit/' : ''; ! is_dir( $prefix) && count( explode( '/', $prefix)) < 4; $prefix .= '../'); if ( ! is_file( $prefix . "env.php")) $prefix = '/web/ajaxkit/'; 
	if ( ! is_file( $prefix . "env.php") && ! is_file( 'requireme.php')) die( "\nERROR! Cannot find env.php in [$prefix] or requireme.php in [.], check your environment! (maybe you need to go to ajaxkit first?)\n\n");
	if ( is_file( 'requireme.php')) require_once( 'requireme.php'); else foreach ( explode( ',', ".,$prefix") as $p) foreach ( array( 'functions', 'env') as $k) if ( is_dir( $p) && is_file( "$p/$k.php")) require_once( "$p/$k.php");
	htg( hm( $_GET, $_POST)); $JSONENCODER = 'jsonraw';
	// check for webkey.json and webkey parameter in request
	if ( is_file( 'webkeys.php') && ! isset( $webkey)) die( jsonsend( jsonerr( 'webkey env not set, run [phpwebkey make] first'))); 
	$good = true; if ( is_file( 'webkeys.php')) $good = false; 
	if ( is_file( 'webkeys.php')) foreach ( file( 'webkeys.php') as $v) if ( strpos( $v, $webkey) !== false) $good = true; 
	if ( ! $good) die( jsonsend( jsonerr( 'did not pass the authenticated form of this web API'))); 
	// actions: [wdir] is fixed/predefined  [action] is function name   others are [one,two,three,...]
	$O = new $CLASS( true); // does not pass [types], expects the user to run init() once locally before using it remotely 
	$p = array(); foreach ( ttl( 'one,two,three,four,five') as $k) if ( isset( $$k)) lpush( $p, $$k); $R = array();
	if ( count( $p) == 0) $R = $O->$action();
	if ( count( $p) == 1) $R = $O->$action( $one);
	if ( count( $p) == 2) $R = $O->$action( $one, $two);
	if ( count( $p) == 3) $R = $O->$action( $one, $two, $three);
	if ( count( $p) == 4) $R = $O->$action( $one, $two, $three, $four);
	if ( count( $p) == 5) $R = $O->$action( $one, $two, $three, $four, $five);
	die( jsonsend( $R));
}
if ( isset( $argv) && count( $argv)) { $L = explode( '/', $argv[ 0]); array_pop( $L); if ( count( $L)) chdir( implode( '/', $L)); } // WARNING! Some external callers may not like you jumping to current directory
// for raw input like JSON POST requests
//if ( ( ! isset( $_GET) && ! isset( $_POST)) || ( ! $_GET && ! $_POST)) { $h = @json_decode( @file_get_contents( 'php://input'), true); if ( $h) $_POST = $h; $out = fopen( 'input', 'w'); fwrite( $out, json_encode( $h)); fclose( $out); } 
?>