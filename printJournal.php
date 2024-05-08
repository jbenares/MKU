<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$journal_code=$_REQUEST[journal_code];
	
	$query="
		select
			*
		from 
			dr_header
		where
			dr_header_id='$dr_header_id'
	";
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);
	$user_id=$r[user_id];

	
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
	/*border:1px solid #000;*/
	padding:10px;
}
.withborder td,.withborder th{
	border:1px solid #000;
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


</style>
</head>
<body>
<div class="container">
	<?php	
		//DISBURSEMENT JOURNAL
		$query="
			select 
				*
			FROM	
				gltran_header
			where
				journal_id='1'
		";
		$result0=mysql_query($query) or die(mysql_error());
		
		while($r0=mysql_fetch_assoc($result0)):
	?>          
	
     <div style="margin-bottom:100px;"><!--Start of Form-->
     
        <div align="left" style="font-size:22pt; font-weight:bolder; text-decoration:underline;">Disbursement Voucher</div>
        
        
        <div class="header" style="">
        	<table style="width:100%;">
                <tr>
                	<td width="19%">Name of Payee:</td>
                    <td width="50%"><?=$r0[account]?></td>
                    <td width="9%">Check #: </td>
                    <td width="22%"><?=$r0[mcheck]?></td>
               	</tr>
                <tr>
					<td>Address:</td>
                    <td><?=$r0[address]?></td>
                    <td>Date:</td>
                    <td><?=$r0['date']?></td>
               	</tr>
               
            </table>
     	</div><!--End of header-->
        <?php
	
			
			$query="
				select 
					*
				FROM	
					gltran_header as h,gltran_detail as d
				where
					h.gltran_header_id=d.gltran_header_id
				and
					journal_id='1'
				and
					d.gltran_header_id='$r0[gltran_header_id]'
			";
			
			$result=mysql_query($query) or die(mysql_error());		
		?>
        <div class="content" >
        	<table cellspacing="0" class="withborder">
            	<tr>
                	<th>Account Description</th>
                    <th>Acode</th>
                    <th>Debit #</th>
                    <th>Credit</th>
                </tr>
           		<?php
				$totaldebit=0;
				$totalcredit=0;
				while($r=mysql_fetch_assoc($result)):
					$debit=$r[debit];
					$credit=$r[credit];
					
					$totaldebit+=$debit;
					$totalcredit+=$credit;
				?>
                    <tr>
                        <td><?=$options->getGchartName($r[gchart_id])?></td>
                        <td><?=$r[gchart_id]?></td>
                        <td align="right"><?=number_format($debit,2,'.',',')?></td>
                        <td align="right"><?=number_format($credit,2,'.',',')?></td>
                    </tr>
                <?php
				endwhile;
				?>
                 
            
                <tr>
                	<td colspan="2"><div align="right">Total</div></td>
                    <td><div align="right">P <?=number_format($totaldebit,2,'.',',')?></div></td>
                    <td><div align="right">P <?=number_format($totalcredit,2,'.',',')?></div></td>
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
    <?php
	endwhile;
	?>

  


</div>
</body>
</html>