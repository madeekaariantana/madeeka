<?php
class db{
	protected $config;
	protected $link;
	public function __construct($config){
		$this->config = $config;
	}
	public function connect(){
		$config =  $this->config;
		$db = mysql_connect($config['host'], $config['user'], $config['password']);
		mysql_select_db($config['database'], $db);
		$this->link = $db;
		return $this;
	}
	public function disconnect(){
		mysql_close($this->link);
		return $this;
	}
	public function fetchAll($query){
		$result = mysql_query($query);
		$rows = array();
		while($row = mysql_fetch_assoc($result)){
			$rows[] = $row;
		}
		mysql_free_result($result);
		return $rows;
	}
	public function quote($value){
		return "'".mysql_real_escape_string($value, $this->link)."'";
	}
	public function query($query){
		mysql_query($query, $this->link);
	}
}