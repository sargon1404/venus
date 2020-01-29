<?php
/**
* The Image Trait
* @package Venus
*/

namespace Venus\Document;

use Venus\App;

/**
* The Image Trait
* Prepares the image of a content element.
* The protected static $image_type and $image_subdir properties must be declared in the classes using this trait
* @property string $image_type The image type. Eg: image/thumb/small_thumb
* @property bool $image_subdir If true, the image is considered to be located in a subdir
*/
trait ImageTrait
{

	/**
	* @var string $image_type The image type. Eg: image/thumb/small_thumb
	*/
	//protected static $image_type = '';
	/**
	* @var bool _image_subdir If true, the image is considered to be located in a subdir
	*/
	//protected static $image_subdir = false;

	/**
	* @var string $image_url The image's url
	*/
	public string $image_url = '';

	/**
	* @var int $image_url The image's width
	*/
	public int $image_width = 0;

	/**
	* @var int $image_url The image's height
	*/
	public int $image_height = 0;

	/**
	* @var string $image_wh The image's width&height html code
	*/
	public string $image_wh = '';

	/**
	* @var string $image_html The image's html code
	*/
	public string $image_html = '';

	/**
	* Returns the object's image type
	*/
	public function getImageType() : string
	{
		return static::$image_type;
	}

	/**
	* Sets the image's type and builds the image properties
	* @param string $type The image's type: image/thumb/small_thumb
	* @return $this
	*/
	public function setImageType(string $type)
	{
		if (empty($this->image)) {
			return;
		}

		$this->image_url = $this->getImageUrl($type);
		$this->image_width = $this->getImageWidth($type);
		$this->image_height = $this->getImageHeight($type);
		$this->image_wh = $this->getImageWh($type);
		$this->image_html = $this->getImageHtml($type);

		return $this;
	}

	/**
	* Inits the image proprties of an object
	*/
	protected function prepareImage()
	{
		if (!isset($this->image)) {
			return;
		}

		$this->setImageType($this->getImageType());
	}

	/**
	* Returns the image's url
	* @param string $type The image's type: image/thumb/small_thumb
	* @return string The image's url
	*/
	public function getImageUrl(string $type = 'image') : string
	{
		if (!$this->image) {
			return '';
		}

		return $this->app->images_url . $this->getImageDir() . $this->getImagePrefix($type) . rawurlencode(basename($this->image));
	}

	/**
	* Returns the folder where the image is stored
	* @return string
	*/
	protected function getImageDir() : string
	{
		$dir = App::sl(rawurldecode($this->getContentDir()));

		if (static::$image_subdir) {
			return $dir . $this->app->file->getSubdir($this->image, true);
		}

		return $dir;
	}

	/**
	* Returns the image's prefix, from type
	* @param string $type The image's type: image/thumb/small_thumb
	* @return string The prefix
	*/
	protected function getImagePrefix(string $type) : string
	{
		if (!$type || $type == 'image') {
			return '';
		}

		return $type . '_';
	}

	/**
	* Returns the image's width
	* @param string $type The image's type: image/thumb/small_thumb
	* @return int The width
	*/
	public function getImageWidth(string $type = 'image') : int
	{
		return $this->app->theme->getImageWidth($this->getType(), $type);
	}

	/**
	* Returns the image's height
	* @param string $type The image's type: image/thumb/small_thumb
	* @return int The height
	*/
	public function getImageHeight(string $type = 'image') : int
	{
		return $this->app->theme->getImageHeight($this->getType(), $type);
	}

	/**
	* Returns the width/height attributes of an <img>. Eg: width="100" height="50"
	* @param string $type The image's type: image/thumb/small_thumb
	* @return string
	*/
	public function getImageWh(string $type = 'image') : string
	{
		if (!$this->image) {
			return '';
		}

		return $this->app->html->imgWh($this->getImageWidth($type), $this->getImageHeight($type));
	}

	/**
	* Returns the <img> code of the image
	* @param string $type The image's type: image/thumb/small_thumb
	* @return string
	*/
	public function getImageHtml(string $type = '') : string
	{
		if (!$this->image) {
			return '';
		}

		$url = $this->getImageUrl($type);
		$width = $this->getImageWidth($type);
		$height = $this->getImageHeight($type);

		return $this->app->html->img($url, $width, $height, $this->seo_image_alt, $this->seo_title);
	}

	/************OUTPUT METHODS**************************************/

	/**
	* Outputs the image
	* @param string $type The image's type: image/thumb/small_thumb
	*/
	public function outputImage(?string $type = null)
	{
		if (!$this->image) {
			return;
		}

		if ($type === null) {
			echo $this->image_html;
		} else {
			echo $this->getImageHtml($type);
		}
	}

	/**
	* Outputs the image inside the link
	* @param string $type The image's type: image/thumb/small_thumb
	*/
	public function outputImageWithLink(?string $type = null)
	{
		$this->outputLink(true, false, $type);
	}

	/**
	* Outputs the image's caption
	*/
	public function outputImageCaption()
	{
		echo App::e($this->image_caption);
	}
}
