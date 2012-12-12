<?php

	include_once( 'class/collection.class.php' );
	include_once( 'class/item.class.php' );
	include_once( 'settings.php' );

	// GCStar item id start at 1
	$itemID = 1;
	$collectionID = 0;


	if ( isset( $_GET['collection'] ) and ! empty( $_GET['collection'] ) )
	{
		$collectionID = $_GET['collection'];
	}
	elseif ( isset( $_SESSION['collection'] ) and ! empty( $_SESSION['collection'] ) )
	{
		$collectionID = $_SESSION['collection'];
	}
	if ( isset( $_GET['item'] ) and ! empty( $_GET['item'] ) )
	{
		$itemID = $_GET['item'];
	}
	elseif ( isset( $_SESSION['item'] ) and ! empty( $_SESSION['item'] ) )
	{
		$itemID = $_SESSION['item'];
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
		echo  gettext( "Type is not valid" ) . " : " . $COLLECTIONS[$collectionID]['type'];
		return 127;
	}

	if( ! isset( $COLLECTIONS[$collectionID] ) )
	{
		echo gettext( "Unknown collection ID" ) . " : $collectionID";
		return 127;
	}
	if ( ! $collection->setFilename( $COLLECTIONS[$collectionID]['file'] ) )
	{
		echo $COLLECTIONS[$collectionID]['file'] . ' : ' . gettext( 'File does not exists or is not readable !' );
		return 127;
	}
	if ( ! $collection->setThumbsDir( $COLLECTIONS[$collectionID]['thumbs-dir'] ) )
	{
		echo $COLLECTIONS[$collectionID]['thumbs-dir'] . ' : ' . gettext( 'Directory does not exists or is not readable !' );
		return 127;
	}
	$item = $collection->getItem( $itemID );
	if ( ! $item )
	{
		echo gettext( "Could not load collection's item !" );
		return 127;
	}

	ob_start();
?>
<?php if( $COLLECTIONS[$collectionID]['type'] == 'series' ):?>
<form class="big">
<?php else:?>
<form>
<?php endif;?>
	<img class="thumbnail-<?php echo $DETAIL_SIZE;?>" src="<?php echo $item->getThumbnail( $DETAIL_SIZE );?>" alt="<?php echo $item->title;?>">
	<div class="form-header">
		<label for="detail-ID" class="fixed-length"><?php echo gettext( 'ID' );?></label>
		<input type="text" id="detail-ID" value="<?php echo $item->id;?>">
		<label for="detail-title" class="fixed-length"><?php echo gettext( 'Title' );?></label>
		<input type="text" id="detail-title" value="<?php echo htmlentities( $item->title );?>">
		<?php if( $COLLECTIONS[$collectionID]['type'] == 'film' ):?>
			<?php if( $item->originalTitle != "" ):?>
			<label for="detail-orig-title" class="fixed-length"><?php echo gettext( 'Original Title' );?></label>
			<input type="text" id="detail-orig-title" value="<?php echo htmlentities( $item->originalTitle );?>">
			<?php endif;?>
			<?php if( $item->year ):?>
			<label for="detail-date" class="fixed-length"><?php echo gettext( 'Date' );?></label>
			<input type="text" id="detail-date" value="<?php echo htmlentities( $item->year );?>">
			<?php endif;?>
			<?php if( $item->duration ):?>
			<label for="detail-duration" class="fixed-length"><?php echo gettext( 'Duration' );?></label>
			<input type="text" id="detail-duration" value="<?php echo htmlentities( $item->duration );?> min">
			<?php endif;?>
			<?php if( $item->director != "" ):?>
			<label for="detail-director" class="fixed-length"><?php echo gettext( 'Director' );?></label>
			<input type="text" id="detail-director" value="<?php echo htmlentities( $item->director );?>">
			<?php endif;?>
		<?php endif;?>
		<div id="detail-rating" >
			<span class="left"><?php echo gettext( 'Rating' );?></span>
			<?php for( $i = 0 ; $i < $item->rating ; $i++ ): ?>
			<span class="icon icon-rating"></span>
			<?php endfor; ?>
			<?php for( $i = 0 ; $i < ( 10 - $item->rating ) ; $i++ ): ?>
			<span class="icon icon-rating-disable"></span>
			<?php endfor; ?>
		</div>
	</div>
	<div class="form-body">
		<div class="synopsis" id="detail-summary"><?php echo htmlentities( $item->synopsis );?></div>
		<?php if( count( $item->actors ) ):?>
		<div class="synopsis"><?php echo gettext( 'Actors' );?>: <?php echo $item->getJoinActorList();?></div>
		<?php endif;?>
		<?php if( $COLLECTIONS[$collectionID]['type'] == 'series' ):?>
			<div class="synopsis"><?php echo gettext( 'Series episode list' );?>Liste des &eacute;pisodes
				<ul class="episode">
				<?php foreach( $item->getEpisodeList() as $episode ):?>
					<li class="episode"><?php echo $episode;?></li>
				<?php endforeach;?>
				</ul>
			</div>
		<?php endif;?>
	</div>
</form>
<script>
	$('#popup-title').html( '<?php echo htmlentities( $item->title );?>' );
</script>
<?php
	$html = ob_get_clean();
	echo $html;
	return 0;
?>