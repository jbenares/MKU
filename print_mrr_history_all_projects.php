<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	function getAPVNo($rr_header_id){
		$result = mysql_query("
			select
				h.apv_header_id
			from
				apv_header as h, apv_detail as d 
			where
				h.apv_header_id = d.apv_header_id
			and h.status != 'C'
			and d.rr_id = '$rr_header_id'
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		return $r['apv_header_id'];
	}
	
	$options=new options();	
	$from_date		= $_REQUEST['from_date'];
	$to_date		= $_REQUEST['to_date'];
	$project_id		= $_REQUEST['project_id'];
	$project = $options->getAttribute("projects","project_id",$project_id,"project_name");
	$categ_id1		= $_REQUEST['categ_id1'];
	$categ_id2		= $_REQUEST['categ_id2'];
	$supplier 		= $_REQUEST['supplier'];
	$po_header_id	= $_REQUEST['po_header_id'];
	$driverID		= $_REQUEST['driverID'];
	$stock_id		= $_REQUEST['stock_id'];
	$account_id		= $_REQUEST['account_id'];	
	$rr_type		= $_REQUEST['rr_type'];
	$work_category_id		= $_REQUEST['work_category_id'];
	$sub_work_category_id	= $_REQUEST['sub_work_category_id'];
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
<div class="container" style="width: 100%;">
	
     <div><!--Start of Form-->
     	
     	<div style="font-weight:bolder;">
        	MRR HISTORY REPORT <br />
            <?=$project?> <br />
            From <?=date("m/d/Y",strtotime($from_date))?> to <?=date("m/d/Y",strtotime($to_date))?>
        </div>           
        <div class="content" style="">
        	<?php
			$result = mysql_query("select * from projects order by project_name asc") or die(mysql_error());
			$projects = array();
			while($r = mysql_fetch_assoc($result)){
				$projects[] = $r['project_id'];
			}
            ?>
        
			<?php
			$grand_total_amount = 0;
			$grand_total_quantity = 0;
			$grand_total_optional_quantity = 0;
			foreach($projects as $project_id){
            ?>        
            <?php
				$query="
					select
						quantity,amount,driverID,h.date,h.rr_header_id,h.po_header_id,invoice,account,stock,quantity_cum,d.cost,d.account_id, h.encoded_datetime
					from
						rr_header as h, rr_detail as d, productmaster as p, supplier as s, po_header as po
					where
						h.po_header_id = po.po_header_id
					and
						h.rr_header_id = d.rr_header_id
					and
						d.stock_id = p.stock_id
					and
						s.account_id = h.supplier_id
					and
						h.status != 'C'
					and
						h.date between '$from_date' and '$to_date'
					and
						h.project_id = '$project_id'
					and
						account like '%$supplier%'
					and
						rr_type = '$rr_type'
				";
				if($po_header_id){
				$query.="
					and
						h.po_header_id = '$po_header_id'
				";	
				}
				
				if($categ_id1){
				$query.="
					and
						p.categ_id1 = '$categ_id2'
				";	
				}
				if($categ_id2){
				$query.="
					and
						p.categ_id2 = '$categ_id2'
				";	
				}
				
				if($driverID){
				$query.="
					and
						d.driverID= '$driverID'
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
					order by
						h.date asc, h.rr_header_id asc
				";
				$result=mysql_query($query) or die(mysql_error());
				
				if(mysql_num_rows($result) <= 0){
					continue;	
				}
            ?>
                <table cellpadding="6" width="1200">
                	<caption style="text-align:left; font-weight:bold;"><?=$options->getAttribute("projects","project_id",$project_id,"project_name");?></caption>
                    <tr>
                        <th width="75">DATE</th>
                        <th width="75">TIME ENCODED</th>
                        <th width="75">MRR#</th>
                        <th width="75">APV#</th>
                        <th width="75">PO#</th>
                        <th width="75">INVOICE</th>
                        <th width="75">SUPPLIER</th>
                        <th width="75">DRIVER</th>
                        <th width="75">ITEM</th>
                        <th width="75" style="text-align: right;">QTY</th>
                        <th width="75" style="">UNIT</th>
                        <th width="75" style="text-align: right;">QTY (Optional)</th>
                        <th width="75" style="text-align: right;">PRICE</th>
                        <th width="75" style="text-align: right;">AMOUNT</th>
                        <th width="75" style="text-align: right;">VAT BASE</th>
                        <th  width="75" text-align: right;">VAT</th>
                        
                    </tr>	
                    
                    <?php
					$total_quantity = 0;
					$total_optional_quantity = 0;
					$total_amount = 0;
					$vat_base = 0;
					$vat = 0;
					$total_vat = 0;
					$total_base = 0;
					
					while($r=mysql_fetch_assoc($result)){
						$total_quantity += $r['quantity'];
						$total_optional_quantity += $r['quantity_cum'];
						$total_amount += $r['amount'];
						$vat_base = $r['amount'] / 1.12;
						$vat = ($r['amount'] / 1.12 ) * .12;
						
						$total_base += $r['amount'] / 1.12;
						$total_vat += ($r['amount'] / 1.12 ) * .12;
						

						$driver_name = $options->getAttribute('drivers','driverID',$r['driverID'],'driver_name');
                    ?>	
                    <tr>
                        <td><?=date("m/d/Y",strtotime($r['date']))?></td>
                        <td><?=$r['encoded_datetime']?></td>
                        <td><?=str_pad($r['rr_header_id'],7,0,STR_PAD_LEFT)?></td>                       
                        <!--<td><?=$options->getAttribute('apv_detail','rr_id',$r['rr_header_id'],'apv_header_id')?></td> -->
                        <td><?=getAPVNo($r['rr_header_id'])?></td> 
                        <td><?=str_pad($r['po_header_id'],7,0,STR_PAD_LEFT)?></td>                       
                        <td><?=$r['invoice']?></td>                       
                        <td><?=$r['account']?></td>                       
                        <td><?=$driver_name?></td>                       
                        <td><?=$r['stock']?> <?=(!empty($r['account_id'])) ? "(".$options->getAttribute('account','account_id',$r['account_id'],'account').")" : ""?></td>                       
                        <td style="text-align:right;"><?=number_format($r['quantity'],2,'.',',')?></td>                       
                        <td><?=$r['unit']?></td>   
                        <td style="text-align:right;"><?=number_format($r['quantity_cum'],2,'.',',')?></td>
                        <td style="text-align:right;"><?=number_format($r['cost'],2,'.',',')?></td>                       
                        <td style="text-align:right;"><?=number_format($r['amount'],2,'.',',')?></td>                       
                        <td style="text-align:right;"><?=number_format($vat_base,2,'.',',')?></td>                       
                        <td style="text-align:right;"><?=number_format($vat,2,'.',',')?></td>                       
                    </tr>
                    <?php } 
					?>
                    <?php 
					$grand_vat += $total_vat;
					$grand_vat_base += $total_base;
					$grand_total_amount += $total_amount;
					$grand_total_quantity += $total_quantity;
					$grand_total_optional_quantity += $total_optional_quantity;
					?>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td style="text-align:right; font-weight:bold; border-bottom:1px solid #000;"><?=number_format($total_quantity,2,'.',',')?></td>
                        <td>&nbsp;</td>
                        <td style="text-align:right; border-bottom:1px solid #000;"><?=number_format($total_optional_quantity,2,'.',',')?></td>
                        <td>&nbsp;</td>
                        <td style="text-align:right; font-weight:bold; border-bottom:1px solid #000; width: 5%;"><?=number_format($total_amount,2,'.',',')?></td>
                        <td style="text-align:right; font-weight:bold; border-bottom:1px solid #000; width: 5%;"><?=number_format($total_base,2,'.',',')?></td>
                        <td style="text-align:right; font-weight:bold; border-bottom:1px solid #000; width: 5%;"><?=number_format($total_vat,2,'.',',')?></td>
                    </tr>
                </table>
            <?php } ?>
            
            <table style="margin-top:5px;" width="1200" >
                <tr>
                    <td colspan="8" style="text-align:right; font-weight:bolder; border-top:1px solid #000;">&nbsp;</td>
                    <td width="75" style="text-align:right; font-weight:bolder; border-top:1px solid #000;" ><span style="border-bottom:4px double #000;"><?=number_format($grand_total_quantity,2,'.',',')?></span></td>
                    <td width="75" style="text-align:right; font-weight:bolder; border-top:1px solid #000;" ></td>
					<td width="75" style="text-align:right; border-top:1px solid #000;"><span style='border-bottom:4px double #000; font-weight:bold;'><?=number_format($grand_total_optional_quantity,2,'.',',')?></span></td>
                    <td width="75" style="text-align:right; font-weight:bolder; border-top:1px solid #000; " ></td>
                    <td width="75" style="text-align:right; font-weight:bolder; border-top:1px solid #000;"><span style="border-bottom:4px double #000;"><?=number_format($grand_total_amount,2,'.',',')?></span></td>
                    <td width="75" style="text-align:right; font-weight:bolder; border-top:1px solid #000;"><span style="border-bottom:4px double #000;"><?=number_format($grand_vat_base,2,'.',',')?></span></td>
                    <td width="75" style="text-align:right; font-weight:bolder; border-top:1px solid #000;"><span style="border-bottom:4px double #000;"><?=number_format($grand_vat,2,'.',',')?></span></td>
                </tr>
            </table>
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html> 