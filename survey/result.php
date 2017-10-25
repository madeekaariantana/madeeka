<?php
error_reporting(0);
$config = include('config.php');

$realm = 'Private';
$users = array($config['result']['user']=>$config['result']['password']);
list($username, $password) = each($users);
if(!$is_accept_image && !$is_accept_video){
	if (isset($_SERVER["REDIRECT_HTTP_AUTHORIZATION"]) && $_SERVER["REDIRECT_HTTP_AUTHORIZATION"] != '') {
		//php-fpm
		$d = base64_decode($_SERVER["REDIRECT_HTTP_AUTHORIZATION"]);
		list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', $d);
	}elseif (isset($_SERVER["HTTP_AUTHORIZATION"]) && $_SERVER["HTTP_AUTHORIZATION"] != '') {
		//php-fpm
		$d = base64_decode($_SERVER["HTTP_AUTHORIZATION"]);
		list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', $d);
	}
	if (!isset($_SERVER['PHP_AUTH_USER'])) {
	    header('WWW-Authenticate: Basic realm="'.$realm.'"');
	    header('HTTP/1.0 401 Unauthorized');
	    // var_dump($_SERVER);
	    echo 'Bye byee..';
	    exit;
	} else if($_SERVER['PHP_AUTH_USER'] !== $username or $_SERVER['PHP_AUTH_PW'] !== $password){
	    header('WWW-Authenticate: Basic realm="'.$realm.'"');
	    header('HTTP/1.0 401 Unauthorized');
	    echo 'Bye bye..';
	    exit;
	}
}

include('mysqli.php');
$db = new db($config);
$db->connect();

//question
$rows = (array)$db->fetchAll('select id,code,question from question where is_question=1 and active=1 order by weight');
$questions = array();
foreach($rows as $row){
	$questions[$row['id']] = $row['code'];
}

$query = "select * from answer order by identity";
$answers = $db->fetchAll($query);
$db->disconnect();

$result = array();
foreach($answers as $answer){
	$identity = $answer['identity'];
	$submitted_at = $answer['submitted_at'];
	$unique = $identity.'/'.$submitted_at;
	if(!isset($result[$unique])){
		$result[$unique] = array();
	}
	$result[$unique]['identity'] = $identity;
	$result[$unique]['submitted_at'] = $submitted_at;
	$question_no = (int)$answer['question_no'];
	foreach($questions as $question_id=>$question){
		$_answer = ($question_no == $question_id)?$answer['answer']: '';
		if(!isset($result[$unique][$question_id]))
		 	$result[$unique][$question_id] = '';
		if($_answer !== ''){
			$result[$unique][$question_id] = $_answer;
		}
	}
}

if($_GET['type'] == 'csv'){
	$tmp_filename = tempnam(sys_get_temp_dir(), 'survey');
	$tmpfile = @fopen($tmp_filename, 'w');
	$string = '';
	if($tmpfile){
		$headers = array('Identity', 'Submitted Date');
		foreach($questions as $question){
			$headers[] = strip_tags($question);
		}
		fputcsv($tmpfile, $headers);
		foreach($result as $answer){
			$values = array();
			$identity = $answer['identity'];
			$submitted_at = $answer['submitted_at'];
			$values[] = $identity;
			$values[] = $submitted_at;
			foreach($questions as $question_id=>$question){
				$values[] = $answer[$question_id];
			}
			fputcsv($tmpfile, $values);
		}

		@fclose($tmpfile);
		$string = file_get_contents($tmp_filename);
		@unlink($tmp_filename);
	}
	header('Content-Type: text/csv');
	header('Content-Disposition: attachment; filename="result'.date('-Ymd-His').'.csv"');
	echo $string;
	exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Result</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<h1>Result</h1>
				<a class="btn btn-success" href="result.php?type=csv">Export to CSV</a><p>
			</div>
			<div class="col-sm-12">
				<div class="table-responsive">
<?php
echo '<table class="table table-bordered table-compact table-striped">';
echo '<thead><tr><th>Identity</th><th>Submitted Date</th>';
foreach($questions as $question){
	echo '<th>'.strip_tags($question).'</th>';
}
echo '</tr></thead>';
echo '<tbody>';
foreach($result as $answer){
	$identity = $answer['identity'];
	$submitted_at = $answer['submitted_at'];
	echo '<tr>';
	echo '<td>'.$identity.'</td>';
	echo '<td>'.$submitted_at.'</td>';
	foreach($questions as $question_id=>$question){
		echo '<td>'.$answer[$question_id].'</td>';
	}
	echo '</tr>';
}
echo '</tbody></table>';
?>
</div>
</div>
		</div>
	</div>
</body>
</html>