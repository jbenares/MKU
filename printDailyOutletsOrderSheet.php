<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$date = $_REQUEST['date'];
	
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
	        	DAILY OUTLETS ORDER SHEET
            </div>
        </div>
                
        <div class="content">
        	<table>
			<tr>
				<th width="20"><div class="verticalText"></div></th>
				<th><div>Date : <?=$date?></div></th>
                <?php
				$result=mysql_query("
					select
						*
					from
						account
					order by
						account
				") or die(mysql_error());
				?>
                <?php
				$location_array=array();
				while($r=mysql_fetch_assoc($result)){
					$account_array[] = $r[account_id];
					$account = $r[account];
                ?>
				<th><div class="verticalText"><?=$account?></div></th>
                <?php
				}
                ?>
                <th><div class="verticalText">Total</div></th>
			</tr>
            <tr>
            	<td colspan="<?=count($account_id)+2?>">PASALUBONG</td>
            </tr>
			<?php
			$i=1;
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
            ?>
            <tr>   
				<td><?=$i++?></td>
				<td><?=$stock?></td>
                <?php
				$total = 0;
				foreach($account_array as $account_id){
					$orders = $options->getOrdersFromAccount($date,$account_id,$stock_id);
					$total+=$orders;
                ?>
                	<td><?=($orders)?number_format($orders,0,'.',','):""?></td>
                <?php
				}
                ?>
                <td><?=($total)?number_format($total,0,'.',','):""?></td>
			</tr>
           	<?php
			}
            ?>
            <tr>
            	<td colspan="<?=count($account_id)+2?>">FRESH BREAD</td>
            </tr>
            <?php
            $result=mysql_query("
                select
                    *
                from
                    productmaster as p, categories as c
                where
                    p.categ_id1 = c.categ_id
				and
					category = 'FRESH BREAD'
                order by 
                    stock
            ") or die(mysql_error());
			
			while($r=mysql_fetch_assoc($result)){
				$stock 		= $r[stock];
				$stock_id 	= $r[stock_id];
            ?>
            <tr>   
				<td><?=$i++?></td>
				<td><?=$stock?></td>
                <?php
				$total = 0;
				foreach($account_array as $account_id){
					$orders = $options->getOrdersFromAccount($date,$account_id,$stock_id);
					$total+=$orders;
                ?>
                	<td><?=($orders)?number_format($orders,0,'.',','):""?></td>
                <?php
				}
                ?>
                <td><?=($total)?number_format($total,0,'.',','):""?></td>
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