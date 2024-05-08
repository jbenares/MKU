<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	$from_date		= $_REQUEST['from_date'];
	$to_date		= $_REQUEST['to_date'];
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
        	AGGREGATES ISSUANCE HISTORY REPORT <br />
            <?=$project?> <br />
            From <?=date("m/d/Y",strtotime($from_date))?> to <?=date("m/d/Y",strtotime($to_date))?>
        </div>           
        <div class="content" style="">
        	<?php
			$result = mysql_query("select * from projects order by project_name asc") or die(mysql_error());
			$projects = array();
			while($r = mysql_fetch_assoc($result)){
				$projects[] = $r['project_id'];
			}
            ?>
        
			<?php
			$grand_total_amount = 0;
			$grand_total_quantity = 0;
			$grand_total_optional_quantity = 0;
			foreach($projects as $project_id){
            ?>      
            <?php
				$query="
					select
						*
					from
						issuance_header as h, issuance_detail as d, productmaster as p
					where
						h.issuance_header_id = d.issuance_header_id
					and
						d.stock_id = p.stock_id
					and
						h.status != 'C'
					and
						h.date between '$from_date' and '$to_date'
					and
						h.project_id = '$project_id'
					and
						p.categ_id1 = '1'
					order by
						h.date asc, h.issuance_header_id asc
				";
				$result=mysql_query($query) or die(mysql_error());
				
				if(mysql_num_rows($result) <= 0) continue;
            ?>
           		<table cellpadding="6">
                	<caption style="text-align:left; font-weight:bold;"><?=$options->getAttribute("projects","project_id",$project_id,"project_name");?></caption>
                    <tr>
                        <th style="width:5%;">DATE</th>
                        <th style="width:5%;">RIS#</th>
                        <th style="width:5%;">DRIVER</th>
                        <th style="width:5%;">EQUIP</th>
                        <th style="width:5%;">REFERENCE</th>
                        <th>ITEM</th>
                        <th style="width:5%;">QTY</th>
                        <th style="width:5%;">UNIT</th>
                        <th style="width:5%;">QTY (OPTIONAL)</th>
                        <th style="width:5%;">PRICE</th>
                        <th style="width:5%;">AMOUNT</th>
                    </tr>	
                    
                    <?php
					$total_quantity = 0;
					$total_optional_quantity = 0;
					$total_amount = 0;
					while($r=mysql_fetch_assoc($result)){
						$total_quantity += $r['quantity'];
                        $total_amount += $r['amount'];
						$total_optional_quantity += $r['quantity_cum'];
                    ?>	
                    <tr>
                        <td><?=date("m/d/Y",strtotime($r['date']))?></td>
                        <td><?=str_pad($r['issuance_header_id'],7,0,STR_PAD_LEFT)?></td>                       
                        <td><?=$options->getAttribute('drivers','driverID',$r['driverID'],'driver_name')?></td>                       
                        <td><?=$options->getAttribute('equipment','eqID',$r['equipment_id'],'eq_name')?></td>                       
                        <td><?=$r['_reference']?></td>                       
                        <td><?=$r['stock']?></td>                       
                        <td style="text-align:right;"><?=$r['quantity']?></td>                       
                        <td><?=$r['unit']?></td>                       
                        <td style="text-align:right;"><?=$r['quantity_cum']?></td>                       
                        <td style="text-align:right;"><?=$r['price']?></td>                       
                        <td style="text-align:right;"><?=$r['amount']?></td>                       
                    </tr>
                    <?php } ?>
                    <?php
                    $grand_total_amount += $total_amount;
					$grand_total_quantity += $total_quantity;
					$grand_total_optional_quantity += $total_optional_quantity;
					?>
                 	<tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td style="text-align:right; font-weight:bold; border-bottom:1px solid #000;"><?=number_format($total_quantity,2,'.',',')?></td>
                        <td>&nbsp;</td>
						<td style="text-align:right; font-weight:bold; border-bottom:1px solid #000;"><?=number_format($total_optional_quantity,2,'.',',')?></td>
                        <td>&nbsp;</td>
                        <td style="text-align:right; font-weight:bold; border-bottom:1px solid #000;"><?=number_format($total_amount,2,'.',',')?></td>
                    </tr>
                </table>
       		<?php } ?>
            <table style="margin-top:5px;">
                <tr>
                    <td style="text-align:right; font-weight:bolder; border-top:1px solid #000;" colspan="6">&nbsp;</td>
                    <td style="width:5%; text-align:right; font-weight:bolder; border-top:1px solid #000; border-bottom:4px double #000;"><?=number_format($grand_total_quantity,2,'.',',')?></td>
                    <td style="width:5%; border-top:1px solid #000;">&nbsp;</td>
                    <td style="width:5%; text-align:right; font-weight:bolder; border-top:1px solid #000; border-bottom:4px double #000;"><?=number_format($grand_total_optional_quantity,2,'.',',')?></td>
                    <td style="width:5%; border-top:1px solid #000;">&nbsp;</td>
                    <td style="width:5%; text-align:right; font-weight:bolder; border-top:1px solid #000; border-bottom:4px double #000;"><?=number_format($grand_total_amount,2,'.',',')?></td>
                </tr>
            </table>
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>