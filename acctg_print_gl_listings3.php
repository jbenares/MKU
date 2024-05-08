<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
		
	$account		= $_REQUEST['gchart_id'];
	$startingdate	= $_REQUEST['startingdate'];
	$endingdate		= $_REQUEST['endingdate'];	
	$project_id		= $_REQUEST['project_id'];
	$listing_type	= $_REQUEST['listing_type'];
	$sorting_type	= $_REQUEST['sorting_type'];
	$account_id		= $_REQUEST['account_id'];
	$balance		= 0;
	
	$options=new options();	
	
	function getMRRItems($rr_header_id){
		$result = mysql_query("
			select stock from rr_detail as d, productmaster as p where d.stock_id = p.stock_id
			and rr_header_id = '$rr_header_id'
		") or die(mysql_error());
		$content = "";
		$i = 1;
		while($r = mysql_fetch_assoc($result)){
			if($i == 1){
				$content.="$r[stock]";
			}else{
				$content.="<br>$r[stock]";
			}
		}
		return $content;
	}
	
	set_time_limit(700);
	
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
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<style type="text/css">
body
{
	size: legal portrait;
		
	padding:0px;
	font-family:Arial, Helvetica, sans-serif;
	font-size:11px;
}
.container{
	margin:0 auto;
	padding:0.1in;
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
           General Ledger Listing<br />
			As of <?php echo date("F j, Y",strtotime($startingdate))?> to <?php echo date("F j, Y",strtotime($endingdate))?>
        </div>           
        
        <div class="content" >
        	<table cellspacing="0" class="withborder">
            	<tr>
                	<th style="border-top:1px solid #000; border-bottom:1px solid #000; text-align:left;">Date</th>
                    <th style="border-top:1px solid #000; border-bottom:1px solid #000; text-align:left;">Particulars</th>
                    <th style="border-top:1px solid #000; border-bottom:1px solid #000; text-align:left;">Narrative</th>
                    <th style="border-top:1px solid #000; border-bottom:1px solid #000; text-align:left;" >Reference</th>
                	<th style="border-top:1px solid #000; border-bottom:1px solid #000; text-align:left;">Project</th>
                    <th style="border-top:1px solid #000; border-bottom:1px solid #000; text-align:left;">OR#</th>
                    <th style="border-top:1px solid #000; border-bottom:1px solid #000; text-align:right;">Debit </th>
                    <th style="border-top:1px solid #000; border-bottom:1px solid #000; text-align:right;">Credit</th>
                    <th style="border-top:1px solid #000; border-bottom:1px solid #000; text-align:right;">Balance</th>
                </tr>
                   
                
                	
                <tr>
                    <td style="border-top:1px solid #000; border-bottom:1px solid #000; font-weight:bold;"><?=$options->getACodeFromGChartID($account)?></td>
                    <td style="border-top:1px solid #000; border-bottom:1px solid #000; font-weight:bold;"><?=$options->getGchartName($account)?></td>
                    <td style="border-top:1px solid #000; border-bottom:1px solid #000;"></td>
                    <td style="border-top:1px solid #000; border-bottom:1px solid #000;"></td>
                    <td style="border-top:1px solid #000; border-bottom:1px solid #000;"></td>
                    <td style="border-top:1px solid #000; border-bottom:1px solid #000;"></td>
                    <td style="border-top:1px solid #000; border-bottom:1px solid #000;"></td>
                    <td style="border-top:1px solid #000; border-bottom:1px solid #000;"></td>
                    <td style="border-top:1px solid #000; border-bottom:1px solid #000;"></td>
                </tr>
				<?php
				
				$sqlb = mysql_query("select
									*, sum(g.beg_debit) as bdebit, sum(g.beg_credit) as bcredit, g.mclass
									from
									gchart as g
									where
									g.gchart_void = '0' and
									(g.parent_gchart_id = '$account' or g.gchart_id = '$account')") or die (mysql_error());
				$rb = mysql_fetch_assoc($sqlb);					
				
					$bdebit = $rb['bdebit'];
					$bcredit = $rb['bcredit'];
					$mclass = $rb['mclass'];
					if($mclass == 'L' or $mclass == 'R'){
						$balance = $bcredit - $bdebit;
					}else{
						$balance = $bdebit - $bcredit;
					}	

                ?>
                <tr>
                	<td colspan="6" style="border-top:1px solid #000; border-bottom:1px solid #000;">Balance Forwarded</td>
                    <td style="border-top:1px solid #000; border-bottom:1px solid #000; text-align: right;"><?=number_format($bdebit,2,'.',',')?></td>
                    <td style="border-top:1px solid #000; border-bottom:1px solid #000; text-align: right;"><?=number_format($bcredit,2,'.',',')?></td>
                    <td style="border-top:1px solid #000; border-bottom:1px solid #000; text-align: right;"><?=number_format($balance,2,'.',',')?></td>
                </tr>
                
                <?php

                $query="
					select 
					*
					from
					gltran_header as h,
					gltran_detail as d,
					gchart as g
					where
					h.gltran_header_id = d.gltran_header_id and
					d.gchart_id = g.gchart_id and
					h.`status` != 'C' and
					g.gchart_void = '0' and
					h.date between '$startingdate' and '$endingdate' and
					(g.parent_gchart_id = '$account' or g.gchart_id = '$account')
				";
				
				if(!empty($project_id)){
				$query .= "
					and d.project_id = '$project_id'
				";	
				}
				
				if(!empty($account_id)){
				$query .= " and h.account_id = 's-$account_id'";
				}

				if($sorting_type == '1'){
				$query .= "
					order by
						d.project_id asc, date asc
                ";
                }else if($sorting_type == '2'){
				$query .= "
					order by
						h.account_id asc";
				}
				
                $details_result=mysql_query($query) or die(mysql_error());		
				set_time_limit(700);
                ?>  
                <?php
				$project_id = 'x';
				$account_id = 'x';
                while($details_row=mysql_fetch_assoc($details_result)):
                    $debit	= $details_row['debit'];
                    $credit	= $details_row['credit'];
					
					$t_debit += $details_row['debit'];
					$t_credit += $details_row['credit'];
					if($options->acctg_credit_normal_balance($mclass)){
						$balance+=$credit;
						$balance-=$debit;
					}else{
						$balance+=$debit;
						$balance-=$credit;
					}
					
                    $totaldebit+=$debit;
                    $totalcredit+=$credit;		
					
					if($project_id == 'x'){
						$project_id = $details_row['project_id'];
						$subtotal_debit = 0;
						$subtotal_credit = 0;
					}
					
					if($account_id == 'x'){
						$account_id = $details_row['account_id'];
					}
					
					if($sorting_type == '1'){
						if($project_id != $details_row['project_id']){
							echo "
								<tr>
									<td style='border-top:1px solid #000;'></td>
									<td style='border-top:1px solid #000;'></td>
									<td style='border-top:1px solid #000;'></td>
									<td style='border-top:1px solid #000;'></td>
									<td style='border-top:1px solid #000;'></td>
									<td style='border-top:1px solid #000;'></td>
									<td style='text-align:right; font-weight:bold; border-top:1px solid #000;'><span style='border-bottom:1px solid #000;'>".number_format($subtotal_debit,2)."</span></td>
									<td style='text-align:right; font-weight:bold; border-top:1px solid #000;'><span style='border-bottom:1px solid #000;'>".number_format($subtotal_credit,2)."</span></td>
									<td style='text-align:right; font-weight:bold; border-top:1px solid #000;'><span style='border-bottom:1px solid #000;'>".number_format($subtotal_debit-$subtotal_credit,2)."</span></td>
								</tr>
							";
							$project_id = $details_row['project_id'];
							$subtotal_debit = 0;
							$subtotal_credit = 0;
						}
					}else if($sorting_type == '2'){
						if($account_id != $details_row['account_id']){
							echo "
								<tr>
									<td style='border-top:1px solid #000;'></td>
									<td style='border-top:1px solid #000;'></td>
									<td style='border-top:1px solid #000;'></td>
									<td style='border-top:1px solid #000;'></td>
									<td style='border-top:1px solid #000;'></td>
									<td style='border-top:1px solid #000;'></td>
									<td style='text-align:right; font-weight:bold; border-top:1px solid #000;'><span style='border-bottom:1px solid #000;'>".number_format($subtotal_debit,2)."</span></td>
									<td style='text-align:right; font-weight:bold; border-top:1px solid #000;'><span style='border-bottom:1px solid #000;'>".number_format($subtotal_credit,2)."</span></td>
									<td style='text-align:right; font-weight:bold; border-top:1px solid #000;'><span style='border-bottom:1px solid #000;'>".number_format($subtotal_debit-$subtotal_credit,2)."</span></td>
								</tr>
							";
							$account_id = $details_row['account_id'];
							$subtotal_debit = 0;
							$subtotal_credit = 0;
						}						
						
					}
					
					$subtotal_debit	 += $debit;
					$subtotal_credit += $credit;
					
					if($mclass == 'A' or $mclass == 'E'){
						$sub_total = $subtotal_debit - $subtotal_credit;
					}else{
						$sub_total = $subtotal_credit - $subtotal_debit;
					}
                ?>
                    <tr>
                        <td><?=date("m/d/Y", strtotime($details_row['date']))?></td>
                        <?php
						if($details_row['header'] == "rr_header_id"){
							
						echo "
							<td><div style='width: 200px; word-wrap: break-word'>".$options->getGLAccountName($details_row['account_id'])."<br/>".getMRRItems($details_row['header_id'])."</div></td>
						";	
						}else{
                        ?>
                        <td>
							<div style="width: 200px; word-wrap: break-word">
                            <?php
                                 if($account == 5){
                                    echo $options->getAttribute("sales_invoice","sales_invoice_id",$details_row['header_id'],"invoice_no");
                                 }else{
									 $newsupp = substr($details_row['account_id'],2); 
                            ?>
							<?=$options->getGLAccountName($details_row['account_id']);?>
                        	<?php
                                     if($details_row['header'] == "cv_header_id"){
                                         echo "<br>".$options->getAttribute('cv_header','cv_header_id',$details_row['header_id'],'particulars');
                                     } else if (($details_row['gchart_id'] == 75 || $details_row['gchart_id'] == 2694 || $details_row['gchart_id'] == 2695 || $details_row['gchart_id'] == 2696) && ($details_row['header']=="sales_invoice_id")) {
										 echo '/ '.$options->getAttribute("sales_invoice","sales_invoice_id",$details_row['header_id'],"invoice_no");
									 } else {
                                         
                                         if($details_row['particulars']){                    			
                                             echo $details_row['particulars'];
                                         } else {                    				
                                             echo $details_row['description'];
                                         }			
                                     } 
                                 }
                    		?>
							</div>
                        </td>
                        <?php } ?>
                        <td><?=$options->getGchartName($details_row[gchart_id])?></td>
                        <td><?=($details_row['header'] == "cv_header_id") ? "CV#".$options->getAttribute('cv_header','cv_header_id',$details_row['header_id'],'cv_no')." / ".$details_row['generalreference'] : $details_row['xrefer'] ?> / GL#:<?=$details_row['gltran_header_id']?>/CHECK #: <?=$options->getAttribute('cv_header','cv_header_id',$details_row['header_id'],'check_no')?></td>
                        <td><?=$options->getAttribute('projects','project_id',$details_row['project_id'],'project_name')?></td>
                        <td><?=($details_row['header'] == "cr_header_id") ? $options->getAttribute('cr_header','cr_header_id',$details_row['header_id'],'or_no') : "" ?></td>
                        <td align="right"><?=number_format($debit,2,'.',',')?></td>
                        <td align="right"><?=number_format($credit,2,'.',',')?></td>
                        <td align="right"><?=number_format($balance,2,'.',',')?></td>
                    </tr>
                <?php
                endwhile;
                ?>   
                <?php
				echo "
					<tr>
						<td style='border-top:1px solid #000;'></td>
						<td style='border-top:1px solid #000;'></td>
						<td style='border-top:1px solid #000;'></td>
						<td style='border-top:1px solid #000;'></td>
						<td style='border-top:1px solid #000;'></td>
						<td style='border-top:1px solid #000;'></td>
						<td style='text-align:right; font-weight:bold; border-top:1px solid #000;'><span style='border-bottom:1px solid #000;'>".number_format($subtotal_debit,2)."</span></td>
						<td style='text-align:right; font-weight:bold; border-top:1px solid #000;'><span style='border-bottom:1px solid #000;'>".number_format($subtotal_credit,2)."</span></td>
						<td style='text-align:right; font-weight:bold; border-top:1px solid #000;'><span style='border-bottom:1px solid #000;'>".number_format($sub_total,2)."</span></td>
					</tr>
				";
				?>       
                <tr>
                    <td colspan="6" style="border-top:1px solid #000;"><div align="right">Total</div></td>
                    <td style="border-top:1px solid #000; text-align:right;">
							<?php 
							if($listing_type == '1'){
								$new_t_debit = $t_debit;
							}else if($listing_type == '2'){
								$new_t_debit = $t_debit + $bdebit;
							}else if($listing_type == '3'){
								$new_t_debit = $t_debit + getRunningDebit($account,$startingdate);
							}else if($listing_type == '4'){
								$new_t_debit = $t_debit + $bdebit + getRunningDebit($account,$startingdate);
							}
							?>
						<span style="text-align:right; font-weight:bold; border-bottom:3px double #000;"><?=number_format($new_t_debit,2)?></span>
					</td>
                    <td style="border-top:1px solid #000; text-align:right;">
							<?php 
							if($listing_type == '1'){
								$new_t_credit = $t_credit;						
							}else if($listing_type == '2'){
								$new_t_credit = $t_credit + $bcredit;
							}else if($listing_type == '3'){
								$new_t_credit = $t_credit + getRunningCredit($account,$startingdate);
							}else if($listing_type == '4'){
								$new_t_credit = $t_credit + $bcredit + getRunningCredit($account,$startingdate);
							}
							
							?>
						<span style="text-align:right; font-weight:bold; border-bottom:3px double #000;"><?=number_format($new_t_credit,2,'.',',')?></span>
					</td>
                    <td style="border-top:1px solid #000; text-align:right;"><span style="text-align:right; font-weight:bold; border-bottom:3px double #000;"><?=number_format($balance,2)?></span></td>
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