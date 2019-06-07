/**
* The Tooltips Class
* @author Venus-CMS
* @property {bool} are_enabled True if tooltips are enabled
*/
class VenusTooltips
{

	constructor()
	{
		this.are_enabled = false;
	}

	/**
	* Enables the tooltips
	* @param {string|object} [element] Optional element over which the tooltips will be shown. If not specified, document is used
	* @return {this}
	*/
	enable(element)
	{
		if(!element)
		{
			if(this.are_enabled)
				return this;
		}

		this.are_enabled = true;

		var self = this;
		venus.ready(function(){

			self.init(element);

		});

		return this;
	}

	/**
	* Inits the tooltips
	* Adds onmouseover/onmouseout for all elements with the data-tooltip/data-tooltip-static/data-tooltip-fixed attributes
	* @param {string|object} [element] Optional element over which the tooltips will be shown. If not specified, document is used
	* @private
	*/
	init(element)
	{
		venus.getSelector('[data-tooltip]').each(function(){

			jQuery(this).off('mouseover').mouseover(function(event){

				venus.tooltip.show(this);
				event.stopPropagation();

			});

		});
	}

}

/**
* The Tooltip Class
* @author Venus-CMS
*/
class VenusTooltip
{

	constructor()
	{
		this.obj = null;

		this.init();
	}

	/**
	* Appends the required html code to the <body>
	* @private
	*/
	init()
	{
		var self = this;

		venus.ready(function() {

			var html = '<div id="tooltip"></div>';

			jQuery('body').append(html);

			self.obj = venus.get('tooltip').hide();
			self.obj.click(function(event){
				event.stopPropagation();
			});

		});
	}

	/**
	* Displays a tooltip
	* Tooltip attributes, which control how the tooltip is shown:
	* data-class : If specified, will apply the class to the tooltip
	* data-text : If specified the text of the tooltip will be read from the inner html of element with id=data-text
	* @param {string|object} element The element to which the tooltip is attached. Usually 'this'
	* @param {string} text If specified, will be used as the tooltip's text
	* @param {string} [class_name] If specified, will apply will apply the class to the tooltip
	*/
	show(element, text, class_name)
	{
		if(!this.obj)
			return;

		var obj = venus.get(element);
		this.obj.attr('title', '');
		this.obj.attr('class', this.getClass(obj, class_name));
		this.obj.html(this.getText(obj, text));

		var pos = venus.getPosition(element, this.obj);
		this.obj.css({left: pos.x + 'px', top: pos.y + 'px'});

		//hide the tooltip if we hover out from the element
		var self = this;
		obj.mouseout(function(){

			self.hide();
			obj.off('mouseout');

		});

		this.showObj();
	}

	/**
	* @private
	*/
	showObj()
	{
		this.obj.show();
	}

	/**
	* Returns the text to be shown in the tooltip
	* @param {string|object} element The element to which the tooltip is attached
	* @param {string} text if specified, will be used as the tooltip's text
	* @return {string} The tooltip's text
	* @private
	*/
	getText(element, text)
	{
		if(text)
			return text;

		//do we have a data-tooltip-id attribute? If so, use it to get the text of the tooltip
		var text_id = element.attr('data-text');
		if(text_id)
			return venus.get(text_id).html();

		return element.attr('data-tooltip');
	}

	/**
	* Returns the class to be applied to the tooltip, if any
	* @param {string|object} element The element to which the tooltip is attached
	* @param {string} class_name If specified, will be used as the tooltip's class
	* @return {string} The tooltip's class
	* @private
	*/
	getClass(element, class_name)
	{
		if(!class_name)
			class_name = element.attr('data-class');

		class_name = class_name || '';

		return 'tooltip ' + class_name;
	}

	/**
	* Hides the tooltip
	*/
	hide()
	{
		if(!this.obj)
			return false;

		this.hideObj();
	}

	/**
	* @private
	*/
	hideObj()
	{
		this.obj.hide();
	}

}