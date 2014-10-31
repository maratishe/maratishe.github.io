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
})