<?php
	require_once('../my_Classes/options.class.php');
	include_once("../conf/ucs.conf.php");
	require_once("../my_Classes/numtowords.class.php");
	
	function space($sp){
		$s = str_repeat(" ",$sp);
		return $s;
	}
	
	function adjustSize($s, $size) {
		if (strlen($s) > $size)
		{
			$s = substr($s,0,$size);
		}
		else
		{
			$s = str_pad($s,$size);
		}
		return $s;
	}

	
	$options=new options();	
	
	$cv_header_id = $_REQUEST['id'];
	
	$result = mysql_query("
		select
			*
		from
			cv_header
		where
			cv_header_id = '$cv_header_id'
	") or die(mysql_error());
	
	$r = $aVal = mysql_fetch_assoc($result);
	
	$cv_header_id		= $r['cv_header_id'];
	$cv_header_id_pad	= str_pad($cv_header_id,7,0,STR_PAD_LEFT);
	
	
	if( empty($aVal['first_pdc_date']) && empty($aVal['no_of_payments']) ) die("First PDC Date or No of Payments not specified.");

	$first_pdc_date = $date = $aVal['first_pdc_date'];
	$check_amount = round($aVal['cash_amount']/$aVal['no_of_payments'],2);	

	$convert = new num2words();
	$convert->setNumber($check_amount);
	$words = $convert->getCurrency();

	$aDetails = array();
	for( $i = 1 ; $i <= $aVal['no_of_payments'] ; $i++ ){

		$t = array();
		$t['check_date'] = $date;
		$t['check_amount'] = $check_amount;
		$t['check_words'] = $words;
		$aDetails[] = $t;

		$date = date("Y-m-d",strtotime("+1 month",strtotime($date)));
	}


	
	/*echo "<pre>";
	print_r($aDetails);
	echo "</pre>";*/
	

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script>
    function printPage() { print(); } //Must be present for Iframe printing
    window.onload = function () {
        print();
    }
</script>
<style type="text/css">
body
{
	size: letter portrait;
	font-family:Arial, Helvetica, sans-serif;
	font-size:14px;
	font-weight:bold;
}
.page-break{
	display:block;
	page-break-before:always;  
}
@media print{
  .page-break{
		display:block;
		page-break-before:always;  
  }
  
  body *,.header table td,.content table td,.content table th{
		font-size:14px;   
		font-weight:bold;
  }
}
</style>
</head>
<body>
<?php 
$i = 1;
foreach( $aDetails  as $det): 
?>

<pre>
<?php
/*23 before*/
$details = "<div style='margin-top:-153px;'></div>";
$details .= "<br /><br /><br /><br /><br /><br /><br /><br />". "\n";
$details .= 	space(104)."<b>".date("F, j Y",strtotime($det['check_date']))."</b><br /><br /><br />";

$account = adjustSize(substr($options->getAttribute('supplier','account_id',$aVal['supplier_id'],'account'),0,48),48);

$space = space(25);
#$account="CASH";
#echo '<br/>'.$account;
if($aVal['supplier_id'] == 1790){
	$space = space(39);
}

$details .= "<div style='margin-bottom:-8px;'></div>";
$details .=
		space(13).$account.
		$space."<bold>".number_format($check_amount,2)."</bold>\n\n".
         space(11)."<b >".strtoupper($words).' ONLY </b>'."\n";
		//space(40)."<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CV#".$aVal['cv_no'];
		
echo $details;
if( $i < count($aDetails) ) echo "<div class='page-break'></div>";
$i++;
endforeach;
?>
</pre>
</body>
</html>