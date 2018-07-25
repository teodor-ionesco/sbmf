<?php

namespace Library\Entities;

use Exception;
use PDOException;
use PDO;
use Library\DB;
use Library\Request;
use Library\Response;

class Match implements Def
{
	public static $State;
	private $DB;

	public function __construct()
	{
		if(!Request::is_data())
		{
			throw new Exception("Request mismatch.");
		}

		$this -> DB = DB::init();
	}

	public function read($target)
	{
		if($target === 'all')
		{
			self::$State = $this -> DB -> query("SELECT *
										FROM cb_match
										WHERE cb_match.cb_id = '". Response::read()["data"]["cbid"] ."';")
								-> fetchAll(PDO::FETCH_ASSOC);
			return self::$State;
		}
		else
		{
			$x = explode(":", $target);

			if(is_array($x) && !empty($x[0]) && !empty($x[1]))
			{
				try
				{
					$ret = $this -> DB -> prepare("SELECT *
													FROM cb_match
													WHERE cb_match.$x[0] = :$x[0]
														AND cb_match.cb_id = '". Response::read()["data"]["cbid"] ."';");
					$ret -> execute([":$x[0]" => $x[1]]);

					self::$State = $ret -> fetch(PDO::FETCH_ASSOC);
				}
				catch (PDOException $e)
				{
					//echo $e
					die("Database error occurred. Please contact the Webmaster.");
				}

				return self::$State;
			}
		}

		throw new Exception("Please input a valid target: 'all' or 'col:value'");
	}
}