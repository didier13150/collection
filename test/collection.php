<?php
	include_once( '../class/collection.class.php' );
	include_once( 'settings.php' );

	$collectionID = 0;

	$collection = new VideoCollection();
	if ( ! $collection->setFilename( $COLLECTIONS[$collectionID]['file'] ) )
	{
		$error =  $COLLECTIONS[$collectionID]['file'] . " file does not exists or is not readable !";
	}
	if ( ! $collection->setThumbsDir( $COLLECTIONS[$collectionID]['thumbs-dir'] ) )
	{
		if ( ! isset( $error ) ) $error =  $COLLECTIONS[$collectionID]['thumbs-dir'] . " directory does not exists or is not readable !";
	}
	if ( ! $collection->load() )
	{
		if ( ! isset( $error ) ) $error = "Could not load collection !";
	}

	//$i = 0;
	foreach( $collection->getItems() as $film )
	{
		echo $film->id . "->" . $film->title . "\n";
		//if ( $i > 10 ) break;
		//$i++;
	}
?>