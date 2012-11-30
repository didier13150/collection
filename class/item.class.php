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

class FilmItem extends VideoItem
{
	public $originalTitle;
	public $date;
	public $duration;
	public $director;
	public $country;
	public $trailer;
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