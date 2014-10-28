<?php
set_time_limit( 0);
ob_implicit_flush( 1);
//ini_set( 'memory_limit', '4000M');
for ( $prefix = is_dir( 'ajaxkit') ? 'ajaxkit/' : ''; ! is_dir( $prefix) && count( explode( '/', $prefix)) < 4; $prefix .= '../'); if ( ! is_file( $prefix . "env.php")) $prefix = '/web/ajaxkit/'; if ( ! is_file( $prefix . "env.php")) die( "\nERROR! Cannot find env.php in [$prefix], check your environment! (maybe you need to go to ajaxkit first?)\n\n");
if ( is_file( 'requireme.php')) require_once( 'requireme.php'); else foreach ( array( 'functions', 'env') as $k) require_once( $prefix . "$k.php"); clinit(); 
//clhelp( '');
//htg( clget( ''));

$H = array();
foreach ( flget( '.', '', '', 'md') as $file) {
	$L = ttl( $file, ' '); if ( ! is_numeric( $L[ 0]) || count( $L) < 2) continue; // not my file
	$date = $L[ 0];
	$L = explode( '.', $file); lpop( $L); $v = implode( '.', $L); $L = explode( ' ', $v); lshift( $L); $v = implode( ' ', $L);
	$L = ttl( $v, '--'); $tags = array(); while ( count( $L) > 1) lpush( $tags, lpop( $L)); $title = lshift( $L);
	$L = explode( '.', $file); lpop( $L); lpush( $L, 'html'); $html = implode( '.', $L); if ( ! is_file( $html)) continue; // no HTML, probably temp file  
	$L = explode( '.', $file); lpop( $L); lpush( $L, 'pdf'); $pdf = implode( '.', $L); 
	lpush( $H, compact( ttl( 'date,title,tags,file,html,pdf')));
}
$lines = file( 'index.html'); $out = fopen( 'index.html', 'w');
foreach ( $lines as $line) {
	fwrite( $out, $line); if ( strpos( $line, '<div id="links"') !== 0) continue;
	// the links block
	while ( count( $H)) { 
		extract( lpop( $H)); // date, file, html, pdf, title, tags
		$S = '<div style="position:relative;margin:2px 0px;width:100%;height:auto;font-size:larger;color:#000;">';
		$S .= '<strong>' . $date . '</strong> ';
		$S .= '<a target="_blank" href="' . $html . '">' . $title . '</a> ';
		if ( is_file( $pdf)) $S .= '(<a target="_blank" style="font-size:smaller;" href="' . $pdf . '">pdf</a>) ';
		if ( $tags) $S .= ' <span style="font-size:smaller;">tags: <strong>' . ltt( $tags, ', ') . '</strong></span>'; 
		$S .= '</div>' . "\n";
		fwrite( $out, $S);
	}
	fwrite( $out, "</div>\n</body>\n</html>\n"); 
	break;
}
fclose( $out);

?>