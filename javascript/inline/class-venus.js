/**
* The Venus Inline Class
* @author Venus-CMS
*/
class VenusInline
{

	constructor()
	{
		this.ready_funcs = [];
	}

	ready(func)
	{
		this.ready_funcs.push(func);
	}

}