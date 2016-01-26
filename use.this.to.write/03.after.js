after = function( $box, c) { 
	$box.find( 'img').css({ position: 'relative', float: 'left', width: '40%', height: 'auto', margin: '5px'});
	var T = $box.html(); var lines = T.split( '<br>');
	var head = function( v, k, tags) { 
		v = v.substr( k.length);
		return tags[ 0] + $.trim( v) + tags[ 1];
	}
	var heads = function( v) { for ( var k in RULES.head) if ( v.substr( 0, k.length) == k) { v = head( v, k, RULES.head[ k]); break; }; return v; }
	var oany = function( v, k, tags) { // old any 
		var L = v.split( ' ' + k);
		for ( var i in L) { var L2 = L[ i].split( k + ' '); if ( L2.length == 1) continue; L[ i] = L2.shift()  + tags[ 1] + ' '; while ( L2.length) L[ i] += ' ' +  L2.shift(); }
		return $.ltt(  L, ' ' + tags[ 0]);
	}
	var any2 = function( v, k, tags) { 
		var L = v.split( k); var v2, L2;
		for ( var i = 1; i < L.length; i += 2) {
			v2 = $.trim( L[ i]); if ( ! v2) continue;
			if ( $.ttl( tags[ 0], 'REPLACEME').length > 1) v2 = $.ltt( $.ttl( tags[ 0], 'REPLACEME'), v2);
			else v2 = tags[ 0] + v2 + tags[ 1];
			L[ i] = v2;
		}
		var s = ''; for ( var i in L) s += L[ i]; return s;
	}
	var any2s = function( v) { for ( var k in RULES.twoparts) v = any2( v, k, RULES.twoparts[ k]); return v; }
	var any3 = function( v, k, tags) { 
		var L = v.split( k); var v2, v3;
		for ( var i = 1; i < L.length; i += 3) {
			v2 = $.trim( L[ i]); v3 = $.trim( L[ i + 1]); if ( ! v2 || ! v3) continue;
			if ( $.ttl( tags, 'REPLACEME1').length > 1) v2 = $.ltt( $.ttl( tags, 'REPLACEME1'), v2);
			if ( $.ttl( tags, 'REPLACEME2').length > 1) v2 = $.ltt( $.ttl( v2, 'REPLACEME2'), v3);
			L[ i] = v2; L[ i + 1] = '';
		}
		var s = ''; for ( var i in L) s += L[ i]; return s;
	}
	var any3s = function( v) { for ( var k in RULES.threeparts) v = any3( v, k, RULES.threeparts[ k]); return v; }
	var any1s = function( v, donec3) { for ( var k in RULES.onepart) if ( $.trim( v) == k) return donec3( RULES.onepart[ k][ 0], RULES.onepart[ k][ 1]); donec3( v, false); }
	var urls = function( v) { 
		var L = v.split( 'URL*'); if ( L.length == 1) return v;
		for ( var i = 1; i < L.length; i++) {
			var L2 = L[ i].split( '*'); var name = L2.shift();
			if ( name.substr( 0, 5) == 'http:') url = name; else url = L2.shift();
			L[ i] = '<a target="_blank" href="' + url + '">' + name + '</a> ';
			while ( L2.length) L[ i] += ' ' + L2.shift();
		}
		return $.ltt( L, ' ');
	}
	var ignore = false;
	$( 'body').stopTime().ioloop( $.hk( lines), '1ms', function( dom, value, sleep, c2) { 
		if ( ! value.length) { c2(); if ( c) c( $.ltt( lines, '<br>')); return; }
		var pos = value.shift(); var v = $.trim( lines[ pos]); if ( ! v) return c2( value);  
		any1s( lines[ pos], function( v2, ignore2) { // first process multi-line blocks 
			ignore = ignore2; lines[ pos] = v2; if ( ignore) return c2( value); 
			if ( RULES.head) lines[ pos] = heads( lines[ pos]) ;
			if ( RULES.threeparts) lines[ pos] = any3s( lines[ pos]);
			if ( RULES.twoparts) lines[ pos] = any2s( lines[ pos]);
			c2( value);
		});
	})
	                     
}