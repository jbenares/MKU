<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	$from_date		= $_REQUEST['from_date'];
	$to_date		= $_REQUEST['to_date'];
	$project_id		= $_REQUEST['project_id'];
	$project = $options->getAttribute("projects","project_id",$project_id,"project_name");
	function getMRRQty($from_date,$to_date,$project_id,$stock_id){
		$result = mysql_query("
			select
				sum(quantity) as quantity
			from
				rr_header as h, rr_detail as d
			where
				h.rr_header_id = d.rr_header_id
			and
				date between '$from_date' and '$to_date'
			and
				project_id = '$project_id'
			and
				d.stock_id = '$stock_id'
			and
				h.status != 'C'
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		return $r['quantity'];
	}
	
	function getMRRReference($from_date,$to_date,$project_id,$stock_id){
		$result = mysql_query("
			select
				h.rr_header_id
			from
				rr_header as h, rr_detail as d
			where
				h.rr_header_id = d.rr_header_id
			and
				date between '$from_date' and '$to_date'
			and
				project_id = '$project_id'
			and
				d.stock_id = '$stock_id'
			and
				h.status != 'C'
			group by
				rr_header_id
			order by po_header_id asc
		") or die(mysql_error());
		$array = array();
		while($r = mysql_fetch_assoc($result)){
			$array[] = str_pad($r['rr_header_id'],7,0,STR_PAD_LEFT);
		}
		$list = implode("<br>",$array);
		
		return $list;
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
        	OUTSTANDING MRR REPORT<br />
            <?=$project?> <br />
            From <?=date("m/d/Y",strtotime($from_date))?> to <?=date("m/d/Y",strtotime($to_date))?>
        </div>           
        <div class="content" style="">
        	<table cellpadding="6">
            	<tr>
                    <th>ITEM</th>
                    <th>UNIT</th>
                    <th>PO QTY</th>
                    <th>PO REF</th>
                    <th>MRR QTY</th>
                    <th>MRR REF</th>
                    <th>BALANCE</th>
                </tr>	
                
             	<?php
					$query="
						select
							h.date,
							p.stock,
							sum(quantity) as quantity,
							p.unit,
							d.stock_id
						from
							po_header as h, po_detail as d, productmaster as p
						where
							h.po_header_id = d.po_header_id
						and
							d.stock_id = p.stock_id
						and
							h.status != 'C'
						and
							h.approval_status = 'A'
						and
							h.date between '$from_date' and '$to_date'
						and
							project_id = '$project_id'
						group by
							d.stock_id
						order by stock asc
					";
					$result=mysql_query($query) or die(mysql_error());
					
					while($r=mysql_fetch_assoc($result)){
						$po_qty = $r['quantity'];
						$mrr_qty = getMRRQty($from_date,$to_date,$project_id,$r['stock_id']);
						$balance = $po_qty - $mrr_qty;
				?>	
                        <tr>
                            <td style="vertical-align:top;"><?=htmlentities($r['stock'])?></td>                       
                            <td style="vertical-align:top;"><?=$r['unit']?></td>                       
                            <td style="text-align:right; vertical-align:top;"><?=number_format($po_qty,2,'.',',')?></td>
                            <td style="text-align:right; vertical-align:top;"><?=getPOReference($from_date,$to_date,$project_id,$r['stock_id'])?></td>                       
                            <td style="text-align:right; vertical-align:top;"><?=number_format($mrr_qty,2,'.',',')?></td>                       
                            <td style="text-align:right; vertical-align:top;"><?=getMRRReference($from_date,$to_date,$project_id,$r['stock_id'])?></td>                       
                            <td style="text-align:right; vertical-align:top;"><?=number_format($balance,2,'.',',')?></td>                       
                      	</tr>
				<?php } ?>
            </table>
        
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>