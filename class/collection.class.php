<?php
class Item
{
	public $id;
	public $thumbnail;
	public $title;
	public $_isInit = false;

	public function getThumbnail( $thumb_size )
	{
		$thumb = null;
		if ( ! $this->_isInit )
		{
			return $thumb;
		}
		if ( file_exists( $this->id . '.' . $this->thumbnail . ".cache.$thumb_size.overlay" ) )
		{
			$thumb = $this->id . '.' . $this->thumbnail . ".cache.$thumb_size.overlay";
		}
		elseif ( file_exists( $this->thumbnail . ".cache.$thumb_size.overlay" ) )
		{
			$thumb = $this->thumbnail . ".cache.$thumb_size.overlay";
		}
		elseif ( file_exists( $this->thumbnail ) )
		{
			$thumb = $this->thumbnail;
		}
		return $thumb;
	}
}

class Video extends Item
{
	public $originalTitle;
	public $synopsis;
	public $date;
	public $duration;
	public $director;
	public $country;
	public $trailer;
	public $actors = array();
	public $comment;
	public $rating = 0;

	public function getActorList()
	{
		$list = array();
		foreach( $this->actors as $actor )
		{
			$data = $actor['artist'];
			if ( ! empty( $actor['character'] ) )
			{
				$data .= "( " . $actor['character'] . " )";
			}
			$list[] = $data;
		}
		return implode( ', ', $list);
	}
}

class VideoCollection extends Collection
{
	protected function getItemsFromXML( $itemXML )
	{
		$id = $itemXML['id'];
		if ( ! isset( $id ) or empty( $id ) ) return null;
		$film = new Video();
		$film->id = $id;
		$film->title = $itemXML['title'];
		$film->originalTitle = $itemXML['original'];
		$film->thumbnail = $this->_thumbs_dir . '/' . basename( $itemXML['image'] );
		$film->date = $itemXML['date'];
		$film->duration = $itemXML['time'];
		$film->director = $itemXML['director'];
		$film->country = $itemXML['country'];
		$film->synopsis = $itemXML->synopsis;
		$film->trailer = $itemXML['trailer'];
		$actors = $itemXML->actors;
		if ( isset( $actors ) and ! empty( $actors ) )
		{
			foreach ( $actors->line as $data )
			{
				$film->actors[] = array(
					'artist' => $data->col[0],
					'character' => $data->col[1],
				);
			}
		}
		$film->rating = $itemXML['rating'];
		$film->_isInit = true;
		return $film;
	}
}

abstract class Collection
{
	protected $_isCompressed = false;
	protected $_filename = null;
	protected $_thumbs_dir = null;
	protected $_items;

	public $type = array(
		"video" => 0,
		"audio" => 1,
	);

	public function setFilename( $filename )
	{
		if( @file_exists( $filename ) )
		{
			if ( preg_match( "/.*\.gz$/", $filename ) )
			{
				$this->_isCompressed = true;
			}
			$this->_filename = $filename;
			return true;
		}
		return false;
	}

	public function setThumbsDir( $dir )
	{
		if( @is_dir( $dir ) )
		{
			$this->_thumbs_dir = $dir;
			return true;
		}
		return false;
	}

	abstract protected function getItemsFromXML( $itemXML );

	public function load()
	{
		$xml = $this->getItemsXML();

		if ( ! isset( $xml ) or empty( $xml ) )
		{
			return false;
		}
		foreach( $xml as $itemXML )
		{
			$item = $this->getItemsFromXML( $itemXML );
			if ( $item ) $this->_items[] = $item;
		}
		return true;
	}

	protected function getItemsXML()
	{

		if ( ! isset( $this->_filename ) )
		{
			return null;
		}
		$items = array();
		$lines = implode( gzfile( $this->_filename ) );
		$lines = preg_replace('~(</?|\s)([a-z0-9_]+):~is', '$1$2_', $lines);
		$xml = simplexml_load_string( $lines );

		return $xml;
	}

	protected function getItemXML( $wantedID = null )
	{
		$itemXML = null;
		if ( ! isset( $this->_filename ) )
		{
			return null;
		}
		$items = array();
		$lines = implode( gzfile( $this->_filename ) );
		$lines = preg_replace('~(</?|\s)([a-z0-9_]+):~is', '$1$2_', $lines);
		$xml = simplexml_load_string( $lines );
		foreach( $xml as $itemXML )
		{
			$id = $itemXML['id'];
			if ( ! isset( $id ) or empty( $id )) continue;
			if ( $wantedID == $id ) break;
		}
		return $itemXML;
	}

	public function getItems()
	{
		return $this->_items;
	}

	public function getItem(  $id = null )
	{
		if ( isset( $id ) )
		{
			$itemXML = $this->getItemXML( $id );
			$item = $this->getItemsFromXML( $itemXML );
			if ( $item ) return $item;
		}
		return false;
	}
}
?>
