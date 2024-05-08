<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	$options=new options();	
	
	$startingdate	= $_REQUEST['startingdate'];
	$endingdate		= $_REQUEST['endingdate'];
	$project_id		= $_REQUEST['project_id'];
	$project = $options->getAttribute("projects","project_id",$project_id,"project_name");
	$checkList2		= $_REQUEST['checkList2'];
		
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
	size: legal portrait;
	padding:0px;
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
}
.header
{
	text-align:left;	
	margin-top:20px;
}

.header table, .content table
{
	text-align:left;
}
.header table td, .content table td
{
	padding:3px;
}

.content table{
	border-collapse:collapse;
}
.content table th{
	border-top:1px solid #000;
	border-bottom:1px solid #000;
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
.line_bottom {
	border-bottom-width: 1px;
	border-bottom-style: solid;
	border-bottom-color: #000000;
	border-left: 0px;
	border-right: 0px;
	border-top: 0px;
	width:120px;
}


</style>
</head>
<body>
<div class="container">
    
     <div style="margin-bottom:100px;"><!--Start of Form-->
     	
        <?php
			require("form_heading.php");
        ?>

        <div style="margin-bottom:20px; font-weight:bolder;">
           	Income Statement<br />
            <?=(!empty($project_id))?$project:"All Projects"?> <br />
			As of <?php echo date("m/d/Y",strtotime($startingdate))?> to <?php echo date("m/d/Y",strtotime($endingdate))?>
        </div>      
        
        <div class="content" >
        	<table cellpadding="3">
                <tr>
                    <th>Account Code</th>
                    <th>Account Description</th>
                    <th>Debit</th>
                    <th>Credit</th>
                </tr>
                <?php
				$grand_total_debit = 0;
				$grand_total_credit = 0;
				?>
                <tr>
                	<td></td>
                    <td style="font-weight:bold;">REVENUE</td>
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
                ";
                
                $gchart_result=mysql_query($query) or die(mysql_error());		
				
                ?>  
                <?php
				$sub_total_debit = $sub_total_credit = 0;
                while($gchart_row=mysql_fetch_assoc($gchart_result)):
					$a=$options->solveForDebitCreditForTheMonth($gchart_row['gchart_id'],$startingdate,$endingdate,$project_id,"Credit",FALSE);
					$debit	= $a['debit'];
					$credit	= $a['credit'];
					
					$sub_total_debit 	+= $debit;
					$sub_total_credit 	+= $credit;
					
					if($debit || $credit):
				?>	
				<tr>
                	
                    <td><?=$gchart_row['acode']?></td>
                    <td><?=$gchart_row['gchart']?></td>
                    <td class="alignRight"><?=number_format($debit,2,'.',',')?></td>
                    <td class="alignRight"><?=number_format($credit,2,'.',',')?></td>
                    
                </tr>	
				<?php	
					endif;
				endwhile;
				$grand_total_debit += $sub_total_debit;
				$grand_total_credit += $sub_total_credit;
				
				$total_revenue = $sub_total_credit - $sub_total_debit;
                ?>
                <tr>
                	<td></td>
                	<td style="font-weight:bold;">TOTAL REVENUE</td>
                	<td class="alignRight"></td>
                    <td style="font-weight:bold;" class="alignRight"><?=number_format($total_revenue,2,'.',',')?></td>
                </tr>  
                
                <tr>
                	<td colspan="6"></td>
                </tr>
                
                <tr>
                	<td></td>
                    <td style="font-weight:bold;">EXPENSE</td>
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
                ";
                
                $gchart_result=mysql_query($query) or die(mysql_error());		
				
                ?>  
                <?php
				$sub_total_debit = $sub_total_credit = 0;
                while($gchart_row=mysql_fetch_assoc($gchart_result)):
					$a=$options->solveForDebitCreditForTheMonth($gchart_row['gchart_id'],$startingdate,$endingdate,$project_id,"Debit",FALSE);
					
					$debit	= $a['debit'];
					$credit	= $a['credit'];
					
					$sub_total_debit += $debit;
					$sub_total_credit += $credit;
					
					
					if($debit || $credit ):
				?>	
				<tr>
                    <td><?=$gchart_row['acode']?></td>
                    <td><?=$gchart_row['gchart']?></td>
                    <td class="alignRight"><?=number_format($debit,2,'.',',')?></td>
                    <td class="alignRight"><?=number_format($credit,2,'.',',')?></td>
                </tr>	
				<?php	
					endif;
				endwhile;
				$grand_total_debit += $sub_total_debit;
				$grand_total_credit += $sub_total_credit;
				
				$totalexpense = $sub_total_debit - $sub_total_credit;
                ?>
                <tr>
                	<td></td>
                	<td style="font-weight:bold;">TOTAL EXPENSES</td>
                	<td style="font-weight:bold;" class="alignRight"></td>
                    <td style="font-weight:bold;" class="alignRight" ><?=number_format($totalexpense,2,'.',',')?></td>
                </tr>  
                
                <tr>
                	<td colspan="6"></td>
                </tr>
                
                <tr>
                	<td></td>
                    <td style="font-weight:bold;">TOTAL NET INCOME/LOSSES</td>
                	<td class="alignRight"></td>
                    <td class="alignRight" style="font-weight:bold; border-bottom:3px double #000;"><?=number_format($totalrevenue-$totalexpense,2,'.',',')?></td>
                </tr>
                
            </table>
            
            <table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:20px;" class="summary">
                <tr>
                    <td>Prepared By:<p>
                        <input type="text" class="line_bottom" /><br>&nbsp;</p></td>
                    <td>Checked By:<p>
                        <input type="text" class="line_bottom" /><br>&nbsp;</p></td>
                    <td>Approved By:<p>
                        <input type="text" class="line_bottom" /><br>&nbsp;</p></td>
                </tr>
            </table>
        </div><!--End of content-->
    </div><!--End of Form-->
   
</div>
</body>
</html>