<?php
require_once('my_Classes/options.class.php');
include_once("conf/ucs.conf.php");

$options=new options();	
$from_date		= $_REQUEST['from_date'];
$to_date		= $_REQUEST['to_date'];	
$cleared		= ($_REQUEST['cleared']) ? 1  : 0 ;
$attr_date 	 	= ($cleared) ? "date_cleared" : "cv_date";

function getTax($from_date,$to_date,$cleared,$attr_date){
	$query="
		select
			distinct(wtax) as wtax
		from
			cv_header as h
		where
			status != 'C'
		and
			cleared = '$cleared'
		and
			$attr_date between '$from_date' and '$to_date'
		and
			wtax != '0'
	";
	$a = array();
	$result = mysql_query($query) or die(mysql_error());
	while($r = mysql_fetch_assoc($result)){
		$a[] = $r['wtax'];	
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
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	
     	<div style="font-weight:bolder;">
        	BANK RECONCILATION - WITHHOLDING TAX REPORT<br />
            From <?=date("m/d/Y",strtotime($from_date))?> to <?=date("m/d/Y",strtotime($to_date))?>
        </div>           
        <div class="content" style="">
        	<table cellpadding="6">
            	<tr>
                    <th style="text-align:left;">#</th>
                    <th style="text-align:left;">&nbsp;</th>
                     <th style="text-align:left;" width="300">SUPPLIER</th>
                    <th style="text-align:left;">REFERENCE</th>
	                <th style="text-align:left;">APV AMOUNT</th>
                    <th style="text-align:right;">AMOUNT TAX BASE</th>
                    <th style="text-align:right;">INPUT TAX</th>
                    <th style="text-align:right;">TOTAL</th>
                    <th style="text-align:right;">RATE</th>
                    <th style="text-align:right;">TAX WITHHELD</th>
                </tr>	
                <?php
				$aWtax = getTax($from_date,$to_date,$cleared,$attr_date);
				if(!empty($aWtax)):
				$g_tax_base = $g_tax_witheld = 0;
				foreach($aWtax as $wtax):
				echo "<tr><td colspan='8' style='font-weight:bold;'>WITHOLDING TAX : $wtax %</td></tr>";
			 	?>
             	<?php
				
					$query="
						select
							h.supplier_id,wtax,h.cv_header_id
						from
							cv_header as h, supplier as s
						where
							h.supplier_id = s.account_id
						and
							status != 'C'
						and
							cleared = '$cleared'
						and
							$attr_date between '$from_date' and '$to_date'
						and
							wtax = '$wtax'
						group by account_id
						order by
							account asc
					";
					$result=mysql_query($query) or die(mysql_error());
					$total_cash_amount = 0;
					$total_tax_amount = 0;
					$total_vatable_amount = 0;
					$total_vat_amount = 0;
					$i = 1;
					while($r=mysql_fetch_assoc($result)){
						

						$supplier_id	= $r['supplier_id'];
						$a = $options->getWitholdingTaxAttributes($supplier_id,$from_date,$to_date,$attr_date,$cleared);
						
						$cash_amount 	= $a['cash'];
						$tax_amount 	= $a['tax'];
						$vatable_amount = $a['vatable'];
						$vat_amount 	= $a['vat'];
						$cv_header_id 	= $r['cv_header_id'];
						
						$total_cash_amount += $cash_amount;
						$total_tax_amount += $tax_amount;
						$total_vatable_amount += $vatable_amount;
						$total_vat_amount += $vat_amount;
						
						$supplier 				= $options->getAttribute('supplier','account_id',$supplier_id,'account');
						$tin 					= $options->getAttribute('supplier','account_id',$supplier_id,'tin');
						$vat_type 	            = $options->getAttribute('supplier','account_id',$supplier_id,'vat_type');
						$total_apv = 0;
						
						
						$sqlc = mysql_query("Select * from cv_detail where cv_header_id = '$cv_header_id'") or die (mysql_error());
						while($rc = mysql_fetch_assoc($sqlc)){
							$apv_header_id = $rc['apv_header_id'];
							$sqla = mysql_query("Select ad.amount as total_apv
												from 
												apv_detail as ad,
												apv_header as ah
												where 
												ah.apv_header_id = '$apv_header_id' and
												ah.apv_header_id = ad.apv_header_id and
												ah.`status` != 'C'") or die (mysql_error());
							while($ra = mysql_fetch_assoc($sqla)){
								$total_apv += $ra['total_apv'];
							}
						}
				?>	
                        <tr>
                        	<td><?=$i++?></td>		
                            <td><?=$vat_type?></td>
                            <td><?=$supplier?></td>
							<td>CV # : <?=sprintf("%07d", $cv_header_id)?></td>
                            <td>
								<?php if($total_apv != 0){?>
								<?=number_format($total_apv,2)?>
								<?php } ?>
							</td>
                            <td style="text-align:right;"><?=number_format($vatable_amount,2,'.',',')?></td>
                            <td style="text-align:right;"><?=number_format($vat_amount,2,'.',',')?></td>
                            <td style="text-align:right;"><?=number_format($vat_amount + $vatable_amount,2,'.',',')?></td>
                            <td style="text-align:right;"><?=$r['wtax']?> %</td>
                            <td style="text-align:right;"><?=number_format($vatable_amount*($r['wtax']/100),2,'.',',')?></td>
                      	</tr>
				<?php } ?>
                <tr>
                	<td style="border-top:1px solid #000;">&nbsp;</td>
                    <td style="border-top:1px solid #000;">&nbsp;</td>
                    <td style="border-top:1px solid #000;">&nbsp;</td>
                    <td style="border-top:1px solid #000;">&nbsp;</td>
                    <td style="border-top:1px solid #000;">&nbsp;</td>
                    <td style="border-top:1px solid #000;">&nbsp;</td>
                    <td style="border-top:1px solid #000; font-weight:bold; border-top:1px solid #000; text-align:right;"><span style="border-bottom:3px double #000;"><?=number_format($total_vatable_amount,2,'.',',')?></span></td>
                    <td style="border-top:1px solid #000;">&nbsp;</td>
                    <td style="border-top:1px solid #000;">&nbsp;</td>
                    <td style="border-top:1px solid #000;">&nbsp;</td>
                    <td style="text-align:right; font-weight:bold; border-top:1px solid #000;"><span style="border-bottom:3px double #000;"><?=number_format($total_tax_amount,2,'.',',')?></span></td>
                </tr>
                
                <?php 
				echo "<tr><td colspan='8'>&nbsp;</td></tr>";
				$g_tax_base  += $total_vatable_amount;
				$g_tax_witheld += $total_tax_amount;
				endforeach;
				endif; 
				?>
                
                <tr>
                	<td style="border-top:3px double #000;">&nbsp;</td>
                    <td style="border-top:3px double #000;">&nbsp;</td>
                    <td style="border-top:3px double #000;">&nbsp;</td>
                    <td style="border-top:3px double #000;">&nbsp;</td>
                    <td style="border-top:3px double #000;">&nbsp;</td>
                    <td style="border-top:3px double #000;">&nbsp;</td>
                    <td style="border-top:3px double #000; font-weight:bold; text-align:right;"><span style="border-bottom:3px double #000;"><?=number_format($g_tax_base,2,'.',',')?></span></td>
                    <td style="border-top:3px double #000;">&nbsp;</td>
                    <td style="border-top:3px double #000;">&nbsp;</td>
                    <td style="border-top:3px double #000;">&nbsp;</td>
                    <td style="text-align:right; font-weight:bold; border-top:3px double #000;"><span style="border-bottom:3px double #000;"><?=number_format($g_tax_witheld,2,'.',',')?></span></td>
                </tr>
            </table>
        
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>