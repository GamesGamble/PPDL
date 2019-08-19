<?php
	
	namespace de\alhelm\aluja;
	
	use PDO;
	use PDOException;
	use PDOStatement;
	
	/**
	 * This Class creates a new PDO instance. When zero parameters passed the default settings in this class will be used.
	 *
	 * @name PHP PDO Database Library (PPDL)
	 * @package de alhelm aluja
	 * @author GamesGamble
	 * @link https://github.com/GamesGamble/PPDL
	 * @copyright GamesGamble
	 * @license https://github.com/GamesGamble/PPDL/blob/master/LICENSE.md
	 * Created: 22.07.2019
	 * Last Change: 19.08.2019
	 */
	class DB
	{
		/**
		 * This variable stores the PDO Instance.
		 * @var PDO
		 */
		private $conn;
		/**
		 * This variable stores the PDOStatement instance.
		 * @var PDOStatement
		 */
		private $query;
		/**
		 * This variable stores the result as integer, string or mixed array.
		 * @var mixed
		 */
		private $result;
		/**
		 * DB Constructor that creates new PDO instance with all Database related settings, that can be set by parameters.
		 * @param String $username DB Username
		 * @param String $password DB Password
		 * @param String $database Database Name
		 * @param String $host Database Host
		 *
		 * @return int When Successful returns 1
		 */
		public function __construct(String $username = "ExampleUsername", String $password = "ExamplePassword", String $database = "ExampleDatabase",
		                            String $host = "localhost")
		{
			require_once("exception_including.php"); # Including Custom Exceptions
			try {
				$this->conn = new PDO("mysql:host=$host;dbname=$database", $username, $password,
					array(PDO::ATTR_EMULATE_PREPARES, false)); # Disables Preparing Statement Emulation (Not Supported by all DBs)
			} catch (PDOException $e) {
				try { # Custom Exception that logs the message, file and error code and prints a friendly error message.
					throw new DBException("PDOException : Database connecting failed!",__FILE__, 1, $e);
				} catch (DBException $e) {
					echo $e->getMessage();
				}
			}
			return 1; # Will hopefully only be returned if no Exception is triggered.
		}
		/**
		 * Update SQL routine for Updating specific Data in the DB.
		 * Example:
		 *
		 * $db = new DB(); // Use predefined DB Settings
		 * $binds[] = [":uid", $this->uid, PDO::PARAM_INT, 1]; // Use predefined uid in class
		 * $binds[] = [":username", $this->username, PDO::PARAM_STR, 32]; // Use predefined username in class
		 * $db->update("UPDATE user SET username = :username WHERE uid = :uid", $binds); // Updates username from
		 * specific uid
		 *
		 * @param String $sql SQL Code
		 * @param array $binds SQL Binding for SQL Code
		 * @return int When Successful returns 1
		 */
		public function update(String $sql, array $binds)
		{
			$this->prepare($sql); # Prepares Statement.
			$this->bindAll($binds); # Binds all Input variables of array.
			$this->execute(); # executes the sql command.
			return 1; # Will hopefully only be returned if no Exception is triggered.
		}
		/**
		 * Prepares the Prepared Statement of SQL Code, if Emulation of Prepared Statements is deactivated checks SQL
		 * Syntax.
		 *
		 * @param string $sql SQL Code
		 * @return int When Not Successful returns 0 and when Successful returns 1
		 */
		private function prepare(String $sql)
		{
			try {
				if (!$this->query = $this->conn->prepare($sql)) { # Will only throw if Preparing Statements Emulation is off.
					throw new DBException("prepare: Error in SQL Syntax!",__FILE__, 3);
				}
				if (is_a($this->query, PDOStatement::class)) {
					return 1; # Will hopefully only be returned if no Exception is triggered.
				} else { # Fatal Error, makes sure that preparing was successful.
					throw new DBException("prepare: Object is no PDOStatement!",__FILE__, 2);
				} # Custom Exception that logs the message, file and error code and prints a friendly error message.
			} catch (DBException $e) {
				echo $e->getMessage();
			}
			return 0; # Will hopefully never be returned!
		}
		/**
		 * This Method binds all variables of the $binds array.
		 * Example of $binds array:
		 * $binds[[":uid", "5", PDO::PARAM_INT, 1], // Use predefined uid in class
		 *        [":username", "codepenetrator1337", PDO::PARAM_STR, 32]]; // Use predefined username in class
		 *
		 * @param array $binds array of Binds array for multiple binds in SQL Code
		 */
		private function bindAll(array $binds)
		{
			try {
				foreach ($binds as $bind) { # each array entry will be executed.
					if (count($bind) == 4) { # if array entry has 4 values.
						$this->bind($bind[0], $bind[1], $bind[2], $bind[3]); # execute bind method with all array entry's.
					} else if (count($bind) == 3) { # if array entry has 3 values.
						$this->bind($bind[0], $bind[1], $bind[2]); # execute bind method with all array entry's.
					} else if (count($bind) == 2) { # if array entry has 2 values.
						$this->bind($bind[0], $bind[1]); # execute bind method with all array entry's.
					} else { # Custom Exception that logs the message, file and error code and prints a friendly error message.
						throw new DBException("bindALL: Invalid Parameter count for Binding function!", __FILE__,
							4);
					} # unset variables for freeing space and closes cursors of DB things.
					$binds = NULL;
					$bind = NULL;
				}
			} catch (DBException $e) {
				echo $e->getMessage();
			}
		}
		/**
		 * This Method executes the database binds.
		 *  Example Input variables:
		 *
		 *  $num = ":username";
		 *  $var = "codepenetrator1337";
		 *  $type = PDO::PARAM_STR;
		 *  $length = 32;
		 *
		 * @param $num This must contain an placeholder name for binding
		 * @param $var value of the placeholder
		 * @param int $type type of placeholder
		 * @param null $length length of placeholder
		 * @return int When Not Successful returns 0 and when Successful returns 1
		 */
		private function bind($num, $var, $type = PDO::PARAM_STR, $length = NULL)
		{
			try {
				if (is_a($this->query, PDOStatement::class)) {
					 /** @noinspection PhpUndefinedMethodInspection */ // Method not detected because other Method changes Type
					// of $this->query Object.
					if ($this->query->bindParam($num, $var, $type, $length)) { # Executes bind.
						return 1; # Will hopefully only be returned if no Exception is triggered.
					} else { # Custom Exception that logs the message, file and error code and prints a friendly error message.
						throw new DBException("bind: Internal Binding failed!", __FILE__, 5);
					} # Fatal Error, makes sure that binding was successful.
				} else { # Custom Exception that logs the message, file and error code and prints a friendly error message.
					throw new DBException("bind: Object is no PDOStatement!", __FILE__, 2);
				}
			} catch (DBException $e) {
				echo $e->getMessage();
			}
			return 0; # Will hopefully never be returned!
		}
		/**
		 * Just executes the PDOStatement with Binds and SQL Code.
		 *
		 * @return int When Not Successful returns 0 and when Successful returns 1
		 */
		private function execute()
		{
			try {
				if (is_a($this->query, PDOStatement::class)) {
					/** @noinspection PhpUndefinedMethodInspection */ // Method not detected because other Method changes Type
					// of $this->query Object.
					if ($this->query->execute()) { # executes SQL Code.
						return 1; # Will hopefully only be returned if no Exception is triggered.
					} else { # Custom Exception that logs the message, file and error code and prints a friendly error message.
						throw new DBException("execute: Internal Executing failed!", __FILE__, 6);
					} # Fatal Error, makes sure that the executing was successful.
				} else { # Custom Exception that logs the message, file and error code and prints a friendly error message.
					throw new DBException("execute: Object is no PDOStatement!", __FILE__, 2);
				}
			} catch (DBException $e) {
				echo $e->getMessage();
			}
			return 0; # Will hopefully never be returned!
		}
		/**
		 * This Method just deletes database entry's.
		 *  Example Input:
		 *
		 *  $sql = "DELETE FROM user WHERE uid = :uid";
		 *  $binds = [[":uid", 2, PDO::PARAM_INT, 1]];
		 *
		 * @param String $sql SQL Code
		 * @param array $binds array of Binds array for multiple binds in SQL Code
		 * @return int When Successful returns 1
		 */
		public function delete(String $sql, array $binds)
		{
			$this->prepare($sql); # Prepares Statement.
			$this->bindAll($binds); # Binds all Input variables of array.
			$this->execute(); # executes the sql command.
			return 1; # Will hopefully only be returned if no Exception is triggered.
		}
		/**
		 * This Method just inserts new database entry's.
		 * Example Input:
		 *
		 * $binds = [[":role", 1, PDO::PARAM_INT, 1],
		 *           [":username", "hanswurst", PDO::PARAM_STR, 32],
		 *           [":password", "wurscht", PDO::PARAM_STR, 72]];
		 *  $sql = "INSERT INTO user (role, username, password) VALUES (:role, :username, :password)";
		 *
		 * @param String $sql SQL Code
		 * @param array $binds array of Binds array for multiple binds in SQL Code
		 * @return mixed When successful returns String of row ID of the last row or the value when not succesfull returns
		 * IM001 SQLSTATE
		 */
		public function insert(String $sql, array $binds)
		{
			$this->prepare($sql); # Prepares Statement.
			$this->bindAll($binds); # Binds all Input variables of array.
			$this->execute(); # executes the sql command.
			/** @noinspection PhpUndefinedMethodInspection */ // Method not detected because other Method changes Type
			// of $this->query Object.
			return $this->query->lastInsertID(); # returns the last id that was inserted.
		}
		/*
		 * This Methods just executes a query.
		 * Example Input:
		 *
		 * $sql = "SELECT role FROM user WHERE uid = :uid";
		 * $binds = [[":uid", $this->uid, PDO::PARAM_INT, 11]];
		 * $binds_r = [["role", PDO::PARAM_INT, 1]];
		 */
		public function query(String $sql, array $binds, array $binds_r)
		{
			$this->prepare($sql); # Prepares Statement.
			$this->bindAll($binds); # Binds all Input variables of array.
			$this->execute(); # executes the sql command.
			$binded_r = []; # Initializes Variable
			$results = NULL; # Initializes Variable
			try { # This binds the Results for further using after fetching the results.
				foreach ($binds_r as $bind_r) { # each array entry will be executed.
					if (count($bind_r) == 3) { # if array entry has 3 values.
						$this->bindResult($bind_r[0], $bind_r[1], $bind_r[2]); # execute bindResult method with all array entry's.
					} else if (count($bind_r) == 2) { # if array entry has 2 values.
						$this->bindResult($bind_r[0], $bind_r[1]); # execute bindResult method with all array entry's.
					} else if (count($bind_r) == 1) {#  if array entry has 1 values.
						$this->bindResult($bind_r[0]); # execute bindResult method with all array entry's.
					} else { # Custom Exception that logs the message, file and error code and prints a friendly error message.
						throw new DBException("query: Invalid Parameter count for Binding Result function!", __FILE__,
							7);
					} # saves $bind_r[0] to another variable for further using of the values.
					$binded_r[] = $bind_r[0];
				} # unset variables for freeing space and closes cursors of DB things.
				$binds_r = NULL;
				$bind_r = NULL;
				$this->fetch(); # Fetches the Results from the DB.
				if (!empty($this->result)) { # Checks for Empty Result.
					if (is_int($this->result)) { # Checks if value is a number.
						$results[] = [$binded_r[0], $this->result]; # Results will be saved to another variable for further using.
					} else if (is_string($this->result)) { # Checks if value is a String.
						$results[] = [$binded_r[0], $this->result]; # Results will be saved to another variable for further using.
					} else if (is_array($this->result)) { # Checks if value is a Array.
						for ($i = 0; $i <= sizeof($binded_r) - 1; $i++) { # Each Result will be saved in an Array.
							$results[] = [$binded_r[$i], $this->result[$i]]; # Results will be saved to another variable for further
							# using.
						} # unset variables for freeing space and closes cursors of DB things.
						$i = NULL;
					} else { # Custom Exception that logs the message, file and error code and prints a friendly error message.
						throw new DBException("query: Result has wrong Type!", __FILE__, 8);
					}
				} else { # Custom Exception that logs the message, file and error code and prints a friendly error message.
					throw new DBException("query: Result is Empty!", __FILE__, 9);
				}
			} catch (DBException $e) {
				echo $e->getMessage();
			} # unset variables for freeing space and closes cursors of DB things.
			$binded_r = NULL;
			$this->result = NULL;
			return $results; # Returns true if successful and false if didn't.
		}
		/*
		 * This Method just binds the Results for further using of the values.
		 * Example input:
		 *
		 * $name = "role";
		 * $type = PDO::PARAM_INT;
		 * $length = 1;
		 */
		private function bindResult(String $name, $type = PDO::PARAM_STR, $length = NULL)
		{
			try {
				if (is_a($this->query, PDOStatement::class)) {
					/** @noinspection PhpUndefinedMethodInspection */ // Method not detected because other Method changes Type
					// of $this->query Object.
					if ($this->query->bindColumn($name, $this->result, $type, $length)) { # Just binds the Result.
						return 1; # Will hopefully only be returned if no Exception is triggered.
					} else { # Custom Exception that logs the message, file and error code and prints a friendly error message.
						throw new DBException("bindResult: Internal Binding Result failed!", __FILE__, 10);
					} # Fatal Error, makes sure that the binding was successful.
				} else { # Custom Exception that logs the message, file and error code and prints a friendly error message.
					throw new DBException("bindResult: Object is no PDOStatement!", __FILE__, 2);
				}
			} catch (DBException $e) {
				echo $e->getMessage();
			}
			return 0; # Will hopefully never be returned!
		}
		# This Method just fetches Results from the DB.
		private function fetch()
		{
			try {
				if (is_a($this->query, PDOStatement::class)) {
					/** @noinspection PhpUndefinedMethodInspection */ // Method not detected because other Method changes Type
					// of $this->query Object.
					if ($this->query->fetch(PDO::FETCH_BOUND)) { # Just fetches the Results from the DB (PDO::FETCH_BOUND is
						# the Fetch Type and when changed the complete Database Library needs an overhaul.)
						return 1; # Will hopefully only be returned if no Exception is triggered.
					} else { # Custom Exception that logs the message, file and error code and prints a friendly error message.
						throw new DBException("fetch: Internal Fetching failed!", __FILE__, 11);
					} # Fatal Error, makes sure that fetching was successful.
				} else { # Custom Exception that logs the message, file and error code and prints a friendly error message.
					throw new DBException("fetch: Object is no PDOStatement!", __FILE__, 2);
				}
			} catch (DBException $e) {
				echo $e->getMessage();
			}
			return 0; # Will hopefully never be returned!
		}
		# Destructor
		public function __destruct()
		{ # unset variables for freeing space and closes cursors of DB things. Basically this is needed for disconnecting
			# from the DB.
			$this->result = null;
			$this->query = null;
			$this->conn = null;
		}
	}