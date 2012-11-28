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
	$( '#tab-' + id ).attr( 'href', '#' );
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
	setLoader( '#details', '#popup', 'loader' );
	$('#popup').show();
	setMainOpacity( 0.15 );
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
	setLoader( 'article', window, 'mainloader' );
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

function setLoader( element, parent, loaderClass )
{
	$(element).html( '<img class="' + loaderClass + '" src="img/ajax-loader.gif" alt="Please, Wait..."/>' );
	var loaderWidth = $( '.' + loaderClass ).width();
	var loaderHeight = $( '.' + loaderClass ).height();
	var margin = ( parseInt( $(parent).width() ) - loaderWidth ) / 2;
	$( '.' + loaderClass ).css( 'margin-left', margin + 'px' );
	$( '.' + loaderClass ).css( 'margin-right', margin + 'px' );
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
