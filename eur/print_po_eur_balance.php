<?php
	require_once('../my_Classes/options.class.php');
	include_once("../conf/ucs.conf.php");
	
	$options=new options();	
	$from_date       = $_REQUEST['from_date'];
	$to_date         = $_REQUEST['to_date'];
	$project_id      = $_REQUEST['project_id'];
	$po_equipment_id = $_REQUEST['po_equipment_id'];
	
	function getPOList($from_date,$to_date,$project_id=NULL,$po_equipment_id=NULL){
		$sql = "
			SELECT
				h.po_header_id,h.date,h.project_id,d.stock_id,d.quantity,d.po_detail_id
			FROM
				po_header as h, po_detail as d
			WHERE
				h.po_header_id = d.po_header_id
			and
				h.po_header_id IN (
					SELECT DISTINCT
						(po_header_id) AS po_header_id
					FROM
						eur_header AS h,
						eur_detail AS d
					WHERE
						h.eur_header_id = d.eur_header_id
					AND date BETWEEN '$from_date'AND '$to_date'
					and h. status != 'C'
					and eur_void = '0'
				)
			AND date BETWEEN '$from_date' AND '$to_date'
			and status != 'C'
		";
		
		if($project_id) $sql      .= "and h.project_id = '$project_id'";
		if($po_equipment_id) $sql .= "and d.stock_id = '$po_equipment_id'";

		$sql.= "
			order by h.po_header_id asc
		";
		$result = mysql_query($sql) or die(mysql_error());
		
		$a = array();
		while($r = mysql_fetch_assoc($result)){
			$a[] = $r;	
		}
		return $a;
		#return htmlentities("$r[customer_last_name], $r[customer_first_name] $r[customer_middle_name] $r[customer_appel]");
	}
	
	function getEURList($po_detail_id){
		$result = mysql_query("
			SELECT 
				*
			FROM
				eur_header AS h,
				eur_detail AS d
			WHERE
				h.eur_header_id = d.eur_header_id
			and h. status != 'C'
			and eur_void = '0'
			and d.po_detail_id = '$po_detail_id'
		") or die(mysql_error());
		$a = array();
		while( $r = mysql_fetch_assoc($result) ){
			$a[] = $r;
		}
		return $a;
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>DPRC STATEMENT OF PAYMENTS REPORT</title>
<script>

function printPage() { print(); } //Must be present for Iframe printing
</script>
<link rel="stylesheet" type="text/css" href="../css/dprc_print.css"/>
<style type="text/css">
	.table-content{
		margin-top:5px;
		width:100%;	
	}
	.table-content tr:nth-child(1) td{
		border-top:1px solid #000;
		border-bottom:1px solid #000;
	}
	.table-content td{
		padding:1px 5px;	
	}
	/*.table-content tr:last-child td{
		border-top:2px solid #000;
		border-bottom:2px solid #000;	
		font-weight:bold;
	}*/
</style>
</head>
<body>
<div class="container">
     <div><!--Start of Form-->
     	
     	<div style="font-weight:bolder; border-bottom:1px solid #000;">
        	PO EUR BALANCE<br />
			<?=date("m/d/Y",strtotime($from_date))?> to <?=date("m/d/Y",strtotime($to_date))?>
            <span style="float:right;"><?=date("F j, Y H:i:s")?></span> 
        </div>           
        <div class="content" style="">
        	<table class="table-content">
            	<tr>
                	<td>PO#</td>
                	<td>PO DATE</td>
                    <td>PROJECT</td>
                    <td>PO EQUIPMENT</td>
                    <td style="width:5%; text-align:right;">PO QUANTITY</td>
                    <td>EUR NO</td>
                    <td>EQUIPMENT</td>
                    <td style="width:5%; text-align:right;">ACTUAL HOURS</td>
                    <td style="width:5%; text-align:right;">BALANCE</td>
                </tr>
                <?php 
				$t_amount = 0;
				$t_hours = 0;
				foreach(getPOList($from_date,$to_date,$project_id,$po_equipment_id) as $r): 
					$h_flag = 0;
					$balance = $r['quantity'];
					foreach(getEURList($r['po_detail_id']) as $aEUR): 
						$h_flag = 1;
						$balance -= $aEUR['computed_time'];

						$t_hours += round($aEUR['computed_time'],2);
						?>
	                  	<tr>
	                        <td><?=$r['po_header_id']?></td>
	                        <td><?=date("m/d/Y",strtotime($r['date']))?></td>
	                        <td><?=$options->getAttribute('projects','project_id',$r['project_id'],'project_name')?></td>
	                        <td><?=$options->getAttribute('productmaster','stock_id',$r['stock_id'],'stock')?></td>
	                        <td style="text-align:right;"><?=number_format($r['quantity'],2)?></td>                    
	                    	<td><?=$aEUR['eur_no']?></td>
	                        <td><?=$options->getAttribute('productmaster','stock_id',$aEUR['stock_id'],'stock')?></td>
		                    <td style="text-align:right;"><?=number_format($aEUR['computed_time'],2)?></td>
	                        <td style="text-align:right;"><?=number_format($balance,2)?></td>
	                   	</tr>
                    <?php endforeach; ?>
                    <?php if($h_flag): ?>
                    <tr>
                    	<td>&nbsp;</td>
                    </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
                <tr>
                    <td style="border-top:1px solid #000;"></td>
                    <td style="border-top:1px solid #000;"></td>
                    <td style="border-top:1px solid #000;"></td>
                    <td style="border-top:1px solid #000;"></td>
                    <td style="border-top:1px solid #000;"></td>
                	<td style="border-top:1px solid #000;"></td>
                    <td style="border-top:1px solid #000;"></td>
                    <td style="text-align:right; border-top:1px solid #000; font-weight:bold;"><?=number_format($t_hours,2)?></td>
                    <td style="border-top:1px solid #000;"></td>
               	</tr>
                
            </table>
        </div><!--End of content-->
        <!--<table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:10px;" class="summary">
            <tr>
                <td>Prepared by:<p>
                    <input type="text" class="line_bottom" /><br>Collection/Cashier</p></td>
                <td>Checked by:<p>
                    <input type="text" class="line_bottom" /><br>Database Administrator</p></td>
                <td>Noted by:<p>
                    <input type="text" class="line_bottom" /><br>Database Administrator</p></td>
            </tr>
        </table> -->
    </div><!--End of Form-->
</div>
</body>
</html>