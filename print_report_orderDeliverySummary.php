<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$fromdate		= $_REQUEST['fromdate'];
	$todate			= $_REQUEST['todate'];
	$account_id		= $_REQUEST['account_id'];
	$option			= $_REQUEST['option'];
	
	
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
     	
     	<div align="right" style="font-size:18pt; font-weight:bolder;">
        	ORDER VS DELIVERY
        </div>
        
        <div align="right" style="margin:20px 0px;">	
        	Date: <div align="center" style="display:inline-block; border-bottom:1px dashed #000; width:150px; height:1em;"><?php echo date("m/d/Y")?></div>
        </div>
        
        <?php
		if(empty($account_id)){
			$accounts = $options->report_accountsWithOrdersBetweenDates($fromdate,$todate);
		}else{
			$accounts = array();
			array_push($accounts,$account_id);
		}
		foreach($accounts as $account_id){
			$account_name	= (!empty($account_id))?$options->getAccountName($account_id):"All Accounts";
        ?>   
        <div class="header">
        	<table>
            	<tr>
                	<td width="25%">Customer :</td>
                    <td width="75%"><?=$account_name?></td>
                </tr>
                <tr>
                	<td>Date Covered</td>
                    <td><?=date("F j, Y",strtotime($fromdate))?> to <?=date("F j, Y",strtotime($todate))?> </td>
                </tr>            
            </table>

     	</div><!--End of header-->
        
        <div class="content" style="">
        	<table>
            	<tr>
                	<th width="20">#</th>
                    <th>Item Description</th>
                    <th>Ordered</th>
                    <th>Delivered</th>
                    <th>Over / ( Under )</th>
                </tr>	
                <?php
				$result=mysql_query("
					select
						stock_id,
						sum(quantity) as quantity
					from
						order_header as h, order_details as d
					where
						h.order_header_id= d.order_header_id
					and
						status != 'C'						
					and
						date between '$fromdate' and '$todate'
					and
						account_id = '$account_id'
					group by
						stock_id
				") or die(mysql_error());
				$i = 1;
				while($r=mysql_fetch_assoc($result)){
					$stock_id		= $r['stock_id'];
					$stock_name		= $options->stockAttr($stock_id,'stock');
					$ordered		= $r['quantity'];
					$delivered		= $options->report_stocksDelivered($fromdate,$todate,$stock_id,$account_id);
					
					
					if($ordered > $delivered){
						$diff = $ordered - $delivered;
						$drstatus =	"( $diff )";
					}else{
						$drstatus =	$delivered - $ordered;
					}
					
					if(empty($option) || ( $ordered > $delivered)){
				?>
                	<tr>
                    	<td><?=$i++?></td>
                        <td><?=$stock_name?></td>
                        <td class="align-right"><?=number_format($ordered,2,'.',',')?></td>
                        <td class="align-right"><?=number_format($delivered,2,'.',',')?></td>
                        <td class="align-center"><?=$drstatus?></td>
                    </tr>
                <?php
					}
				}
                ?>
            </table>        
        </div><!--End of content-->
       	<?php
		}
        ?>
    </div><!--End of Form-->
    

  


</div>
</body>
</html>