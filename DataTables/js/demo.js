
/*global SyntaxHighlighter*/
SyntaxHighlighter.config.tagName = 'code';

jQuery(document).ready( function () {
	if ( ! jQuery.fn.dataTable ) {
		return;
	}
	var dt110 = jQuery.fn.dataTable.Api ? true : false;

	// Work around for WebKit bug 55740
	var info = jQuery('div.info');

	if ( info.height() < 115 ) {
		info.css( 'min-height', '8em' );
	}

	var escapeHtml = function ( str ) {
		return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
	};

	// css
	var cssContainer = jQuery('div.tabs div.css');
	if ( jQuery.trim( cssContainer.find('code').text() ) === '' ) {
		cssContainer.find('code, p:eq(0), div').css('display', 'none');
	}

	// init html
	var table = jQuery('<p/>').append( jQuery('table').clone() ).html();
	jQuery('div.tabs div.table').append(
		'<code class="multiline brush: html;">\t\t\t'+
			escapeHtml( table )+
		'</code>'
	);
	//SyntaxHighlighter.highlight({}, $('#display-init-html')[0]);

	// Allow the demo code to run if DT 1.9 is used
	if ( dt110 ) {
		// json
		var ajaxTab = jQuery('ul.tabs li').eq(3).css('display', 'none');

		jQuery(document).on( 'init.dt', function ( e, settings ) {
			var api = new jQuery.fn.dataTable.Api( settings );

			var show = function ( str ) {
				ajaxTab.css( 'display', 'block' );
				$('div.tabs div.ajax code').remove();

				// Old IE :-|
				try {
					str = JSON.stringify( str, null, 2 );
				} catch ( e ) {}

				jQuery('div.tabs div.ajax').append(
					'<code class="multiline brush: js;">'+str+'</code>'
				);
				SyntaxHighlighter.highlight( {}, $('div.tabs div.ajax code')[0] );
			};

			// First draw
			var json = api.ajax.json();
			if ( json ) {
				show( json );
			}

			// Subsequent draws
			api.on( 'xhr.dt', function ( e, settings, json ) {
				show( json );
			} );
		} );

		// php
		var phpTab = jQuery('ul.tabs li').eq(4).css('display', 'none');

		
	}
	else {
		//$('ul.tabs li').eq(3).css('display', 'none');
		//$('ul.tabs li').eq(4).css('display', 'none');
	}

	
} );