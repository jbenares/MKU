<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	$from_date		= $_REQUEST['from_date'];
	$to_date		= $_REQUEST['to_date'];
	$project_id		= $_REQUEST['project_id'];
	$project = $options->getAttribute("projects","project_id",$project_id,"project_name");
	$categ_id		= $_REQUEST['categ_id'];
	$stock_id		= $_REQUEST['stock_id'];
	$allowed		= $_REQUEST['allowed'];
	$work_category_id		= $_REQUEST['work_category_id'];
	$sub_work_category_id	= $_REQUEST['sub_work_category_id'];
	
	function getPOQty($pr_header_id,$from_date,$to_date,$project_id,$work_category_id,$sub_work_category_id,$stock_id){
		$sql = "
			select 
				concat('PO#:',lpad(h.po_header_id,7,0)) as po, sum(quantity) as quantity, date
			from
				po_header as h, po_detail as d
			where
				h.po_header_id = d.po_header_id
			and
				pr_header_id = '$pr_header_id'
			and
			(
				stock_id = '$stock_id' or stock_id in (select stock_id from productmaster where parent_stock_id = '$stock_id')
			)
			and
				status != 'C'
		";
		if($from_date && $to_date){
		$sql .= "
			and date between '$from_date' and '$to_date'
		";	
		}
		
		if($project_id){
		$sql .= "
			and project_id = '$project_id'
		";	
		}
		
		if($work_category_id){
		$sql .= "
			and work_category_id = '$work_category_id'
		";	
		}
		
		if($sub_work_category_id){
		$sql .= "
			and sub_work_category_id = '$sub_work_category_id'
		";	
		}
		$sql.="
			group by h.po_header_id
		";
		//echo $sql. "<br>";
		
		$result =  mysql_query($sql) or die(mysql_error());	
		$a = array();
		while($r = mysql_fetch_assoc($result)){
			$t         = array();
			$t['po']   = $r['po'];
			$t['qty']  = $r['quantity'];
			$t['date'] = $r['date'];
			$a[]       = $t;
		}
		
		return $a;
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
<link rel="stylesheet" type="text/css" href="css/print.css"/>
<style type="text/css">
</style>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	
     	<div style="font-weight:bolder;">
        	RTP HISTORY REPORT <br />
            <?=$project?> <br />
            From <?=date("m/d/Y",strtotime($from_date))?> to <?=date("m/d/Y",strtotime($to_date))?>
        </div>           
        <div class="content" style="">
        	<table cellpadding="6">
            	<tr>
                	<th style="text-align:left; width:10%;">DATE</th>
                    <th style="text-align:left; width:10%;">RTP#</th>
                    <th style="text-align:left;">ITEM</th>
                    <th style="text-align:right; width:10%;">QTY</th>
                    <th style="text-align:left; width:10%;">UNIT</th>  

                    <th style="text-align:left; width:10%;">PO DATE</th>  

                    <th style="text-align:left; width:10%;">PO #</th>  
                    <th style="text-align:right; width:10%;">PO QTY</th>  
                    <th style="text-align:right; width:10%;">BALANCE</th>  
                    <th style="text-align:left; width:10%;">DAYS</th>  
                </tr>	
                
             	<?php
					$query="
						select
							h.project_id,
							h.date,
							h.pr_header_id,
							stock,
							sum(d.quantity) as quantity,
							p.unit,
							d.stock_id
						from
							pr_header as h, pr_detail as d, productmaster as p
						where
							h.date between '$from_date' and '$to_date'	
						and
							h.status != 'C'
						and
							h.pr_header_id = d.pr_header_id
						and
							d.stock_id = p.stock_id
						
					";
					
					$query.= ($allowed) ? "and allowed = '1'" : "and allowed = '0'";
					
					if($project_id){
					$query.="
						and
							h.project_id = '$project_id'
					";		
					}
					
					if($categ_id){
					$query.="
						and
							p.categ_id1 = '$categ_id'
					";	
					}
					
					if($stock_id){
					$query.="
						and
							d.stock_id= '$stock_id'
					";	
					}
					
					if($work_category_id){
					$query.="
						and
							work_category_id = '$work_category_id'
					";	
					}
					
					if($sub_work_category_id){
					$query.="
						and
							sub_work_category_id = '$sub_work_category_id'
					";	
					}
					
					$query.="
						group by h.pr_header_id, d.stock_id
						order by
							h.date asc, h.pr_header_id asc
					";
					//echo $query;
					$result=mysql_query($query) or die(mysql_error());
					$total_quantity = 0;
					$total_po_qty = 0;
					while($r=mysql_fetch_assoc($result)){
						$total_quantity += $r['quantity'];
						$a = getPOQty($r['pr_header_id'],$from_date,$to_date,$project_id,$work_category_id,$sub_work_category_id,$r['stock_id']);
						
				?>	
                        <tr>
                        <td style="border-top:1px dashed #000;"><?=date($r['date'])?></td>
                        <td style="border-top:1px dashed #000;"><?=str_pad($r['pr_header_id'],7,0,STR_PAD_LEFT)?></td>                       
                        <td style="border-top:1px dashed #000;"><?=htmlentities($r['stock'])?></td>                       
                        <td style="text-align:right; border-top:1px dashed #000;"><?=number_format($r['quantity'],2)?></td>                       
                        <td style="border-top:1px dashed #000;"><?=$r['unit']?></td>             

                        <?php
						$i = 1;
						$pr_balance = $r['quantity'];
						if(!empty($a)){
							foreach($a as $b){
								$total_po_qty += $b['qty'];
								$pr_balance   -= $b['qty'];
#echo $b['date'].'---'.$r['date'];
								$interval = date_diff (date_create($b['date']),date_create($r['date']));
								$date_diff = $interval->format("%d");
								
								if($i == 1){
									echo "
										<td style=\"border-top:1px dashed #000;\">".date($b['date'])."</td>
										<td style=\"border-top:1px dashed #000;\">".$b['po']."</td>
										<td style='text-align:right; border-top:1px dashed #000;'>".number_format($b['qty'],2)."</td>
										<td style='text-align:right; border-top:1px dashed #000;'>".number_format($pr_balance,2)."</td>
										<td style=\"border-top:1px dashed #000;\">".$date_diff." days</td>
										</tr>
									";
								}else{
									echo "
										<tr>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td>".$b['po']."</td>
										<td style='text-align:right;'>".number_format($b['qty'],2)."</td>
										<td style='text-align:right;'>".number_format($pr_balance,2)."</td>
										<td></td>
									";
									if($i < count($a) ){
										echo "</tr>";	
									}
								}	
								$i++;
							}
						}else{
							echo "
								<td style='border-top:1px dashed #000;'></td>
								<td style='border-top:1px dashed #000;'></td>
								<td style='border-top:1px dashed #000;'></td>
								<td style='text-align:right; border-top:1px dashed #000;'>".number_format($pr_balance,2)."</td>
								<td style='border-top:1px dashed #000;'></td>
							";	
						}
                        ?>
                    </tr>
				<?php } ?>
                <tr>
                	<td style="width:10%;"></td>
                    <td style="width:10%;"></td>
					<td style="text-align:right;">TOTAL:</td>
                    <td style="width:10%; text-align:right; font-weight:bolder; border-bottom:4px double #000;"><?=number_format($total_quantity,4,'.',',')?></td>
                    <td style="width:10%;"></td>
                    <td style="width:10%;"></td>
                    <td style="width:10%;"></td>
                    <td style="width:10%; text-align:right; font-weight:bolder; border-bottom:4px double #000;"><?=number_format($total_po_qty,4,'.',',')?></td>
                    <td></td>
                </tr>
            </table>
        
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>