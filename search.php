<?php
	include_once( 'class/collection.class.php' );
	include_once( 'class/item.class.php' );
	include_once( 'include/functions.php' );
	include_once( 'settings.php' );

	$collectionID = 0;
	$sort = 'title';
	$search = null;

	if ( isset( $_GET['collection'] ) and ! empty( $_GET['collection'] ) )
	{
		$collectionID = $_GET['collection'];
	}
	elseif ( isset( $_SESSION['collection'] ) and ! empty( $_SESSION['collection'] ) )
	{
		$collectionID = $_SESSION['collection'];
	}
	if ( isset( $_GET['sort'] ) and ! empty( $_GET['sort'] ) )
	{
		$sort = $_GET['sort'];
	}
	if ( isset( $_GET['search'] ) and ! empty( $_GET['search'] ) )
	{
		$search = removeAccents( $_GET['search'] );
	}

	ob_start();

	if( ! isset( $search ) )
	{
		echo "Nothing to search !";
		return 127;
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
		echo $COLLECTIONS[$collectionID]['type'] . " is not a valid type !";
		return 127;
	}
	if ( ! $collection->setFilename( $COLLECTIONS[$collectionID]['file'] ) )
	{
		echo $COLLECTIONS[$collectionID]['file'] . " file does not exists or is not readable !";
		return 127;
	}
	if ( ! $collection->setThumbsDir( $COLLECTIONS[$collectionID]['thumbs-dir'] ) )
	{
		echo $COLLECTIONS[$collectionID]['thumbs-dir'] . " directory does not exists or is not readable !";
		return 127;
	}
	if ( ! $collection->load() )
	{
		echo "Could not load collection !";
		return 127;
	}
	$collection->sort( $sort );
	$items = $collection->getItems();
	$occurencies = array();
	$regex = getRegex( removeAccents( $search ) );
	/*
	$hits = array();
	$searches = preg_split( "/\s+/", $search, -1, PREG_SPLIT_NO_EMPTY );
	$nbOfWorld = str_word_count( $search );

	foreach( $items as $item )
	{
		for( $i = 0 ; $i < $nbOfWorld ; $i++ )
		{
			if ( preg_match( "/$regex/i", removeAccents( $item->title ) ) )
			{
				$hits[$i][] = $item;
			}
		}
	}

	for( $i = 0 ; $i < ( $nbOfWorld - 1 ) ; $i++ )
	{
		$occurencies = array_intersect( $hits[$i], $hits[$i+1] );
	}
	if ( $nbOfWorld == 1 )
	{
		$occurencies = $hits[0];
	}*/
	foreach( $items as $item )
	{
		if ( preg_match( "/$regex/i", removeAccents( $item->title ) ) )
		{
			$occurencies[] = $item;
		}
		elseif ( preg_match( "/$regex/i", removeAccents( $item->originalTitle ) ) )
		{
			$occurencies[] = $item;
		}
	}

?>
<form class="big">
<?php if( count( $occurencies ) ):?>
	<ul class="search">
	<?php foreach( $occurencies as $item ):?>
		<li class="search">
			<a href="./index.php?query=details&amp;collection=<?php echo $collectionID;?>&amp;item=<?php echo $item->id;?>" class="item" id="item-<?php echo $item->id;?>">
				<?php echo $item->title;?>
			</a><br>
			<?php if( $item->originalTitle != "" ):?>
			(<?php echo $item->originalTitle;?>)
			<?php endif;?>
			<?php if( $item->director != "" ):?>
			de <?php echo $item->director;?>
			<?php endif;?>
			<?php if( $item->year ):?>
			(<?php echo $item->year;?>)
			<?php endif;?>
		</li>
	<?php endforeach;?>
	</ul>
<?php else:?>
	<span class="error">Aucun film ne correspond à la demande ("<?php echo $search;?>")</span>
<?php endif;?>
</form>
<script>
	init( false );
	modifyRef();
	<?php foreach( $occurencies as $item ):?>
		bindItem( '<?php echo $item->id;?>' );
	<?php endforeach;?>
</script>
<?php
	$html = ob_get_clean();
	echo $html;
	return 0;
?>
