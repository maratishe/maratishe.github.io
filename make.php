<?php
set_time_limit( 0);
ob_implicit_flush( 1);		
//ini_set( 'memory_limit', '4000M');
for ( $prefix = is_dir( 'ajaxkit') ? 'ajaxkit/' : ''; ! is_dir( $prefix) && count( explode( '/', $prefix)) < 4; $prefix .= '../'); if ( ! is_file( $prefix . "env.php")) $prefix = '/web/ajaxkit/'; if ( ! is_file( $prefix . "env.php")) die( "\nERROR! Cannot find env.php in [$prefix], check your environment! (maybe you need to go to ajaxkit first?)\n\n");
if ( is_file( 'requireme.php')) require_once( 'requireme.php'); else foreach ( array( 'functions', 'env') as $k) require_once( $prefix . "$k.php"); clinit(); 
//clhelp( '');
//htg( clget( ''));

$H = array(); $TAGS = tth( 'all=0');
foreach ( flget( '.', '', '', 'md') as $file) {
	$L = ttl( lshift( ttl( $file, ' ')), '.'); if ( ! is_numeric( $L[ 0])) continue; // not my file
	$date = $L[ 0]; extract( fpathparse( $file)); // fileroot, filename
	$bad = '  '; $goodroot = str_replace( '--', '_', $fileroot); 
	for ( $i = 0; $i < strlen( $bad); $i++) $goodroot = str_replace( substr( $bad, $i, 1), '.', $goodroot);
	for ( $i = 0; $i < 3; $i++) $goodroot = str_replace( '..', '.', $goodroot);
	$goodroot = str_replace( '._.', '_', $goodroot);
	$map = array(); foreach ( ttl( 'md,html,pdf') as $ext) if ( is_file( "$fileroot.$ext")) $map[ $ext] = "$fileroot.$ext";
	foreach ( $map as $ext => $file2) { $c = 'mv ' . strdblquote( $file2) . ' ' . strdblquote( "$goodroot.$ext"); if ( $file2 != "$goodroot.$ext") `$c`; $map[ $ext] = "$goodroot.$ext"; }
	$tags = ttl( $goodroot, '_'); $title = str_replace( '.', ' ', lshift( $tags));
	$html = null; $pdf = null; $file = $map[ 'md']; foreach ( $map as $ext => $file2) $$ext = $file2;
	foreach ( $tags as $tag) { htouch( $TAGS, $tag, 0, false, false); $TAGS[ $tag]++; }; $TAGS[ 'all']++;
	lpush( $H, compact( ttl( 'date,title,tags,file,html,pdf'))); if ( ! $html) continue;
	// upgrade the html file
	$L = file( $html); $L2 = file( 'header.html'); $out = fopen( $html, 'w');
	fwrite( $out, "<title>$title</title>\n"); foreach ( $L2 as $line) fwrite( $out, $line);
	while ( count( $L) && strpos( trim( lfirst( $L)), '<p><style>') === false) lshift( $L);   
	fwrite( $out, "\n"); foreach ( $L as $v) fwrite( $out, $v); fclose( $out);
}
foreach ( flget( '.', '', '', 'html') as $file) {
	extract( fpathparse( $file)); if ( is_file( "$fileroot.md")) continue; // old format
	$L = ttl( $fileroot, '.'); $date = lshift( $L); $tags = $L; if ( ! is_numeric( $date)) continue; // date, tags
	$s = implode( '', file( $file)); $L = ttl( $s, '<hdr>'); lshift( $L); $L = ttl( lshift( $L), '</hdr>'); $title = trim( lshift( $L)); // title
	$out = fopen( "$fileroot.htm", 'w'); fwrite( $out, "<title>$title</title>\n"); fwrite( $out, implode( '', file( "header.html")) . "\n"); fwrite( $out, $s); fclose( $out); // this is the actual file
	if ( is_file( "$fileroot.pdf")) $pdf = "$fileroot.pdf"; else $pdf = ''; $html = "$fileroot.htm"; // pdf, html
	foreach ( $tags as $tag) { htouch( $TAGS, "$tag", 0, false, false); $TAGS[ "$tag"]++; }
	lpush( $H, compact( ttl( 'date,file,html,pdf,title,tags')));
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
	while ( count( $H)) { // articles 
		extract( lpop( $H)); // date, file, html, pdf, title, tags
		$S = '<div style="position:relative;margin:2px 0px;width:100%;height:auto;font-size:larger;color:#000;">';
		$S .= '<strong>' . $date . '</strong> ';
		$S .= '<a target="_blank" href="' . $html . '">' . $title . '</a> ';
		if ( $pdf && is_file( $pdf)) $S .= '(<a target="_blank" style="font-size:smaller;" href="' . $pdf . '">pdf</a>) ';
		if ( $tags) { 
			$S .= ' <span style="font-size:12px;">tags:';
			foreach ( $tags as $tag) $S .= ' <a class="local" onclick="javascript:return false;">' . $tag . '</a>';
			$S .= '</span>';
		}
		$S .= '</div>' . "\n";
		fwrite( $out, $S);
	}
	fwrite( $out, "</div>\n</body>\n</html>\n"); 
	break;
}
fclose( $out); `rm -Rf index.html`; `mv index2.html index.html`;

?>                       