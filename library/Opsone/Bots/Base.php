<?php
class Opsone_Bots_Base
{
	protected $_url;
	protected $_dom;
	protected $_table;
	protected $_config;

	protected $_widthImageThumbnail = 98;
	protected $_heightImageThumbnail = 147;

	public function __construct($url)
	{
		$this->_url = $url;
		$this->_table = Model_BookTable::getInstance();
		$this->_config = Zend_Registry::get('Zend_Config');
	}

	protected function _getElement($q)
	{
		$elements =  $this->_dom->query($q);
		foreach ($elements as $element) {
			return $element;
		}
	}

	protected function _formate($value)
	{
		$value = utf8_decode(trim($value));
		if (!isset($value) || empty($value)) return null;
		return $value;
	}

	protected function _file_get_contents($url)
	{
	    $ch = curl_init();

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6"); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, $url);

        $content = curl_exec($ch);

        curl_close($ch);
        return $content;
	}

	protected function _trim($string)
	{
		$string = trim($string);
		$string = str_replace("\t", " ", $string);
		$string = mb_ereg_replace("[ ]+", " ", $string);
		return $string;
	}

	protected function _replaceSpecialChar($string)
	{
	    $string = str_replace('Ã©', 'é', $string);
	    $string = str_replace('Ãª', 'ê', $string);
	    $string = str_replace('Ã®', 'î', $string);
	    $string = str_replace('Ã¨', 'è', $string);
	    $string = str_replace('Ã', 'é', $string);
	    return $string;
	}

	/******************************************************/

	protected function _alert($message=null)
	{
		if (is_null($message)) $message = 'Un changement du dom à été détècté sur '.$this->_url;

		mail('jeremy@chaufourier.fr', 'manganext problème', $message);
	}

	protected function _image($book)
	{
		if ($book->image) return;

		if ($book->image_src && @fopen($book->image_src, 'r'))
		{
			$dst = APPLICATION_PATH . '/../public/img/';
			$medium = $dst . 'medium/' . $book->id .'.jpg';
			$thumbnail = $dst . 'thumbnails/' . $book->id .'.jpg';
			@copy($book->image_src, $medium);

			$canvas = new Imagick();
			$canvas->newImage($this->_widthImageThumbnail, $this->_heightImageThumbnail, "white");
			$canvas->setFormat("jpg");

			$img = new Imagick($medium);
			$img->ThumbnailImage($this->_widthImageThumbnail, $this->_heightImageThumbnail, true, false);

			$canvas->compositeImage($img, imagick::COMPOSITE_OVER, ($canvas->getImageWidth() - $img->getImageWidth() ) / 2, ($canvas->getImageHeight() - $img->getImageHeight() ) / 2);

			$canvas->writeImage($thumbnail);
			$canvas->destroy();
			$img->destroy();

			$path = $this->_config->baseUrl . 'img/thumbnails/' . $book->id .'.jpg';

			if ($book->image != $path) {
				$book->image = $path;
				$book->save();
			}
		}
		/*else
		{
			if ($book->image_src != null) {
				$book->image_src = null;
				$book->save();
			}
		}*/
	}
}
?>