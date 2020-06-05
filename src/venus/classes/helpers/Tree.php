<?php
/**
* The Tree Class
* @package Venus
*/

namespace Venus\Helpers;

/**
* The Tree Class
* Items manipulation class
*/
class Tree
{
	use \Venus\AppTrait;

	/**
	* @var string $prefix String used as prefix for items
	*/
	public string $prefix = '---';

	/**
	* Returns the prefix corresponding to a node of level $level
	* @param int $level The item's level
	* @return string The prefix of the item
	*/
	public function getPrefix(string $level) : string
	{
		if (!$level) {
			return '';
		}

		return str_repeat($this->prefix, $level);
	}

	/**
	* Returns the position,level,lineage of an item
	* @param int $item_id The id of the item
	* @param array $items All the defined items. Must be in the item_id => item_parent format. The array should be sorted by parent,order
	* @return array Returns item's position,level,lineage
	*/
	public function getPosition(int $item_id, array $items) : array
	{
		$sort_array = [];
		$item_position = [];
		if (!$items || count($items) == 1) {
			return [1, 0, $item_id];
		}

		$position = 1;
		foreach ($items as $id => $item_parent) {
			if ($item_parent) {
				continue;
			}

			$level = 0;

			$sort_array[$id] = [$position, $level, $id];
			if ($id == $item_id) {
				$item_position = $sort_array[$id];
				break;
			}

			$position++;

			unset($items[$id]);

			if (!$this->getPositionCallback($item_id, $item_position, $id, $position, $level + 1, $sort_array, $items, [$id])) {
				break;
			}
		}

		return $item_position;
	}

	/**
	* Callback for get_position
	* @internal
	*/
	protected function getPositionCallback(int &$item_id, int &$item_position, int $current_id, int &$position, int $level, array &$sort_array, array &$items, array $lineage) : bool
	{
		foreach ($items as $id => $item_parent) {
			if ($current_id != $item_parent) {
				continue;
			}

			$clineage = array_merge($lineage, [$id]);

			$sort_array[$id] = [$position, $level, implode('-', $clineage)];
			if ($id == $item_id) {
				$item_position = $sort_array[$id];
				return false;
			}

			$position++;

			unset($items[$id]);

			if (!$this->getPositionCallback($item_id, $item_position, $id, $position, $level + 1, $sort_array, $items, $clineage)) {
				return false;
			}
		}

		return true;
	}

	/**
	* Returns the order value of a new item, based on which value (first/last/after item x) is selected. Will also update the order of the other items from $table
	* @param int $order The order value for the new item
	* @param string $table The name of the database table
	* @param string $item_order_field The name of the order field from $table
	* @param int $current_parent The parent of the new item
	* @param string $item_parent_field The name of the field containing the item's parent
	* @param bool $current_parent_is_int If true $current_parent will be sanitized as int
	* @param bool $reverse_order The items are in reverse order
	* @param string $extra_where_sql Extra sql where code
	* @return int The item's order
	*/
	public function updateOrder(int $order, string $table, string $item_order_field, int $current_parent = 0, string $item_parent_field = '', bool $current_parent_is_int = true, bool $reverse_order = false, string $extra_where_sql = '') : int
	{
		var_dump("To Do");
		die;
		// The item's order is actually top->down,with the last item's order=1 and first item's order=max(order).
		// The reason is,when we insert an item as the first item,as usually happens,when a new item is created,we update just it's data to max(position).
		// If we set it's order as 1,we'll have to update the order of all existing items to position+1

		if ($current_parent_is_int) {
			$current_parent = (int)$current_parent;
		}

		$item_order = 0;
		$parent_where = '';
		$parent_where2 = '';

		if ($item_parent_field) {
			$parent_where = "WHERE `{$item_parent_field}` = {$current_parent}";
			$parent_where2 = " AND `{$item_parent_field}` = {$current_parent}";
			if ($extra_where_sql) {
				$parent_where .= ' AND ' . $extra_where_sql;
				$parent_where2 .= ' AND ' . $extra_where_sql;
			}
		} else {
			if ($extra_where_sql) {
				$parent_where .= ' WHERE ' . $extra_where_sql;
				$parent_where2 .= ' AND ' . $extra_where_sq2;
			}
		}

		if ($reverse_order) {
			if ($order == 0) {
				$order = -1;
			} elseif ($order == -1) {
				$order = 0;
			}
		}

		if ($order == 0) { //first
			//we just set the item's position to max(order). No further updates required
			$item_order = (int)$this->app->db->selectResult($table, "MAX(`{$item_order_field}`)", $parent_where) + 1;
		} elseif ($order == -1) { //last
			//the order of the item is -1,update the order of the other items to order=order+1
			$item_order = 1;
			$this->app->db->writeQuery("UPDATE {$table} SET `{$item_order_field}` = `{$item_order_field}` + 1 {$parent_where}");
		} else {	//after the element with current order $order
			if ($reverse_order) {
				$item_order = $order + 1;
				$sql = "UPDATE {$table} SET `{$item_order_field}` = `{$item_order_field}` + 1 WHERE `{$item_order_field}` > {$order} {$parent_where2}";
			} else {
				$item_order = $order;
				$sql = "UPDATE {$table} SET `{$item_order_field}` = `{$item_order_field}` + 1 WHERE `{$item_order_field}` >= {$order} {$parent_where2}";
			}

			$this->app->db->writeQuery($sql);
		}

		return $item_order;
	}

