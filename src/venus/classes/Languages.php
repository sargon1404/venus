<?php
/**
* The Languages Class
* @package Venus
*/

namespace Venus;

/**
* The Languages Class
* Container for multiple languages
*/
class Languages extends Items
{
	/**
	* @internal
	*/
	protected static $id_name = 'lid';

	/**
	* @internal
	*/
	protected static $table = 'venus_languages';

	/**
	* @internal
	*/
	protected static $class = '\Venus\Language';

	/**
	* @see \Mars\Items::load()
	* {@inheritDoc}
	*/
	public function load(array $where = [], string $order_by = '', string $order = '', int $limit = 0, int $limit_offset = 0, string $fields = 'l.*') : array
	{
		$table = $this->getTable();
		$fields.= ',p.name as parent_name, p.files as parent_files';

		$sql = $this->db->sql->select($fields)->from($table, 'l')->leftJoin($table . ' AS p', '', 'l.parent = p.lid')->where($where)->orderBy($order_by, $order)->limit($limit, $limit_offset);

		return $this->loadBySql($sql);
	}
}