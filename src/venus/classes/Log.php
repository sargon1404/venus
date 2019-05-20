<?php
/**
* The Log Class
* @package Venus
*/

namespace Venus;

/**
* The Log Class
* The system's log object
*/
class Log extends \Mars\Log
{
	/**
	* Logs an action in the database.
	* @param string $action The action to log
	* @param string $text The content to log, if any
	* @param int $id The id of the item the action is performed upon,if any
	* @return $this
	*/
	public function action(string $action, string $text = '', int $id = 0)
	{
		if (is_array($text)) {
			$text = implode(',', $text);
		}
		if ($id) {
			$text = $id . ' - ' . $text;
		}

		$insert_data =
		[
			'action' => $action,
			'text' => $text,
			'uid' => (int)$this->app->user->uid,
			'ip' => $this->app->user->ip,
			'query_string' => $_SERVER['QUERY_STRING'],
			'timestamp' => $this->app->db->unixTimestamp()
		];

		$this->app->db->insert('venus_log', $insert_data);

		return $this;
	}

	/**
	* Logs the action performed on multiple database items
	* @param string $action The action to log
	* @param string $table The database table from which the ids/title of the items will be read
	* @param array $ids Array with the ids of the items to log the action for
	* @param string $id_field The name of the id field from $table
	* @param string $title_field The name of the title field from $table
	* @return $this
	*/
	public function actionArray(string $action, string $table, $ids, string $id_field, string $title_field)
	{
		$text = '';

		if ($ids) {
			$log_array = [];

			$this->app->db->sql->select([$id_field, $title_field])->from($table)->whereIn($id_field, $ids);
			$this->app->db->readQuery();

			while ($item = $this->app->db->fetchRow()) {
				$log_array[] = $item[0] . '-' . $item[1];
			}

			$this->app->db->free();

			$text = implode(',', $log_array);
		}

		$this->logAction($action, $text);

		return $this;
	}
}
