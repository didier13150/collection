<?php
	require_once( 'gettext/gettext.inc' );
	include_once( 'class/collection.class.php' );
	include_once( 'class/item.class.php' );
	include_once( 'include/functions.php' );
	include_once( 'settings.php' );

	defined( 'APPLICATION_PATH' ) || define( 'APPLICATION_PATH', dirname(__FILE__) );

	$collectionID = 0;
	$page = 0;
	$sort = 'title';
	$language = $DEFAULT_LANGUAGE;

	if ( isset( $_GET['collection'] ) and ! empty( $_GET['collection'] ) )
	{
		$collectionID = $_GET['collection'];
	}
	elseif ( isset( $_SESSION['collection'] ) and ! empty( $_SESSION['collection'] ) )
	{
		$collectionID = $_SESSION['collection'];
	}
	if ( isset( $_GET['page'] ) and ! empty( $_GET['page'] ) )
	{
		$page = $_GET['page'];
	}
	elseif ( isset( $_SESSION['page'] ) and ! empty( $_SESSION['page'] ) )
	{
		$page = $_SESSION['page'];
	}
	if ( isset( $_GET['sort'] ) and ! empty( $_GET['sort'] ) )
	{
		$sort = $_GET['sort'];
	}
	if ( isset( $_GET['lang'] ) and ! empty( $_GET['lang'] ) )
	{
		$language = $_GET['lang'];
	}
	elseif ( isset( $_SESSION['language'] ) and ! empty( $_SESSION['language'] ) )
	{
		$language = $_SESSION['language'];
	}

	$start = $page * $NB_ITEM_PER_PAGE;
	$collection = null;
	if( $COLLECTIONS[$collectionID]['type'] == 'film' )
	{
		$collection = new FilmsCollection();
	}
	elseif( $COLLECTIONS[$collectionID]['type'] == 'series' )
	{
		$collection = new SeriesCollection();
	}

	// I18N support information here
	putenv( "LANG=$language" );
	T_setlocale( LC_ALL, $language );

	// Set the text domain as 'messages'
	$domain = 'messages';
	T_bindtextdomain( $domain, APPLICATION_PATH . "/locale" );
	T_bind_textdomain_codeset( $domain, 'UTF-8' );
	T_textdomain( $domain );

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

	ob_start();

	$collection->sort( $sort );
	$items = $collection->getItems( $start, $NB_ITEM_PER_PAGE );
	$maxItem = $collection->count();
?>
<div class="borded">
	<span>
		<span class="left bold">Page</span>
		<?php for( $i = 0 ; $i <= intval( $maxItem / $NB_ITEM_PER_PAGE ) ; $i++ ):?>
			<?php if( ( $i * $NB_ITEM_PER_PAGE ) == $start ):?>
				<span class="page-selected">&nbsp;<?php echo $i;?>&nbsp;</span>
			<?php else:?>
				&nbsp;<a href="index.php?query=collection&amp;collection=<?php echo $collectionID;?>&amp;page=<?php echo $i;?>&amp;lang=<?php echo $language;?>" class="<?php echo "page";?>" id="page-<?php echo $i;?>"><?php echo $i;?>&nbsp;</a>
			<?php endif;?>
		<?php endfor;?>
	</span>
	<span class="right collection-data">
		<span class="bold"><?php echo $maxItem;?></span>
			<?php if ( count( $items ) > 1 ):?>
				<?php echo i18n2html( 'Items on collection' );?>
			<?php else:?>
				<?php echo i18n2html( 'Item on collection' );?>
			<?php endif;?>
	</span>
</div>
<div id="sort-container">
	<label for="sort-fields"><?php echo i18n2html( 'Sort by' );?></label>
	<select id="sort-fields">
		<optgroup label="<?php echo i18n2html( 'Common options' );?>">
			<option value="id"<?php if ( $sort == 'id' ) echo ' selected';?>><?php echo i18n2html( 'Identifiant' );?></option>
			<option value="title"<?php if ( $sort == 'title' ) echo ' selected';?>><?php echo i18n2html( 'Title' );?></option>
		</optgroup>
		<?php if( $COLLECTIONS[$collectionID]['type'] == 'film' ):?>
			<optgroup label="<?php echo i18n2html( 'Film options' );?>">
				<option value="originalTitle"<?php if ( $sort == 'originalTitle' ) echo ' selected';?>><?php echo i18n2html( 'Original Title' );?></option>
				<option value="year"<?php if ( $sort == 'year' ) echo ' selected';?>><?php echo i18n2html( 'Year' );?></option>
				<option value="duration"<?php if ( $sort == 'duration' ) echo ' selected';?>><?php echo i18n2html( 'Duration' );?></option>
			</optgroup>
		<?php endif;?>
	</select>
</div>
<div id="thumbnails">
	<?php foreach( $items as $id => $item ):?>
	<a href="index.php?query=details&amp;collection=<?php echo $collectionID;?>&amp;item=<?php echo $item->id;?>&amp;lang=<?php echo $language;?>" class="item thumbnail-container" id="item-<?php echo $item->id;?>">
	<img class="thumbnail-<?php echo $THUMB_SIZE;?>" src="<?php echo $item->getThumbnail( $THUMB_SIZE );?>" alt="<?php echo $item->title;?>" title="<?php echo $item->title;?>">
	</a>
	<?php endforeach;?>
</div>
<script>
	init( false );
	modifyRef();
	collection = <?php echo $collectionID;?>;
	<?php foreach( $items as $id => $item ):?>
		bindItem( '<?php echo $item->id;?>' );
	<?php endforeach;?>
	<?php for( $i = 0 ; $i <= intval( $maxItem / $NB_ITEM_PER_PAGE ) ; $i++ ):?>
		bindPage( '<?php echo $i;?>' );
	<?php endfor;?>
	resizeArticle();
	bindSelect();
</script>
<?php
	$html = ob_get_clean();
	echo $html;
	return 0;
?>