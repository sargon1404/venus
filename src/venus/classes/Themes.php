<?php
/**
* The Themes Class
* @package Venus
*/

namespace Venus;

/**
* The Themes Class
* Container for multiple themes
*/
class Themes extends Items
{
	/**
	* @internal
	*/
	protected static string $id_name = 'tid';

	/**
	* @internal
	*/
	protected static string $table = 'venus_themes';

	/**
	* @internal
	*/
	protected static string $class = '\Venus\Theme';

	/**
	* @see \Mars\Items::load()
	* {@inheritdoc}
	*/
	public function load(array $where = [], string $order_by = '', string $order = '', int $limit = 0, int $limit_offset = 0, string $fields = 't.*') : array
	{
		$table = $this->getTable();
		$fields.= ',p.name as parent_name, p.templates as parent_templates, p.has_javascript_dir as parent_has_javascript_dir, p.params as parent_params';

		$sql = $this->db->sql->select($fields)->from($table, 't')->leftJoin($table . ' AS p', '', 't.parent = p.tid')->where($where)->orderBy($order_by, $order)->limit($limit, $limit_offset);

		return $this->loadBySql($sql);
	}
}
