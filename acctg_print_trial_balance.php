<?php
require_once('my_Classes/options.class.php');
include_once("conf/ucs.conf.php");
require_once(dirname(__FILE__).'/library/lib.php');

$from = $_REQUEST['from'];
$to   = $_REQUEST['to'];

$options=new options();	

function getBalanceAccountBalanceForwarded($from,$to,$gchart_id,$debit=TRUE){
	/*get beginning balance*/
	$options 	= new options();
	$mclass 	= $options->getAttribute('gchart','gchart_id',$gchart_id,'mclass');
	//$beg_bal 	= $options->getAttribute('gchart','gchart_id',$gchart_id,'beg_bal');
        $beg_bal = 0;
	#$beg_bal = lib::getAttribute('gchart','gchart_id',$gchart_id,'beg_bal');
	
	$balance=0;
	if($options->acctg_credit_normal_balance($mclass)){
				$result = mysql_query("
							select
								g.gchart_id,
								(sum(credit) - sum(debit)) as balance
							from
								gltran_header as h, gltran_detail as d, gchart as g
							where
								h.gltran_header_id = d.gltran_header_id
							and d.gchart_id = g.gchart_id
							and g.gchart_id = '$gchart_id'
							and h.date between '$from' and '$to'
							and status != 'C'
							group by g.gchart_id
							order by g.gchart_id asc
					
			") or die(mysql_error());
			$r = mysql_fetch_assoc($result);
			$r['balance'] -= $beg_bal;
			$balance=$r[balance];
	}else{
			$result = mysql_query("
							select
								g.gchart_id,
								(sum(debit) - sum(credit)) as balance
							from
								gltran_header as h, gltran_detail as d, gchart as g
							where
								h.gltran_header_id = d.gltran_header_id
							and d.gchart_id = g.gchart_id
							and g.gchart_id = '$gchart_id'
							and h.date between '$from' and '$to'
							and status != 'C'
							group by g.gchart_id
							order by g.gchart_id asc
							/*and parent_gchart_id = '0'*/
					
			") or die(mysql_error());
			$r = mysql_fetch_assoc($result);
			$r['balance'] += $beg_bal;
			$balance=$r[balance];
	}
	
	return $balance;
	set_time_limit(5);		
}

function getBalanceAccountBalanceForwarded2($from,$to,$gchart_id,$debit=TRUE){
	$result = mysql_query("
							select
								g.gchart_id,
								sum(debit) as db,
								sum(credit) as cr,
								beg_bal
							from
								gltran_header as h, gltran_detail as d, gchart as g
							where
								h.gltran_header_id = d.gltran_header_id
							and 
								d.gchart_id = g.gchart_id
							and
								g.parent_gchart_id = '$gchart_id'
							and h.date between '$from' and '$to'
							and status != 'C'
							group by g.gchart_id
							order by g.gchart_id asc
		") or die(mysql_error());
	$balance = 0;
	$options 	= new options();
	while($r = mysql_fetch_assoc($result)){

		
		$mclass 	= $options->getAttribute('gchart','gchart_id',$r['gchart_id'],'mclass');
		//$beg_bal 	= $options->getAttribute('gchart','gchart_id',$r['gchart_id'],'beg_bal');
                 $beg_bal = 0;

		if($options->acctg_credit_normal_balance($mclass)){
				$balance -= $r[db];
				$balance += $r[cr];
				$balance -= $beg_bal;
		}else{
				$balance += $r[db];
				$balance -= $r[cr];
				$balance += $beg_bal;
		}
	}
	return $balance;
    set_time_limit(5);
}

function getBalanceAccountBalanceForwarded3($from,$to,$gchart_id,$debit=TRUE){
	
	$result = mysql_query("
							select
								g.gchart_id,
								sum(debit) as db,
								sum(credit) as cr,
								beg_bal,
								gchart
							from
								gltran_header as h, gltran_detail as d, gchart as g
							where
								h.gltran_header_id = d.gltran_header_id
							and 
								d.gchart_id = g.gchart_id
							and
								g.parent_gchart_id = '$gchart_id'
							and h.date between '$from' and '$to'
							and status != 'C'
							group by g.gchart_id
							order by g.gchart_id asc
		") or die(mysql_error());
	
	$options 	= new options();
	while($r = mysql_fetch_assoc($result)){
		$balance = 0;
		$mclass 	= $options->getAttribute('gchart','gchart_id',$r['gchart_id'],'mclass');
		
                 $beg_bal = 0;

		if($options->acctg_credit_normal_balance($mclass)){
				$balance -= $r[db];
				$balance += $r[cr];
				$balance -= $beg_bal;
		}else{
				$balance += $r[db];
				$balance -= $r[cr];
				$balance += $beg_bal;
		}
								?>	
									<tr>
										<td style='padding-left:50px;'><?=$r[gchart]?></td>
										<td class="alignRight"><?=numform($balance)?></td>
										<td class="alignRight"><?=numform(0)?></td>
									</tr>	
								<?php
	}

}

function numform($num) {
	if($num==0) $num = "&nbsp;";
	else if($num < 0 ) $num = "( ".number_format(abs($num),2)." )";
	else $num = number_format($num, 2);
	
	return $num;
}
	
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
	font-size:11px;
}

.header
{
	text-align:center;	
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
.content table td,.content table th{
	/*border:1px solid #000;*/
	padding:3px;
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
    
    	<?php
			require("form_heading.php");
        ?>

        <div style="text-align:left; font-size:12px; margin-bottom:20px; font-weight:bold;">
           	TRIAL BALANCE<br />
			<em>From <?=date("F j, Y",strtotime($from));?> To <?=date("F j, Y",strtotime($to));?></em>
        </div>      
       	<?php
			$grand_total_debit = 0;
			$grand_total_credit = 0;
        ?>
        <div class="content" >
        	<table cellspacing="0">
                <tr>
                    <th>Account Description</th>
                    <th class="alignRight">Debit</th>
                    <th class="alignRight">Credit</th>
                </tr>
                   
             	<tr>
                    <td style="font-weight:bold;">ASSETS</td>
                    <td></td>
                    <td></td>
                </tr>
                
                <?php
				#ASSET
				$subclass_asset = array('6'=>'CURRENT ASSEST','8'=>'LONG TERM ASSETS','7'=>'FIXED ASSETS');
				$subclass_lia = array('9'=>'CURRENT LIABILITIES','10'=>'LONG TERM LIABILITIES','11'=>'OTHER LIABILITIES');
				
				$total_debit_sub = 0;
				$total_credit_sub = 0;
				$db_grand_amount = 0;
				$cr_grand_amount = 0;
               //foreach($subclass_asset as $key => $value) {
						?>
							<!--<tr>
								<td style='padding-left:20px;'><b><?=$value?></b></td>
							</tr>-->
						<?php
						$result = mysql_query(
											 "			 
												SELECT
													g.gchart_id,g.gchart
												FROM gchart AS g
												WHERE (
												g.gchart_id
													IN (
														SELECT gchart_id
														FROM gchart
														WHERE mclass =  'A'
														AND enable =  'Y'
														AND parent_gchart_id =  '0'
														/*AND sub_mclass =  '$key'*/
													)
												)
												GROUP BY g.gchart_id
												ORDER BY g.gchart_id
											 ");		
					while($r=mysql_fetch_assoc($result)){
						$gchart_id 	= $r['gchart_id'];
						$gchart		= $r['gchart'];
						$debit = $credit = 0;
						$balance = 0;
						if($gchart_id == 61){
							$balance = getBalanceAccountBalanceForwarded($from,$to,$gchart_id) + getBalanceAccountBalanceForwarded2($from,$to,$gchart_id);
							echo getBalanceAccountBalanceForwarded3($from,$to,$gchart_id);
						}else{
							if($gchart_id != 70){
								$balance = getBalanceAccountBalanceForwarded($from,$to,$gchart_id) + getBalanceAccountBalanceForwarded2($from,$to,$gchart_id);
							}
							
								
							if($balance && $gchart_id != 70 ) {
								?>	
									<tr>
										<td style='padding-left:50px;'><?=$gchart?></td>
										<td class="alignRight"><?=numform($balance)?></td>
										<td class="alignRight"><?=numform(0)?></td>
									</tr>	
								<?php
							}
						}
							$total_debit_sub += $balance;
							$total_credit_sub += 0;
					}
					
					$db_grand_amount+=$total_debit_sub;
					$cr_grand_amount+=$total_credit_sub;
					
					$grand_total_debit += $db_grand_amount;
					$grand_total_credit += $cr_grand_amount;
					?>
				
					<?php
					$total_credit_sub=0;
					$total_debit_sub=0;
			  // }
                ?>
                <!--<tr>
                	
                    <td style="font-weight:bold;">TOTAL ASSETS</td>
                    <td class="alignRight" style="font-weight:bold;"><?=numform($db_grand_amount)?></td>
                    <td class="alignRight" style="font-weight:bold;"><?=numform($cr_grand_amount)?></td>
                </tr>  
                
                <tr>
                	<td colspan="3">&nbsp;</td>
                </tr>
                
                <tr>
                    <td style="font-weight:bold;">LIABILITIES</td>
                    <td></td>
                    <td></td>
                </tr>-->
                <?php
				#LIABILITY
				$total_debit_sub = 0;
				$total_credit_sub = 0;
				$db_grand_amount = 0;
				$cr_grand_amount = 0;
				
               //foreach($subclass_lia as $key => $value) {
					?>
						<!--<tr>
							<td style='padding-left:20px;'><b><?=$value?></b></td>
						</tr>-->
					<?php
				
				
					$result = mysql_query(
								"			 
										SELECT 
											g.gchart_id,g.gchart
										FROM gchart AS g
										WHERE (
										g.gchart_id
										IN (

										SELECT gchart_id
										FROM gchart
										WHERE mclass =  'L'
										AND enable =  'Y'
										AND parent_gchart_id =  '0'
										/*AND sub_mclass =  '$key'*/
										)
										)
										GROUP BY g.gchart_id
										ORDER BY g.gchart_id
								");		

					while($r=mysql_fetch_assoc($result)){
						$gchart_id 	= $r['gchart_id'];
						$gchart		= $r['gchart'];
						
						$debit = $credit = 0;
						$balance = getBalanceAccountBalanceForwarded($from,$to,$gchart_id,FALSE) + getBalanceAccountBalanceForwarded2($from,$to,$gchart_id,FALSE);
						
						$total_debit_sub += 0;
						$total_credit_sub += $balance;
							
						if($balance){
					?>	
						<tr>
							<td style='padding-left:50px;'><?=$gchart?></td>
							<td class="alignRight"><?=numform(0)?></td>
							<td class="alignRight"><?=numform($balance)?></td>
						</tr>	
					<?php
						}
					}
					$db_grand_amount+=$total_debit_sub;
					$cr_grand_amount+=$total_credit_sub;
					
					$grand_total_debit += $db_grand_amount;
					$grand_total_credit += $cr_grand_amount;
					?>
					<!--<tr>
						
						<td style="font-weight:bold;padding-left:20px;">TOTAL <?=$value?></td>
						<td class="alignRight" style="font-weight:bold;"><?=numform($total_debit_sub)?></td>
						<td class="alignRight" style="font-weight:bold;"><?=numform($total_credit_sub)?></td>
					</tr>-->  
                
				
					<?php
					$total_credit_sub=0;
					$total_debit_sub=0;
			   //}
                ?>
               <!-- <tr>
                	
                    <td style="font-weight:bold;">TOTAL LIABILITIES</td>
                    <td class="alignRight" style="font-weight:bold;"><?=numform($db_grand_amount)?></td>
                    <td class="alignRight" style="font-weight:bold;"><?=numform($cr_grand_amount)?></td>
                </tr>  
                
                <tr>
                	<td colspan="3">&nbsp;</td>
                </tr>
                
                <tr>
                    <td style="font-weight:bold;">RETAINED EARNINGS</td>
                    <td></td>
                    <td></td>
                </tr>
                -->
                <?php
				#RETAINED EARNINGS
                $query="
                    select 
                        *
                    FROM	
                       gchart
					where
						mclass='R'
					order by 
						gchart asc
	            ";
                
                $result = mysql_query($query) or die(mysql_error());		
				
				$total_debit = 0;
				$total_credit = 0;
				
                while($r=mysql_fetch_assoc($result)){
					$gchart_id 	= $r['gchart_id'];
					$gchart		= $r['gchart'];
					
					$debit = $credit = 0;
					$balance = getBalanceAccountBalanceForwarded($from,$to,$gchart_id,FALSE);
					
					$total_debit += 0;
					$total_credit += $balance;
						
					if($balance){
				?>	
                    <tr>
                        <td style='padding-left:50px;'><?=$gchart?></td>
                        <td class="alignRight"><?=numform(0)?></td>
                        <td class="alignRight"><?=numform($balance)?></td>
                    </tr>	
				<?php
					}
				}
				$grand_total_debit += $total_debit;
				$grand_total_credit += $total_credit;
                ?>
                <!--<tr>
                	
                    <td style="font-weight:bold;">TOTAL RETAINED EARNINGS</td>
                    <td class="alignRight" style="font-weight:bold;"><?=numform($total_debit)?></td>
                    <td class="alignRight" style="font-weight:bold;"><?=numform($total_credit)?></td>
                </tr>  
                
                <tr>
                	<td colspan="3">&nbsp;</td>
                </tr>
                
                <tr>
                    <td style="font-weight:bold;">INCOME</td>
                    <td></td>
                    <td></td>
                </tr>-->
                <?php
				#INCOME
                $query="
                    select 
                        *
                    FROM	
                       gchart
					where
						mclass='I'
					order by 
						gchart asc
	            ";
                
                $result = mysql_query($query) or die(mysql_error());		
				
				$total_debit = 0;
				$total_credit = 0;
				
                while($r=mysql_fetch_assoc($result)){
					$gchart_id 	= $r['gchart_id'];
					$gchart		= $r['gchart'];
					
					$debit = $credit = 0;
					$balance = getBalanceAccountBalanceForwarded($from,$to,$gchart_id,FALSE);
					
					$total_debit += 0;
					$total_credit += $balance;
						
					if($balance){
				?>	
                    <tr>
                        <td style='padding-left:50px;'><?=$gchart?></td>
                        <td class="alignRight"><?=numform(0)?></td>
                        <td class="alignRight"><?=numform($balance)?></td>
                    </tr>	
				<?php
					}
				}
				$grand_total_debit += $total_debit;
				$grand_total_credit += $total_credit;
                ?>
               <!-- <tr>
                	
                    <td style="font-weight:bold;">TOTAL INCOME</td>
                    <td class="alignRight" style="font-weight:bold;"><?=numform($total_debit)?></td>
                    <td class="alignRight" style="font-weight:bold;"><?=numform($total_credit)?></td>
                </tr>  
                
                <tr>
                	<td colspan="3">&nbsp;</td>
                </tr>
                
                <tr>
                    <td style="font-weight:bold;">EXPENSE</td>
                    <td></td>
                    <td></td>
                </tr>
                -->
                 <?php
				#EXPENSE
                $query="
                    select 
                        *
                    FROM	
                       gchart
					where
						mclass='E'
					order by 
						gchart asc
	            ";
                
                $result = mysql_query($query) or die(mysql_error());		
				
				$total_debit = 0;
				$total_credit = 0;
				
                while($r=mysql_fetch_assoc($result)){
					$gchart_id 	= $r['gchart_id'];
					$gchart		= $r['gchart'];
					
					$debit = $credit = 0;
					$balance = getBalanceAccountBalanceForwarded($from,$to,$gchart_id);
					
					$total_debit += $balance;
					$total_credit += 0;
						
					if($balance){
				?>	
                    <tr>
                        <td style='padding-left:50px;'><?=$gchart?></td>
                        <td class="alignRight"><?=numform($balance)?></td>
                        <td class="alignRight"><?=numform(0)?></td>
                    </tr>	
				<?php
					}
				}
				$grand_total_debit += $total_debit;
				$grand_total_credit += $total_credit;
                ?>
               <!-- 
				   <tr>
						
						<td style="font-weight:bold;">TOTAL EXPENSE</td>
						<td class="alignRight" style="font-weight:bold;"><?=numform($total_debit)?></td>
						<td class="alignRight" style="font-weight:bold;"><?=numform($total_credit)?></td>
					</tr> 
                -->
                <tr>
                	<td colspan="3">&nbsp;</td>
                </tr>
                
                <tr>
                	<td style="font-weight:bold;">TOTAL</td>
                    <td class="alignRight" style="font-weight:bold;"><span style="border-bottom:3px double #000;"><?=numform($grand_total_debit)?></span></td>
                    <td class="alignRight" style="font-weight:bold;"><span style="border-bottom:3px double #000;"><?=numform($grand_total_credit)?></span></td>
                </tr>
			</table>
        </div><!--End of content-->
    </div><!--End of Form-->
   
</div>
</body>
</html>