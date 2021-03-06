<?php
/**
* The Bruteforce Protection Class
* @package Venus
*/

namespace Venus\Helpers;

/**
* The Bruteforce Protection Class
* Protects agains bruteforce attempts
*/
class Bruteforce
{
	use \Venus\AppTrait;

	/**
	* @var int $ip_max_attemps The number of failed attempts before an IP is marked as blocked
	*/
	public int $ip_max_attemps = 0;

	/**
	* @var int $ip_block_seconds The number of seconds to block an IP marked as blocked
	*/
	public int $ip_block_seconds = 0;

	/**
	* @var int $user_max_attemps The number of failed attempts before an user is marked as blocked
	*/
	public int $user_max_attemps = 0;

	/**
	* @var int $user_block_seconds The number of seconds to block an user marked as blocked
	*/
	public int $user_block_seconds = 0;

	/**
	* @var string $scope The bruteforce scope. Eg: frontend,admin. Any string can be used as a custom scope
	*/
	protected string $scope = 'frontend';

	/**
	* @var object $data Stores data about the failed attempts
	*/
	protected ?object $data = null;

	/**
	* @internal
	*/
	protected string $ips_table = 'venus_bruteforce_ips';

	/**
	* @internal
	*/
	protected string $users_table = 'venus_bruteforce_users';

	/**
	* Builds the Bruteforce object
	* @param string $scope Custom bruteforce scope, if any
	*/
	public function __construct(string $scope = '')
	{
		$this->app = $this->getApp();

		$this->ip_max_attemps = $this->app->config->bruteforce_ip_max_attemps;
		$this->ip_block_seconds = $this->app->config->bruteforce_ip_block_seconds;
		$this->user_max_attemps = $this->app->config->bruteforce_user_max_attemps;
		$this->user_block_seconds = $this->app->config->bruteforce_user_block_seconds;

		if ($scope) {
			$this->scope = $scope;
		}
	}

	/**
	* Returns the table where the failed attempts, by ip, are stored
	*/
	protected function getIpsTable() : string
	{
		return $this->ips_table;
	}

	/**
	* Returns the table where the failed attempts, by user, are stored
	*/
	protected function getUsersTable() : string
	{
		return $this->users_table;
	}

	/**
	* Returns the number of failed attempts
	* @return int
	*/
	public function getAttempts() : int
	{
		if (!$this->data) {
			return 0;
		}

		return $this->data->attempts;
	}

	/**
	* Returns the number of seconds elapsed from the last failed attempt
	* @return int The number of seconds, or null if there was no invalid attempt
	*/
	public function getLastAttempt() : ?int
	{
		if (!$this->data) {
			return null;
		}

		return $this->data->last;
	}

	/**
	* Returns the number of allowed max attempts
	* @return int
	*/
	public function getMaxAttempts() : int
	{
		if (!$this->data) {
			return $this->ip_max_attemps;
		}

		return $this->data->max_attempts;
	}

	/**
	* Returns the number of seconds for which the ip/user is blocked
	* @return int
	*/
	public function getBlockSeconds() : int
	{
		if (!$this->data) {
			return $this->ip_block_seconds;
		}

		return $this->data->block_seconds;
	}

	/**
	* Determines if the IP or the user are blocked
	* @param string $ip The IP
	* @param int $user_id The user's ID
	* @return bool
	*/
	public function isBlocked(string $ip, int $user_id) : bool
	{
		if ($this->ipIsBlocked($ip)) {
			return true;
		}
		if ($this->userIsBlocked($user_id)) {
			return true;
		}

		return false;
	}

	/**
	* Determines if an IP is blocked
	* @param string $ip The IP
	* @return bool
	*/
	public function isIpBlocked(string $ip) : bool
	{
		$this->data = $this->getAttemptsByIp($ip);
		if (!$this->data) {
			return false;
		}

		$this->data->type = 'ip';
		$this->data->max_attempts = $this->ip_max_attemps;
		$this->data->block_seconds = $this->ip_block_seconds;

		if ($this->data->attempts > $this->ip_max_attemps - 1) {
			return true;
		}

		return false;
	}

	/**
	* Determins if an user is blocked
	* @param int $user_id The user ID
	* @return bool
	*/
	public function isUserBlocked(int $user_id) : bool
	{
		if (!$user_id) {
			return false;
		}

		$this->data = $this->getAttemptsByUserId($user_id);
		if (!$this->data) {
			return false;
		}

		$this->data->type = 'user';
		$this->data->max_attempts = $this->user_max_attemps;
		$this->data->block_seconds = $this->user_block_seconds;

		if ($this->data->attempts > $this->user_max_attemps - 1) {
			return true;
		}

		return false;
	}

