<?php
require_once(dirname(__FILE__).'/../conf/ucs.conf.php');
require_once(dirname(__FILE__).'/../library/lib.php');

function updateRRGL(){
	$sql = "
		select
			gltran_header_id, header_id
		from
			gltran_header
		where
			header = 'rr_header_id'
		and status != 'C'
	";
	$result = mysql_query($sql) or die(mysql_error());
	
	while( $r = mysql_fetch_assoc( $result ) ){
		$gltran_header_id = $r['gltran_header_id'];
		$rr_header_id     = $r['header_id'];

		$project_id = lib::getAttribute('rr_header','rr_header_id',$rr_header_id,'project_id');

		$sql = "update gltran_detail set project_id = '$project_id' where gltran_header_id = '$gltran_header_id'; ";
		echo $sql . "<br>";
		//mysql_query($sql) or die(mysql_error());
		//set_time_limit(30);
	}
}

updateRRGL();



?>