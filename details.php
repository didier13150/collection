<?php
	require_once( 'gettext/gettext.inc' );
	include_once( 'class/collection.class.php' );
	include_once( 'class/item.class.php' );
	include_once( 'include/functions.php' );
	include_once( 'settings.php' );

	defined( 'APPLICATION_PATH' ) || define( 'APPLICATION_PATH', dirname(__FILE__) );

	// GCStar item id start at 1
	$itemID = 1;
	$collectionID = 0;
	$language = $DEFAULT_LANGUAGE;

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
	if ( isset( $_GET['lang'] ) and ! empty( $_GET['lang'] ) )
	{
		$language = $_GET['lang'];
	}
	elseif ( isset( $_SESSION['language'] ) and ! empty( $_SESSION['language'] ) )
	{
		$language = $_SESSION['language'];
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
	$item = $collection->getItem( $itemID );
	if ( ! $item )
	{
		echo i18n2html( "Could not load collection's item !" );
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
		<label for="detail-ID" class="fixed-length"><?php echo i18n2html( 'Identifiant' );?></label>
		<input type="text" id="detail-ID" value="<?php echo $item->id;?>">
		<label for="detail-title" class="fixed-length"><?php echo i18n2html( 'Title' );?></label>
		<input type="text" id="detail-title" value="<?php echo htmlentities( $item->title );?>">
		<?php if( $COLLECTIONS[$collectionID]['type'] == 'film' ):?>
			<?php if( $item->originalTitle != "" and $item->originalTitle != $item->title ):?>
			<label for="detail-orig-title" class="fixed-length"><?php echo i18n2html( 'Original Title' );?></label>
			<input type="text" id="detail-orig-title" value="<?php echo htmlentities( $item->originalTitle );?>">
			<?php endif;?>
			<?php if( $item->year ):?>
			<label for="detail-date" class="fixed-length"><?php echo i18n2html( 'Date' );?></label>
			<input type="text" id="detail-date" value="<?php echo htmlentities( $item->year );?>">
			<?php endif;?>
			<?php if( $item->duration ):?>
			<label for="detail-duration" class="fixed-length"><?php echo i18n2html( 'Duration' );?></label>
			<input type="text" id="detail-duration" value="<?php echo htmlentities( $item->duration );?> min">
			<?php endif;?>
			<?php if( $item->director != "" ):?>
			<label for="detail-director" class="fixed-length"><?php echo i18n2html( 'Director' );?></label>
			<input type="text" id="detail-director" value="<?php echo htmlentities( $item->director );?>">
			<?php endif;?>
		<?php endif;?>
		<div id="detail-rating" >
			<span class="left"><?php echo i18n2html( 'Rating' );?></span>
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
		<div class="synopsis"><?php echo i18n2html( 'Actors' );?>: <?php echo $item->getJoinActorList();?></div>
		<?php endif;?>
		<?php if( $COLLECTIONS[$collectionID]['type'] == 'series' ):?>
			<div class="synopsis"><?php echo i18n2html( 'Series episode list' );?>Liste des &eacute;pisodes
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
	$('#popup-title').html( 'Collection - ' + detailsTitle + ' - ' + '<?php echo htmlentities( $item->title );?>' );
</script>
<?php
	$html = ob_get_clean();
	echo $html;
	return 0;
?>