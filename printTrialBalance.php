<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");

	$startingdate=$_REQUEST[startingdate];
	$endingdate=$_REQUEST[endingdate];	
	
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

.alignRight{
	text-align:right;	
}

</style>
</head>
<body>
<div class="container">

	<?php
		$tmpStartingDate=explode("-",$startingdate);
	?>
    
    
     <div style="margin-bottom:100px;"><!--Start of Form-->
     
     	<div align="center" style="font-size:18pt; font-weight:bolder; margin-bottom:10px;">
        	TRIAL BALANCE<br />
			FOR THE MONTH ENDING <?=date("F",strtotime($startingdate));?>, <?=$tmpStartingDate[0]?>
       	</div>
        

        
        <div class="content" >
        	<table cellspacing="0" class="withborder">
            	<tr>
                	<th colspan="2">For the Month</th>
                    <th colspan="4"></th>
                </tr>
                <tr>
                	<th>Debit</th>
                    <th>Credit</th>
                    <th colspan="2">Account Description</th>
                    <th>Year to Date</th>
                    <th>Last Year</th>
                </tr>
                   
             	<tr>
                	<td></td>
                    <td></td>
                    <td>ASSETS</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                
                <?php
				$overallTotalDebit=0;
				$overallTotalCredit=0;
				$overallYearToDate=0;
				$overallLastYear=0;
				
                $query="
                    select 
                        *
                    FROM	
                       gchart
					where
						mclass='A'
					and
						enable='Y'
                ";
                
                $gchart_result=mysql_query($query) or die(mysql_error());		
				
                ?>  
                <?php
				$totaldebit=0;
				$totalcredit=0;
				$totalyeartodate=0;
				$totallastyear=0;
				
                while($gchart_row=mysql_fetch_assoc($gchart_result)):
					$a=$options->solveForDebitCreditForTheMonth($gchart_row['gchart_id'],$startingdate,$endingdate);
					$yeartodate=$options->solveForYearToDate($gchart_row['gchart_id'],$endingdate);
					$lastyear=$options->solveForLastYear($gchart_row['gchart_id'],$endingdate);
					
					$debit=$a[debit];
					$credit=$a[credit];
					
					$totaldebit+=$debit;
					$totalcredit+=$credit;
					$totalyeartodate+=$yeartodate;
					$totallastyear+=$lastyear;
					
					
					if($debit!=0 || $credit!=0 || $lastyear!=0 || $yeartodate!=0):
				?>	
				<tr>
                	<td class="alignRight"><?=number_format($debit,2,'.',',')?></td>
                    <td class="alignRight"><?=number_format($credit,2,'.',',')?></td>
                    <td><?=$gchart_row[gchart]?></td>
                    <td><?=$gchart_row[acode]?></td>
                    <td class="alignRight"><?=number_format($yeartodate,2,'.',',')?></td>
                    <td class="alignRight"><?=number_format($lastyear,2,'.',',')?></td>
                </tr>	
				<?php	
					endif;
				endwhile;
				$overallTotalDebit+=$totaldebit;
				$overallTotalCredit+=$totalcredit;
				$overallYearToDate+=$totalyeartodate;
				$overallLastYear+=$totallastyear;
                ?>
                <tr>
                	<td class="alignRight"><?=number_format($totaldebit,2,'.',',')?></td>
                    <td class="alignRight"><?=number_format($totalcredit,2,'.',',')?></td>
                    <td>TOTAL ASSETS</td>
                    <td></td>
                    <td class="alignRight"><?=number_format($totalyeartodate,2,'.',',')?></td>
                    <td class="alignRight"><?=number_format($totallastyear,2,'.',',')?></td>
                </tr>  
                
                <tr>
                	<td colspan="6"></td>
                </tr>
                
                
                <tr>
                	<td></td>
                    <td></td>
                    <td>LIABILITIES</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                
                <?php
                $query="
                    select 
                        *
                    FROM	
                       gchart
					where
						mclass='L'
					and
						enable='Y'
                ";
                
                $gchart_result=mysql_query($query) or die(mysql_error());		
				
                ?>  
                <?php
				$totaldebit=0;
				$totalcredit=0;
				$totalyeartodate=0;
				$totallastyear=0;
                while($gchart_row=mysql_fetch_assoc($gchart_result)):
					$a=$options->solveForDebitCreditForTheMonth($gchart_row['gchart_id'],$startingdate,$endingdate);
					$yeartodate=$options->solveForYearToDate($gchart_row['gchart_id'],$endingdate);
					$lastyear=$options->solveForLastYear($gchart_row['gchart_id'],$endingdate);
					
					$debit=$a[debit];
					$credit=$a[credit];
					
					$totaldebit+=$debit;
					$totalcredit+=$credit;
					$totalyeartodate+=$yeartodate;
					$totallastyear+=$lastyear;
					
					if($debit!=0 || $credit!=0 || $lastyear!=0 || $yeartodate!=0):
				?>	
				<tr>
                	<td class="alignRight"><?=number_format($debit,2,'.',',')?></td>
                    <td class="alignRight"><?=number_format($credit,2,'.',',')?></td>
                    <td><?=$gchart_row[gchart]?></td>
                    <td><?=$gchart_row[acode]?></td>
                    <td class="alignRight"><?=number_format($yeartodate,2,'.',',')?></td>
                    <td class="alignRight"><?=number_format($lastyear,2,'.',',')?></td>
                </tr>	
				<?php	
					endif;
				endwhile;
				$overallTotalDebit+=$totaldebit;
				$overallTotalCredit+=$totalcredit;
				$overallYearToDate+=$totalyeartodate;
				$overallLastYear+=$totallastyear;
                ?>
                <tr>
                	<td class="alignRight"><?=number_format($totaldebit,2,'.',',')?></td>
                    <td class="alignRight"><?=number_format($totalcredit,2,'.',',')?></td>
                    <td>TOTAL LIABILITIES</td>
                    <td></td>
                    <td class="alignRight"><?=number_format($totalyeartodate,2,'.',',')?></td>
                    <td class="alignRight"><?=number_format($totallastyear,2,'.',',')?></td>
                </tr>  
                
                <tr>
                	<td colspan="6"></td>
                </tr>
                
                <tr>
                	<td></td>
                    <td></td>
                    <td>RETAINED EARNINGS</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                
                <?php
                $query="
                    select 
                        *
                    FROM	
                       gchart
					where
						mclass='R'
					and
						enable='Y'
                ";
                
                $gchart_result=mysql_query($query) or die(mysql_error());		
				
                ?>  
                <?php
				$totaldebit=0;
				$totalcredit=0;
				$totalyeartodate=0;
				$totallastyear=0;
                while($gchart_row=mysql_fetch_assoc($gchart_result)):
					$a=$options->solveForDebitCreditForTheMonth($gchart_row['gchart_id'],$startingdate,$endingdate);
					$yeartodate=$options->solveForYearToDate($gchart_row['gchart_id'],$endingdate);
					$lastyear=$options->solveForLastYear($gchart_row['gchart_id'],$endingdate);
					
					$debit=$a[debit];
					$credit=$a[credit];
					
					$totaldebit+=$debit;
					$totalcredit+=$credit;
					$totalyeartodate+=$yeartodate;
					$totalyeartodate+=$lastyear;
					
					if($debit!=0 || $credit!=0 || $lastyear!=0 || $yeartodate!=0):
				?>	
				<tr>
                	<td class="alignRight"><?=number_format($debit,2,'.',',')?></td>
                    <td class="alignRight"><?=number_format($credit,2,'.',',')?></td>
                    <td><?=$gchart_row[gchart]?></td>
                    <td><?=$gchart_row[acode]?></td>
                    <td class="alignRight"><?=number_format($yeartodate,2,'.',',')?></td>
                    <td class="alignRight"><?=number_format($lastyear,2,'.',',')?></td>
                </tr>	
				<?php	
					endif;
				endwhile;
				$overallTotalDebit+=$totaldebit;
				$overallTotalCredit+=$totalcredit;
				$overallYearToDate+=$totalyeartodate;
				$overallLastYear+=$totallastyear;
                ?>
                <tr>
                	<td class="alignRight"><?=number_format($totaldebit,2,'.',',')?></td>
                    <td class="alignRight"><?=number_format($totalcredit,2,'.',',')?></td>
                    <td>TOTAL RETAINED EARNINGS</td>
                    <td></td>
                    <td class="alignRight"><?=number_format($totalyeartodate,2,'.',',')?></td>
                    <td class="alignRight"><?=number_format($totallastyear,2,'.',',')?></td>
                </tr>  
                
                <tr>
                	<td colspan="6"></td>
                </tr>
                
                <tr>
                	<td></td>
                    <td></td>
                    <td>REVENUE</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                
                <?php
                $query="
                    select 
                        *
                    FROM	
                       gchart
					where
						mclass='I'
					and
						enable='Y'
                ";
                
                $gchart_result=mysql_query($query) or die(mysql_error());		
				
                ?>  
                <?php
				$totaldebit=0;
				$totalcredit=0;
				$totalyeartodate=0;
				$totallastyear=0;
                while($gchart_row=mysql_fetch_assoc($gchart_result)):
					$a=$options->solveForDebitCreditForTheMonth($gchart_row['gchart_id'],$startingdate,$endingdate);
					$yeartodate=$options->solveForYearToDate($gchart_row['gchart_id'],$endingdate);
					$lastyear=$options->solveForLastYear($gchart_row['gchart_id'],$endingdate);
					$debit=$a[debit];
					$credit=$a[credit];
					
					$totaldebit+=$debit;
					$totalcredit+=$credit;
					$totalyeartodate+=$yeartodate;
					$totallastyear+=$lastyear;
					
					if($debit!=0 || $credit!=0 || $lastyear!=0 || $yeartodate!=0):
				?>	
				<tr>
                	<td class="alignRight"><?=number_format($debit,2,'.',',')?></td>
                    <td class="alignRight"><?=number_format($credit,2,'.',',')?></td>
                    <td><?=$gchart_row[gchart]?></td>
                    <td><?=$gchart_row[acode]?></td>
                    <td class="alignRight"><?=number_format($yeartodate,2,'.',',')?></td>
                    <td class="alignRight"><?=number_format($lastyear,2,'.',',')?></td>
                </tr>	
				<?php	
					endif;
				endwhile;
				$overallTotalDebit+=$totaldebit;
				$overallTotalCredit+=$totalcredit;
				$overallYearToDate+=$totalyeartodate;
				$overallLastYear+=$totallastyear;
                ?>
                <tr>
                	<td class="alignRight"><?=number_format($totaldebit,2,'.',',')?></td>
                    <td class="alignRight"><?=number_format($totalcredit,2,'.',',')?></td>
                    <td>TOTAL REVENUE</td>
                    <td></td>
                    <td class="alignRight"><?=number_format($totalyeartodate,2,'.',',')?></td>
                    <td class="alignRight"><?=number_format($totallastyear,2,'.',',')?></td>
                </tr>  
                
                <tr>
                	<td colspan="6"></td>
                </tr>
                
                <tr>
                	<td></td>
                    <td></td>
                    <td>EXPENSE</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                
                <?php
                $query="
                    select 
                        *
                    FROM	
                       gchart
					where
						mclass='E'
					and
						enable='Y'
                ";
                
                $gchart_result=mysql_query($query) or die(mysql_error());		
				
                ?>  
                <?php
				$totaldebit=0;
				$totalcredit=0;
				$totalyeartodate=0;
				$totallastyear=0;
                while($gchart_row=mysql_fetch_assoc($gchart_result)):
					$a=$options->solveForDebitCreditForTheMonth($gchart_row['gchart_id'],$startingdate,$endingdate);
					$yeartodate=$options->solveForYearToDate($gchart_row['gchart_id'],$endingdate);
					$lastyear=$options->solveForLastYear($gchart_row['gchart_id'],$endingdate);
					
					$debit=$a[debit];
					$credit=$a[credit];
					
					$totaldebit+=$debit;
					$totalcredit+=$credit;
					$totalyeartodate+=$yeartodate;
					$totallastyear+=$lastyear;
					
					if($debit!=0 || $credit!=0 || $lastyear!=0 || $yeartodate!=0):
				?>	
				<tr>
                	<td class="alignRight"><?=number_format($debit,2,'.',',')?></td>
                    <td class="alignRight"><?=number_format($credit,2,'.',',')?></td>
                    <td><?=$gchart_row[gchart]?></td>
                    <td><?=$gchart_row[acode]?></td>
                    <td class="alignRight"><?=number_format($yeartodate,2,'.',',')?></td>
                    <td class="alignRight"><?=number_format($lastyear,2,'.',',')?></td>
                </tr>	
				<?php	
					endif;
				endwhile;
				$overallTotalDebit+=$totaldebit;
				$overallTotalCredit+=$totalcredit;
				$overallYearToDate+=$totalyeartodate;
				$overallLastYear+=$totallastyear;
                ?>
                <tr>
                	<td class="alignRight"><?=number_format($totaldebit,2,'.',',')?></td>
                    <td class="alignRight"><?=number_format($totalcredit,2,'.',',')?></td>
                    <td>TOTAL EXPENSES</td>
                    <td></td>
                    <td class="alignRight"><?=number_format($totalyeartodate,2,'.',',')?></td>
                    <td class="alignRight"><?=number_format($totallastyear,2,'.',',')?></td>
                </tr>  
                
                <tr>
                	<td colspan="6"></td>
                </tr>
                
                <tr>
                	<td class="alignRight"><?=number_format($overallTotalDebit,2,'.',',')?></td>
                    <td class="alignRight"><?=number_format($overallTotalCredit,2,'.',',')?></td>
                    <td>TOTAL</td>
                    <td></td>
                    <td class="alignRight"><?=number_format($overallYearToDate,2,'.',',')?></td>
                    <td class="alignRight"><?=number_format($overallLastYear,2,'.',',')?></td>
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