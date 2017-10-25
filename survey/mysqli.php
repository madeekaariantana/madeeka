<?php
class db{
	protected $config;
	protected $db;
	public function __construct($config){
		$this->config = $config;
	}
	public function connect(){
		$config =  $this->config;
		$db = mysqli_connect($config['host'], $config['user'], $config['password'], $config['database'], 3306);
		$this->db = $db;
		return $this;
	}
	public function disconnect(){
		mysqli_close($this->db);
		return $this;
	}
	public function fetchAll($query){
		$result = mysqli_query($this->db, $query);
		$rows = array();
		while($row = mysqli_fetch_assoc($result)){
			$rows[] = $row;
		}
		mysqli_free_result($result);
		return $rows;
	}
	public function quote($value){
		return "'".mysqli_real_escape_string($this->db, $value)."'";
	}
	public function query($query){
		return mysqli_query($this->db, $query);
	}
	public function multiQuery($query){
		return mysqli_multi_query($this->db, $query);
	}
}