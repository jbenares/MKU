<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$service_rr_header_id=$_REQUEST[id];
	
	$query="
		select
			  *
		 from
			  service_rr_header as h,
			  projects as p
		 where
			h.project_id = p.project_id
		and
			service_rr_header_id = '$service_rr_header_id'
	";
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);
	$user_id		= $r['user_id'];
	$project_id		= $r['project_id'];
	$project_name	= $r['project_name'];
	$date			= $r['date'];
	$po_header_id	= $r['po_header_id'];
	$po_header_id_pad	= str_pad($po_header_id,7,0,STR_PAD_LEFT);
	$rr_header_id_pad	= str_pad($rr_header_id,7,0,STR_PAD_LEFT);
	$supplier_id 	= $r['supplier_id'];
	$supplier		= $options->attr_Supplier($supplier_id,'account');


	
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
  
  .page-break{
		display:block;
		page-break-before:always;  
  }
}
	
body
{
	size: legal portrait;
		
	padding:0px;
	/*margin:0px;*/
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
	letter-spacing:2px;
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
.content{
	margin-top:20px;	
}

.content table{
	border-collapse:collapse;
}
.content table td,.content table th{
	border:1px solid #000;
	padding:5px;
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

.footer td{
	border:none;
}

.align-right{
	text-align:right;	
}

.inline{
	display:inline-block;	
}

</style>
</head>
<body>
<div class="container">
	
    	<?php
			require("form_heading.php");
        ?>
    
	
            <div style="text-align:right; font-weight:bolder;">
                S.R.R #. : <?=str_pad($rr_header_id,7,0,STR_PAD_LEFT)?><br />
            </div>   
            <div style="text-align:center; font-size:14px;">
                Service Receiving Report
            </div>
        <div class="header" style="">
        	<table style="width:100%;">
            <tr>
                <td width="19%">Supplier:</td>
                <td width="47%" style="border-bottom:1px solid #000;"><?=$supplier?></td>
                
                <td width="7%">Date:</td>
                <td width="27%" style="border-bottom:1px solid #000;"><?=date("F j, Y",strtotime($date))?></td>
            </tr>
            <tr>
              <td>Project / Section:</td>
              <td style="border-bottom:1px solid #000;"><?=$project_name?></td>
              
              <td>PO #:</td>
              <td style="border-bottom:1px solid #000;"><?=$po_header_id_pad?></td>
            </tr>
           
        </table>
     	</div><!--End of header-->
        <?php
	
			
			$query="
				select
					*
				from
					service_rr_detail as d, productmaster as p
				where
					d.stock_id = p.stock_id
				and
					service_rr_header_id = '$service_rr_header_id'
			";
			
			$result=mysql_query($query) or die(mysql_error());		
		?>
        <div class="content" >
        	<table cellspacing="0" style="margin-top:30px;">
            	<tr>
                	<th>Designation</th>
                    <th width="60">No</th>
                    <th width="60">No. of Days</th>
                    <th width="60">Rate / Day</th>
                    <th width="60">Amount</th>
                    
                </tr>
           		<?php
				$totalamount=0;
				while($r=mysql_fetch_assoc($result)):
					$stock_id 		= $r['stock_id'];
					$quantity 		= $r['quantity'];
					$stock 			= $r['stock'];
					$days			= $r['days'];
					$rate_per_day	= $r['rate_per_day'];
					$amount			= $r['amount'];
					
					$totalamount += $amount;
					
				?>
                    <tr>
                        <td><?=$stock?></td>
                        <td class="align-right"><?=$quantity?></td>
                        <td class="align-right"><?=$days?></td>
                        <td class="align-right"><?=number_format($rate_per_day,2,'.',',')?></td>
                        <td class="align-right"><?=number_format($amount,2,'.',',')?></td>
                    </tr>
                <?php
				endwhile;
				?>
                
            </table>
            <div style="text-align:right; margin-top:10px;">
            	Total > <span style="padding:0px 5px; border-bottom:1px solid #000; font-weight:bolder;"><?=number_format($totalamount,2,'.',',')?></span>
            </div>
            
            <div style="margin-top:20px;">
				<div class="inline" style="width:200px; margin:0px 10px;">
                	<span style="margin-bottom:30px; display:block;">Received & Checked by:</span>
                    <span style="height:30px;">
	                	&nbsp;
                   	</span>
                	<p style="border-top:1px solid #000; padding-top:5px; text-align:center;" >
                    	Warehouseman
                    </p>
                </div>
                
                <div class="inline" style="width:200px; margin:0px 10px; vertical-align:top;">
                	<span style="margin-bottom:30px; display:block;">Noted by:</span>
                    <span style="height:30px;">
	                	&nbsp;
                   	</span>
                	<p style="border-top:1px solid #000; padding-top:5px; text-align:center;" >
                    	P.I.C / MCD Head / Finance
                    </p>
                </div>
                <div class="inline" style="width:200px; margin:0px 10px;">
                	<span style="margin-bottom:30px; display:block;">Encoded by: </span>
                    <span style="height:30px;">
	                	<?=$options->getUserName($user_id);?>
                   	</span>
                	<p style="border-top:1px solid #000; padding-top:5px; text-align:center;" >&nbsp;
                    	
                    </p>
                </div>
            </div>
            
            
            
            
        </div><!--End of content-->
    </div><!--End of Form-->
    

  


</div>
</body>
</html>