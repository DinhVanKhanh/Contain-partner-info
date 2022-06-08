<?php
global $WEBSERVER_FLG;
$WEBSERVER_FLG = 2;
require_once __DIR__ . '/../../../common_files/connect_db.php';
global $DB_SERVER_SERVICE_NAME, $NAME_SERVICE_DB, $USER_SERVICE_DB, $PASS_SERVICE_DB;
$host   = $DB_SERVER_SERVICE_NAME;
$user   = $USER_SERVICE_DB;
$pass   = $PASS_SERVICE_DB;
$dbname = $NAME_SERVICE_DB;
// echo '<pre>';
// print_r(get_defined_vars());
// echo '<pre>';
// die();
class Database
{
	public $conn = null, $stmt = null;
	protected $table = "";
	private $_host = "", $_user = "", $_pass = "", $_dbname = "";
	protected $_prefix = 'DEMO_';
	const FLAG_ADD = 1, FLAG_EDIT = 2;

	public function __construct()
	{
		global $host, $user, $pass, $dbname;
		$this->_host   = $host;
		$this->_user   = $user;
		$this->_pass   = $pass;
		$this->_dbname = $dbname;
		$this->connect();
	}

	private function connect()
	{
		try {
			$this->conn = new PDO("mysql:host={$this->_host};dbname={$this->_dbname}", $this->_user, $this->_pass);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->conn->exec('SET NAMES "utf8"');
		} catch (PDOException $e) {
			die('サーバーへの接続のエラーであります。');
		}
	}

	private function checkTypeData($param)
	{
		return is_string($param) ? "'{$param}'" : intval($param);
	}

	public function addSQL(array $data, $where = '', $custom = ''): string
	{
		$sql = isset($data['keys']) ? "INSERT INTO {$this->table}({$data['keys']}) VALUES %s %s %s" : "INSERT INTO {$this->table} VALUES %s %s %s";
		$values = '';

		// Insert one record
		if (count($data) == 1) {
			foreach ($data as $params) {
				foreach ($params as $value) {
					$values .= $this->checkTypeData($value) . ',';
				}
			}
			$values = '(' . trim($values, ',') . ')';
			goto Result;
		}

		// Insert multi record
		if (count($data) > 1) {
			foreach ($data as $params) {
				$values .= '(';
				foreach ($params as $value) {
					$values .= $this->checkTypeData($value) . ',';
				}
				$values .= trim($values, ',') . '),';
			}
			$values = trim($values, ',');
			goto Result;
		}

		Result:
		return sprintf($sql, $values, $where, $custom);
	}

	public function editSQL(array $data, $where = ''): string
	{
		$sql = "UPDATE {$this->table} SET %s %s";
		$values = '';

		foreach ($data as $key => $value) {
			$values .= "`{$key}`=" . $this->checkTypeData($value) . ',';
		}
		$values = trim($values, ',');
		return sprintf($sql, $values, $where);
	}

	public function deleteSQL($where): string
	{
		return "DELETE FROM {$this->table} {$where}";
	}

	protected function convertJP(string $text): string
	{
		return mb_convert_encoding($text, 'CP932', 'ASCII,JIS,UTF-8,eucJP-win,SJIS-win');
	}

	public function __destruct()
	{
		$conn = $stmt = null;
		$table = "";
	}
}
