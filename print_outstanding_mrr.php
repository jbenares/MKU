<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	$from_date		= $_REQUEST['from_date'];
	$to_date		= $_REQUEST['to_date'];
	$project_id		= $_REQUEST['project_id'];
	$project = $options->getAttribute("projects","project_id",$project_id,"project_name");
	
	$supplier 		= $_REQUEST['supplier'];
	$po 			= $_REQUEST['po_header_id'];
	$stock			= $_REQUEST['stock'];
	$sortby			= $_REQUEST['sortby'];
	function getMRRQty($po_header_id,$stock_id){
		$result = mysql_query("
			select
				sum(quantity) as quantity
			from
				rr_header as h, rr_detail as d
			where
				h.rr_header_id = d.rr_header_id
			and h.po_header_id = '$po_header_id'
			-- and date between '$from_date' and '$to_date'
			-- and project_id = '$project_id'
			and d.stock_id = '$stock_id'
			and h.status != 'C'
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		return $r['quantity'];
	}
	
	function getMRRReference($po_header_id,$stock_id){
		$result = mysql_query("
			select
				*
			from
				rr_header as h, rr_detail as d
			where
				h.rr_header_id = d.rr_header_id
			and
				po_header_id  = '$po_header_id'
			and
				d.stock_id = '$stock_id'
			and
				h.status != 'C'
			order by h.rr_header_id asc, invoice asc
		") or die(mysql_error());
		$array = array();
		while($r = mysql_fetch_assoc($result)){
			$t = array();
			$t['rr_header_id'] 	= $r['rr_header_id'];
			$t['invoice']		= $r['invoice'];
			$t['quantity']		= $r['quantity'];
			$t['quantity_cum']	= $r['quantity_cum'];
			$t['date']	= $r['date'];
			array_push($array,$t);
		}
		return $array;
	}
	
	function getPOReference($from_date,$to_date,$project_id,$stock_id){
		$result = mysql_query("
			select
				h.po_header_id
			from
				po_header as h, po_detail as d
			where
				h.po_header_id = d.po_header_id
			and
				date between '$from_date' and '$to_date'
			and
				project_id = '$project_id'
			and
				d.stock_id = '$stock_id'
			and
				h.status != 'C'
			and
				h.approval_status = 'A' 
			group by
				po_header_id
			order by po_header_id asc
		") or die(mysql_error());
		$po_array = array();
		while($r = mysql_fetch_assoc($result)){
			$po_array[] = str_pad($r['po_header_id'],7,0,STR_PAD_LEFT);
		}
		$list = implode("<br>",$po_array);
		
		return $list;
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
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	
     	<div style="font-weight:bolder;">
        	OUTSTANDING PO REPORT<br />
            <?=$project?> <br />
            From <?=date("m/d/Y",strtotime($from_date))?> to <?=date("m/d/Y",strtotime($to_date))?>
        </div>           
        <div class="content" style="">
        	<table cellpadding="6">
            	<tr>
                	<th style="text-align:left;">PO #</th>
                	<th style="text-align:left;">PO DATE</th>
                    <th style="text-align:left;">SUPPLIER</th>
                    <th style="text-align:left;">ITEM</th>
                    <th style="text-align:right;">PO QTY</th>
                    <th style="text-align:left;">UNIT</th>
                    <th style="text-align:left;">MRR #</th>
                    <th style="text-align:left;">MRR DATE</th>
                    <th style="text-align:left;">INVOICE</th>
                    <th style="text-align:right;">MRR QTY</th>
                    <th style="text-align:right;">OPTIONAL QTY</th>
                    <th style="text-align:right;">BALANCE</th>
                </tr>	
                
             	<?php
                 $query="
					select
						h.po_header_id,h.supplier_id,stock,quantity,unit,d.stock_id, account, h.closed, h.date
					from
						po_header as h, po_detail as d, productmaster as p, supplier as s
					where
						h.po_header_id = d.po_header_id
					and d.stock_id = p.stock_id
					and s.account_id = h.supplier_id
					and h.status != 'C'
					and h.approval_status = 'A'
					and	 account like '%$supplier%'
					and stock like '%$stock%'
					and h.date between '$from_date' and '$to_date'";
               
               if(!empty($project_id)){
                   $query.=" and project_id = '$project_id'";
               }
					
					$query.=" and h.po_header_id like '%$po%'
					order by $sortby asc
				";
				$result=mysql_query($query) or die(mysql_error());
				$total_po_qty = $total_mrr_qty = $total_mrr_optional_qty = 0;
				while($r=mysql_fetch_assoc($result)){
					
				?>	
					<?php
					$rr = getMRRReference($r['po_header_id'],$r['stock_id']);
					#if po is closed balance is equal to total rr quantity
					#else po is equal to its own quantity
					
					
					if($r['closed']){
						$balance = getMRRQty($r['po_header_id'],$r['stock_id']);
					}else{
						$balance = $r['quantity'];
					}
					
					#echo "Balance : $r[quantity]";
					
					if(!empty($rr)){
						$item = "";
                    	foreach($rr as $a){
							
							$balance -= $a['quantity'];
							
							$color="#000";
							#if($item != $r[stock] ){
								#echo $balance;
								if($balance < 1){
									$color="red";
								}
								#$item = $r[stock];
							#}
							
							$total_po_qty 			+= $r['quantity'];
							$total_mrr_qty 			+= $a['quantity'];
							$total_mrr_optional_qty += $a['quantity_cum'];

					?>
                            <tr style="color:<?=$color?>;">
                                <td style="vertical-align:top; text-align:left;"><?=str_pad($r['po_header_id'],7,0,STR_PAD_LEFT)?></td>
                                <td style="vertical-align:top; text-align:left;"><?=$r['date']?></td>                       
                                <td style="vertical-align:top; text-align:left;"><?=$r['account']?></td>                       
                                <td style="vertical-align:top; text-align:left;"><?=htmlentities($r['stock'])?></td>                       
                                <td style="vertical-align:top; text-align:right;"><?=number_format($r['quantity'],4,'.',',')?></td>                       
                                <td style="vertical-align:top; text-align:left;"><?=$r['unit']?></td>                       
                                <td style="vertical-align:top; text-align:left;"><?=str_pad($a['rr_header_id'],7,0,STR_PAD_LEFT)?></td>                       
                                <td style="vertical-align:top; text-align:left;"><?=$a['date']?></td>                                                      
                                <td style="vertical-align:top; text-align:left;"><?=$a['invoice']?></td>                                                      
                                <td style="vertical-align:top; text-align:right;"><?=number_format($a['quantity'],4,'.',',')?></td>                       
                                <td style="vertical-align:top; text-align:right;"><?=number_format($a['quantity_cum'],4,'.',',')?></td>                       
                                <td style="vertical-align:top; text-align:right;"><?=number_format($balance,4,'.',',')?></td>                       
                            </tr>	
					<?php	
						$a = array();
						}
					}else{
						$total_po_qty 			+= $r['quantity'];
						$total_mrr_qty 			+= $a['quantity'];
						$total_mrr_optional_qty += $a['quantity_cum'];
					?>	
                    	<tr>
                            <td style=""><?=str_pad($r['po_header_id'],7,0,STR_PAD_LEFT)?></td>
							<td style="vertical-align:top; text-align:left;"><?=$r['date']?></td>  
                            <td style="vertical-align:top; text-align:left;"><?=$r['account']?></td>                       
                            <td style="vertical-align:top; text-align:left;"><?=htmlentities($r['stock'])?></td>                       
                            <td style="vertical-align:top; text-align:right;"><?=number_format($r['quantity'],4,'.',',')?></td>                       
                            <td style="vertical-align:top; text-align:left;"><?=$r['unit']?></td>                       
                            <td style="vertical-align:top; text-align:left;"><?=str_pad($a['rr_header_id'],7,0,STR_PAD_LEFT)?></td>      
							<td style="vertical-align:top; text-align:left;"><?=$a['date']?></td>       							
                            <td style="vertical-align:top; text-align:left;"><?=$a['invoice']?></td>                                                      
                            <td style="vertical-align:top; text-align:right;"><?=number_format($a['quantity'],4,'.',',')?></td>                       
                            <td style="vertical-align:top; text-align:right;"><?=number_format($a['quantity_cum'],4,'.',',')?></td>                       
                            <td style="vertical-align:top; text-align:right;"><?=number_format($balance,4,'.',',')?></td>                       
                        </tr>	
                    <?php } ?>
				<?php } ?>
                <tr>
                    <td style="border-top:1px solid #000; font-weight:bold;"></td>
                    <td style="vertical-align:top; text-align:left; border-top:1px solid #000; font-weight:bold;"></td>                       
                    <td style="vertical-align:top; text-align:left; border-top:1px solid #000; font-weight:bold;"></td>                       
                    <td style="vertical-align:top; text-align:left; border-top:1px solid #000; font-weight:bold;"></td>                       
                    <td style="vertical-align:top; text-align:right; border-top:1px solid #000; font-weight:bold;"><?=number_format($total_po_qty,4,'.',',')?></td>                       
                    <td style="vertical-align:top; text-align:left; border-top:1px solid #000; font-weight:bold;"></td>                       
                    <td style="vertical-align:top; text-align:left; border-top:1px solid #000; font-weight:bold;"></td>                       
                    <td style="vertical-align:top; text-align:left; border-top:1px solid #000; font-weight:bold;"></td>                                                      
                    <td style="vertical-align:top; text-align:left; border-top:1px solid #000; font-weight:bold;"></td>                                                      
                    <td style="vertical-align:top; text-align:right; border-top:1px solid #000; font-weight:bold;"><?=number_format($total_mrr_qty,4,'.',',')?></td>                       
                    <td style="vertical-align:top; text-align:right; border-top:1px solid #000; font-weight:bold;"><?=number_format($total_mrr_optional_qty,4,'.',',')?></td>                       
                    <td style="vertical-align:top; text-align:right; border-top:1px solid #000; font-weight:bold;"></td>                       
                </tr>	
            </table>
        
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>