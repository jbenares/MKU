<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$stock_id 		= $_REQUEST['stock_id'];
	$stock_name 	= $options->getAttribute('productmaster','stock_id',$stock_id,'stock');
	$project_id		= $_REQUEST['project_id'];
	$project_name	= $options->getAttribute('projects','project_id',$project_id,'project_name');
	$from_date		= $_REQUEST['from_date'];
	$to_date		= $_REQUEST['to_date'];


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

body
{		
	padding:0px;
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
	letter-spacing:1px;
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

.align-right{
	text-align:right;	
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
     	
     	<div style="font-weight:bolder;">
        	STOCK CARD REPORT (PROJECT) <br />
           	<?=$stock_name?> <br />
            <?=$project_name?>
        </div>
               	
        <div class="content" >
        	<table cellspacing="0">
            	<tr>
                	<th>Date</th>
                    <th>Type of Transaction</th>
                    <th>Reference</th>
                    <th>Quantity In</th>
                    <th>Quantity Out</th>
                    <th>Balance</th>
                </tr>
                <?php
                $balance=0;
				$beginning_date = date("Y-m-d",strtotime("-1 day",strtotime($from_date)));
				$balance = $options->inventory_projectwarehousebalance($beginning_date,$stock_id,$project_id);
				?>
                <tr>
                	<td colspan="5">Beginning Balance</td>
                    <!-- <td class="align-right"><?=number_format($balance,2,'.',',')?></td> -->
                    <td><?php echo $balance; ?></td>
                </tr>
                
           		
                 <?php
				/*****************
				RECEIVING
				*****************/
				
				$query="
					select
						*
					from
						rr_header as h, rr_detail as d
					where
						h.rr_header_id = d.rr_header_id
					and status != 'C'
					and stock_id = '$stock_id'
					and rr_in = 'P'
					and date between '$from_date' and '$to_date'
					and project_id = '$project_id'
					and rr_type = 'M'
				";
				
			
				$result=mysql_query($query) or die(mysql_error());
				?>
                
                <?php
				while($r=mysql_fetch_assoc($result)):
					$header_id			= $r['rr_header_id'];
					$header_id_pad		= str_pad($header_id,7,0,STR_PAD_LEFT);
					$account_id			= $r['supplier_id'];
					$account			= $options->getAttribute('supplier','account_id',$account_id,'account');
					
					
					$qtyin				= $r['quantity'];
					$qtyout				= 0;
					
					$data[]= array(
						'date' 		=> $r['date'],
						'transac' 	=> "Stocks Receiving ",
						'reference'	=> "M.R.R #: $header_id_pad ",
						'qtyin' 	=> $qtyin,
						'qtyout'	=> $qtyout
						
					);
				endwhile;
				?>
                
                <?php
				/*****************
				STOCKS TRANSFER
				*****************/
				
				$query="
					select
						*
					from
						transfer_header as h, transfer_detail as d
					where
						h.transfer_header_id = d.transfer_header_id
					and
						status != 'C'
					and stock_id = '$stock_id'
					and date between '$from_date' and '$to_date'
					and 
					(
						project_id = '$project_id'
						or from_project_id = '$project_id'
					)
				";
			
				$result=mysql_query($query) or die(mysql_error());
				?>
                
                <?php
				while($r=mysql_fetch_assoc($result)):
					$header_id			= $r['transfer_header_id'];
					$header_id_pad		= str_pad($header_id,7,0,STR_PAD_LEFT);					
										
					
					if( $r['from_project_id'] == $project_id ){ #out

						$data[]= array(
							'date' 		=> $r['date'],
							'transac' 	=> "Stocks Transfer",
							'reference'	=> "T.S #: $header_id_pad ",
							'qtyin' 	=> 0,
							'qtyout'	=> $r['quantity']
						);

					} else if( $r['project_id'] == $project_id ) { #in

						$data[]= array(
							'date' 		=> $r['date'],
							'transac' 	=> "Stocks Transfer",
							'reference'	=> "T.S #: $header_id_pad ",
							'qtyin' 	=> $r['quantity'],
							'qtyout'	=> 0
						);

					}

					
				endwhile;
				?>
                
                
                 <?php
				/*****************
				ISSUANCE
				*****************/
				
				$query="
					select
						*
					from
						issuance_header as h, issuance_detail as d
					where
						h.issuance_header_id = d.issuance_header_id
					and status != 'C'
					and project_id = '$project_id'
					and stock_id = '$stock_id'
					and date between '$from_date' and '$to_date'
				";
			
				$result=mysql_query($query) or die(mysql_error());
				?>
                
                <?php
				while($r=mysql_fetch_assoc($result)):
					$header_id			= $r['issuance_header_id'];
					$header_id_pad		= str_pad($header_id,7,0,STR_PAD_LEFT);					
					
					$qtyin				= 0;
					$qtyout				=  $r['quantity'];
					
					$data[]= array(
						'date' 		=> $r['date'],
						'transac' 	=> "Stocks Issuance",
						'reference'	=> "R.I.S #: $header_id_pad ",
						'qtyin' 	=> $qtyin,
						'qtyout'	=> $qtyout
					);
				endwhile;
				?>
             
                <?php
				/*****************
				INVENTORY ADJUSTMENT
				*****************/
				
				$query="
					select
						h.invadjust_header_id,
						quantity,date
					from
						invadjust_header as h, invadjust_detail as d 
					where
						h.invadjust_header_id=d.invadjust_header_id
					and status != 'C'
					and date between '$from_date' and '$to_date'
					and stock_id='$stock_id'
					and project_id = '$project_id'
				";
				$result=mysql_query($query) or die(mysql_error());
				?>
             
                <?php
				while($r=mysql_fetch_assoc($result)):
					$invadjust_header_id 		= $r['invadjust_header_id'];
					$invadjust_header_id_pad	= str_pad($invadjust_header_id,7,0,STR_PAD_LEFT);
					
					$data[]= array(
						'date' 		=> $r['date'],
						'transac' 	=> "Inventory Adjustments ",
						'reference'	=> "ADJ #: $invadjust_header_id_pad",
						'qtyin' => ($r['quantity']>=0)?$r['quantity']:0,
						'qtyout' =>($r['quantity']<0)?abs($r['quantity']):0
						
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
						*
					from
						return_header as h, return_detail as d
					where
						h.return_header_id = d.return_header_id
					and status != 'C'
					and stock_id = '$stock_id'
					and date between '$from_date' and '$to_date'
					and project_id = '$project_id'
				";
				
			
				$result=mysql_query($query) or die(mysql_error());
				?>
                
                <?php
				while($r=mysql_fetch_assoc($result)):
					$header_id			= $r['return_header_id'];
					$header_id_pad		= str_pad($header_id,7,0,STR_PAD_LEFT);
					$account_id			= $r['supplier_id'];
					$account			= $options->getAttribute('supplier','account_id',$account_id,'account');
					
					$qtyout				= 0;
					$qtyin				= $r['quantity'];
					
					$data[]= array(
						'date' 		=> $r['date'],
						'transac' 	=> "Stock Returns",
						'reference'	=> "RET #: $header_id_pad ",
						'qtyin' 	=> $qtyin,
						'qtyout'	=> $qtyout
						
					);
				endwhile;
				?>


				<?php
				/*****************
				FABRICATION IN (FINISHED PRODUCT)
				*****************/
				
				$query="
					select
						*
					from
						fabrication_product as d 
						inner join fabrication as h on h.fabrication_id = d.fabrication_id
					where
						status != 'C'
					and product_stock_id = '$stock_id'
					and date between '$from_date' and '$to_date'
					and to_project_id = '$project_id'
					and product_void = '0'
				";

				$result=mysql_query($query) or die(mysql_error());
				?>
                
                <?php
				while($r=mysql_fetch_assoc($result)):
					$header_id			= $r['fabrication_id'];
					$header_id_pad		= str_pad($header_id,7,0,STR_PAD_LEFT);
					$account_id			= "";
					$account			= "";
					
					$qtyout				= 0;
					$qtyin				= $r['product_quantity'];
					
					$data[]= array(
						'date' 		=> $r['date'],
						'transac' 	=> "Finished Product Fabrication",
						'reference'	=> "FAB #: $header_id_pad ",
						'qtyin' 	=> $qtyin,
						'qtyout'	=> $qtyout
					);
				endwhile;
				?>

				<?php
				/*****************
				FABRICATION WASTE CUT
				*****************/
				
				$query="
					select
						*
					from
						fabrication
					where
						status != 'C'
					and excess_stock_id = '$stock_id'
					and date between '$from_date' and '$to_date'
					and from_project_id = '$project_id'
				";

				$result=mysql_query($query) or die(mysql_error());
				?>
                
                <?php
				while($r=mysql_fetch_assoc($result)):
					$header_id			= $r['fabrication_id'];
					$header_id_pad		= str_pad($header_id,7,0,STR_PAD_LEFT);
					$account_id			= "";
					$account			= "";
					
					$qtyout				= 0;
					$qtyin				= $r['excess_quantity'];
					
					$data[]= array(
						'date' 		=> $r['date'],
						'transac' 	=> "Finished Product Waste Cut",
						'reference'	=> "FAB #: $header_id_pad ",
						'qtyin' 	=> $qtyin,
						'qtyout'	=> $qtyout
					);
				endwhile;
				?>

				<?php
				/*****************
				FABRICATION OUT (RAW MATS)
				*****************/
				
				$query="
					select
						*
					from
						fabrication_raw_mat as d 
						inner join fabrication as h on h.fabrication_id = d.fabrication_id
					where
						status != 'C'
					and raw_mat_stock_id = '$stock_id'
					and date between '$from_date' and '$to_date'
					and from_project_id = '$project_id'
					and raw_mat_void = '0'
				";

				$result=mysql_query($query) or die(mysql_error());
				?>
                
                <?php
				while($r=mysql_fetch_assoc($result)):
					$header_id			= $r['fabrication_id'];
					$header_id_pad		= str_pad($header_id,7,0,STR_PAD_LEFT);
					$account_id			= "";
					$account			= "";
					
					$qtyout				= $r['raw_mat_quantity'];
					$qtyin				= 0;
					
					$data[]= array(
						'date' 		=> $r['date'],
						'transac' 	=> "Raw Mat Fabrication",
						'reference'	=> "FAB #: $header_id_pad ",
						'qtyin' 	=> $qtyin,
						'qtyout'	=> $qtyout
					);
				endwhile;
				?>

				<?php
				/*****************
				SALES RETURN
				*****************/
				
				$query="
					select
						*
					from
						sales_return_header as h
						inner join sales_return_detail as d on h.sales_return_header_id = d.sales_return_header_id
					where
						status != 'C'
					and stock_id = '$stock_id'
					and date between '$from_date' and '$to_date'
					and project_id = '$project_id'
					and sales_return_void = '0'
				";

				$result=mysql_query($query) or die(mysql_error());
				?>
                
                <?php
				while($r=mysql_fetch_assoc($result)):
					$header_id			= $r['sales_return_header_id'];
					$header_id_pad		= str_pad($header_id,7,0,STR_PAD_LEFT);
					$account_id			= "";
					$account			= "";
					
					$qtyout				= 0;
					$qtyin				= $r['quantity'];
					
					$data[]= array(
						'date' 		=> $r['date'],
						'transac' 	=> "Sales Return",
						'reference'	=> "SR #: $header_id_pad ",
						'qtyin' 	=> $qtyin,
						'qtyout'	=> $qtyout
					);
				endwhile;
				?>
                
                <?php
				$date=array();
				
				if($data):
					foreach ($data as $key => $row) {
						$date[]  = $row['date'];
					}
					
					array_multisort($date, SORT_ASC, $data);
					
					foreach ($data as $key => $row):
						$balance += $row[qtyin];
						$balance -= $row[qtyout];
						
						$quantity_in 	= $row[qtyin];
						$quantity_out 	= $row[qtyout];
						
						$reference		= $row['reference'];
						$account		= $row['account'];
						
						
						if($quantity_in!=0 || $quantity_out!=0){
					?>
						<tr>
							<td><?=date("n/j/Y",strtotime($row['date']))?></td>
							<td><?=$row['transac']?></td>
                            <td><?=$reference?></td>
							<td class="align-right"><?=number_format($row['qtyin'],2,'.',',')?></td>
							<td class="align-right"><?=number_format($row['qtyout'],2,'.',',')?></td>
							<td class="align-right"><?=number_format($balance,2,'.',',')?></td>
						</tr>
					<?php
						}
					endforeach;
				endif;
				?>	
			
            </table>            
        </div><!--End of content-->
    </div><!--End of Form-->

</div>
</body>
</html>