$( document).ready( function() { 
	var share = new Share( '#sharebuttons', { 
		title: $.trim( $( 'title').text()),
		description: $.trim( $( 'title').text()) + ' at maratishe.github.io. ',
		ui: { flyout: 'bottom left'},
		networks: { 
			google_plus: { enabled: true, url: document.location.href},
			twitter: { enabled: true},
			facebook: { enabled: true},
			linkedin: { url: document.location.href}, 
			email: { enabled: true, to: 'maratishe@gmail.com', title: 'Comment to ' + $.trim( $( 'title').text()), description: "Marat, \n\n Concerning your GithubPage '" + $.trim( $( 'title').text()) + "'...\n\n"}
		}
	})
	//share.open();
	//share.toggle();
	$( 'header')
	.append( '<meta property="og.title" content="' + $.trim( $( 'title').text()) + '"/>' + "\n")
	.append( '<meta property="og.image" content="http://maratishe.github.io/myphoto.jpg"/>' + "\n")
	.append( '<meta property="og.description" content="A Github Page on "' + $( 'title').text() + '" at  maratishe.github.io"/>' + "\n");
	// make sure that each image is floated only after , not before
	$( 'img').css({ clear: 'left'});
	$( 'h1').css({ 'margin-top': '50px', clear: 'both'});
	var oneimg = function( $img) { $img.css({ clear: 'both', position: 'relative', display: 'block', 'float': 'left', height: 'auto', margin: '5px', width: '40%'}); if ( $img.parent().width() > 500) $img.css({ width: '80%'}); if ( $img.parent().width() > 700) $img.css({ width: '40%'}); if ( $img.parent().width() > 1000) $img.css({ width: '30%'}); if ( $img.parent().width() <= 500) $img.css({ width: '98%'}); $img.attr( 'width', ''); $img.attr( 'height'); }
	var clickimg = function( $img) { var v = Math.round( $img.width()); var c = v; $img.click( function() { 
		if ( c == '100%') { $img.css({ width: v + 'px'}); c = v; }
		else { $img.css({ width: '100%'}); c = '100%'; }
	})}
	var checkimgs = function() { $( 'body').find( 'img').each( function() { oneimg( $( this)); clickimg( $( this)); })}; checkimgs();
	$( window).resize( function() { checkimgs(); })
})