<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$stock_id=$_REQUEST[stock_id];
	$stock	= $options->attr_stock($stock_id,'stock');
	$cost	= $options->attr_stock($stock_id,'cost');	
	$reportdate=$_REQUEST[reportdate];

	$balance=0;		
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>JOB ORDER</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<style type="text/css">


@media print and (width: 8.5in) and (height: 14in) {
  @page {
	  margin: 1in;
  }
}
	
body
{
	size: legal portrait;
	padding:0px;
	/*margin:0px;*/
	font-family:Arial, Helvetica, sans-serif;
	font-size:10pt;
}
.container{
	width:8.3in;
	/*height:10.8in;*/
	margin:0px auto;
	/*border:1px solid #000;*/
	padding:0.1in;
	/*overflow:auto;*/
}

.header
{
	text-align:center;	
	margin-top:20px;
}

.header table, .content table
{
	width:100%;
	text-align:left;
	

}
.header table td, .content table td
{
	padding:3px;
	
}

.content table{
	border-collapse:collapse;
}
.content table td,.content table th{
	border:1px solid #000;
	padding:10px;
}
hr
{
	margin:40px 0px;	
	border:1px dashed #999;

}

.clearfix:after {
	content: ".";
	display: block;
	clear: both;
	visibility: hidden;
	line-height: 0;
	height: 0;
}
 
.clearfix {
	display: inline-block;
}

html[xmlns] .clearfix {
	display: block;
}
 
* html .clearfix {
	height: 1%;
}


</style>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	
     	<div align="right" style="font-size:18pt; font-weight:bolder;">
        	STOCK CARD
        </div>
        <div>
        Stock : <?=$options->getMaterialName($stock_id);?>
        </div>
        <?php
		if($_REQUEST[priceoption]=="y"):
		?>
            <div>
            Cost : P <?=number_format($cost,2,'.',',');?>
            </div>
       	<?php
		endif;
		?>
        <div class="content" >
        	<table cellspacing="0">
            	<tr>
                	<th>Date</th>
                    <th>Type of Transaction</th>
                    <th>Quantity In</th>
                    <th>Quantity Out</th>
                    <th>Balance</th>
                </tr>
                
           		<?php
				/*****************
				RECEIVING
				*****************/
				
				$query="
					SELECT
						date,
						quantity
					FROM
						rr_header as h, rr_detail as d
					where
						h.rr_header_id = d.rr_header_id
					and
						stock_id='$stock_id'
					AND
						status != 'C'
					AND
						date <= '$reportdate'
				";
				$result=mysql_query($query) or die(mysql_error());
				?>
             
                
                <?php
				while($r=mysql_fetch_assoc($result)):
					
					$data[]= array(
						'date' => $r['date'],
						'transac' => 'STOCKS RECEIVING',
						'qtyin' =>$r['quantity'],
						'qtyout' => 0
					);
				?>
                <?php
				endwhile;
				?>
                
                <?php
				/*****************
				TRANSFERS
				*****************/
				
				$query="
					select
						date,
						quantity
					from
						transfer_header as h, transfer_detail as d
					where
						h.transfer_header_id = d.transfer_header_id
					and
						status != 'C'
					and
						date <= '$reportdate'
					and
						stock_id = '$stock_id'
				";
				$result=mysql_query($query);
				?>
             
                
                <?php
				while($r=mysql_fetch_assoc($result)):
					
					$data[]= array(
						'date' => $r['date'],
						'transac' => 'STOCK TRANSFERS',
						'qtyin' => 0,
						'qtyout' => $r['quantity']
					);
					
				?>
                <?php
				endwhile;
				?>
                
                <?php
				/*****************
				STOCK RETURNS
				*****************/
				
				$query="
					select
						date,
						quantity
					from
						return_header as h, return_detail as d
					where
						h.return_header_id = d.return_header_id
					and
						status != 'C'
					and
						date <= '$reportdate'
					and
						stock_id = '$stock_id'
				";
				$result=mysql_query($query) or die(mysql_error());
				?>
             
                
                <?php
				while($r=mysql_fetch_assoc($result)):
					
					$data[]= array(
						'date' => $r['date'],
						'transac' => 'STOCK RETURNS',
						'qtyin' => $r['quantity'],
						'qtyout' => 0
						
					);
				?>
                <?php
				endwhile;
				?>
               
                <?php
				/*****************
				PRODUCTION USED
				*****************/
				
				$query="
					select
						date,
						quantity
					from
						production_header as h, production_detail as d
					where
						h.production_header_id = d.production_header_id
					and
						status != 'C'
					and
						date <= '$reportdate'
					and
						d.stock_id = '$stock_id'
				";
				$result=mysql_query($query) or die(mysql_error());
				?>

                <?php
				while($r=mysql_fetch_assoc($result)):
					
					$data[]= array(
						'date' => $r['date'],
						'transac' => 'USED FOR PRODUCTION',
						'qtyin' => 0,
						'qtyout' => $r['quantity']
						
					);
					
				?>
                <?php
				endwhile;
				?>
                
                <?php
				/*****************
				PRODUCTION PRODUCED
				*****************/
				
				$query="
					select
						date,
						actualoutput
					from
						production_header
					where
						stock_id = '$stock_id'
					and	
						status != 'C'
					and
						date <= '$reportdate'
				";
				$result=mysql_query($query) or die(mysql_error());
				?>

                <?php
				while($r=mysql_fetch_assoc($result)):
					$balance-=$r[qty];
					
					$data[]= array(
						'date' => $r['date'],
						'transac' => 'PRODUCED FROM PRODUCTION',
						'qtyin' => $r['actualoutput'],
						'qtyout' => 0
						
					);
				?>
                <?php
				endwhile;
				?>      
                
                <?php
				/*****************
				INVENTORY ADJUSTMENTS
				*****************/
				
				$query="
					select
						date,
						quantity
					from
						invadjust_header as h, invadjust_detail as d
					where
						h.invadjust_header_id = d.invadjust_header_id
					and
						status != 'C'
					and
						date <= '$reportdate'
					and
						stock_id = '$stock_id'
				";
				$result=mysql_query($query) or die(mysql_error());
				?>

                <?php
				while($r=mysql_fetch_assoc($result)):
					
					$data[]= array(
						'date' => $r['date'],
						'transac' => 'INVENTORY ADJUSTMENTS',
						'qtyin' => ($r['quantity']>=0)?$r['quantity']:0,
						'qtyout' =>($r['quantity']<0)?$r['quantity']:0
						
					);
				?>
                <?php
				endwhile;
				?>     
                
					
				<?php	
				$date=array();
				
				if($data):
					foreach ($data as $key => $row) {
						$date[]  = $row['date'];
					}
					array_multisort($date, SORT_ASC, $data);
					$balance=0;
					foreach ($data as $key => $row):
						$balance+=$row[qtyin];
						$balance-=$row[qtyout];
					?>
						<tr>
							<td><?=$row['date']?></td>
							<td><?=$row['transac']?></td>
							<td style="text-align:right"><?=number_format($row['qtyin'],3,'.',',')?></td>
							<td style="text-align:right"><?=number_format($row['qtyout'],3,'.',',')?></td>
							<td style="text-align:right"><?=number_format($balance,3,'.',',')?></td>
						</tr>
					<?php
					endforeach;
				endif;
				?>
            </table>            
        </div><!--End of content-->
    </div><!--End of Form-->
    

  


</div>
</body>
</html>