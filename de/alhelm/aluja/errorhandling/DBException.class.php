<?php
	
	namespace de\alhelm\aluja;
	
	use Exception;
	/*
	 * This is just an Exception Class with Logging Support. It Logs the detailed Error Messages and prints
	 * very generic error messages to the end users.
	 */
	require_once("AlujaException.class.php"); // Needs the AlujaException Class as parent Class.
	
	class DBException extends AlujaException
	{
		public function __construct(String $message, $file = __FILE__,int $code = 000, Exception $previous = null)
		{
			$level = "Error";
			
			$message = "[{$file}] [{$level}] $code : $message".PHP_EOL;
			error_log($message); // Detailed Log Entry with Errorcode, Errorlevel and a detailed message.
			
			$message = $this->friendlyMSG ($code); // Searches generic error message for error id and sets message to it.
			
			parent::__construct($message, $code, $previous); // generic error message will be forwarded to the AlujaException Class.
		}
		
		private function friendlyMSG (String $code) {
			require_once ("DBException_errors.php"); // Needs array of error messages.
			if (!array_key_exists($code, $friendlyMSG)) { // Searches for error message.
				$message = "No Error Message available!"; // Default Error Message.
				return $message; // returns error message if not found.
			}
			$message = $friendlyMSG[$code]; // sets error message if found.
			return $message; // returns error message if found.
		}
		
		public function __toString()
		{
			return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
		}
	}