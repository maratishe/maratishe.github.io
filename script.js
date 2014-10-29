$( document).ready( function() { 
	var share = new Share( '#sharebuttons', { 
		title: $.trim( $( 'title').text()),
		description: 'Migrated to GithubPages. First time to have full control over what the blog looks and behaves like. http://maratishe.github.io',
		ui: { flyout: 'bottom left'},
		networks: { 
			google_plus: { enabled: true, url: document.location.href},
			twitter: { enabled: true},
			facebook: { enabled: true},
			linkedin: { url: document.location.href}, 
			email: { enabled: true, to: 'maratishe@gmail.com', title: 'Comment to ' + $.trim( $( 'title').text()), description: "Marat, \n\n Concerning your GithubPage '" + $.trim( $( 'title').text()) + "'...\n\n"}
		}
		
	})
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