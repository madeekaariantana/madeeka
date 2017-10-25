<?php
error_reporting(0);
$config = include('config.php');
include('db.php');
$db = new db($config);
$db->connect();

if(empty($_POST)){
	return;
}
$identity = $_POST['identity'];
if(empty($identity)){
	return;
}
$identity = $db->quote($identity);
$submitted_at = $db->quote(date('Y-m-d H:i:s'));
foreach($_POST as $key=>$value){
	$pos = strpos($key, 'name_');
	if($pos === false){
		continue;
	}
	$question_no = (int)substr($key, 5);
	if($question_no <=0){
		continue;
	}
	$answer = $value;
	if(empty($answer)){
		$answer = 'null';
	}else{
		if(is_array($answer)){
			$answer = implode(';', $answer);
		}
		$answer = $db->quote($answer);
	}
	$query = "insert into answer (identity,question_no, answer, submitted_at) 
		values({$identity},{$question_no},{$answer},{$submitted_at})";
	$db->query($query);
}
$db->disconnect();
header('Location: success.php');exit;