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
            
			
            font-weight: bold;
        }
    }

</style>
</head>
<body>
		
	
    <table class="table-po" width="100%" height="100%" border="0">
		<tr>
			<td rowspan="2" width="50%"></td>
			<td colspan="2" width="50%" style="font-family: Times New Roman;font-weight: bold; text-align: center; font-size: 25px;"></td>
		</tr>
		<tr>
			<td width="400px;" style="text-align: center; font-weight: bold;padding: 5px;"></td>
			<td width="380px;" style="text-align: center;padding: 5px;"><?=$po_header_id_pad?></td>
		</tr>
		<tr>
			<td rowspan="2" style="font-size: 12px;padding: 5px;"></td>
			<td style="font-weight: bold;padding: 5px;"></td>
			<td style="text-align: center;padding: 5px;"><?=$pr_header_id_pad?></td>
		</tr>
		<tr>
			<td style="font-weight: bold;padding: 5px;"></td>
			<td style="text-align: center;padding: 5px;"><?=date("F j, Y",strtotime($date))?></td>
		</tr>
		<!-- Part 2 -->
        <tr>
			<td style="font-weight: bold;padding-left: 20px;"> <?=$supplier?></td>
			<td colspan="2" style="font-weight: bold;padding: 5px; "> <?=$project_name?></td>
		</tr>
		<tr>
			<td style="font-weight: bold; padding: 5px;">  <?=$supplier_address?></td>
			<td colspan="2" style="font-weight: bold; padding: 5px;">  <?=$location?></td>
		</tr>
		<tr>
			<td style="font-weight: bold; padding: 5px;"> <?=$contactno?></td>
			<td colspan="2" style="font-weight: bold; padding: 5px;"><?=$contact?></td>
		</tr>
		<tr>
			<td colspan="3" >
				<table width="100%" style="margin: 0px; border-collapse: collapse;">
				<tr>
					<td width="30%" style="font-weight: bold; text-align: center; padding: 5px;"></td>
					<td width="30%" style="font-weight: bold; text-align: center; padding: 5px;"></td>
					<td width="30%" style="font-weight: bold; text-align: center; padding: 5px;"></td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
		<tr>
			<td colspan="3">
				<table style="border-collapse: collapse;" border="0" width="100%">
				<tr>
					<td width="10%" style="font-weight: bold; text-align: center; padding: 5px;"></td>
					<td width="40%" style="font-weight: bold; text-align: center; padding: 5px;"></td>
					<td width="10%" style="font-weight: bold; text-align: center; padding: 5px;" colspan="2"></td>
					<td width="15%" style="font-weight: bold; text-align: center; padding: 5px;"></td>
					<td width="25%" style="font-weight: bold; text-align: center; padding: 5px;"></td>
				</tr>
				
				<!-- Nested Table -->
				
				<tr>
					<td colspan="6">
					<div style="height: 560px;">
						<table border="0" width="100%" style="border-collapse: collapse;">
							
									<!-- items starts -->
										<?php 
										foreach($po as $ad){
										?>
										<tr>
											<td width="10%" ><?=$ad['stockcode']?></td>
											<td width="40%"><?=$ad['stock'],' ',$ad['details']?></td>
											<td colspan="2" width="10%" style="text-align: right; padding-right: 20px; padding-top: 0px;"><?=number_format($ad['quantity'],2)?><?=$ad['unit']?></td>					
											<td width="15%" style="text-align: right; padding-right: 20px;"><?=number_format($ad['cost'],2)?></td>
											<td width="25%" style="text-align: right; padding-right: 20px;"><?=number_format($ad['amount'],2)?></td>
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
										?>
														
			
							<tr>
								<td></td>
								<td>*** NOTHING FOLLOWS ***</td>
								<td colspan="2"></td>
								<td></td>
								<td></td>
							</tr>	
							<tr>
								<td colspan="6" height="300"></td>
							</tr>
							<tr>
								<td></td>
								<td><?=$description?></td>
								<td colspan="4"></td>
							</tr>
						</table>
						
					</div>
					</td>					
				</tr>
				
				<!-- Part 3 -->
				<tr>
					<td colspan="4" rowspan="3">
					<table width="100%" border=0" style="border: 0px 0px 0px 0px;">
					<tr>
						<td width="30%" style="border: none; text-align: center;">
							<input type="text" style="border: none; text-align: center;" value ="<?=$options->getUserName($user_id);?>" /><br />
							
						</td>
						<td width="30%" style="border: none; text-align: center;">
						
						</td>
						<td width="30%" style="border: none; text-align: center;">
							<input type="text" style="border: none; text-align: center;" value ="<?=$datetime_encoded?>" /><br />
							
						</td>
					</tr>
					<tr>
						<td colspan="3" style="border: none; text-align: center;"><br /><br /></td>
					</tr>
					<tr>
												
						<td width="25%" style="border: none; text-align: center;">
							<input type="text" style="border: none; text-align: center;" value ="" /><br />
						
						</td>
						<td width="50%" style="border: none; text-align: center;">
							Michael John S. Ku
							<br/ >
							
						</td>
						<td width="25%" style="border: none; text-align: center;">
							<input type="text" style="border: none; text-align: center;" value ="" /><br />
							
						</td>
					</tr>
					</table>
					</td>
					<td style="text-align: center; font-weight: bold;"></td>
					<td style="text-align: right; padding-right: 20px;"><?=number_format($total,2)?></td>
				</tr>
				<tr>
					<td style="text-align: center; font-weight: bold;"></td>
					<td style="text-align: right; padding-right: 20px;"><?=number_format($vat_amount,2)?></td>
				</tr>
				<tr>
					<td style="text-align: center; font-weight: bold;"></td>
					<td style="text-align: right; padding-right: 20px;"><?=number_format($net,2)?></td>
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
