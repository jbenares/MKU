<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	
	$account=$_REQUEST[gchart_id];
	$startingdate=$_REQUEST[startingdate];
	$endingdate=$_REQUEST[endingdate];	
	
	$options=new options();	
	
	$result=mysql_query("
		select
			mclass
		from
			gchart
		where
			gchart_id='$account'
	") or die(mysql_error());
	$r=mysql_fetch_assoc($result);
	$mclass=$r[mclass];
	
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
	
     <div style="margin-bottom:100px;"><!--Start of Form-->
     
     	<div align="center" style="font-size:18pt; font-weight:bolder; margin-bottom:10px;">
        	General Ledger Listing<br />
			As of <?php echo date("m/d/Y",strtotime($startingdate))?> to <?php echo date("m/d/Y",strtotime($endingdate))?>
       	</div>
        

        
        <div class="content" >
        	<table cellspacing="0" class="withborder">
            	<tr>
                	<th>Date</th>
                    <th>Particulars</th>
                    <th>Narrative</th>
                	<th>Reference</th>
                    <th>Debit </th>
                    <th>Credit</th>
                    <th>Balance</th>
                </tr>
                   
                
                	
                <tr>
                    <td><?=$options->getACodeFromGChartID($account)?></td>
                    <td><?=$options->getGchartName($account)?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <?php

                $query="
                    select 
                        *
                    FROM	
                        gltran_header as h,gltran_detail as d
                    where
                        h.gltran_header_id=d.gltran_header_id
                    and
                        gchart_id='$account'
					and
						status!='C'
					and
						date < '$startingdate'
					order by
						date asc
                ";
                
                $details_result=mysql_query($query) or die(mysql_error());		
                ?>  
                <?php
                $totaldebit=0;
                $totalcredit=0;
				
                while($details_row=mysql_fetch_assoc($details_result)):
                    $debit=$details_row[debit];
                    $credit=$details_row[credit];
                    
                    $totaldebit+=$debit;
                    $totalcredit+=$credit;
				endwhile;
				
				if($mclass=="L" || $mclass=="R" || $mclass=="I"){
					$balance=$totalcredit-$totaldebit;
				}else{
					$balance=$totaldebit-$totalcredit;	
				}
                ?>
                <tr>
                	<td colspan="6">Balance Forwarded</td>
                    <td><div align="right"><?=number_format($balance,2,'.',',')?></div></td>
                </tr>
                
                <?php

                $query="
                    select 
                        *
                    FROM	
                        gltran_header as h,gltran_detail as d
                    where
                        h.gltran_header_id=d.gltran_header_id
                    and
                        gchart_id='$account'
					and
						status!='C'
					and
						date between '$startingdate' and '$endingdate'
					order by
						date asc
                ";
                
                $details_result=mysql_query($query) or die(mysql_error());		
                ?>  
                <?php
                $totaldebit=0;
                $totalcredit=0;
                while($details_row=mysql_fetch_assoc($details_result)):
                    $debit=$details_row[debit];
                    $credit=$details_row[credit];
					
					if($mclass=="L" || $mclass=="R" || $mclass=="I"){
						$balance+=$credit;
						$balance-=$debit;
					}else{
						$balance+=$debit;
						$balance-=$credit;
					}
					
                    $totaldebit+=$debit;
                    $totalcredit+=$credit;	
                ?>
                    <tr>
                        <td><?=date("m/d/Y", strtotime($details_row['date']))?></td>
                        <td><?=$options->getGLAccountName($details_row['account_id']);?></td>
                        <td><?=$options->getGchartName($details_row[gchart_id])?></td>
                        <td><?=$details_row[xrefer]?></td>
                        <td align="right"><?=number_format($debit,2,'.',',')?></td>
                        <td align="right"><?=number_format($credit,2,'.',',')?></td>
                        <td align="right"><?=number_format($balance,2,'.',',')?></td>
                    </tr>
                <?php
                endwhile;
                ?>   
                <tr>
                <td colspan="4"><div align="right">Total</div></td>
                <td><div align="right"><?=number_format($totaldebit,2,'.',',')?></div></td>
                <td><div align="right"><?=number_format($totalcredit,2,'.',',')?></div></td>
                <td><div align="right"><?=number_format($balance,2,'.',',')?></div></td>
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