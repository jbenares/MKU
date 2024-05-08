<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	
	$journal_id=$_REQUEST[journal_id];
	$startingdate=$_REQUEST[startingdate];
	$endingdate=$_REQUEST[endingdate];	
	$ris= $_REQUEST[ris];

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
	font-size:10px;
}
.container{
	width:11in;
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
     	
        <?php
			require("form_heading.php");
        ?>

        <div style="text-align:center; font-size:14px; margin-bottom:20px;">
           Journal Listing - <?=$options->getJournalName($journal_id);?><br />
			As of <?php echo date("F j, Y",strtotime($startingdate))?> to <?php echo date("F j, Y",strtotime($endingdate))?>
        </div>   

        
        <div class="content" >
        	<table cellspacing="0" class="withborder">
            	<tr>
                	<th width="8%">Date</th>
                    <th width="14%">Reference</th>
                    <th width="20%">Cross Reference</th>
                	<th width="20%">Payee / Account</th>
                    <th width="13%">Explanation</th>
                    <th width="13%">Debit #</th>
                    <th width="12%">Credit</th>
                </tr>
               	<?php

				//DISBURSEMENT JOURNAL
				$query="
					select 
						*
					FROM	
						gltran_header
					where
						journal_id='$journal_id'
					and
						date between '$startingdate' and '$endingdate'
					and
						status!='C'	
					
				";
				
				if($journal_id=='2'){
					if(!empty($ris)){
						$query.="AND (
										header_id !=  '0'
											AND 
										header !=  ''
									)";
					}else{
						$query.="AND (
										header_id =  '0'
											OR 
										header =  ''
									)
					";
					}
				} 
				$query.="Order by xrefer asc";
				#echo $query;
				$header_result=mysql_query($query) or die(mysql_error());

				$grandtotaldebit=0;
				$grandtotalcredit=0;
				while($header_row=mysql_fetch_assoc($header_result)):
				?>        
                
                	
                	<tr>
                        <td><?=$header_row['date']?></td>
                        <td>
	                        <?php
	                        	if($journal_id==1){
	                        		?>
	                        			CV #: <?=$options->getAttribute("cv_header","cv_header_id",$header_row[header_id],"cv_no")?>
	                        		<?php
	                        	}
								else{
	              
	                					echo $header_row[xrefer];		
	                        	}
	                        ?>
                    	</td>
                        <td>
						    <?php
	                        	if($journal_id==3){
	                        		?>
	                        			OR #: <?=$options->getAttribute("cr_header","cr_header_id",$header_row[header_id],"or_no")?>
	                        		<?php
	                        	}
								/*if($journal_id==7){
									?>
	                        			 <?=$options->getAttribute("sales_invoice","sales_invoice_id",$header_row[header_id],"invoice_no")?>
	                        		<?php
								}*/
								else{
	              
	                					echo $header_row[mcheck];		
	                        	}
	                        ?>
						</td>
                        <td><?=$options->getGLAccountName($header_row[account_id])?></td>
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
							journal_id='$journal_id'
						and
							d.gltran_header_id='$header_row[gltran_header_id]'
						and
							status!='C'
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
                    ?>
                        <tr>
                        	<td></td>
                            <td></td>
                            <td></td>
                            <td><?=$options->getACodeFromGChartID($details_row[gchart_id])?></td>
                            <td><?=$options->getGchartName($details_row[gchart_id])?></td>
                            <td align="right"><?=number_format($debit,2,'.',',')?></td>
                            <td align="right"><?=number_format($credit,2,'.',',')?></td>
                        </tr>
                    <?php
                    endwhile;
                    ?>   
                <tr>
                	<td colspan="5"><div align="right">Total</div></td>
                    <td><div align="right">P <?=number_format($totaldebit,2,'.',',')?></div></td>
                    <td><div align="right">P <?=number_format($totalcredit,2,'.',',')?></div></td>
                </tr>           
                <?php
                $grandtotalcredit+=$totalcredit;
                $grandtotaldebit+=$totaldebit;
				endwhile;
				?>
            	<tr><td colspan="7"></td></tr>
            	 <tr style="font-weight:bolder;">
                	<td colspan="5"><div align="right">Grand Total</div></td>
                    <td><div align="right">P <?=number_format($grandtotaldebit,2,'.',',')?></div></td>
                    <td><div align="right">P <?=number_format($grandtotalcredit,2,'.',',')?></div></td>
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