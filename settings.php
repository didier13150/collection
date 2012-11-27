<?php
	$COLLECTIONS = array(
		0 => array(
			// Title on tab
			'title' => 'Films',
			// GCStar collection file
			'file' => '/data/video/films.gcs',
			// Directory of thumbnails
			'thumbs-dir' => 'data/films-pictures',
		),
		1 => array(
			// Title on tab
			'title' => 'Animations',
			// GCStar collection file
			'file' => '/data/video/animations.gcs',
			// Directory of thumbnails
			'thumbs-dir' => 'data/animations-pictures',
		),
		2 => array(
			// Title on tab
			'title' => 'Spectacles',
			// GCStar collection file
			'file' => '/data/video/spectacles.gcs',
			// Directory of thumbnails
			'thumbs-dir' => 'data/spectacles-pictures',
		),
		3 => array(
			// Title on tab
			'title' => 'Séries',
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
?>
