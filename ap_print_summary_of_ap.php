<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");

	$from=$_REQUEST['from'];
	$to=$_REQUEST['to'];
	
	$options=new options();	
	
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
	font-family:Arial, Helvetica, sans-serif;
	font-size:11px;
}
.container{
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
	/*border:1px solid #000;*/
	padding:3px;
}
.withborder td,.withborder th{
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

.noborder{
	border:none;	
}

.alignRight{
	text-align:right;	
}

</style>
</head>
<body>
<div class="container">
    
     <div style="margin-bottom:100px;"><!--Start of Form-->
     
     	<?php
			require("form_heading.php");
        ?>

        <div style="text-align:center; font-size:14px; margin-bottom:20px;">
           	SUMMARY OF ACCOUNTS PAYABLE<br />
			<span style="font-size:8px; font-style:italic;">From <?=date("F j, Y",strtotime($from))?>To <?=date("F j, Y",strtotime($to))?></span>
        </div>   
             
        <div class="content" >
        	<table cellspacing="0" class="withborder">
                <tr>
                	<th style="border-top:1px solid #000; border-bottom:1px solid #000; text-align:left;">SUPPLIER</th>
                    <th style="border-top:1px solid #000; border-bottom:1px solid #000; text-align:right;">CHARGES</th>
                    <th style="border-top:1px solid #000; border-bottom:1px solid #000; text-align:right;">PAYMENTS</th>
                    <th style="border-top:1px solid #000; border-bottom:1px solid #000; text-align:right;">BALANCE</th>
                </tr>
                <?php
                $query="
                    select 
                        *
                    FROM	
                       supplier
					order by account asc
                ";
                
                $result=mysql_query($query) or die(mysql_error());		
				
                ?>  
                <?php
				$total_charges = $total_disbursement = $total_balance = 0;
                while($r=mysql_fetch_assoc($result)):
					$supplier=$r[account];
					$supplier_id=$r[account_id];
					
					set_time_limit(30);
					$charge=$options->getBalanceFromSupplier($from,$to,$supplier_id);
					$disbursement=$options->getDisbursementForSupplier($from,$to,$supplier_id);
					
					$balance=$charge-$disbursement;	
					
					
					if($balance > 0 or $balance < 0){
						
					$total_charges += $charge;
					$total_disbursement += $disbursement;
					$total_balance += $balance;
					
					
				?>	
                    <tr>
                        <td><?=$supplier?></td>
                        <td style="text-align:right;"><?=number_format($charge,2,'.',',')?></td>
                        <td style="text-align:right;"><?=number_format($disbursement,2,'.',',')?></td>
                        <td style="text-align:right;"><?=number_format($balance,2,'.',',')?></td>
                        
                    </tr>	
				<?php	
					}
				endwhile;
                ?>
                <tr>
                	<td style="text-align:right; border-top:1px solid #000;"></td>
                    <td style="text-align:right; border-top:1px solid #000;"><?=number_format($total_charges,2,'.',',')?></td>
                    <td style="text-align:right; border-top:1px solid #000;"><?=number_format($total_disbursement,2,'.',',')?></td>
                    <td style="text-align:right; border-top:1px solid #000;"><?=number_format($total_balance,2,'.',',')?></td>
                </tr>

            </table>
            <table  class="noborder" style="border:none; margin-top:20px;">
            	<tr>
                	<td>Prepared by:</td>
                    <td>Checked by:</td>
                    <td>Approved by:</td>
                    <td>Released by:</td>
                    <td>Received by:</td>
                </tr>
            </table>
        </div><!--End of content-->
    </div><!--End of Form-->
   
</div>
</body>
</html>