<?php
$CLASS = 'blog'; class blog { // USER code 
	public $silent = false;
	public function __construct( $silent = false) { $this->silent = $silent; }
	public function update() { 
		$H = jsonload( 'index.json'); $TAGS = array(); $TAGS[ 'all'] = 0; // load the previous info
		foreach ( $H as $h) { extract( $h); foreach ( ttl( $tags, ' ') as $tag) { htouch( $TAGS, "$tag", 0, false, false); $TAGS[ "$tag"]++; $TAGS[ 'all']++; }} 
		foreach ( flget( '.', '', '', 'raw') as $file) { 
			extract( fpathparse( $file)); if ( is_file( "$fileroot.htm")) continue; // already done
			$L = ttl( $fileroot, '.'); $date = lshift( $L); $tags = $L; if ( ! is_numeric( $date)) continue; // date, tags
			$s = implode( '', file( $file)); $L = ttl( $s, '<hdr>'); lshift( $L); $L = ttl( lshift( $L), '</hdr>'); $title = trim( lshift( $L)); // title
			$out = fopen( "$fileroot.htm", 'w'); fwrite( $out, "<title>$title</title>\n"); fwrite( $out, implode( '', file( "header.html")) . "\n"); fwrite( $out, $s); fclose( $out); // this is the actual file
			$file = "$fileroot.htm"; // html
			foreach ( $tags as $tag) { htouch( $TAGS, "$tag", 0, false, false); $TAGS[ "$tag"]++; $TAGS[ 'all']++; }
			$tags = ltt( $tags, ' '); 
			lunshift( $H, compact( ttl( 'date,title,file,tags')));
		}
		$lines = file( 'index.html'); $out = fopen( 'index2.html', 'w');
		foreach ( $lines as $line) {
			if ( strpos( $line, '<div id="tags"') !== false) { // categories   -- continue
				$S = '<div id="tags">categories: ';
				foreach ( $TAGS as $tag => $count) $S .= ' <a class="local" onclick="javascript:return false;"><span>' . $tag . '</span><span style="font-sizes:smaller;"> (' . $count . ')</span></a><br/>';
				$S .= "</div>\n";
				fwrite( $out, $S); 
				continue;
			}
			fwrite( $out, $line); if ( strpos( $line, '<div id="links"') !== 0) continue;
			foreach ( $H as $h) { 
				extract( $h);  // date, title, file, tags
				$S = '<div style="position:relative;margin:2px 0px;width:100%;height:auto;font-size:larger;color:#000;">';
				$S .= '<strong>' . $date . '</strong> ';
				$S .= '<a target="_blank" href="' . $file . '">' . $title . '</a> ';
				if ( $tags) { 
					$S .= ' <span style="font-size:12px;">tags:';
					foreach ( ttl( $tags, ' ') as $tag) $S .= ' <a class="local" onclick="javascript:return false;">' . $tag . '</a>';
					$S .= '</span>';
				}
				$S .= '</div>' . "\n";
				fwrite( $out, $S);
			}
			fwrite( $out, "</div>\n</body>\n</html>\n"); 
			break;
		}
		fclose( $out); `rm -Rf index.html`; `mv index2.html index.html`; jsondump( $H, 'index.json');
	}
	public function listup() { foreach ( jsonload( 'index.json') as $pos => $h) { 
		extract( $h); echo "#$pos  $date  $title\n";
	}}
	public function purge( $pos = null) { $H = jsonload( 'index.json'); foreach ( $H as $pos2 => $h) if ( $pos !== null && $pos == $pos2) unset( $H[ $pos2]); $H = hv( $H); jsondump( $H, 'index.json'); }
}
if ( isset( $argv) && count( $argv) && strpos( $argv[ 0], "$CLASS.php") !== false) { // direct CLI execution, redirect to one of the functions 
	// this is a standalone script, put the header
	set_time_limit( 0);
	ob_implicit_flush( 1);
	for ( $prefix = is_dir( 'ajaxkit') ? 'ajaxkit/' : ''; ! is_dir( $prefix) && count( explode( '/', $prefix)) < 4; $prefix .= '../'); if ( ! is_file( $prefix . "env.php")) $prefix = '/web/ajaxkit/'; 
	if ( ! is_file( $prefix . "env.php") && ! is_file( 'requireme.php')) die( "\nERROR! Cannot find env.php in [$prefix] or requireme.php in [.], check your environment! (maybe you need to go to ajaxkit first?)\n\n");
	if ( is_file( 'requireme.php')) require_once( 'requireme.php'); else foreach ( explode( ',', ".,$prefix") as $p) foreach ( array( 'functions', 'env') as $k) if ( is_dir( $p) && is_file( "$p/$k.php")) require_once( "$p/$k.php");
	chdir( clgetdir()); clparse(); $JSONENCODER = 'jsonencode'; // jsonraw | jsonencode    -- jump to lib dir
	// help
	clhelp( "FORMAT: php$CLASS WDIR COMMAND param1 param2 param3...     ($CLNAME)");
	foreach ( file( $CLNAME) as $line) if ( ( strpos( trim( $line), '// SECTION:') === 0 || strpos( trim( $line), 'public function') === 0) && strpos( $line, '__construct') === false) clhelp( trim( str_replace( 'public function', '', $line)));
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
	//if ( ! is_file( 'webkey.json') || ! isset( $webkey)) die( jsonsend( jsonerr( 'webkey env not set, run [phpwebkey make] first'))); 
	//$h = jsonload( 'webkey.json'); if ( ! isset( $h[ "$webkey"])) die( jsonsend( jsonerr( 'no such webkey in your current environment')));
	//$wdir = $h[ "$webkey"]; if ( ! is_dir( "$wdir")) die( jsonsend( jsonerr( "no dir $wdir in local filesystem, webkey env is wrong")));
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
?>