$( document).ready( function() { 
	var one = function( $box) { $box.click( function() { 
		var tag = $box.find( 'span').get().length ? $.trim( $box.find( 'span').first().text()) : $.trim( $box.text());  
		$( 'div[id="links"]').children().each( function() { 
			var $box2 = $( this); 
			if ( tag == 'all') return $box2.fadeIn( 'fast');
			var found = false; $box2.find( 'a[class="local"]').each( function() { if ( $.trim( $( this).text()) == tag) found = true; })
			if ( found) $box2.fadeIn( 'fast'); else $box2.fadeOut( 'fast');
		})
		
	})}
	$( 'a[class="local"]').each( function() { one( $( this)); })
	$( 'div[class="bg1"]').fadeTo( 'fast', 0.8);
	$( 'div[class="bg2"]').fadeTo( 'fast', 0.5);
})