	/**
	* Returns the invalid attempts data originating from an IP
	* @param string $ip The IP
	* @return object The data
	*/
	protected function getAttemptsByIp(string $ip) : ?object
	{
		$this->deleteExpiredByIp();

		$table = $this->getIpsTable();

		$this->app->db->readQuery(
			"
			SELECT attempts, UNIX_TIMESTAMP() - `timestamp` as last
			FROM {$table}
			WHERE ip_crc = CRC32(:ip) AND ip = :ip AND scope = :scope",
			['ip' => $ip, 'scope' => $this->scope]
		);

		return $this->app->db->getRow();
	}

	/**
	* Returns the invalid attempts data originating from an user
	* @param string $user_id The user's ID
	* @return object The data
	*/
	public function getAttemptsByUserId(int $user_id) : ?object
	{
		$this->deleteExpiredByUserId();

		$table = $this->getUsersTable();

		$this->app->db->readQuery(
			"
			SELECT attempts, UNIX_TIMESTAMP() - `timestamp` as last
			FROM {$table}
			WHERE user_id = {$user_id} AND scope = :scope",
			['scope' => $this->scope]
		);

		return $this->app->db->getRow();
	}

	/**
	* Inserts an invalid attempt into the database
	* @param string $ip The IP
	* @param int $user_id The user id
	* @return $this
	*/
	public function insert(string $ip, int $user_id = 0)
	{
		$ips_table = $this->getIpsTable();
		$users_table = $this->getUsersTable();

		//insert the IP's invalid attempt
		$this->app->db->readQuery("SELECT COUNT(*) FROM {$ips_table} WHERE ip_crc = CRC32(:ip) AND ip = :ip AND scope = :scope", ['ip' => $ip, 'scope' => $this->scope]);
		$ips_count = $this->app->db->getCount();

		if ($ips_count) {
			$this->app->db->writeQuery("UPDATE {$ips_table} SET attempts = attempts + 1, `timestamp` = UNIX_TIMESTAMP() WHERE ip_crc = CRC32(:ip) AND ip = :ip AND scope = :scope", ['ip' => $ip, 'scope' => $this->scope]);
		} else {
			$this->app->db->writeQuery("INSERT INTO {$ips_table} VALUES(:ip, CRC32(:ip), 1, UNIX_TIMESTAMP(), :scope)", ['ip' => $ip, 'scope' => $this->scope]);
		}

		if (!$user_id) {
			return $this;
		}

		//insert the user's invalid attempt
		if ($this->app->db->count($users_table, ['user_id' => $user_id, 'scope' => $this->scope])) {
			$this->app->db->writeQuery("UPDATE {$users_table} SET attempts = attempts + 1, `timestamp` = UNIX_TIMESTAMP() WHERE user_id = {$user_id} AND scope = :scope", ['scope' => $this->scope]);
		} else {
			$this->app->db->writeQuery("INSERT INTO {$users_table} VALUES({$user_id}, 1, UNIX_TIMESTAMP(), :scope)", ['scope' => $this->scope]);
		}

		return $this;
	}

	/**
	* Deletes all expired attempts
	* @return $this
	*/
	public function deleteExpired()
	{
		$this->deleteExpiredByIp();
		$this->deleteExpiredByUserId();
	}

	/**
	* Deletes all IP expired attempts
	* @return $this
	*/
	public function deleteExpiredByIp()
	{
		$this->deleteExpiredFromTable($this->getIpsTable(), $this->ip_block_seconds);

		return $this;
	}

	/**
	* Deletes all user expired attempts
	* @return $this
	*/
	public function deleteExpiredByUserId()
	{
		$this->deleteExpiredFromTable($this->getUsersTable(), $this->user_block_seconds);

		return $this;
	}

	/**
	* Deletes the expired attepts from a table
	* @param string $table The table
	* @param int $block_seconds The number of seconds each invalid attempt is blocked
	*/
	protected function deleteExpiredFromTable(string $table, int $block_seconds)
	{
		$this->app->db->writeQuery("DELETE FROM {$table} WHERE `timestamp` < UNIX_TIMESTAMP() - {$block_seconds} AND scope = :scope", ['scope' => $this->scope]);
	}

	/**
	* Deletes all attempts originating from an IP & an user from the table
	* @param string $ip The IP
	* @param string $user_id The user's id
	*/
	public function delete(string $ip, int $user_id)
	{
		$this->deleteIp($ip);
		$this->deleteUserId($user_id);
	}

	/**
	* Deletes all attempts originating from an IP from the table
	* @param string $ip The IP
	*/
	public function deleteIp(string $ip)
	{
		$table = $this->getIpsTable();

		$this->app->db->writeQuery(
			"
			DELETE FROM {$table}
			WHERE ip_crc = CRC32(:ip) AND ip = :ip AND scope = :scope",
			['ip' => $ip, 'scope' => $this->scope]
		);
	}

	/**
	* Deletes all attempts originating from an user from the table
	* @param string $user_id The user's id
	*/
	public function deleteUserId(int $user_id)
	{
		$table = $this->getUsersTable();

		$this->app->db->writeQuery(
			"
			DELETE FROM {$table}
			WHERE user_id = {$user_id} AND scope = :scope",
			['scope' => $this->scope]
		);
	}
}
