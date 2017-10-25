<?php
error_reporting(0);
$config = include('config.php');
include('mysqli.php');
$db = new db($config);
$db->connect();

//query question
$rows = (array)$db->fetchAll('select * from question where active = 1 order by weight');
$questions = array();
$form = '';
$no = 0;
foreach($rows as $row){
	$questions[$row['id']] = $row;
	$weight = (int)$row['weight'];
	$is_question = $row['is_question'] == '1';
	if($is_question){
		$no++;
	}
	if($weight <= 11){
		$form .= draw_question($row, $no, array(5,6));
	}else{
		$n = $no - 9;
		$form .= draw_question($row, $n, array(8,3));
	}

}
$db->disconnect();

function draw_question($row, &$no, $cols_width = array(7,4)){
	$id = $row['id'];
	$question = $row['question'];
	$help = implode('', array_map(function($value){
		return '<span class="help-block">'.trim($value).'</span>';
	},explode("\n",$row['help'].'')));
	$is_question = $row['is_question'] == '1';
	$o = '';
	if(!$is_question){
		$o .= '<div class="form-group section"><div class="col-lg-12">'.$question.$help.'</div></div>';
		return $o;
	}
	$type = $row['type'];
	$is_multiple_answer = $row['is_multiple_answer'] == '1';
	$is_required = $row['is_required'] == '1';
	if($is_required){
		$question .= '<span class="required">*</span>';
	}
	$question .= $help;
	$option = $row['option'];
	$w_col1 = $cols_width[0];
	$w_col2 = $cols_width[1];
	$o = '<div class="form-group">
	<div class="col-lg-1"><label class="control-label">'.$no.'.</div></label>
	<label for="id_'.$id.'" class="col-lg-'.$w_col1.' control-label">'.$question.'</label>
	<div class="col-lg-'.$w_col2.'">';

	if($type == 'scale'){
		$o.='<div class="radio">';
		for($i=1; $i<=5; $i++){
			$o.='<label class="radio-inline"><input type="radio" name="name_'.$id.'" value="'.$i.'"';
			if($i==1 && $is_required){
				$o.= ' required';
			}
			$o.='>'.$i.'</label>';
		}
		$o.= '<div class="help-block with-errors"></div>';
		$o.='</div>';
	}elseif($type == 'short_text'){
		$o .= '<input type="text" class="form-control" id="id_'.$id.'" name="name_'.$id.'"';
		if($is_required){
			$o.= ' required';
		}
		$o .='>';
		$o.= '<div class="help-block with-errors"></div>';
	}elseif($type == 'text'){
		$o .= '<textarea class="form-control" id="id_'.$id.'" name="name_'.$id.'"';
		if($is_required){
			$o.= ' required';
		}
		$o .='></textarea>';
		$o.= '<div class="help-block with-errors"></div>';
	}elseif($type =='email'){
		$o .= '<input type="email" class="form-control" id="id_'.$id.'" name="name_'.$id.'"';
		if($is_required){
			$o.= ' required';
		}
		$o .='>';
		$o.= '<div class="help-block with-errors"></div>';
	}elseif($type == 'choice'){
		$options = array_map(function($value){
			return trim($value);
		},explode(';', $option));
		$cr = $is_multiple_answer?'checkbox':'radio';
		$cr_name = $is_multiple_answer?'name_'.$id.'[]':'name_'.$id;
		$no_option = 0;
		foreach($options as $option){
			$no_option++;
			$o .= '<div class="'.$cr.'" style="display:inline-block;padding-right:20px;">
        <label class="'.$cr.'-inline">
          <input type="'.$cr.'" name="'.$cr_name.'" value="'.$option.'"';
		  	if($no_option === 1 && $is_required){
				  $o.= ' required data-error="Please select one of these options."';
			  }
		  	$o .='> '.$option.'
        </label>
      </div>';
		}
		$o.= '<div class="help-block with-errors"></div>';
	}
	$o .='</div>
</div>';
	return $o;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Survey</title>
	<link rel="stylesheet" href="asset/css/bootstrap-3.3.7.min.css">
	<link rel="shortcut icon" href="favicon.ico" />
	<style>
body{
	font-size:12px;
}
span.required{
	color: red;
	font-weight: bold;
	font-size: 1.2em;
}
.form-group{
	border: 1px solid #aaa;
	border-top: 0;
	margin-bottom: 0;
	padding-bottom: 1px;
	padding-top: 10px;
}
.section{
	border-width:0 0 1px 0;
	padding-top: 20px;

}
.help-block{
	color: inherit;
}
form>.form-group:last-child{
	padding-top: 10px;
	padding-bottom: 10px;
	border: 0;
}
form label{
	font-weight: 400;
}
.help-block{
	font-weight: normal;
	font-size: .9em;
}
.body{
	background-image: url(asset/img/photography.png);
	background-repeat: repeat;
}
	</style>
</head>
<body>
	<p>
	<div class="container">
		<div class="row">
			<div class="col-lg-1" style="text-align:center">
				<img src="asset/img/logo.png"/>
			</div>
			<div class="col-lg-11">
				<center>
				<h1>Kuesioner Analisis Faktor-faktor penerimaan yang mempengaruhi pembelian kembali pelanggan pada B2C</i></h1>
				<h3>Studi Kasus: <a href="https://www.gramedia.com" target="_blank">gramedia.com</a></h3>
				</center>
			</div>
			<div class="col-lg-12">
				 <p><b>Tujuan Penelitian:</b><p>
				 <p>
				 	Penelitian  ini bertujuan  untuk  mengetahui  Faktor-faktor apa saja yang mendorong  niat untuk membeli kembali di gramedia.com.
				 	<br/>Hasil dari penelitian ini diharapkan mampu memberikan kontribusi sebagai tambahan referensi untuk sisi akademis juga dapat memberikan referensi dan rekomendasi bagi perusahaan e-commerce.
				 	<br/>Penelitian ini dilakukan oleh I Made Eka Ariantana, mahasiswa MTI Universitas Indonesia angkatan 2015 dan dilakukan dalam waktu empat bulan.
				 </p>
			</div>
			<div class="col-lg-12">
				<form id="form" action="save.php" method="post" class="form-horizontal"
					data-toggle="validator">
					<input type="hidden" name="identity" value="<?php echo md5(uniqid());?>">
					<?php echo $form;?>
					<div class="form-group">
					<div class="col-lg-10">
						<div class="col-lg-12">
							<b>Disclaimer:</b> Data-data yang diberikan hanya akan dipergunakan untuk kepentingan penelitian dan tidak akan diberikan kepada pihak ketiga.
						</div>
						<div class="col-lg-6">
						TERIMA KASIH ATAS PARTISIPASI ANDA
						</div>
						<div class="col-lg-6">
						</div>
					</div>
					<div class="col-lg-2">
						<button type="submit" class="btn btn-success btn-lg">Submit</button>
					</div>
				</div>
				</form>
			</div>
		</div>
	</div>
	<div id="modal" class="modal fade" tabindex="-1" role="dialog">
	  	<div class="modal-dialog" role="document">
		    <div class="modal-content">
				<div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Responden yang terhormat,</h4>
				</div>
				<div class="modal-body">
					<p style="font-size:1.2em">
						Perkenalkan saya I Made Eka Ariantana, mahasiswa jurusan Magister Teknologi Informasi, Universitas Indonesia (NPM 1506812123).
					</p>
					<p style="font-size:1.2em">
						Saat ini saya sedang melakukan penelitian Tugas Akhir dan memerlukan beberapa informasi untuk mendukung penelitian tersebut.
					</p>
					<p style="font-size:1.2em">
						Untuk itu saya mohon dengan hormat kepada Bapak/Ibu/Sdr/Sdri bersedia menjawab pertanyaan yang telah disediakan secara obyektif dan sesuai dengan kondisi yang dirasakan. Sesuai dengan etika dalam penelitian, data yang saya peroleh akan dijaga kerahasiannya dan digunakan semata-mata untuk kepentingan penelitian.
					</p>
					<p style="font-size:1.2em">
						Jika Bapak/Ibu/Sdr/Sdri memenuhi kriteria responden yang dipersyaratkan, yaitu responden yang pernah melakukan transaksi pada gramedia.com, saya meminta kesediaannya untuk mengisi kuesioner penelitian ini (waktu yang dibutuhkan &plusmn;5 menit).
					</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">OK</button>
				</div>
		    </div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<script src="asset/js/jquery-2.2.4.min.js"></script>
	<script src="asset/js/bootstrap-3.3.7.min.js"></script>
	<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.5/validator.min.js"></script>-->
	<script src="asset/js/validator.js"></script>
	<script>
$(function(){
	$('#modal').modal('show');
})
	</script>
</body>
</html>
