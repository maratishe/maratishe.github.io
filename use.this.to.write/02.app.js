$.ioutils.nolog = false; var after = null; var RULES = {};
$( document).ready( function() { $( '#read').load( 'stub.html', function() { $( 'body').kvstorage( function( KVS) {
	$( 'body').css({ overflow: 'visible'}); $( 'title').empty().append( 'blog writer');
	var text = $( '#write').editable( '#000,0.1', '#000,0');
	text.value( $( '#read').html()); var before = null; var TEXT = text;
	var check = function() { $( 'body').stopTime().oneTime( '1s', function() { after( text.inner(), function( v) { 
		if ( v == before) return check(); 
		$( '#read').empty().append( v); 
		check(); 
		before = v; 
	})})}
	//text.onhange( function( v) { $( '#read').empty().append( v); })
	check();
	// panel
	var $p = $( 'body').ioover({ position: 'fixed', bottom: '5px', left: '-3px', width: '25%', height: 'auto'});
	var panel = function( name, onon, onoff, donec) {
		$p.ioover( true).css({ height: '5px'});
		var $box = $p.ioover( true).css({ position: 'relative', width: '100%', height: '80px'})
		.ioground( '#fff', 0.9);
		var $box2 = $box.ioover().ioground( '#C54', 0.8).css({ border: '1px solid #000'}).ioover();
		var area = $box2.ioover({ border: '0px', position: 'absolute', display: 'block', width: '100%', height: '100%', 'background-color': 'transparent', color: '#fff', 'font-size': $.io.defs.fonts.small}, 'textarea', { overflow: 'hidden', name: 'text'})
		var $h = $box.ioover({ position: 'absolute', left: '101%', top: '0px', width: 'auto', height: 'auto'})
		.ioground( '#C54', 0.4).ioground( '#fff', 0.9)
		.ioover( true).append( name).css({ color: '#000', 'font-size': '30px'});
		$box.css({ left: '-100%'}); var on = false;
		$h.click( function() { 
			if ( on) { on = false; $box.css({ left: '-100%'}); if ( onoff) onoff( area, $box2); }
			else { on = true; $box.css({ left: '3px'}); if ( onon) onon( area, $box2); }
		})
		if ( donec) donec( $box2);
	}
	panel( '+', null, function( area) { var v = $.trim( area.val()); if ( ! v) return; text.inner().append( '<br/>'  + v + '<br/>'); })
	panel( '>', function( area) { area.val(  $( '#read').html()); }, null)
	panel( 'LS', function( area, $box) { KVS.get( 'richtml', function( h) { $box.ioanimoutemptyin( 'fast', function() { 
		var L = []; if ( h && h.richtml) L = h.richtml; L.unshift( 'new,clear'); 
		var makenew = function() { $box.ioanimoutemptyin( 'fast', function() { 
			var text = $box.ioover( true).editable( '#fff,0.2', '#fff,0.5'); text.value( 'name?');
			$box.ioover( true).iotextbuttons( 'done', function() { L[ 0] = $.trim( text.text()); if ( ! L[ 0]) return; L = $.hk( $.hvak( L)); KVS.set({ richtml: L}, function() {
				var h = {}; h[ 'richtml.' + L[ 0]] = $( '#read').html(); KVS.set( h, function() { $box.ioanimoutemptyin( 'fast', function() { $box.append( 'OK'); })})
			})}, '#fff');
			
		})}
		$box.iotextbuttons( $.ltt( L), function( k) {
			if ( k == 'new') return makenew();
			if ( k == 'clear') { KVS.clear(); $box.ioanimoutemptyin( 'fast', function() { $box.append( 'OK'); }); }
			KVS.get( 'richtml.' + k, function( h) { TEXT.value( h[ 'richtml.' + k]); $box.ioanimoutemptyin( 'fast', function() { $box.append( 'OK'); })})
		}, '#fff');
		
	})})}, null)
	$.getScript( 'rules.js'); // replaces RULES
	$( 'body').ioover().css({ left: '49%', top: '2px', width: 'auto', height: 'auto'})
	.ioground( '#C54', '0.8').ioover( true).append( '<<<').css({ color: '#fff'})
	.click( function() { TEXT.inner().empty().append( $( '#read').html()); })
})})})