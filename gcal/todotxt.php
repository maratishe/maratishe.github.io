<?php
ini_set( 'memory_limit', '4000M');
$CLASS = 'todotxt'; class todotxt { // USER code 
	public $silent = false;
	public function __construct( $silent = false) { $this->silent = $silent; }
	// SECTION: gcalcli (python) api, also uses own  /code/gcal interface
	public function maketodos( $A, $files = 'todo.current.md=0,todo.regulars.md=7,todo.midterm.md=10,todo.longterm.md=30') { foreach ( tth( $files) as $f => $r) { 
		extract( tsburst( tsystem()));  $now = "$yyyy-$mm-$dd"; extract( fpathparse( $f)); 
		extract( tsburst( tsystem() + $r * 24 * 60 * 60)); $then = "$yyyy-$mm-$dd";
		foreach ( file( $f) as $v) { if ( trim( $v)) $A[ "due:$then $now " . trim( $v) . " #" . lpop( ttl( $fileroot, '.'))] = true; }
	}; ksort( $A); $out = fopen( 'todo.txt', 'w'); foreach ( $A as $v => $t) { $L = ttl( $v, ' '); $due = lshift( $L); lpush( $L, $due); fwrite( $out, ltt( $L, ' ') . "\n"); }; fclose( $out); }
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
	public function add( $config = 'file=deadlines.200704.tale-takamatsu.txt,calendar=deadlines,when=2020-07-04,duration=allday', $noapicalls = true) { 
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
		echo "DELETE  $c\n"; if ( $noapicalls && $noapicalls == 'yes') echo "no ap calls, skip\n"; else system( $c);  
		$c = "php /code/gcal/gcal.php add " . strdblquote( htt( $H[ "$name"]));
		echo "ADD  $c\n"; if ( $noapicalls && $noapicalls == 'yes') echo "no ap calls, skip\n"; else system( $c);  
		jsondump( $H, "$calendar.json"); echo "DONE > $calendar.json\n";
	}
	// SECTION: manual labor automation
	public function jobhunt( $in = 'jobhunt.txt', $reject= 'jobhunt.reject.txt') { // jobhunt.reject.txt should be in multi-part key per line format
		$blocks = array(); $block = array(); extract( fpathparse( $in)); $L = file( $in); 
		// map: no, id, update, univ, title, field, post, tenure, deadline, url
		$map = tth( 'No.=no,データ番号=id,更新日=update,機関名=univ,タイトル=title,研究分野=field,職種=post,勤務形態=tenure,募集終了日=deadline,ＵＲＬ=url');
		while ( count( $L)) { 
			$v = trim( lshift( $L)); if ( ! $v) continue; 
			if ( strpos( $v, 'No.') !== 0) continue; $block = array( $v); //echo "RAW   $v   "; 
			while ( count( $L)) { 
				$v = trim( lshift( $L));       
				while( count( $L)) { // collect all further lines within the block
					$iskey = false; foreach ( $map as $k2 => $v2) if ( count( ttl( lfirst( $L), '：')) > 1 && lshift( ttl( lfirst( $L), '：')) == $k2) $iskey = true; 
					if ( $iskey) break; // next key
					$v .= ' ' . trim( lshift( $L));
				}
				lpush( $block, ltt( ttl( $v, ' '), ' '));  if ( strpos( $v, 'ＵＲＬ') !== 0) continue; //echo substr( $v, 0, 40) . "...\n"; 
				break; 
			}
			if ( $block) lpush( $blocks, $block);
		}
		$H = array(); $stats = array(); $A = array(); // { tag: { key: count, ...}, ...}
		if ( is_file( $reject)) $reject = file( $reject); else $reject = null; if ( $reject) foreach ( $reject as $i => $v) $reject[ $i] = trim( $v); if ( $reject) $reject = hvak( $reject); 
		foreach ( $blocks as $block) { $h = array(); foreach ( $block as $v) { 
			//die( " v[$v] in block  " . implode( "\n", $block)); 
			$v = trim( $v); if ( ! $v) continue; $L = ttl( $v, '：'); $k = lshift( $L); $v = $L; 
			$K = null; if ( isset( $map[ "$k"])) $K = $map[ "$k"]; if ( ! $K) die( " ERROR! no map for key[$k] in block " . implode( "\n", $block) . "\n"); 
			if ( $K == 'id') $v = array( lshift( $L)); $V = ltt( $v, ' '); 
			$h[ "$K"] = $V; htouch( $stats, "$K"); htouch( $stats[ "$K"], "$V", 0, false, false); $stats[ "$K"][ "$V"]++; 
		}; extract( $h); $k = "$id $univ $title $field $tenure $deadline"; if ( $reject && isset( $reject[ "$k"])) { echo "REJECT  $k \n"; continue; }; $H[ "$k"] = $h; }
		// there is a reject list, output only the non-rejected data
		if ( $reject) echo "\n\n\n"; 
		if ( $reject) foreach ( $H as $k => $h) { echo "\n"; foreach ( $h as $k2 => $v2) echo "$k2 : $v2\n"; }
		if ( $reject) die( '');
		// no reject list yet, output data for manual edit (select-out)
		echo "\n\n\n"; // order by deadline 
		$bytenure = array(); foreach ( $H as $k => $h) { extract( $h); htouch( $bytenure, $tenure); $bytenure[ "$tenure"][ "$k"] = $h; }
		$stats2 = array(); foreach ( $bytenure as $k => $hs) $stats2[ "$k"] = count( $hs); echo "tenure stats : " . htt( $stats2) . "\n";
		$order = '常勤 (任期なし),Full-time (Tenured),常勤 (テニュアトラック),常勤 (任期あり),非常勤 (任期あり),Full-time (Nontenured),Part-time (Nontenured)';
		foreach ( ttl( $order) as $k1) { echo "\n\n\n\n" . "==== TENURE  $k1  ===== \n"; if ( ! isset( $bytenure[ "$k1"])) continue; foreach ( $bytenure[ "$k1"] as $k2 => $v2) {  echo "$k2\n"; }; unset( $bytenure[ "$k1"]); }
		echo "\n\n\n"; foreach ( $bytenure as $k1 => $h1) foreach ( $h1 as $k2 => $h2) echo "$k2\n";  // leftovers
	}
	public function deadlines( $in = 'deadlines.txt', $reject= 'deadlines.reject.txt', $dryrun = true) { // deadlines.reject.txt  automatically means that it is in reject mode
		if ( $dryrun === 'yes') $dryrun = true; if ( $dryrun === 'no') $dryrun = false; 
		// map: no, id, update, univ, title, field, post, tenure, deadline, url
		echo "new info"; 	$blocks = array(); $block = array(); extract( fpathparse( $in)); 
		foreach ( file( $in) as $v) { 
			if ( strpos( $v, '---deadline---') === 0) { if ( $block) lpush( $blocks, $block); echo '.'; $L = ttl( trim( $v), ' '); lshift( $L); $when = lshift( $L); $what = ltt( $L, ' '); if ( ! $when || ! $what) die( " ERROR ! Wrong format for top line\n"); $block = array( "$when $what"); continue; }
			lpush( $block, $v);
		}
		echo " ok\n"; if ( $block) lpush( $blocks, $block); $H = array(); 
		echo "existing files"; 
		foreach ( flget( '.', 'deadlines', '', 'txt') as $f) { // process existing files first (overwritten with new stuff)
			$L = ttl( $f, '.'); $filetype = lpop( $L); if ( $filetype != 'txt') continue; lshift( $L); 
			$when = lshift( $L); if ( strlen( $when) != 6 || ! is_numeric( $when)) continue; // not a base file
			$what = ltt( $L, '.'); // when
			$H[ "$when $what"] = file( $f); echo '.'; //echo "   $when($what)";
		}
		foreach ( $blocks as $block) {  $L = ttl( lshift( $block), ' '); if ( ! $L) continue; $when = lshift( $L); $what = ltt( $L, ' '); $K = "$when $what"; $H[ "$K"] = $block;  }
		ksort( $H, SORT_NUMERIC); echo " ok\n"; 
		if ( ! is_file( $reject)) { foreach ( $H as $k => $v) echo "$k\n"; echo "# put the above text in $reject  for further processing logic.\n"; die(); }
		// reject logic
		$file = 'deadlines.backup.' . substr( tyyyymmdd(), 2) . '.tbz'; `rm -Rf $file`; $c = "tar jcvf $file deadlines.*.txt"; echo "BACKUP   $c   ... "; procpipe( $c); echo " ok\n"; 
		echo "SIZECHECK"; foreach ( $H as $k => $vs) echo '  ' . count( $vs) . '/' . strlen( implode( ' ', $vs)); echo " OK\n"; sleep( 3); //die();
		echo "CLEANUP"; foreach ( flget( '.', 'deadlines', '', 'txt') as $f) { $L = ttl( $f, '.'); lshift( $L); $when = lshift( $L); if ( ! is_numeric( $when) || strlen( $when) != 6) continue; $c = 'rm -Rf "' . $f . '"'; echo '.'; procpipe( $c); }
		echo " ok\n"; //die();
		echo "PRUNE/DUMP(leave only rejects) (starting with " . count( $H) . " keys) "; 
		foreach ( file( $reject) as $v) { $v = trim( $v); if ( ! $v) continue; if ( ! isset( $H[ "$v"])) { echo "x"; continue; }; echo '.'; $out = fopen( 'deadlines.' . ltt( ttl( $v, ' '), '.') . '.txt', 'w'); foreach ( $H[ "$v"] as $v2) fwrite( $out, trim( $v2) . "\n"); fclose( $out); unset( $H[ "$v"]); }
		echo " ok\n"; //die();
		echo "# " . count( $H) . " keys left, will remove them  (dryrun:" . jsonraw( $dryrun) . ")  sleep(5)..."; sleep( 5); echo " ok\n";   
		foreach ( $H as $k => $vs) {
			$L = ttl( $k); $when = lshift( $L); $what = ltt( $L, ' '); echo "REMOVE $when $what : "; 
			$file = null; foreach ( flget( '.') as $f) { if ( $file) continue; extract( fpathparse( $f)); if ( $filetype != 'txt') continue; if ( count( ttl( "***$f**", $when)) == 1 || count( ttl( "***$f***", $what)) == 1)  if ( $file) continue; $file = $f; }
			if ( ! $file) { echo " no file for this key (correct!)\n"; continue; }
			if ( ! $dryrun) { echo " SKIP=remove\n"; continue; }
			echo " DRYRUN(write to file)\n"; $out = fopen( 'deadlines.' . ltt( ttl( $k, ' '), '.') . '.txt', 'w'); foreach ( $vs as $v) fwrite( $out, trim( $v) . "\n"); fclose( $out);
		}
		echo "# " . count( $H) . " keys left, will remove them  (dryrun:" . jsonraw( $dryrun) . ")\n";
	}
	// SECTION: overall functionality
	public function addall( $calendars = 'deadlines,jobhunt,fundhunt', $noapicalls = true) { foreach ( ttl( $calendars) as $calendar) {  `rm -Rf $calendar.json`; foreach ( flget( '.', $calendar, '', 'txt') as $f) {  // filenames should start from  yymmdd.  or  yymmddhhmm
		echo "\n\n"; $L = ttl( $f, '.'); lshift( $L); $time = lshift( $L); if ( ! is_numeric( $time)) continue; 
		$yyyy = '20' . substr( $time, 0, 2); $mm = substr( $time, 2, 2); $dd = substr( $time, 4); $when = "$yyyy-$mm-$dd";
		if ( strlen( $time) > 6) { $mm2 = substr( $time, 6, 2); $dd = substr( $time, 8, 2); $when .= " $mm2:$dd"; }
		echo "$f   $time   > $when\n";
		$this->add( "calendar=$calendar,file=$f,when=$when,duration=allday", $noapicalls);
	}}}
	public function make( $calendars = 'deadlines,jobhunt,fundhunt') { $A = array(); foreach ( ttl( $calendars) as $calendar) { // makes  .md, .html
		// .md part
		echo "making $calendar.[md,md.txt,html]..."; $out = fopen( "$calendar.md", 'w'); $keymap = array();
		fwrite( $out, "# $calendar <span id=" . strdblquote( 'top') . "></span>\n\n"); 
		fwrite( $out, '<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF8">' . "\n\n");
		fwrite( $out, "this file is generated automatically, do not make manual changes to it!\n\n"); 
		$bywhen = array(); $H = jsonload( "$calendar.json"); 
		foreach ( $H as $k => $h) { extract( $h); if ( substr( $title, 0, 1) == '-') $title = '<strike>' . substr( $title, 1) . '</strike>'; if ( substr( $title, 0, 1) == '+') $title = '<strong>' . substr( $title, 1) . '</strong>'; $H[ "$k"][ 'title'] = $title; }
		foreach ( $H as $k => $h) { extract( $h); $bywhen[ "$k"] = lshift( ttl( $when, ' ')); }
		asort( $bywhen); foreach ( $bywhen as $k => $when3) { extract( $H[ "$k"]); $keymap[ "$k"] = substr( md5( $k), 0, 10); fwrite( $out, "$when3 [$title](#" . $keymap[ "$k"] . ")  \n"); }
		fwrite( $out, "\n\n"); foreach ( $H as $k => $h) { 
			extract( $h); // when, when2, url1, url2, title, duration, description
			fwrite( $out, "## $title  (" . lshift( ttl( $when, ' ')) . ") <span id=" . strdblquote( $keymap[ "$k"]) . "></span> <span style=" . strdblquote( 'color:#666;') . ">[→top](#top)</span>\n\n");
			$files = flget( '.', $calendar, $title, 'txt'); if ( ! $files) continue; 
			foreach ( file( lshift( $files)) as $v) { $v = trim( $v); if ( ! $v) fwrite( $out, "\n\n"); if ( ! $v) continue; $L = ttl( $v, ' '); foreach ( $L as $i => $v2) if ( strpos( $v2, 'http') === 0) $L[ $i] = "[$v2]($v2)"; $v= ltt( $L, ' '); fwrite( $out, $v . '  ' . "\n"); }
			fwrite( $out, " <span style=" . strdblquote( 'color:#666;') . ">[→top](#top)</span>");
			fwrite( $out, "\n\n\n"); extract( tsburst( tsystem())); 
			$A[ "due:" . lshift( ttl( $when, ' ')) . " $yyyy-$mm-$dd $title #$calendar"] = true; 
		}
		fclose( $out); `cat $calendar.md > $calendar.md.txt`; echo " OK\n"; 
		$c = "pandoc -f markdown -t html $calendar.md > $calendar.html"; echo "$c ... "; procpipe( $c); echo " OK\n";
	}; $this->maketodos( $A); }
	public function sync( $here = '.', $there = '/local/githubpages/gcal') { // removes all files from there which are not found in here
		if ( $here != '.') chdir( $here); $here = array(); foreach ( flget( '.') as $f) if ( is_file( $f)) $here[ "$f"] = true; 
		foreach ( $here as $f => $t) { $c = "rsync -avz " . strdblquote( $f) . " $there/."; procpipe( $c); echo "PUT $c\n"; }
		chdir( $there); foreach ( flget( '.') as $f) if ( is_file( "$f") && ! isset( $here[ "$f"])) { echo "delete $there/$f\n"; $c = 'rm -Rf ' . strdblquote( $f); procpipe( $c); }
		`chmod -R 777 *`;
	}
	// SECTION: macro actions
	public function update() { foreach ( ttl( 'addall,make,sync') as $f) $this->$f(); }
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