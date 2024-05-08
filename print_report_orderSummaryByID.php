<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$fromdate		= $_REQUEST['fromdate'];
	$todate			= $_REQUEST['todate'];
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
        	ORDER SUMMARY BY ID
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
        	Customer  : <?=$account_name?>
     	</div><!--End of header-->
        
        <div class="content" style="">
        	<table style="text-align:center;">
            	<tr>
                	<th width="20">#</th>
                    <th>Order #</th>
                </tr>	
                <?php
				$result=mysql_query("
					select
						order_header_id
					from
						order_header
					where
						status != 'C'						
					and
						date between '$fromdate' and '$todate'
					and
						account_id = '$account_id'
				") or die(mysql_error());
				$i = 1;
				while($r=mysql_fetch_assoc($result)){
					$order_header_id		= $r['order_header_id'];
					$order_header_id_pad	= str_pad($order_header_id,7,0,STR_PAD_LEFT);
				?>
                	<tr>
                    	<td><?=$i++?></td>
                        <td><?=$order_header_id_pad?></td>
                    </tr>
                <?php
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