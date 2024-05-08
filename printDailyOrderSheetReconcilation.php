<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$date = $_REQUEST['date'];
	$accounts = $options->getAllAccounts();
	
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
     	<div style="margin-bottom:20px;" >
	        <span><?=$title?></span>
            <div style="text-align:center; font-weight:bolder; ">
	        	DAILY OUTLETS ORDER SHEET RECONCILATION
            </div>
        </div>
                
        <div class="content">
        	<table>
			<tr>
				<th>Product</th>
				<th>Outlet</th>
                <th>Order</th>
                <th>Delivered</th>
                <th>Balance</th>
			</tr>
			<?php
			$i=1;
			$balance=0;
            $result=mysql_query("
                select
                    *
                from
                    productmaster as p, categories as c
                where
                    p.categ_id1 = c.categ_id
				and
					category = 'PASALUBONG'
                order by 
                    stock
            ") or die(mysql_error());
			
			while($r=mysql_fetch_assoc($result)){
				$stock 		= $r[stock];
				$stock_id 	= $r[stock_id];
				
				foreach($accounts as $account_id){
					
				$ordered	= $options->getOrdersFromAccount($date,$account_id,$stock_id);
				$delivered 	= $options->getDeliveriesFromAccount($date,$account_id,$stock_id);
				
				$balance+=$ordered;
            ?>
            <tr> 
            	<td><?=$stock?></td>
                <td><?=$options->getAccountName($account_id)?></td>  
                <td><?=$ordered?></td>
                <td></td>
                <td><?=$balance?></td>
			</tr>
            <?php
			$balance-=$delivered;
            ?>
             <tr> 
            	<td><?=$stock?></td>
                <td><?=$options->getAccountName($account_id)?></td>  
                <td></td>
                <td><?=$delivered?></td>
                <td><?=$balance?></td>
			</tr>
           	<?php
				}
			}
            ?>
		</table>
        </div><!--End of content-->
    </div><!--End of Form-->
    

  


</div>
</body>
</html>