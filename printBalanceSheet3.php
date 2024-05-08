<?php	
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	require_once(dirname(__FILE__).'/library/lib.php');

	$month = $_REQUEST['month'];
	$year  = $_REQUEST['year'];	
	
	$options=new options();	
	
	function getBalance($month,$year,$gchart_id,$debit=TRUE){
		$from_date 	= "$year-01-01";
		$to_date 	= date("Y-m-d",strtotime("+1 month -1 day",strtotime("$year-$month-01")));			

		/*this part should not display child entries*/
		/*chlid accounts are not displayed here*/
			
		$result = mysql_query("
						select
							g.gchart_id,
							/*g.gchart as act,
							sum(debit) as db,
							sum(credit) as cr,
							beg_bal,*/
							(sum(debit) - sum(credit)) as balance
							/*(sum(debit) - sum(credit)) as balance*/
						from
							gltran_header as h, gltran_detail as d, gchart as g
						where
							h.gltran_header_id = d.gltran_header_id
						and d.gchart_id = g.gchart_id
						and
						/*(
							d.gchart_id = '$gchart_id'
							or g.parent_gchart_id = '$gchart_id'
						)*/
						g.gchart_id = '$gchart_id'
						and h.date <= '$to_date'
						and status != 'C'
						group by g.gchart_id
						order by g.gchart_id asc
						/*and parent_gchart_id = '0'*/
				
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		
		//$t_debit  = $r['debit'];
		//$t_credit = $r['credit'];
		$options 	= new options();
		$mclass 	= $options->getAttribute('gchart','gchart_id',$r[gchart_id],'mclass');
		$beg_bal 	= $options->getAttribute('gchart','gchart_id',$r[gchart_id],'beg_bal');

		//if( $debit ){ /*normal balance is debit*/
		//	$t_debit += $beg_bal;
	//	} else { /*normal balance is credit*/
		//	$t_credit += $beg_bal;
		//}
		
		
		//set_time_limit(30);
		//if($debit){
		//	return $t_debit - $t_credit;
		//}else{
		//	return $t_credit - $t_debit;
		//}
		if($options->acctg_credit_normal_balance($mclass)){
			$r['balance'] -= $beg_bal;
		}else{
			$r['balance'] += $beg_bal;
		}
	
		return $r['balance'];
				
	}
	
	function getBalance2($month,$year,$gchart_id,$debit=TRUE){
		$from_date 	= "$year-01-01";
		$to_date 	= date("Y-m-d",strtotime("+1 month -1 day",strtotime("$year-$month-01")));			

		/*this part should not display child entries*/
		/*chlid accounts are not displayed here*/
			
		$result = mysql_query("
						select
							g.gchart_id,
							/*g.gchart as act,
							sum(debit) as db,
							sum(credit) as cr,
							beg_bal,*/
							(sum(debit) - sum(credit)) as balance
						from
							gltran_header as h, gltran_detail as d, gchart as g
						where
							h.gltran_header_id = d.gltran_header_id
						and d.gchart_id = g.gchart_id
						and
						g.parent_gchart_id = '$gchart_id'
						and h.date <= '$to_date'
						and status != 'C'
						group by g.gchart_id
						order by g.gchart_id asc
				
		") or die(mysql_error());
		$balance = 0;
		while($r = mysql_fetch_assoc($result)){
		
			/*$t_debit  = $r['debit'];
			$t_credit = $r['credit'];

			/*get beginning balance*/
			$options 	= new options();
			$mclass 	= $options->getAttribute('gchart','gchart_id',$r['gchart_id'],'mclass');
			$beg_bal 	= $options->getAttribute('gchart','gchart_id',$r[gchart_id],'beg_bal');

			//if( $debit ){ /*normal balance is debit*/
			//	$t_debit += $beg_bal;
		//	} else { /*normal balance is credit*/
			//	$t_credit += $beg_bal;
			//}
			
			
			//set_time_limit(30);
			//if($debit){
			//	return $t_debit - $t_credit;
			//}else{
			//	return $t_credit - $t_debit;
			//}
			if($options->acctg_credit_normal_balance($mclass)){
				$r['balance'] -= $beg_bal;
			}else{
				$r['balance'] += $beg_bal;
			}
				$balance +=$r['balance'];
		}	
		
		return $balance;
	}
	
	function solveForLastYearIncome($year){			
		$startingdate="$year-01-01";
		
		#REVENUE
		$result = mysql_query("
			select 
				(sum(credit - debit) + sum(beg_bal)) as revenue
			from
				gltran_header as h,gltran_detail as d, gchart as g
			where
				d.gchart_id = g.gchart_id
			and h.gltran_header_id=d.gltran_header_id
			and date < '$startingdate'
			and status != 'C'
			and mclass = 'I'
				
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		$revenue = $r['revenue'];
		
		$result = mysql_query("
			select 
				(sum(debit - credit) + sum(beg_bal)) as expense
			from
				gltran_header as h,gltran_detail as d, gchart as g
			where
				d.gchart_id = g.gchart_id
			and h.gltran_header_id=d.gltran_header_id
			and date < '$startingdate'
			and status != 'C'
			and mclass = 'E'
				
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		$expense = $r['expense'];
		
		$income = $revenue - $expense;
					
		return $income;
	}
	
	function solveToDateIncome($month,$year){
		$from_date 	= "$year-01-01";
		$to_date 	= date("Y-m-d",strtotime("+1 month -1 day",strtotime("$year-$month-01")));
		
		$result = mysql_query("
			select 
				(sum(credit - debit) + sum(beg_bal)) as revenue
			from
				gltran_header as h,gltran_detail as d, gchart as g
			where
				d.gchart_id = g.gchart_id
			and h.gltran_header_id=d.gltran_header_id
			and date between '$from_date' and '$to_date'
			and status!='C'
			and mclass = 'I'
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		$revenue = $r['revenue'];
		
		$result = mysql_query("
			select 
				(sum(debit - credit) + sum(beg_bal)) as expense
			from
				gltran_header as h,gltran_detail as d, gchart as g
			where
				d.gchart_id = g.gchart_id
			and h.gltran_header_id=d.gltran_header_id
			and date between '$from_date' and '$to_date'
			and status!='C'
			and mclass = 'E'
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		$expense = $r['expense'];
		
		$income = $revenue - $expense;
		
		return $income;
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
	/*margin:0px;*/
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
	text-align:left;
	

}
.header table td, .content table td
{
	padding:2px;
	
}

.content table{
	border-collapse:collapse;
}
.content table td,.content table th{
	/*border:1px solid #000;*/
	padding:10px;
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
     	<div style=" font-weight:bolder; margin-bottom:10px;">
        	<?=$title?> <br />
        	BALANCE SHEET<br />
			FOR THE MONTH ENDING <?=date("F",strtotime("$year-$month-01"));?>, <?=$year?>
       	</div>
        

        
        <div class="content" >
        	<table cellspacing="0" >
                <tr>
                    <th colspan="2">Account Description</th>
                    <th>Year to Date</th>
                </tr>
                <tr>
                    <td style="font-weight:bold;">ASSETS</td>
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
						mclass='A'
					and
						enable='Y'
					and
						parent_gchart_id = '0'
                ";
                $grand_amount = 0;
                $result=mysql_query($query) or die(mysql_error());		
                ?>  
                <?php
                while($r = mysql_fetch_assoc($result)){
					$amount = getBalance($month,$year,$r['gchart_id'],TRUE);
					$amount2 = getBalance2($month,$year,$r['gchart_id'],TRUE);
					$subtotal=$amount+$amount2;
					$grand_amount +=$subtotal ;
					if($subtotal){
				?>	
				<tr>
                    <td><?=$r['gchart']?></td>
                    <td></td>
                    <td class="alignRight"><?=number_format($subtotal,2,'.',',')?></td>
                </tr>	
				<?php	
					}
				}
				
                ?>
                <tr>
                    <td style="font-weight:bold;">TOTAL ASSETS</td>
                    <td></td>
                    <td class="alignRight" style="font-weight:bold;"><?=number_format($grand_amount,2,'.',',')?></td>
                </tr>  
                
                <tr>
                	<td colspan="3"></td>
                </tr>
                
                <tr>
                    <td style="font-weight:bold;">LIABILITIES</td>
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
                
                $result=mysql_query($query) or die(mysql_error());		
				
                ?>  
                <?php
				$sub_yeartodate=0;
				$grand_amount = 0;
                while( $r = mysql_fetch_assoc($result) ):
					$amount = getBalance($month,$year,$r['gchart_id'],FALSE);
					$amount2 = getBalance2($month,$year,$r['gchart_id'],FALSE);
					$subtotal=$amount+$amount2;
					$sub_yeartodate += $subtotal;
				if($subtotal){
				?>	
                <?php if($r['gchart_id'] == 729){ 
					$_d = date("Y-m-d",strtotime("+1 month -1 day",strtotime("$year-$month-01")));
	                $link = "<a href='admin.php?view=2f631491a4db180cd4ea&date=$_d' target=\"_blank\">$r[gchart]</a>";
                } else {
                	$link = $r['gchart'];
                } 
				?>
				<tr>
                    <td><?=$link?></td>
                    <td></td>
                    <td class="alignRight"><?=number_format($subtotal,2,'.',',')?></td>
                </tr>	
				<?php	
				}
				endwhile;
				$grand_amount += $sub_yeartodate;
                ?>
                <tr>
                    <td style="font-weight:bold;">TOTAL LIABILITIES</td>
                    <td></td>
                    <td class="alignRight"><?=number_format($sub_yeartodate,2,'.',',')?></td>
                </tr>  
                
                <tr>
                	<td colspan="3"></td>
                </tr>
                
                <?php
                $income = solveForLastYearIncome($year);
				#echo "INCOME : $income;";
				?>
                
                <tr>
                    <td style="font-weight:bold;">RETAINED EARNINGS</td>
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
                
                $result=mysql_query($query) or die(mysql_error());		
				
                ?>  
                <?php
				$sub_yeartodate=0;				
                while($r = mysql_fetch_assoc($result)):
					$amount = getBalance($month,$year,$r['gchart_id'],FALSE);					
					
					$grand_amount +=$amount ;
					if($r['gchart_id'] == 932){
						#echo "INCOME : $income";
						$amount += $income;
						//echo "<b>Retained Earnings : ".number_format($amount,2,'.',',')." <br> Last Year's Income : ".number_format($income,2,'.',',')."</b>";
					}					
					
					$sub_yeartodate += $subtotal;
				if($amount){
				?>	
                    <tr>
                        <td><?=$r['gchart']?></td>
                        <td></td>
                        <td class="alignRight"><?=number_format($amount,2,'.',',')?></td>
                    </tr>	                
				<?php
                }
                endwhile;
                $grand_amount += $sub_yeartodate;
				?>
                
                <?php
				$income = solveToDateIncome($month,$year);
				?>	
                <tr>
                	<?php
					$_from_date = "$year-01-01";
					$_d = date("Y-m-d",strtotime("+1 month -1 day",strtotime("$year-$month-01")));
					$link = "<a href='admin.php?view=7f31450032f7e8abdc77&from_date=$_from_date&to_date=$_d&b=View Summary' target=\"_blank\">NET INCOME / LOSS</a>";
					?>
                    <td style="font-weight:bold;"><?=$link?></td>
                    <td></td>
                    <td class="alignRight"><?=number_format($income,2,'.',',')?></td>
                </tr>
				<?php
				$sub_yeartodate += $income;	
				$grand_amount += $income;
				
				#$sub_debit += $income_debit;
				#$sub_credit += $income_credit;
				#$sub_yeartodate += $income;
				
				#$grand_debit += $sub_debit;
				#$grand_credit += $sub_credit;
				#$grand_yeartodate += $sub_yeartodate;
				
				#$grand_l_debit += $sub_debit;
				#$grand_l_credit += $sub_credit;
				#$grand_l_yeartodate += $sub_yeartodate;
								
                ?>
                <tr>
                    <td style="font-weight:bold;">TOTAL RETAINED EARNINGS</td>
                    <td></td>
                    <td class="alignRight"><?=number_format($sub_yeartodate,2,'.',',')?></td>
                </tr>  
                
                <tr>
                	<td colspan="3"></td>
                </tr>
                
                <tr>
                    <td style="font-weight:bold;">TOTAL LIABILITIES & RETAINED EARNINGS</td>
                    <td></td>
                    <td class="alignRight" style="font-weight:bold;"><?=number_format($grand_amount,2,'.',',')?></td>
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