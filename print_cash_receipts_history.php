<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	$from_date		= $_REQUEST['from_date'];
	$to_date		= $_REQUEST['to_date'];
	$project_id		= $_REQUEST['project_id'];
	$vat_type		= $_REQUEST['vat_type'];
	
	$project = $options->getAttribute("projects","project_id",$project_id,"project_name");
/*
	$categ_id		= $_REQUEST['categ_id'];
	$supplier		= $_REQUEST['supplier'];
	$po_header_id	= $_REQUEST['po_header_id'];
	$driverID		= $_REQUEST['driverID'];
	$stock_id		= $_REQUEST['stock_id'];
	$account_id		= $_REQUEST['account_id'];	
	$rr_type 		= $_REQUEST['rr_type'];
	
*/
function getDeductions($cr_header_id){
	$result = mysql_query("
		select sum(_amount) as amount from cr_detail where cr_header_id = '$cr_header_id'
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	
	return $r['amount'];
	
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>JOB ORDER</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<link rel="stylesheet" type="text/css" href="css/print.css"/>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	
     	<div style="font-weight:bolder;">
        	CASH RECEIPTS HISTORY REPORT <br />
            <?=$project?> <br />
            From <?=date("m/d/Y",strtotime($from_date))?> to <?=date("m/d/Y",strtotime($to_date))?>
        </div>           
        <div class="content" style="">
        	<table cellpadding="6">
            	<tr>
                	<th style="text-align:left;">CR #</th>
                    <th style="text-align:left;">OR TYPE</th>
                    <th style="text-align:left;">DATE</th>
                    <th style="text-align:left;">PROJECT</th>
                    <th style="text-align:left;">PARTICULARS</th>
                    <th style="text-align:left;">INVOICE #</th>
                    <th style="text-align:left;">OR #</th>
                    <th style="text-align:left;">RECEIVED FROM</th>
                    <th style="text-align:left;">PROJECT</th>
                    <th style="text-align:left;">BANK</th>
                    <th style="text-align:left;">CHECK DATE</th>
                    <th style="text-align:left;">CHECK NO</th>
                    <th style="text-align:right;">AMOUNT</th>
                </tr>	
                
             	<?php
					$query="
						select
							*
						from
							cr_header as h left join projects as p on h.project_id = p.project_id
						where
							date between '$from_date' and '$to_date'
						and
							status != 'C'
					";
					
					if( $vat_type ) $query .= " and or_type = '$vat_type'";
					
					if($project_id){
					$query .= "and h.project_id = '$project_id'";	
					}
					
					
					$query.="
						order by
							or_no asc
					";
					$result=mysql_query($query) or die(mysql_error());

					$total_amount = 0;
					while($r=mysql_fetch_assoc($result)){
						$deductions = getDeductions($r['cr_header_id']);
						$total_amount += $r['amount'] -$deductions;

						$project_name = $options->getAttribute('projects','project_id',$r['project_id'],'project_name');
				?>	
                        <tr>
                        	<td><?=str_pad($r['cr_header_id'],7,0,STR_PAD_LEFT)?></td>                       
                            <td><?=$r['or_type']?></td>
                            <td><?=date("m/d/Y",strtotime($r['date']))?></td>
                            <td><?=$r['project_name']?></td>
                            <td><?=$r['particulars']?></td>
							<td><?=$r['invoice']?></td>
                            <td><?=$r['or_no']?></td>
                            <td><?=$r['received_from']?></td>
                            <td><?=$project_name?></td>
                            <td><?=$r['bank']?></td>
                            <td><?=($r['check_date'] != "0000-00-00") ? $r['check_date'] : "" ?></td>
                            <td><?=$r['check_no']?></td>                       
                            <td style="text-align:right;"><?=number_format($r['amount']- $deductions,2)?></td>                       
                      	</tr>
				<?php } ?>
                <tr>
                	<td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td style="text-align:right; font-weight:bold;"><span style='border-bottom:1px solid #000;'><?=number_format($total_amount,2,'.',',')?></span></td>
                </tr>
            </table>
        
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>