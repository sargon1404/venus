<?php
/**
* The Html Markup Class
* @package Venus
*/

namespace Venus\Markup;

/**
* The Html Markup Class
*/
class Html extends Markup implements MarkupInterface
{
	/**
	* @var array $allowed_tags The list of allowed tags
	*/
	protected $allowed_tags = [];

	/**
	* @var array $allowed_attributes The list of allowed attributes
	*/
	protected $allowed_attributes = [];

	/**
	* @see \Venus\Markup\MarkupInterface::parse()
	* {@inheritDoc}
	*/
	public function parse(string $text) : string
	{
		$this->is_filtered = true;

		$text = str_replace("\r\n", "\n", $text);

		$this->buildAllowed();

		$text = $this->app->text->filter($text, $this->getAllowedAttributes(), $this->getAllowedTags());

		if ($this->url_nofollow) {
			$parser = $this->getParserObj();
			$text = $parser->parseNofollow($text);
		}

		$text = nl2br($text);

		return $this->app->plugins->filter('markupHtmlParse', $text, $this);
	}

	/**
	* Builds the list of allowed tags & attributes
	*/
	protected function buildAllowed()
	{
		$this->allowed_tags = [];
		$this->allowed_attributes = [];

		if ($this->canParse('b')) {
			$this->addAllowed(['b', 'strong']);
		}
		if ($this->canParse('i')) {
			$this->addAllowed(['i']);
		}
		if ($this->canParse('u')) {
			$this->addAllowed(['u']);
		}
		if ($this->canParse('del')) {
			$this->addAllowed(['del']);
		}

		if ($this->canParse('h')) {
			for ($i = 1; $i <= 6; $i++) {
				$this->addAllowed(["h{$i}"]);
			}
		}

		if ($this->canParse('table')) {
			$this->addAllowed(['table', 'tbody', 'tr', 'td']);
		}
		if ($this->canParse('hr')) {
			$this->addAllowed(['hr', 'br']);
		}

		if ($this->canParse('list')) {
			$this->addAllowed(['ul', 'ol', 'li']);
		}

		if ($this->canParse('img')) {
			$this->addAllowed(['img'], ['img.src', 'img.alt']);
		}

		if ($this->canParse('url')) {
			$this->addAllowed(['a'], ['a.target', 'a.rel', 'a.href', 'a.title']);
		}
		if ($this->canParse('code')) {
			$this->addAllowed(['code']);
		}
		if ($this->canParse('quote')) {
			$this->addAllowed(['blockquote', 'cite']);
		}
	}

	/**
	* Returns the list of allowed tags
	* @return array
	*/
	protected function getAllowedTags() : ?array
	{
		if (!$this->allowed_tags) {
			return null;
		}

		return $this->allowed_tags;
	}

	/**
	* Resets the comma delimited list of allowed attributes
	* @return string
	*/
	protected function getAllowedAttributes() : string
	{
		return implode(',', $this->allowed_attributes);
	}

	/**
	* Adds tags & attributes on the allowed list
	* @param array $tags The tags to add
	* @param array $attributes The attributes to add
	*/
	protected function addAllowed(array $tags, array $attributes = [])
	{
		foreach ($tags as $tag) {
			$this->allowed_tags[] = $tag;
		}

		foreach ($attributes as $attribute) {
			$this->allowed_attributes[] = $attribute;
		}
	}

	/**
	* @see \Venus\Markup\MarkupInterface::convert()
	* {@inheritDoc}
	*/
	public function convert(string $text) : string
	{
		return $this->app->plugins->filter('markupHtmlConvert', $text, $this);
	}

	/**
	* @see \Venus\Markup\MarkupInterface::quote()
	* {@inheritDoc}
	*/
	public function quote(string $text, string $cite = '') : string
	{
		$code = '<blockquote>';
		if ($cite) {
			$code.= '<cite>' . $cite . '</cite>';
		}

		$code.= $text . '</blockquote>';

		return $code;
	}
}
