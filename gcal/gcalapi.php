<?php
$CLASS = 'gcalapi'; class gcalapi { // USER code 
	public $silent = false;
	public function __construct( $silent = false) { $this->silent = $silent; }
	public function add( $config = 'file=200704.tale@高松.deadlines.txt,calendar=deadlines,when=2020-07-04,duration=allday', $noapicalls = false) { 
		$config = hm( tth( 'file=200704.tale@高松.deadlines.txt,calendar=deadlines,when=2020-07-04,duration=allday'), tth( $config)); 
		extract( $config); // file, calendar, when, duration
		extract( fpathparse( $file)); $map = hvak( ttl( $fileroot, '.')); unset( $map[ "$calendar"]); 
		if ( count( ttl( $when, ' ')) == 1) $when .= ' 00:00:00'; extract( tsburst( tsste( $when))); 
		$when2 = round( $mm) . '/' . round( $dd) . "/$yyyy"; if ( $duration != 'allday') $when2 .= ' ' . round( $hh) . ":$mm2"; 
		$name = ltt( hk( $map), '.'); $H = is_file( "$calendar.json") ? jsonload( "$calendar.json") : array();
		$url = "http://maratishe.github.io/gcal/$calendar.md#$name"; $title = $name; $description = $url;
		$H[ "$name"] = compact( ttl( 'calendar,title,when,when2,duration,description'));
		$c = "php /code/gcal/gcal.php delete $calendar " . strdblquote( $name); 
		echo "DELETE  $c\n"; if ( $noapicalls) echo "no ap calls, skip\n"; else system( $c);  
		$c = "php /code/gcal/gcal.php add " . strdblquote( htt( $H[ "$name"]));
		echo "ADD  $c\n"; if ( $noapicalls) echo "no ap calls, skip\n"; else system( $c);  
		jsondump( $H, "$calendar.json"); echo "DONE > $calendar.json\n";
	}
	public function makemd( $calendar = 'deadlines') { 
		$out = fopen( "$calendar.md", 'w'); 
		fwrite( $out, "# $calendar\n\n"); 
		fwrite( $out, "this file is generated automatically, do not make manual changes to it!\n\n"); $bywhen = array(); $H = jsonload( "$calendar.json"); 
		foreach ( $H as $k => $h) { extract( $h); $bywhen[ "$k"] = $when2; }
		asort( $bywhen); foreach ( $bywhen as $k => $when) { extract( $h); fwrite( $out, "$when [$title](#$k)  \n"); }
		fwrite( $out, "\n\n"); foreach ( $H as $k => $h) { 
			fwrite( $out, "## $title  ($when) <span id=" . strdblquote( $k) . "></span>\n\n");
			$files = flget( '.', $title, '', 'txt'); if ( ! $files) continue; 
			foreach ( file( lshift( $files)) as $v) { $v = trim( $v); if ( ! $v) fwrite( $out, "\n\n"); else fwrite( $out, $v . '  ' . "\n\n"); }
			fwrite( $out, "\n\n\n");
		}
		fclose( $out); `chmod -R 777 *`; 
	}
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
	clparse(); $JSONENCODER = 'jsonencode'; // jsonraw | jsonencode    -- jump to lib dir
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
	htg( hm( $_GET, $_POST)); $JSONENCODER = 'jsonencode';
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