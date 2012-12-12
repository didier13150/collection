<?php
	$COLLECTIONS = array(
		0 => array(
			// Title on tab
			'title' => 'Films',
			// GCStar collection type
			// Can be 'film' or 'series' for now
			'type' => 'film',
			// GCStar collection file
			'file' => '/data/video/films.gcs',
			// Directory of thumbnails
			'thumbs-dir' => 'data/films-pictures',
		),
		1 => array(
			// Title on tab
			'title' => 'Animations',
			// GCStar collection type
			'type' => 'film',
			// GCStar collection file
			'file' => '/data/video/animations.gcs',
			// Directory of thumbnails
			'thumbs-dir' => 'data/animations-pictures',
		),
		2 => array(
			// Title on tab
			'title' => 'Spectacles',
			// GCStar collection type
			'type' => 'film',
			// GCStar collection file
			'file' => '/data/video/spectacles.gcs',
			// Directory of thumbnails
			'thumbs-dir' => 'data/spectacles-pictures',
		),
		3 => array(
			// Title on tab
			'title' => 'Séries',
			// GCStar collection type
			'type' => 'series',
			// GCStar collection file
			'file' => '/data/video/series.gcs',
			// Directory of thumbnails
			'thumbs-dir' => 'data/series-pictures',
		),
	);

	/*
	 * Size of thumbnail on main page
	 * 0 => 54px x 75px
	 * 1 => 86px x 120px
	 * 2 => 108px x 151px
	 * 3 => 162px x 226px
	 * 4 => 200px x 280px
	 */
	$THUMB_SIZE = 2;

	/*
	 * Size of thumbnail on detail popup
	 */
	$DETAIL_SIZE = 4;

	/*
	 * Number of item per page
	 */
	$NB_ITEM_PER_PAGE = 40;

	$SIZE_OF_THUMBNAILS = array(
		0 => array( 'width' => 54, 'height' => 75 ),
		1 => array( 'width' => 86, 'height' => 120 ),
		2 => array( 'width' => 108, 'height' => 151 ),
		3 => array( 'width' => 162, 'height' => 226 ),
		4 => array( 'width' => 200, 'height' => 280 ),
	);

?>
