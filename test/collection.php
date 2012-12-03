<?php
	include_once( '../class/collection.class.php' );
	include_once( '../class/item.class.php' );
	include_once( '../settings.php' );

	$collectionID = 0;
	$start = 0;
	$sort = 'title';

	// Get Command line options. Only with CLI.
	if (PHP_SAPI === 'cli')
	{
		parse_str( implode( '&', array_slice( $argv, 1 ) ), $_GET );
	}

	if ( isset( $_GET['collection'] ) and ! empty( $_GET['collection'] ) )
	{
		$collectionID = $_GET['collection'];
	}
	if ( isset( $_GET['start'] ) and ! empty( $_GET['start'] ) )
	{
		$start = $_GET['start'];
	}
	if ( isset( $_GET['sort'] ) and ! empty( $_GET['sort'] ) )
	{
		$sort = $_GET['sort'];
	}

	$collection = null;
	if( $COLLECTIONS[$collectionID]['type'] == 'film' )
	{
		$collection = new FilmsCollection();
	}
	elseif( $COLLECTIONS[$collectionID]['type'] == 'series' )
	{
		$collection = new SeriesCollection();
	}

	if( ! isset( $collection ) )
	{
		echo $COLLECTIONS[$collectionID]['type'] . " is not a valid type !\n";
		return 127;
	}
	if ( ! $collection->setFilename( $COLLECTIONS[$collectionID]['file'] ) )
	{
		echo $COLLECTIONS[$collectionID]['file'] . " file does not exists or is not readable !\n";
		return 127;
	}
	if ( ! $collection->setThumbsDir( '../' . $COLLECTIONS[$collectionID]['thumbs-dir'] ) )
	{
		echo $COLLECTIONS[$collectionID]['thumbs-dir'] . " directory does not exists or is not readable !\n";
		return 127;
	}
	if ( ! $collection->load() )
	{
		echo "Could not load collection !";
		return 127;
	}

	print "Number of item: " . $collection->count() . "\n";

	$collection->sort( 'id' );

	$mistake = array(
		'synopsis' => array(),
		'duration' => array(),
	);

	foreach( $collection->getItems() as $item )
	{
		/*echo $item->id . "->" . $item->title . "\n";
		if( $COLLECTIONS[$collectionID]['type'] == 'series' )
		{
			foreach( $item->getEpisodeList() as $episode )
			{
				echo "\t$episode\n";
			}
		}*/
		if ( $item->synopsis == "" ) $mistake['synopsis'][] = $item->title;
		if ( ! $item->duration ) $mistake['duration'][] = $item->title;
	}

	if ( count ( $mistake['synopsis'] ) )
	{
		echo "Film without synopsis\n";
		foreach( $mistake['synopsis'] as $name )
		{
			echo "\t$name\n";
		}
	}

	if ( count ( $mistake['duration'] ) )
	{
		echo "Film without valid duration\n";
		foreach( $mistake['duration'] as $name )
		{
			echo "\t$name\n";
		}
	}
?>