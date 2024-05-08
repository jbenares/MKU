<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	$fromdate 	= $_REQUEST['fromdate'];
	$todate		= $_REQUEST['todate'];
	$date_difference = floor( (strtotime($todate) - strtotime($fromdate)) / (60*60*24) );
	
	$dfromdate = date("m/d/Y",strtotime($fromdate));
	$dtodate = date("m/d/Y",strtotime($todate));
	

	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>JOB ORDER</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<link rel="stylesheet" type="text/css" href="css/printable.css" />
<style type="text/css">
	table{
		width:100%;
		border-collapse:collapse;
	}
	table th{
		border:1px solid #000;
	}
	
	table td{
		border-left:1px solid #000;
		border-right:1px solid #000;	
		padding:0px 5px;
	}
	table tr:last-child td{
		border-bottom:1px solid #000;	
	}
</style>
</head>
<body>
<div class="container">
	
    	<?php
			require("form_heading.php");
        ?>
    
        <div style="text-align:center; font-size:14px;">
            Product Issuance Statistics Report <br />
            As of <?=$dfromdate?> to <?=$dtodate?>
        </div>
            
        <?php
			$query="
				select
					sum(quantity) as quantity,
					stock,
					unit
				from
					issuance_header as h, issuance_detail as d, productmaster as p
				where
					h.issuance_header_id = d.issuance_header_id 
				and
					d.stock_id = p.stock_id
				and
					h.status != 'C'
				and
					date between '$fromdate' and '$todate'	
				group by
					d.stock_id
				order by
					stock asc
			";
			
			$result=mysql_query($query) or die(mysql_error());		
		?>
        <div class="content" style="margin-top:20px;" >
        	<table cellspacing="0" class="issuance_table">
            	<tr>
                	<th>ITEM</th>
                    <th># OF TIMES ISSUED</th>
                    <th>UNIT</th>
                    <th>NO. OF DAYS</th>
                    <th>PERCENTAGE</th>
                </tr>
           		<?php
				$totalamount=0;
				while($r=mysql_fetch_assoc($result)):
					$stock 		= $r['stock'];
					$unit		= $r['unit'];
					$quantity 	= $r['quantity'];
					
					$percentage = ($quantity / $date_difference) * 100;
				?>
                    <tr>
                        <td><?=$stock?></td>
                        <td style="text-align:right;"><?=$quantity?></td>
                        <td><?=$unit?></td>
                        <td style="text-align:center;"><?=$date_difference?></td>
                        <td style="text-align:right;"><?=number_format($percentage,2,'.',',')?> %</td>
                    </tr>
                <?php
				endwhile;
				?>
                               
            </table>
            
        </div><!--End of content-->
    </div><!--End of Form-->
    
</div>
</body>
</html>