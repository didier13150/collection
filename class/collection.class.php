<?php

abstract class Collection
{
	protected $_isCompressed = false;
	protected $_filename = null;
	protected $_thumbs_dir = null;
	protected $_items;

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

	public function getItems( $offset = null, $length = null )
	{
		if ( ! isset( $offset ) and ! isset( $length ) )
		{
			return $this->_items;
		}
		return array_slice( $this->_items, $offset, $length, true );
	}

	public function getItem( $id = null )
	{
		if ( isset( $id ) )
		{
			$itemXML = $this->getItemXML( $id );
			$item = $this->getItemsFromXML( $itemXML );
			if ( $item ) return $item;
		}
		return false;
	}

	public function count()
	{
		return count( $this->_items );
	}
}

/*
<item
  id="1"
  name="Mentalist - Season 1"
  series="Mentalist"
  season="1"
  part="1"
  title=""
  specialep="0"
  seen="0"
  firstaired="2008-09-23"
  time="60"
  country=""
  director=""
  producer=""
  music=""
  rating="0"
  ratingpress="0"
  age=""
  image="series/pictures/Mentalist_-_Season_1_0.jpg"
  audio=""
  subt=""
  videofile=""
  format="DVD"
  location=""
  added="22/11/2012"
  webPage="http://www.thetvdb.com/?tab=season&amp;seriesid=82459&amp;seasonid=33577&amp;lid=17##Tvdb FR"
  borrower="none"
  lendDate=""
  borrowings=""
  favourite="0"
  tags=""
 >
  <synopsis>The Mentalist raconte l'histoire de Patrick Jane, employé comme consultant indépendant pour le Bureau d'Investigation de Californie (CBI). Auparavant, il gagnait sa vie en tant que médium et assistait la police sur certaines affaires mais sa vie bascula lorsqu'il perdit les deux personnes les plus chères de sa vie en aidant la police à retrouver un tueur en série. Il utilise maintenant ses extraordinaires dons d'observation pour aider le CBI.</synopsis>
  <comment></comment>
  <genre>
   <line>
    <col>Drama</col>
   </line>
  </genre>
  <actors>
   <line>
    <col>Simon Baker</col>
    <col></col>
   </line>
  </actors>
  <episodes>
   <line>
    <col>1</col>
    <col>John le Rouge</col>
   </line>
  </episodes>
 </item>
*/
class SeriesCollection extends Collection
{
	protected function getItemsFromXML( $itemXML )
	{
		$id = $itemXML['id'];
		if ( ! isset( $id ) or empty( $id ) ) return null;
		$item = new SeriesItem();
		$item->id = $id;
		$item->title = $itemXML['series'] . ' - saison ' . $itemXML['season'];
		$item->synopsis = $itemXML->synopsis;
		$item->thumbnail = $this->_thumbs_dir . '/' . basename( $itemXML['image'] );
		$actors = $itemXML->actors;
		if ( isset( $actors ) and ! empty( $actors ) )
		{
			foreach ( $actors->line as $data )
			{
				$item->actors[] = array(
					'artist' => $data->col[0],
					'character' => $data->col[1],
				);
			}
		}
		$episodes = $itemXML->episodes;
		if ( isset( $episodes ) and ! empty( $episodes ) )
		{
			foreach ( $episodes->line as $data )
			{
				$item->episodes[] = array(
					'id' => $data->col[0],
					'name' => $data->col[1],
				);
			}
		}
		$item->rating = $itemXML['rating'];
		$item->_isInit = true;
		return $item;
	}
}
/*
<item
  id="1"
  title="Anthony Kavanagh"
  date=""
  time="80 mins"
  director=""
  country=""
  genre=",,,"
  image="spectacles/pictures/Anthony_Kavanagh_0.jpg"
  backpic=""
  original=""
  webPage="http://www.themoviedb.org/movie/62200##The Movie DB (FR)"
  seen="0"
  added="27/11/2012"
  region=""
  format="DVD"
  number="1"
  identifier="0"
  place=""
  rating="0"
  ratingpress="0"
  audio=""
  subt=""
  age=""
  video=""
  serie=""
  rank=""
  trailer=""
  borrower="none"
  lendDate=""
  borrowings=""
  favourite="0"
  tags=""
 >
  <synopsis>Anthony Kavanagh, Enregistré en Juin 2000 à l'opéra de Massy</synopsis>
  <comment></comment>
  <actors>
   <line>
    <col>Anthony Kavanagh</col>
    <col></col>
   </line>
  </actors>
 </item>
*/
class FilmsCollection extends Collection
{
	protected function getItemsFromXML( $itemXML )
	{
		$id = $itemXML['id'];
		if ( ! isset( $id ) or empty( $id ) ) return null;
		$item = new FilmItem();
		$item->id = $id;
		$item->title = $itemXML['title'];
		if ( $item->title == "" )
		{
			$item->title = $itemXML['name'];
		}
		$item->originalTitle = $itemXML['original'];
		$item->thumbnail = $this->_thumbs_dir . '/' . basename( $itemXML['image'] );
		$item->date = $itemXML['date'];
		$item->duration = $itemXML['time'];
		$item->director = $itemXML['director'];
		$item->country = $itemXML['country'];
		$item->synopsis = $itemXML->synopsis;
		$item->trailer = $itemXML['trailer'];
		$actors = $itemXML->actors;
		if ( isset( $actors ) and ! empty( $actors ) )
		{
			foreach ( $actors->line as $data )
			{
				$item->actors[] = array(
					'artist' => $data->col[0],
					'character' => $data->col[1],
				);
			}
		}
		$item->rating = $itemXML['rating'];
		$item->_isInit = true;
		return $item;
	}
}
?>
