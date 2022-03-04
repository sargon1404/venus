<?php
/**
* The Output Trait
* @package Venus
*/

namespace Venus\Document;

/**
* The Output Trait
* Outputs an object's common properties
*/
trait OutputTrait
{
	/**
	* Outputs the ID
	*/
	public function outputId()
	{
		echo App::e($this->getId());
	}

	/**
	* Outputs the title
	*/
	public function outputTitle()
	{
		echo App::e($this->title);
	}

	/**
	* Outputs the text
	*/
	public function outputText()
	{
		echo $this->text;
	}

	/**
	* Outputs the url
	*/
	public function outputUrl()
	{
		echo App::e($this->url);
	}

	/**
	* Outputs the description
	*/
	public function outputDescription()
	{
		echo $this->description;
	}

	/**
	* Outputs the html code
	*/
	public function outputHtml()
	{
		echo $this->html;
	}

	/**
	* Outputs the content
	*/
	public function outputContent()
	{
		echo $this->content;
	}

	/**
	* Outputs the read more text
	*/
	public function outputReadMoreText()
	{
		echo $this->text_read_more;
	}

	/**
	* Outputs the read more link
	* @param string $extra Extra code to be placed inside <a>, if any
	*/
	public function outputReadMoreLink(string $extra = '')
	{
		echo '<a href="' . App::e($this->url) . '"' . $this->link_attributes . $extra . '>' . App::__('read_more') . '</a>';
	}

	/**
	* Outputs the link
	* @param bool $show_image If true will show the image
	* @param bool $show_title If true will show the title
	* @param string $image_type The image's type: image/thumb/small_thumb
	* @param string $extra Extra code to be placed inside <a> , if any
	*/
	public function outputLink(bool $show_image = false, bool $show_title = true, ?string $image_type = null, string $extra = '')
	{
		echo '<a href="' . App::e($this->url) . '"' . $this->link_attributes . $extra . '>';

		if ($show_image) {
			$this->outputImage($image_type);
		}
		if ($show_title) {
			$this->outputTitle();
		}

		echo '</a>';
	}

	/**
	* Outputs the link properties
	*/
	public function outputLinkAttributes()
	{
		echo $this->link_attributes;
	}

	/**
	* Formats and returns a timestamp
	* @param bool $use_lang If true will output the timestamp using a language string. Eg: 'On xxxx:xx:xx'
	* @param int $timestamp The timestamp to output. If empty, this->timestamp is used
	* @param string $lang_index The lang index to use
	* @return string The formated timestamp
	*/
	protected function getTimestamp(bool $use_lang, int $timestamp, string $lang_index) : string
	{
		$date = $this->app->format->timestamp($timestamp);
		if ($use_lang) {
			$date = App::__($lang_index, '{TIMESTAMP}', $date);
		}

		return $date;
	}

	/**
	* Outputs a timestamp
	* @param bool $use_lang If true will output the timestamp using a language string. Eg: 'On xxxx:xx:xx'
	*/
	public function outputTimestamp(bool $use_lang = false)
	{
		if (empty($this->timestamp)) {
			return;
		}

		echo $this->getTimestamp($use_lang, $this->timestamp, 'item_timestamp_date');
	}

	/**
	* Outputs the creation date
	* @param bool $use_lang If true will output the date using a language string. Eg: 'On xxxx:xx:xx'
	*/
	public function outputCreatedDate(bool $use_lang = false)
	{
		if (empty($this->created_timestamp)) {
			return;
		}

		echo $this->getTimestamp($use_lang, $this->created_timestamp, 'item_created_date');
	}

	/**
	* Outputs the last modified date
	* @param bool $use_lang If true will output the date using a language string. Eg: 'On xxxx:xx:xx'
	*/
	public function outputModifiedDate(bool $use_lang = true)
	{
		if (empty($this->modified_timestamp)) {
			return;
		}

		echo $this->getTimestamp($use_lang, $this->modified_timestamp, 'item_modified_date');
	}

	/**
	* Outputs the start date
	* @param bool $use_lang If true will output the date using a language string. Eg: 'On xxxx:xx:xx'
	*/
	public function outputStartDate(bool $use_lang = false)
	{
		if (empty($this->start_date)) {
			return;
		}

		echo $this->getTimestamp($use_lang, $this->start_date, 'item_start_date');
	}

	/**
	* Alias for output_start_date
	* @param bool $use_lang If true will output the date using a language string. Eg: 'On xxxx:xx:xx'
	*/
	public function outputDate(bool $use_lang = false)
	{
		$this->outputStartDate($use_lang);
	}

	/**
	* Outputs the end date
	* @param bool $use_lang If true will output the date using a language string. Eg: 'On xxxx:xx:xx'
	*/
	public function outputEndDate(bool $use_lang = false)
	{
		if (empty($this->end_date)) {
			return;
		}

		echo $this->getTimestamp($use_lang, $this->end_date, 'item_end_date');
	}

	/**
	* Outputs the pagination
	*/
	public function outputPagination()
	{
		echo $this->app->ui->buildPagination($this->pagination_url, $this->pages_count, $this->items_per_page, '', true);
	}
}
