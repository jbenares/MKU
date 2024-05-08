<?php
	require_once('my_Classes/options.class.php');
	#require_once('my_Classes/numbertowords.class.php');
	require_once('my_Classes/numtowords.class.php');
    require_once(dirname(__FILE__).'/library/lib.php');	
	include_once("conf/ucs.conf.php");

	$options=new options();	
	
	#$c=new NumToWords();
	$convert = new num2words();
	
	$ev_header_id	= $_REQUEST['ev_header_id'];	
	
	$query="
		select
            h.*, account as supplier_name
		from
		  ev_header as h
          left join supplier as s on h.supplier_id = s.account_id
		where
		  ev_header_id = '$ev_header_id'
	";

    $aTrans = lib::getTableAttributes($query);
	
	
	$result = mysql_query("
		select
			sum(amount) as amount
		from
			ev_detail
		where
			ev_header_id = '$ev_header_id'
	") or die(mysql_error());

	$r = mysql_fetch_assoc($result);	

    $aTrans['total_amount']   = $r['amount'];
    $aTrans['vatable_amount'] = (($aTrans['total_amount']) / (1 + ($aTrans['vat']/100)));    
    $aTrans['wtax_amount']  =  $aTrans['vatable_amount'] * ($aTrans['wtax']/100);	
    $aTrans['cash_amount'] = $aTrans['total_amount'] - $aTrans['wtax_amount'];
	
	$convert = new num2words();	
	
	$aTrans['cash_amount'] = round($aTrans['cash_amount'],2);
	$convert->setNumber($aTrans['cash_amount']);	
	
	$words = strtoupper($convert->getCurrency());
	$aTrans['cash_amount_in_words'] = $aTrans['cash_amount'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ORDER SHEET</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<link rel="stylesheet" type="text/css" href="css/printable.css" />
<style type="text/css">
*{
	font-size:16px;	
}

.header_table,.content_table{
	width:100%;
}
.content_table th{
	border-top:1px solid #000;
	border-bottom:1px solid #000;	
}

.entry td{
	font-size:11px;	
}
</style>
</head>
<body>
<div class="container">	    
    <div>
    	<?=$title?>
    </div>
    
    <div style="position:absolute; top:1.6cm; left:3.5cm; line-height:20px;">
    	DV#<?=str_pad($aTrans['ev_header_id'],5,0,STR_PAD_LEFT)?><br />
        <?=lib::ymd2mdy($aTrans['date'])?><br />
        <?=number_format($aTrans['cash_amount'],2)?><br />
        <?=$aTrans['supplier_name']?>		
    </div>
    
    <!--<div style="position:absolute; top:1.5cm; right:1cm;">
    	<br />
        <br />        
    </div>
    
    <div style="position:absolute; top:7.80cm; left:4.2cm; line-height:20px;">
    	DV#<?=str_pad($aTrans['ev_header_id'],5,0,STR_PAD_LEFT)?><br />
        <br />
        <br />
        <?=number_format($aTrans['cash_amount'],2,'.',',')?>
    	<div style="font-size:11px; margin-left:20px;">&nbsp;</div>
    </div>    
        
    
    <div style="position:absolute; top:8.5cm; right:4cm;">
    	<?=lib::ymd2mdy($aTrans['date'])?>
    </div>-->
    
    <div style="position:absolute; top:5cm; left:1cm; width:100%; font-size:10px;">
    	<table style="width:80%; border-collapse:collapse;" class="entry">
        	<tr>
            	<td style="width:10%; border-bottom:1px solid #000;">A/C Code</td>
                <td style="border-bottom:1px solid #000;">A/C Name</td>
                <td style="width:10%; border-bottom:1px solid #000;">Debit</td>
                <td style="width:10%; border-bottom:1px solid #000;">Credit</td>
            </tr>          
			<?php

            $sql = "
                select
                    d.*, g.acode, g.gchart, project_name
                from
                    ev_detail as d
                    left join gchart as g on d.gchart_id = g.gchart_id
                    left join projects as p on d.project_id = p.project_id
                where
                    d.ev_header_id = '$aTrans[ev_header_id]'

            ";

            $arr = lib::getArrayDetails($sql);
			$aTotal  = array();
			$total_debit = 0;
			$total_credit = 0;
			
            if( count($arr) ){
                foreach ($arr as $r) {
                    $aTotal['debit'] += $r['amount'];
                ?>
                    <tr>
                        <td><?=$r['acode']?></td>
                        <td><?=$r['gchart']?> ( <?=$r['project_name']?> )</td>
                        <td><?=number_format($r['amount'],2,'.',',')?></td>
                        <td>&nbsp;</td>
                    </tr>
                <?php
                }

            }
			
				
            if( $aTrans['wtax_amount'] > 0 ) {
                $aTotal['credit'] += $aTrans['wtax_amount'];
                $aDetail = lib::getTableAttributes("select * from gchart where gchart_id = '$aTrans[wtax_gchart_id]'");
            ?>
                <tr>
                    <td><?=$aDetail['acode']?></td>
                    <td><?=$aDetail['gchart']?></td>
                    <td>&nbsp;</td>
                    <td><?=number_format($aTrans['wtax_amount'],2,'.',',')?></td>
                </tr>
            <?php
            }

            if( $aTrans['cash_amount'] > 0 ) {
                $aTotal['credit'] += $aTrans['cash_amount'];
                $aDetail = lib::getTableAttributes("select * from gchart where gchart_id = '$aTrans[cash_gchart_id]'");
            ?>
                <tr>
                    <td><?=$aDetail['acode']?></td>
                    <td><?=$aDetail['gchart']?></td>
                    <td>&nbsp;</td>
                    <td><?=number_format($aTrans['cash_amount'],2,'.',',')?></td>
                </tr>
            <?php } ?>
            <tr>
            	<td style="border-top:1px solid #000;"></td>
                <td style="border-top:1px solid #000;"></td>
                <td style="border-top:1px solid #000;"><?=number_format($aTotal['debit'],2,'.',',')?></td>
                <td style="border-top:1px solid #000;"><?=number_format($aTotal['credit'],2,'.',',')?></td>
            </tr>
        </table>
        
        <table cellspacing="0" cellpadding="5" align="center" width="80%" style="border:none; text-align:center; margin-top:30px;" class="summary">
            <tr>
                <td>Prepared By:<p>
                    <input type="text" class="line_bottom" /><br><br></p></td>
                <td>Checked By:<p>
                    <input type="text" class="line_bottom" /><br>Camille Ku</p></td>
                <td>Approved By:<p>
                    <input type="text" class="line_bottom" /><br>Michael John S. Ku</p></td>
            </tr>
        </table>
    </div>
        
    
</div>
</body>
</html>
