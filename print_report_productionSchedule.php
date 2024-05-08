<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$date			= $_REQUEST['date'];
	$account_id		= $_REQUEST['account_id'];
	
	
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
        	PRODUCTION SCHEDULE
        </div>
        
        <div align="right" style="margin:20px 0px;">	
        	Date: <div align="center" style="display:inline-block; border-bottom:1px dashed #000; width:150px; height:1em;"><?php echo date("m/d/Y")?></div>
        </div>
        
        <div class="header">
        	Production Date : <?=date("F j, Y", strtotime($date))?>
        </div>
        
        <div class="content" style="">
        	<table>
            	<tr>
                	<th width="20">#</th>
                    <th>Item Description</th>
                    <th>Beginning Balance</th>
                    <th>Buffer</th>
                    <th>Ordered</th>
                    <th>Produce</th>
                    <th>Remarks</th>
                </tr>	
                <?php
				$result=mysql_query("
					select
						*
					from
						production
					where
						status != 'C'
					and
						date = '$date'
				") or die(mysql_error());
				$i = 1;
				while($r=mysql_fetch_assoc($result)){
					$production_id		= $r['production_id'];
					$stock_id			= $r['stock_id'];
					$stock_name			= $options->attr_stock($stock_id,'stock');
					$required			= $r['required'];
					$actual				= $r['actual'];
					$buffer				= $r['buffer'];
					$orders				= $r['orders'];
					$beginning_balance	= $r['beginning_balance'];
					
				?>
                	<tr>
                    	<td><?=$i++?></td>
                        <td><?=$stock_name?></td>
                        <td class="align-right"><?=number_format($beginning_balance,2,'.',',')?></td>
                        <td class="align-right"><?=number_format($buffer,2,'.',',')?></td>
                        <td class="align-right"><?=number_format($orders,2,'.',',')?></td>
                        <td class="align-right"><?=number_format($required,2,'.',',')?></td>
                        <td class="align-center"><?=$options->report_productionFormulation($production_id)?></td>
                    </tr>
                <?php
				}
                ?>
            </table>        
        </div><!--End of content-->
    </div><!--End of Form-->
    

  


</div>
</body>
</html>