<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	$from_date		= $_REQUEST['from_date'];
	$to_date		= $_REQUEST['to_date'];	
	$cleared		= ($_REQUEST['cleared']) ? 1  : 0 ;
	$attr_date 	 	= ($cleared) ? "date_cleared" : "cv_date";
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
                    <th style="text-align:left;">TIN #</th>
                     <th style="text-align:left;">&nbsp;</th>
                    <th style="text-align:left;">SUPPLIER</th>
                    <th style="text-align:right;">AMOUNT TAX BASE</th>
                    <th style="text-align:right;">INPUT TAX</th>
                    <th style="text-align:right;">TOTAL</th>
                    <th style="text-align:right;">RATE</th>
                    <th style="text-align:right;">TAX WITHHELD</th>
                </tr>	
                
             	<?php
				
					$query="
						select
							*
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
							wtax != '0'
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
						$cv_header_id			= $r['cv_header_id'];
						$cv_header_id_pad		= str_pad($cv_header_id,7,0,STR_PAD_LEFT);
						$cv_date				= $r['cv_date'];
						$check_date				= $r['check_date'];
						$check_no				= $r['check_no'];
						$supplier_id			= $r['supplier_id'];
						$supplier 				= $options->getAttribute('supplier','account_id',$supplier_id,'account');
						$tin 					= $options->getAttribute('supplier','account_id',$supplier_id,'tin');
						$vat_type				= $r['vat_type'];

						$cash_amount = $options->getCashAmount($cv_header_id);
						$tax_amount = $options->getTaxAmount($cv_header_id);
						$vatable_amount = $options->getVatableAmount($cv_header_id);
						$vat_amount = $options->getVatAmount($cv_header_id);
						
						$total_cash_amount += $cash_amount;
						$total_tax_amount += $tax_amount;
						$total_vatable_amount += $vatable_amount;
						$total_vat_amount += $vat_amount;
				?>	
                        <tr>
                        	<td><?=$i++?></td>		
                            <td><?=$tin?></td>
                            <td style ="text-align:center;"><?=$vat_type?></td>
                            <td><?=$supplier?></td>
                            <td style="text-align:right;"><?=number_format($vatable_amount,2,'.',',')?></td>
                            <td style="text-align:right;"><?=number_format($vat_amount,2,'.',',')?></td>
                            <td style="text-align:right;"><?=number_format($vat_amount + $vatable_amount,2,'.',',')?></td>
                            <td style="text-align:right;"><?=$r['wtax']?> %</td>
                            <td style="text-align:right;"><?=number_format($tax_amount,2,'.',',')?></td>
                      	</tr>
				<?php } ?>
                <tr>
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
            </table>
        
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>