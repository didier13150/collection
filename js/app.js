var bgcolor;
var collection = 0;
var searchDefault;
var sortBy;
var defaultSortBy;
var collectionErrorStr = 'Error';
var itemErrorStr = 'Error';
var searchErrorStr = 'Error';
var searchTitle = 'Search';
var detailsTitle = 'Details';
var lang = 'en';

function init( full ) {
	if ( full )
	{
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
				posPopup();
			}
		);
		$('#search-text').bind(
			'focus',
			function() {
				$('#search-text').css( 'color', 'inherit' );
				$('#search-text').val( '' );
			}
		);
		$('#search-text').bind(
			'focusout',
			function() {
				$('#search-text').css( 'color', 'lightgrey' );
				$('#search-text').val( searchDefault );
			}
		);
		resizeArticle();
		posPopup();
	}
	$('#sort-container').show();
}

function bindItem( item ) {
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
			sortBy = defaultSortBy;
			getCollection();
		}
	);
}

function bindPage( id ) {
	$( '#page-' + id ).on(
		'click',
		function() {
			getCollection( id );
		}
	);
}

function bindSearch() {
	$( '#search-btn' ).on(
		'click',
		function() {
			search();
		}
	);
}

function bindSelect() {
	$( '#sort-fields' ).off( 'change' );
	$( '#sort-fields' ).on(
		'change',
		function() {
			sortBy = $('#sort-fields').val();
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
	$('#popup-title').html( 'Collection - ' + detailsTitle );
	setLoader( '#details', '#popup', 'loader' );
	$('#popup').show();
	setMainOpacity( 0.15 );
	var arg = '';
	if( parseInt( item ) >= 0 ) {
		arg = '?collection=' + collection + '&item=' + item + '&lang=' + lang;
	}
	$.ajax({
		url: 'details.php' + arg,
		success: function( data ) {
			$('#details').html( data );
		},
		error: function() {
			alert( itemErrorStr.replace( "%item", item ).replace( "%collection", collection ) );
			$('#popup').hide();
			setMainOpacity( 1 );
		}
	});
}

function getCollection( offset )
{
	setLoader( 'article', window, 'mainloader' );
	$('.item').off( 'click' );
	$('.tab').removeClass( 'current-tab' );
	$('#tab-' + collection ).addClass( 'current-tab' );
	var arg = '';
	if( parseInt( collection ) >= 0 ) {
		arg = '?collection=' + collection;
		if ( parseInt( offset ) >= 0 ) {
			arg += '&page=' + offset;
		}
		if ( sortBy ) {
			arg += '&sort=' + sortBy;
		}
		arg += '&lang=' + lang;
	}
	$.ajax({
		url: 'collection.php' + arg,
		success: function( data ) {
			$('article').html( data );
		},
		error: function() {
			$('article').html( '' );
			alert( collectionErrorStr.replace( "%collection", collection ) );
		}
	});
}

function search()
{
	$('#popup-title').html( 'Collection - ' + searchTitle );
	setLoader( '#details', '#popup', 'loader' );
	$('#popup').show();
	setMainOpacity( 0.15 );
	var arg = '';
	var target = $('#search-text').val();
	if( target == searchDefault ) {
		target = '';
	}
	arg = '?collection=' + collection + '&search=' + target;
	if ( sortBy ) {
		arg += '&sort=' + sortBy;
	}
	arg += '&lang=' + lang;
	$.ajax({
		url: 'search.php' + arg,
		success: function( data ) {
			$('#details').html( data );
		},
		error: function( jqXHR, strerror, status ) {
			alert( searchErrorStr.replace( "%target", target ).replace( "%collection", collection ) );
			$('#popup').hide();
			setMainOpacity( 1 );
		}
	});
}

function setSearchDefault( activate )
{
	if ( activate )
	{
		$('#search-text').val( '' );
		$('#search-text').css( 'color', 'inherit' );
	}
	else
	{
		$('#search-text').css( 'color', 'lightgrey' );
		$('#search-text').val( searchDefault );
	}
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
	var articleHeight = $(window).height() - 156;
	if ( articleHeight < 160 )
	{
		articleHeight = 160;
	}
	$('#thumbnails').css( 'height', articleHeight + 'px' );
}

function posPopup()
{
	var width = $(window).width();
	var popupWidth = $('#popup').width();
	var margin = ( width - popupWidth ) /2;
	$('#popup').css( 'left', margin + 'px' );
}

function modifyRef()
{
	$('a').attr( 'href', '#' );
	$('form').removeAttr( 'target' );
	$('form').removeAttr( 'method' );
	$('input').removeAttr( 'name' );
}
