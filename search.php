<?php
	require_once( 'gettext/gettext.inc' );
	include_once( 'class/collection.class.php' );
	include_once( 'class/item.class.php' );
	include_once( 'include/functions.php' );
	include_once( 'settings.php' );

	defined( 'APPLICATION_PATH' ) || define( 'APPLICATION_PATH', dirname(__FILE__) );

	$collectionID = 0;
	$sort = 'title';
	$search = null;
	$language = $DEFAULT_LANGUAGE;

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
	if ( isset( $_GET['lang'] ) and ! empty( $_GET['lang'] ) )
	{
		$language = $_GET['lang'];
	}
	elseif ( isset( $_SESSION['language'] ) and ! empty( $_SESSION['language'] ) )
	{
		$language = $_SESSION['language'];
	}

	// I18N support information here
	putenv( "LANG=$language" );
	T_setlocale( LC_ALL, $language );

	// Set the text domain as 'messages'
	$domain = 'messages';
	T_bindtextdomain( $domain, APPLICATION_PATH . "/locale" );
	T_bind_textdomain_codeset( $domain, 'UTF-8' );
	T_textdomain( $domain );

	if( ! isset( $search ) )
	{
		echo i18n2html( "Nothing to search !!!" );
		return 0;
	}

	ob_start();

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
		echo  i18n2html( "Type is not valid" ) . " : " . $COLLECTIONS[$collectionID]['type'];
		return 127;
	}

	if( ! isset( $COLLECTIONS[$collectionID] ) )
	{
		echo i18n2html( "Unknown collection ID" ) . " : $collectionID";
		return 127;
	}
	if ( ! $collection->setFilename( $COLLECTIONS[$collectionID]['file'] ) )
	{
		echo $COLLECTIONS[$collectionID]['file'] . ' : ' . i18n2html( 'File does not exists or is not readable !' );
		return 127;
	}
	if ( ! $collection->setThumbsDir( $COLLECTIONS[$collectionID]['thumbs-dir'] ) )
	{
		echo $COLLECTIONS[$collectionID]['thumbs-dir'] . ' : ' . i18n2html( 'Directory does not exists or is not readable !' );
		return 127;
	}

	if ( ! $collection->load() )
	{
		echo i18n2html( "Could not load collection's item !" );
		return 127;
	}
	$collection->sort( $sort );
	$items = $collection->getItems();
	$occurencies = array();

	$combinations = getAllCombinations( $search );
	foreach( $combinations as $combination )
	{
		$regex = getRegex( removeAccents( $combination ) );

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
	}

?>
<form class="big">
<?php if( count( $occurencies ) ):?>
	<ul class="search">
	<?php foreach( $occurencies as $item ):?>
		<li class="search">
			<span class="icon icon-video"></span>
			<a href="./index.php?query=details&amp;collection=<?php echo $collectionID;?>&amp;item=<?php echo $item->id;?>&amp;lang=<?php echo $language;?>" class="item" id="item-<?php echo $item->id;?>">
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
	<span class="error"><?php echo i18n2html( 'No item match the query !' );?> ("<?php echo $search;?>")</span>
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
