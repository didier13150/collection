<?php
class Item
{
	public $id;
	public $thumbnail;
	public $title;
	public $rating = 0;
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

class VideoItem extends Item
{
	public $synopsis;
	public $actors = array();

	public function getJoinActorList()
	{
		$list = array();
		foreach( $this->actors as $actors )
		{
			$data = $actors['artist'];
			if ( $actors['character'] != "" )
			{
				$data .= "(" . $actors['character'] . ")";
			}
			$list[] = $data;
		}
		return implode( ', ', $list);
	}
}

class FilmItem extends VideoItem
{
	public $originalTitle;
	public $date;
	public $duration;
	public $director;
	public $country;
	public $trailer;

	public static function parseDuration( $raw )
	{
		$matches = null;
		$duration = 0;

		if ( preg_match( '/^(\d+)$/', $raw, $matches ) )
		{
			$duration = intval( $matches[1] );
		}
		elseif ( preg_match( '/^(\d+)\s*mins?$/i', $raw, $matches ) )
		{
			$duration = intval( $matches[1] );
		}
		elseif ( preg_match( '/^(\d+)\s*h\s*(\d+)\s*m?i?n?s?/i', $raw, $matches ) )
		{
			$duration = intval( $matches[1] ) * 60 + intval( $matches[2] );
		}
		return $duration;
	}
}

class SeriesItem extends VideoItem
{
	public $episodes = array();

	public function getEpisodeList()
	{
		$list = array();
		foreach( $this->episodes as $episode )
		{
			$data = $episode['id'] . ' - ' . $episode['name'];
			$list[] = $data;
		}
		return $list;
	}
}

?>