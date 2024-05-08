<?php
	require_once('my_Classes/options.class.php');
	require_once('my_Classes/numbertowords.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$n =new NumToWords();
	
	$po_header_id=$_REQUEST[id];

	
	$query="
		select
			 *
		 from
			  po_header as h,
			  projects as p
		 where
			h.project_id = p.project_id
		and
			po_header_id = '$po_header_id'
	";
	$result=mysql_query($query);
	$r = $aTrans = mysql_fetch_assoc($result);
    
    $po_header_id_pad     = str_pad($po_header_id,7,0,STR_PAD_LEFT);
    $pr_header_id         = $r['pr_header_id'];
    $project_id           = $r['project_id'];
    $project_name         = $r['project_name'];
    $datetime_encoded    = $r['datetime_encoded'];
    $location         	  = $r['location'];
    $contact         	  = $r['contact'];
    $description          = $r['description'];
    $status               = $r['status'];
    $date                 = $r['date'];
    $user_id              = $r['user_id'];
    $supplier_id          = $r['supplier_id'];
    $supplier             = $options->attr_Supplier($supplier_id,'account');
    $supplier_address     = $options->attr_Supplier($supplier_id,'address');
    $contactno    		  = $options->attr_Supplier($supplier_id,'contactno');
    $terms                = $r['terms'];
    $remarks              = $r['remarks'];
    $vat                  = $r['vat'];
    $wtax                 = $r['wtax'];
    $discount_amount      = $r['discount_amount'];
    
    $pr_header_id_pad     = str_pad($pr_header_id,7,0,STR_PAD_LEFT);
    
    $work_category_id     = $r['work_category_id'];
    $sub_work_category_id = $r['sub_work_category_id'];
    $scope_of_work        = $r['scope_of_work'];
    
    $work_category        = $options->attr_workcategory($work_category_id,'work');
    $sub_work_category    = $options->attr_workcategory($sub_work_category_id,'work');
    $note                 = $r['note'];
	
	$query="
		select
			 approval_date
		 from
			  pr_header
		where
		 pr_header_id = '$pr_header_id'
			  
	";
	$result=mysql_query($query);
	$a = mysql_fetch_assoc($result);
	
	
	function getAdvance_po($po_header_id){
	
	$sql = mysql_query("Select sum(ed.amount) as total_amount, eh.ev_header_id, eh.`status`
						from 
						ev_header as eh,
						ev_detail as ed
						where 
						eh.po_header_id = '$po_header_id' and
						ed.ev_header_id = eh.ev_header_id and
						eh.`status` != 'C'") or die (mysql_error())	;
	$r = mysql_fetch_assoc($sql);
	
		return $r['total_amount'];
	}
	
	$app_date = $a['approval_date'];
	
	function getPODetails($po_header_id){
		$query="
			select
                p.barcode,
				d.stock_id,
				p.stockcode,
				p.stock,
				p.unit,
				d.quantity,
				d.cost,
				d.amount,
                d.discount,
				d.details,
				chargables,
				person
			from
				po_detail as d, productmaster as p
			where
				d.stock_id = p.stock_id
			and
				po_header_id='$po_header_id'";
		$result = mysql_query($query) or die(mysql_error());
		$a = array();
		while($r = mysql_fetch_assoc($result)){
			$tmp = array();
            $tmp['barcode']        = $r['barcode'];
			$tmp['quantity'] 		= $r['quantity'];
            $tmp['stock_id']		= $r['stock_id'];
            $tmp['stock'] 			= $r['stock'];
            $tmp['stockcode']		= $r['stockcode'];
            $tmp['unit']			= $r['unit'];
            $tmp['cost']			= $r['cost'];
            $tmp['amount']			= $r['amount'];
            $tmp['discount']          = $r['discount'];
            $tmp['details']			= $r['details'];
			$tmp['chargables']		= $r['chargables'];
			$tmp['person']			= $r['person'];
			$a[] = $tmp;
		}
		return $a;
	}
	
	$po = getPODetails($po_header_id);
	
	

	function count_det($po_header_id){
		$count = 0;
		
		$q = mysql_query("select * from po_detail where po_header_id = '$po_header_id'") or die (mysql_error());
		$count = mysql_num_rows($q);
		
		return $count;
	}
	
	$counted = count_det($po_header_id);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<link rel="stylesheet" type="text/css" href="css/print_po.css"/>
<style type="text/css">	
	*{ font-size:14px; }
    .table-po{ border-collapse: collapse; }

    
    .table-po thead > tr:nth-child(2) td{
        border:1px solid #000;
        font-weight: bold;
    }
    .table-po thead table td{
        border:none;
    }
    .table-po tbody td{
        border-left: 1px solid #000;
        border-right: 1px solid #000;
    }
    .table-po tfoot tr td:nth-child(2){
        border-left: 1px solid #000;
    }


	td,th, .content td, .content th {
		font-family: Arial;		
		text-align:left;
	}

	.header td{ font-weight:bold; }

    @media screen {
        div.divFooter {
            display: none;
        }
    }
    @media print {
        div.divFooter {
            position: fixed;
            bottom: 0;

            font-family: "Times New Roman";
            font-size: 11px;
        }

        .table-po thead > tr:nth-child(2) td{
            border:1px solid #000;
            font-weight: bold;
        }
    }

</style>
</head>
<body>
		
	
    <table class="table-po" width="100%" border="1">
		<tr>
			<td rowspan="2" width="50%"><img src="images/logo_main.png" width="350px;" height="70px;"/></td>
			<td colspan="2" width="50%" style="font-family: Times New Roman;font-weight: bold; text-align: center; font-size: 25px;">PURCHASE ORDER</td>
		</tr>
		<tr>
			<td width="400px;" style="text-align: center; font-weight: bold;padding: 5px;">PO No.</td>
			<td width="380px;" style="text-align: center;padding: 5px;"><?=$po_header_id_pad?></td>
		</tr>
		<tr>
			<td rowspan="2" style="font-size: 12px;padding: 5px;">
				Lot 8 and 29, Blk. 28, Circumferential Road, Taculing, Bacolod City
			<br />
				Tel No. (034)460-1504  #8226 Fax No. (034)441-3972
			<br />
				Email Address: mkuconstruction707@gmail.com
			</td>
			<td style="font-weight: bold;padding: 5px;">Requistion Slip No.(PR No)</td>
			<td style="text-align: center;padding: 5px;"><?=$pr_header_id_pad?></td>
		</tr>
		<tr>
			<td style="font-weight: bold;padding: 5px;">Date</td>
			<td style="text-align: center;padding: 5px;"><?=date("F j, Y",strtotime($date))?></td>
		</tr>
		<!-- Part 2 -->
        <tr>
			<td style="font-weight: bold;padding: 5px;"> SUPPLIER: <?=$supplier?></td>
			<td colspan="2" style="font-weight: bold;padding: 5px; "> PROJECT/LOCATION: <?=$project_name?></td>
		</tr>
		<tr>
			<td style="font-weight: bold; padding: 5px;"> ADDRESS: <?=$supplier_address?></td>
			<td colspan="2" style="font-weight: bold; padding: 5px;"> ADDRESS: <?=$location?></td>
		</tr>
		<tr>
			<td style="font-weight: bold; padding: 5px;"> CONTACT DETAILS: <?=$contactno?></td>
			<td colspan="2" style="font-weight: bold; padding: 5px;"> CONTACT DETAILS: <?=$contact?></td>
		</tr>
		<tr>
			<td colspan="3" >
				<table width="100%" style="margin: 0px; border-collapse: collapse;">
				<tr>
					<td width="30%" style="font-weight: bold; text-align: center; padding: 5px;">SHIPPING METHOD</td>
					<td width="30%" style="font-weight: bold; text-align: center; padding: 5px;">DELIVERY ITEMS</td>
					<td width="30%" style="font-weight: bold; text-align: center; padding: 5px;">DELIVERY DATE</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<table style="border-collapse: collapse;" border="1" width="100%">
				<tr>
					<td width="10%" style="font-weight: bold; text-align: center; padding: 5px;">ITEM CODE</td>
					<td width="40%" style="font-weight: bold; text-align: center; padding: 5px;">DESCRIPTION</td>
					<td width="10%" style="font-weight: bold; text-align: center; padding: 5px;">QTY</td>
					<td width="10%" style="font-weight: bold; text-align: center; padding: 5px;">UOM</td>
					<td width="15%" style="font-weight: bold; text-align: center; padding: 5px;">UNIT PRICE</td>
					<td width="25%" style="font-weight: bold; text-align: center; padding: 5px;">TOTAL</td>
				</tr>
				<!-- items starts -->
				<?php 
				foreach($po as $ad){
				?>
				<tr>
					<td><?=$ad['stockcode']?></td>
					<td><?=$ad['stock'],' ',$ad['details']?></td>
					<td style="text-align: right; padding-right: 20px;"><?=number_format($ad['quantity'],2)?></td>
					<td style="text-align: right; padding-right: 20px;"><?=$ad['unit']?></td>					
					<td style="text-align: right; padding-right: 20px;"></td>
					<td style="text-align: right; padding-right: 20px;"></td>
				</tr>
				<?php
					
					$total += $ad['amount'];
					
				}
				$vatable = $vat_amount = $net = 0;
				
				if($vat != 0){
					$vatable  = $total/1.12;
					$vat_amount = $total - $vatable;
					
					$net = $total - $vat_amount;
				}else{
					$net = $total;
				}
				
				
				$remain = 19 - $counted;
				
				?>
				<tr>
					<td><br /></td>
					<td>*** NOTHING FOLLOWS ***</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<?php
				
				for($x=0;$x<=$remain;$x++){
				?>
				<tr>
					<td><br /></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<?php
				}
				?>
				<!-- Part 3 -->
				<tr>
					<td colspan="4" rowspan="3">
					<table width="100%" border="0" style="border: 0px 0px 0px 0px;">
					<tr>
						<td width="30%" style="border: none; text-align: center;">
							<input type="text" class="line_bottom" value ="<?=$options->getUserName($user_id);?>" /><br />
							Prepared By 
						</td>
						<td width="30%" style="border: none; text-align: center;">
						
						</td>
						<td width="30%" style="border: none; text-align: center;">
							<input type="text" class="line_bottom" value ="<?=$datetime_encoded?>" /><br />
							Date Prepared 
						</td>
					</tr>
					<tr>
						<td colspan="3" style="border: none; text-align: center;"><br /><br /></td>
					</tr>
					<tr>
												
						<td width="30%" style="border: none; text-align: center;">
							<input type="text" class="line_bottom" value ="" /><br />
							Received By 
						</td>
						<td width="30%" style="border: none; text-align: center;">
							APPROVED
						</td>
						<td width="30%" style="border: none; text-align: center;">
							<input type="text" class="line_bottom" value ="" /><br />
							Date Received 
						</td>
					</tr>
					</table>
					</td>
					<td style="text-align: center; font-weight: bold;">Subtotal</td>
					<td style="text-align: right; padding-right: 20px;"></td>
				</tr>
				<tr>
					<td style="text-align: center; font-weight: bold;">VAT</td>
					<td style="text-align: right; padding-right: 20px;"></td>
				</tr>
				<tr>
					<td style="text-align: center; font-weight: bold;">Total</td>
					<td style="text-align: right; padding-right: 20px;"></td>
				</tr>
				</table>
			</td>
		</tr>
    </table>
    
     <!-- 
    <table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:10px;" class="summary">
        <tr>
            <td>Prepared By:<p>
                <input type="text" class="line_bottom" value = "<?=$options->getUserName($user_id);?>" /><br>Purchasing Staff <br>
                <span style='font-size:10px;'><?=$aTrans['datetime_encoded']?></span>
            </p></td>
            <td>Checked By:<p>
                <input type="text" class="line_bottom" /><br>Purchasing Manager</p></td>
            <td>Approved By:<p>
                <input type="text" class="line_bottom" /><br>President / G.M.</p></td>
        </tr>
    </table>
    <?php if(!empty($note) || 1){ ?>
    <div style='font-size:12px;'>
    	Note: <?=$note?><br>
    </div>
    <?php } ?>-->

    <!--<div class="divFooter">
        F-PUR-004<br>
        Rev. 1 10/09/15
    </div>
<div class="page-break"></div>-->
</body>
</html>