	/**
	* Creates a tree; arranges the items in a tree hierarchy
	* @param array $items Array with all the defined items. Must be in the format $id => $item_data, ordered by item_position
	* @param string $item_title_field The name of the field containing the item's title
	* @param string $item_level_field The name of the field containing the item's level
	* @return array Returns the created tree
	*/
	public function create(array $items, string $item_title_field, string $item_level_field) : array
	{
		$options = [];
		if (!$items) {
			return $options;
		}

		foreach ($items as $id => $item) {
			$level = $item[$item_level_field];

			$options[$id] = $this->getPrefix($level) . $item[$item_title_field];
		}

		return $options;
	}

	/**
	* Returns a tree; arranges the items in a tree hierarchy
	* @param array $items Array with all the defined items.Must be in the $id => $item_data format
	* @param string $item_parent_field The name of the field containing the item's parent
	* @param array $item_children_field The name of the field which will be filled with the item's children
	* @return array Returns the created tree
	*/
	public function get(array $items, string $item_parent_field, string $item_children_field = 'children') : array
	{
		$tree_items = [];

		foreach ($items as $id => $item) {
			if ($item[$item_parent_field] != 0) {
				continue;
			}

			$children = $this->getChildren($items, $id, $item_parent_field, $item_children_field);

			$tree_items[$id] = $item;
			$tree_items[$id][$item_children_field] = $children;
		}

		return $tree_items;
	}

	/**
	* Returns the children of an item
	* @param array $items The items list
	* @param int $id The id of the item to return the children for
	* @param string $item_parent_field The name of the field containing the item's parent
	* @param array $item_children_field The name of the field which will be filled with the item's children
	* @return array The children
	*/
	protected function getChildren(array &$items, int $id, string $item_parent_field, string $item_children_field) : array
	{
		$children = [];

		foreach ($items as $cid => $item) {
			if ($item[$item_parent_field] != $id) {
				continue;
			}

			$children2 = $this->getChildren($items, $cid, $item_parent_field, $item_children_field);

			$children[$cid] = $item;
			$children[$cid][$item_children_field] = $children2;
		}

		return $children;
	}

	/**
	* Returns the sorted items as position,level,lineage
	* @param array $items All the defined items. The format must be item_id => item_parent. The array should be sorted by parent,order
	* @return array The sorted array
	*/
	public function sort(array $items) : array
	{
		if (!$items) {
			return [];
		}

		$position = 1;
		$sort_array = [];

		foreach ($items as $id => $item_parent) {
			if ($item_parent) {
				continue;
			}

			$level = 0;
			$sort_array[$id] = [$position, $level, $id];
			$position++;

			unset($items[$id]);

			$this->sortCallback($id, $position, $level + 1, $sort_array, $items, [$id]);
		}

		return $sort_array;
	}

	/**
	* Callback for sort
	* @internal
	*/
	protected function sortCallback(int $current_id, int &$position, int $level, array &$sort_array, array &$items, array $lineage)
	{
		foreach ($items as $id => $item_parent) {
			if ($current_id != $item_parent) {
				continue;
			}

			$clineage = array_merge($lineage, [$id]);
			$sort_array[$id] = [$position, $level, implode('-', $clineage)];
			$position++;

			unset($items[$id]);

			$this->sortCallback($id, $position, $level + 1, $sort_array, $items, $clineage);
		}
	}

	/**
	* Returns all the items and the subitems. The return array will also contain the $ids
	* @param array $ids Array with the ids to return the subitems for.
	* @param array $items All the defined items. The format should be item_id=>item_parent.The array should be sorted by parent,order
	* @return array The items and subitems
	*/
	public function getItemAndSubitems(array $ids, array $items) : array
	{
		if (!$items) {
			return [];
		}

		$return_array = $ids;

		foreach ($items as $id => $item_parent) {
			if ($item_parent) {
				continue;
			}

			unset($items[$id]);

			$this->getItemAndSubitemsCallback($id, $return_array, $items);
		}

		return $return_array;
	}

	/**
	* Callback for get_item_and_subitems
	* @internal
	*/
	protected function getItemAndSubitemsCallback(int $current_id, array &$return_array, array &$items)
	{
		foreach ($items as $id => $item_parent) {
			if ($current_id != $item_parent) {
				continue;
			}

			if (in_array($item_parent, $return_array)) {
				if (!in_array($id, $return_array)) {
					$return_array[] = $id;
				}
			}

			unset($items[$id]);

			$this->getItemAndSubitemsCallback($id, $return_array, $items);
		}
	}

	/**
	* Returns the neighbours(previous and next items from the array) of $item_id
	* @param int $item_id The id of the item to return the position for
	* @param array $items The format must be item_id = >values. The array should be sorted by parent,order
	* @param array $previous The previous item will be copied here (out)
	* @param array &$next The next item will be copied here (out)
	*/
	public function getItemNeighbours(int $item_id, array $items, array &$previous, array &$next)
	{
		if (!$items) {
			return;
		}

		$previous = [];
		$previous_temp = [];
		$next = [];
		$found = false;

		foreach ($items as $id => $arr) {
			if ($item_id == $id) {
				$found = true;
				$previous = $previous_temp;
			}
			if (!$found) {
				$previous_temp = $arr;
			} else {
				if ($item_id == $id) {
					continue;
				} else {
					$next = $arr;
					break;
				}
			}
		}
	}
}
