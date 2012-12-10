<?php
	include_once( 'class/collection.class.php' );
	include_once( 'class/item.class.php' );
	include_once( 'settings.php' );

	$collectionID = 0;
	$page = 0;
	$sort = 'title';

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

	ob_start();

	$collection->sort( $sort );
	$items = $collection->getItems( $start, $NB_ITEM_PER_PAGE );
	$maxItem = $collection->count();
?>
<script>
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
<div class="borded">
	<span>
		<span class="bold">Page</span>
		<?php for( $i = 0 ; $i <= intval( $maxItem / $NB_ITEM_PER_PAGE ) ; $i++ ):?>
			<?php if ( $i != 0 ) echo "|";?>
				<?php if( ( $i * $NB_ITEM_PER_PAGE ) == $start ):?>
					<span class="page-selected">&nbsp;<?php echo $i;?>&nbsp;</span>
				<?php else:?>
					&nbsp;<a href="index.php?query=collection&amp;collection=<?php echo $collectionID;?>&amp;page=<?php echo $i;?>" class="<?php echo "page";?>" id="page-<?php echo $i;?>"><?php echo $i;?>&nbsp;</a>
			<?php endif;?>
		<?php endfor;?>
	</span>
	<span class="right collection-data">
		<span class="bold"><?php echo $maxItem;?></span>
		&eacute;l&eacute;ment<?php if ( count( $items ) > 1 ) echo 's';?> dans la collection
	</span>
</div>
<div id="sort-container">
	<label for="sort-fields">Class&eacute;s par </label>
	<select id="sort-fields">
		<optgroup label="Options générales">
			<option value="id"<?php if ( $sort == 'id' ) echo ' selected';?>>Identifiant</option>
			<option value="title"<?php if ( $sort == 'title' ) echo ' selected';?>>Titre</option>
		</optgroup>
		<?php if( $COLLECTIONS[$collectionID]['type'] == 'film' ):?>
			<optgroup label="Options pour les films">
				<option value="originalTitle"<?php if ( $sort == 'originalTitle' ) echo ' selected';?>>Titre original</option>
				<option value="year"<?php if ( $sort == 'year' ) echo ' selected';?>>Ann&eacute;e</option>
				<option value="duration"<?php if ( $sort == 'duration' ) echo ' selected';?>>Dur&eacute;e</option>
			</optgroup>
		<?php endif;?>
	</select>
</div>
<div id="thumbnails">
	<?php foreach( $items as $id => $item ):?>
	<a href="index.php?query=details&amp;collection=<?php echo $collectionID;?>&amp;item=<?php echo $item->id;?>" class="item thumbnail-container" id="item-<?php echo $item->id;?>">
	<img class="thumbnail-<?php echo $THUMB_SIZE;?>" src="<?php echo $item->getThumbnail( $THUMB_SIZE );?>" alt="<?php echo $item->title;?>" title="<?php echo $item->title;?>">
	</a>
	<?php endforeach;?>
</div>
<?php
	$html = ob_get_clean();
	echo $html;
	return 0;
?>