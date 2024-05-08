<?php
class DB{
	private static $mysqli;
	public static function conn(){
		if( !self::$mysqli ){
			self::$mysqli = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_NAME);
			if(self::$mysqli->connect_error){
				echo self::$mysqli->connect_error;
			}
		}

		return self::$mysqli;
	}
}
?>