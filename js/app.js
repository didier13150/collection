var bgcolor;
var collection = 0;

function init() {
	bgcolor = $('body').css( 'background-color' );
	$( '.icon-close' ).on(
		'click',
		function() {
			$('#popup').hide();
			setMainOpacity( 1 );
		}
	);
	$('#popup').Draggable(
	{
		handle: 'span.icon-move',
		grid: [25,25],
	});
	$(window).resize(
		function() {
			resizeArticle();
		}
	);
}

function bindThumbnail( item ) {
	$( '#item-' + item ).on(
		'click',
		function() {
			getItem( item );
		}
	);
}

function bindTab( id ) {
	$( '#tab-' + id ).on(
		'click',
		function() {
			collection = id;
			getCollection();
		}
	);
}
function setMainOpacity( opacity )
{
	if ( parseInt( opacity ) == 1 ) {
		$('body').css( 'background-color', bgcolor );
	}
	else {
		$('body').css( 'background-color', 'rgb( 0, 0, 0 )' );
	}
	$('header').css( 'opacity', parseFloat( opacity ) );
	$('nav').css( 'opacity', parseFloat( opacity ) );
	$('article').css( 'opacity', parseFloat( opacity ) );
	$('footer').css( 'opacity', parseFloat( opacity ) );
}

function getItem( item )
{
	var marginLoader = ( parseInt( $('#popup').width() ) - 600 ) / 2;
	$('#popup').show();
	setMainOpacity( 0.15 );
	$('#details').html( '<img class="loader" src="img/ajax-loader.gif" alt="Please, Wait"/>' );
	$('.mainloader').css( 'margin-left', marginLoader + 'px' );
	$('.mainloader').css( 'margin-right', marginLoader + 'px' );
	var arg = '';
	if( parseInt( item ) >= 0 ) {
		arg = '?collection=' + collection + '&item=' + item;
	}
	$.ajax({
		url: 'details.php' + arg,
		success: function( data ) {
			$('#details').html( data );
		},
		error: function() {
			alert( 'Error reported when trying to get details about item ' + item + ' on collection ' + collection );
			$('#popup').hide();
			setMainOpacity( 1 );
		}
	});
}

function getCollection()
{
	var marginLoader = ( parseInt( $(window).width() ) - 600 ) / 2;
	$('article').html( '<img class="mainloader" src="img/ajax-loader.gif" alt="Please, Wait"/>' );
	$('.mainloader').css( 'margin-left', marginLoader + 'px' );
	$('.mainloader').css( 'margin-right', marginLoader + 'px' );
	$('.item').off( 'click' );
	$('.tab').removeClass( 'current-tab' );
	$('#tab-' + collection ).addClass( 'current-tab' );
	var arg = '';
	if( parseInt( collection ) >= 0 ) {
		arg = '?collection=' + collection;
	}
	$.ajax({
		url: 'collection.php' + arg,
		success: function( data ) {
			$('article').html( data );
		},
		error: function() {
			$('article').html( '' );
			alert( 'Error reported when trying to get collection ' + collection );
		}
	});
}

function resizeArticle()
{
	var articleHeight = $(window).height() - 180;
	if ( articleHeight < 160 )
	{
		articleHeight = 160;
	}
	$('#thumbnails').css( 'height', articleHeight + 'px' );
}
