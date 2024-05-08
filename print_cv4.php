<?php
	require_once('my_Classes/options.class.php');
	#require_once('my_Classes/numbertowords.class.php');
	require_once('my_Classes/numtowords.class.php');
	
	include_once("conf/ucs.conf.php");

	$options=new options();	
	
	#$c=new NumToWords();
	$convert = new num2words();
	
	$cv_header_id	= $_REQUEST['id'];
	$bdo 			= $_REQUEST['bdo'];
	
	$query="
		select
			 *
		 from
			  cv_header
		 where
		      cv_header_id = '$cv_header_id'
	";
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);
	
	$cv_header_id			= $r['cv_header_id'];
	$cv_header_id_pad		= str_pad($r['cv_header_id'],7,0,STR_PAD_LEFT);
	$cv_date				= date("m/d/Y",strtotime($r['cv_date']));
	$check_date				= date("m/d/Y",strtotime($r['check_date']));
	$check_no				= $r['check_no'];
	$supplier_id 			= $r['supplier_id'];
	$supplier				= $options->getAttribute('supplier','account_id',$supplier_id,'account');
	$supplier_address		= $options->getAttribute('supplier','account_id',$supplier_id,'address');
	$percent				= $r['percent'];
	
	$cash_gchart_id			= $r['cash_gchart_id'];
	$cash_account			= $options->getAttribute('gchart','gchart_id',$cash_gchart_id,'gchart');
	$cash_acode				= $options->getAttribute('gchart','gchart_id',$cash_gchart_id,'acode');
	
	$ap_gchart_id			= $r['ap_gchart_id'];
	$ap_account				= $options->getAttribute('gchart','gchart_id',$ap_gchart_id,'gchart');
	$ap_acode				= $options->getAttribute('gchart','gchart_id',$ap_gchart_id,'acode');
	$type					= $r['type'];
	$particulars			= $r['particulars'];
	
	$status					= $r['status'];
	$user_id				= $r['user_id'];	
	
	$wtax					= $r['wtax'];
	$vat					= $r['vat'];
	$wtax_gchart_id 		= $r['wtax_gchart_id'];
	$vat_gchart_id			= $r['vat_gchart_id'];
	$cv_no					= $r['cv_no'];
	
	$materials_gchart_id  	= 119;
	$materials_account		= $options->getAttribute('gchart','gchart_id',$materials_gchart_id,'gchart');
	$materials_acode		= $options->getAttribute('gchart','gchart_id',$materials_gchart_id,'acode');
	
	$vat_account			= $options->getAttribute('gchart','gchart_id',$vat_gchart_id,'gchart');
	$vat_acode				= $options->getAttribute('gchart','gchart_id',$vat_gchart_id,'acode');
	
	$wtax_account			= $options->getAttribute('gchart','gchart_id',$wtax_gchart_id,'gchart');
	$wtax_acode				= $options->getAttribute('gchart','gchart_id',$wtax_gchart_id,'acode');
	
	$advances_gchart_id = $options->getAttribute("supplier","account_id",$supplier_id,"advances_gchart_id");
	$advances_acode		= $options->getAttribute('gchart','gchart_id',$advances_gchart_id,'acode');
	$advances_gchart	= $options->getAttribute("gchart","gchart_id",$advances_gchart_id,"gchart");
	
	$project_id			= $r['project_id'];
	$project_name		= $options->attr_Project($project_id,'project_name');
	$project_code		= $options->attr_Project($project_id,'project_code');
	$project_name_code	= ($project_id)?"$project_name - $project_code":"";
	
	$vat_gchart_id	= $r['vat_gchart_id'];
	
	$retention_gchart_id	= $r['retention_gchart_id'];
	$chargable_gchart_id	= $r['chargable_gchart_id'];
	$retention_amount		= $r['retention_amount'];
	$chargable_amount		= $r['chargable_amount'];
	

	$retention_acode		= $options->getAttribute('gchart','gchart_id',$retention_gchart_id,'acode');
	$chargable_acode		= $options->getAttribute('gchart','gchart_id',$chargable_gchart_id,'acode');
	$retention_account		= $options->getAttribute('gchart','gchart_id',$retention_gchart_id,'gchart');
	$chargable_account		= $options->getAttribute('gchart','gchart_id',$chargable_gchart_id,'gchart');
	
	$rmy_gchart_id			= $r['rmy_gchart_id'];	
	$rmy_amount				= $r['rmy_amount'];
	$rmy_account			= $options->getAttribute('gchart','gchart_id',$rmy_gchart_id,'gchart');
	$rmy_acode			= $options->getAttribute('gchart','gchart_id',$rmy_gchart_id,'acode');
	
	function getDR($cv_header_id){
		$sql = mysql_query("select
								*
								from
								cv_header as h,
								cv_detail as d
								where
								h.cv_header_id = d.cv_header_id and
								h.`status` != 'C' and
								h.cv_header_id = '$cv_header_id'") or die (mysql_error());
									
		while($r = mysql_fetch_assoc($sql)){
			$apv_id = $r['apv_header_id'];
			
			$sql2 = mysql_query("select
								d.rr_id,
								v.invoice
								from
								apv_header as h,
								apv_detail as d,
								rr_header as c,
								rr_detail as v
								where
								h.apv_header_id = d.apv_header_id and
								d.rr_id = c.rr_header_id and
								c.rr_header_id = v.rr_header_id and
								h.`status` != 'C' and
								c.`status` != 'C' and
								h.apv_header_id = '$apv_id'
								group by d.rr_id") or die (mysql_error());
			while($r2 = mysql_fetch_assoc($sql2)){
				$val[] = $r2['invoice'];
			}								
		}	
		
		return $val;
	}	
	
	$result = mysql_query("
					select 
						amount,apv_header_id
					from 
						cv_detail
					where
						cv_header_id = '$cv_header_id'
	") or die(mysql_error());

	$amount = 0;
	$discount_ap = 0;
	while($r = mysql_fetch_assoc($result)):
		$amount += $r['amount'];
		$discount_ap += $options->getAttribute("apv_header","apv_header_id",$r['apv_header_id'],"discount_amount");
	endwhile;
	
	
    //$amount      = $r['amount'];
    $vatable     = (($amount) / (1 + ($vat/100)));
    $tax_amount  =  $vatable * ($wtax/100);	
    $cash_amount = $amount - $retention_amount - $chargable_amount - $tax_amount - $rmy_amount;
	
	$convert = new num2words();
	#$cash_amount = number_format($cash_amount,2,'.','');
	
	$cash_amount = round($cash_amount,2);
	$convert->setNumber($cash_amount);
	#$c->setNumber($cash_amount);
	
	#$words = $c->num_words().$c->appendDecimal();
	$words = strtoupper($convert->getCurrency());
	$_cash_amount = $cash_amount;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<link rel="stylesheet" type="text/css" href="css/printable.css" />
<style type="text/css">
*{
	font-size:16px;	
}

.header_table,.content_table{
	width:100%;
}
.content_table th{
	border-top:1px solid #000;
	border-bottom:1px solid #000;	
}

.entry td{
	font-size:11px;	
}

td{
	font-size: 12px;	
}
</style>
</head>
<body>
<div class="container">	    
    <div style="text-align: center;">
    	<img src="images/logo_main.png" style="width: 500px;"/>
		<p>Lot 8 and 29, Blk. 28, Circumferential Road, Taculing, Bacolod City</p>
		<p>Tel. No. (034)460-1504 Fax No. (034)441-3972</p>
    </div>
	<table width="100%" style="padding: 10px;" border="0">
	<tr>
		<td style="font-weight: bold; font-size: 20px;" width="50%">CHECK VOUCHER</td>
		<td width="50%" style="text-align: right; font-weight: bold;">
			CV. No <?=str_pad($cv_no,7,0,STR_PAD_LEFT)?><br />
			DATE : <?=$cv_date?>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="border-bottom: 1px solid black;">PAID TO: <?=$supplier?></td>
	</tr>
	<tr>
		<td colspan="2" style="border-bottom: 1px solid black;">ADDRESS: <?=$supplier_address?></td>
	</tr>
	<tr>
		<td style="text-align: center; font-weight: bold;">Particulars</td>
		<td style="text-align: center; font-weight: bold;">Amount</td>
	</tr>
	<tr>
		<td style="text-align: center;"><p style="paddin-left: 40px; padding-right: 40px;">
		<p style="font-size: 10px; font-weight: bold;">
			<?php 
				$val2 = getDR($cv_header_id);
			
				foreach($val2 as $value){
					echo " DR: <span sltye='font-weight: bold; font-size: 12px;'>".$value.'</span>';
				}	
				
				echo $particulars;
			?>
		</p>
		</td>
		<td style="text-align: center; font-weight: bold;"><?=number_format($_cash_amount,2,'.',',')?></td>
	</tr>
	
	<tr>
		<td colspan="2" style="text-align">
		<br />
		<table style="width:80%; border-collapse:collapse; margin: 0 auto;" class="entry">
        	<tr>
            	<td style="width:20%; border-bottom:1px solid #000;">Account Code</td>
                <td style="border-bottom:1px solid #000;">Account Name</td>
                <td style="width:10%; border-bottom:1px solid #000;">Debit</td>
                <td style="width:10%; border-bottom:1px solid #000;">Credit</td>
            </tr>
            <?php if($type == "M") { ?>
				<?php
                
                $total_vat_amount  = 0;
                $total_wtax_amount = 0;
                $total_cash_amount = 0;
                $total_debit       = 0;
                $total_credit      = 0;
				
                $vatable     = ($amount / (1 + ($vat/100)));
                $vat_amount  = $vatable * ($vat/100);
                $tax_amount  =  $vatable * ($wtax/100);
                $cash_amount = $amount - $tax_amount;
                $cash_amount -= $chargable_amount;
                $cash_amount -= $retention_amount;
                $cash_amount -= $rmy_amount;
				
                $total_vat_amount  += $vat_amount;
                $total_wtax_amount += $tax_amount;
                $total_cash_amount += $cash_amount;
                ?>
     
                
                <?php if($amount > 0){ ?>
                <tr>
                    <td><?=$ap_acode?></td>
                    <td><?=$ap_account?></td>
                    <td><?=number_format($amount+$discount_ap,2,'.',',')?></td>
                    <td>&nbsp;</td>
                </tr>
                <?php $total_debit += ($amount+$discount_ap) ?>
                <?php } ?>
				
                <!-- Debit end -->
				<!-- Credit start -->
				<?php if($discount_ap > 0){ ?>
					<tr>
						<td><?=$advances_acode?></td>
						<td><?=$advances_gchart?></td>
						<td>&nbsp;</td>
						<td><?=number_format($discount_ap,2,'.',',')?></td>
					</tr>
                <?php $total_credit += $discount_ap ?>
                <?php } ?>
                
                <?php if($total_wtax_amount > 0){ ?>
                <tr>
                    <td><?=$wtax_acode?></td>
                    <td><?=$wtax_account?></td>
                    <td>&nbsp;</td>
                    <td><?=number_format($total_wtax_amount,2,'.',',')?></td>
                </tr>
                <?php $total_credit += $total_wtax_amount ?>
                <?php } ?>

                <!-- subtract retention and chargables here -->
                <?php if($retention_amount > 0){ ?>
                <tr>
                    <td><?=$retention_acode?></td>
                    <td><?=$retention_account?></td>
                    <td>&nbsp;</td>
                    <td><?=number_format($retention_amount,2,'.',',')?></td>
                </tr>
                <?php $total_credit += $retention_amount ?>
                <?php } ?>
                
                <?php if($chargable_amount > 0){ ?>
                <tr>
                    <td><?=$chargable_acode?></td>
                    <td><?=$chargable_account?></td>
                    <td>&nbsp;</td>
                    <td><?=number_format($chargable_amount,2,'.',',')?></td>
                </tr>
                <?php $total_credit += $chargable_amount ?>
                <?php } ?>
    
                <?php if($rmy_amount > 0){ ?>
                <tr>
                    <td><?=$rmy_acode?></td>
                    <td><?=$rmy_account?></td>
                    <td>&nbsp;</td>
                    <td><?=number_format($rmy_amount,2,'.',',')?></td>
            	</tr>
                <?php $total_credit += $rmy_amount ?>
                <?php } ?>
	
                <?php if($total_cash_amount > 0){ ?>
                <tr>
                    <td><?=$cash_acode?></td>
                    <td><?=$cash_account?></td>
                    <td>&nbsp;</td>
                    <td><?=number_format($total_cash_amount,2,'.',',')?></td>
                </tr>
                <?php $total_credit += $total_cash_amount; ?>
                <?php } ?>
				<!-- credit end -->
            
            <?php } else { ?>
				<?php
                $result = mysql_query("select gchart,acode,amount,project_id from cv_detail as d, gchart as g where d.gchart_id = g.gchart_id and cv_header_id = '$cv_header_id'") or die(mysql_error());
				$total = 0;
				$total_vat_amount = 0;
				$total_wtax_amount = 0;
				$total_cash_amount = 0;
				$total_debit = 0;
				$total_credit = 0;
				
				if(mysql_num_rows($result) <= 4):
				while($r=mysql_fetch_assoc($result)){
					$amount = $r['amount'];
					$total += $r['amount'];
					$project_name = $options->getAttribute("projects","project_id",$r['project_id'],"project_name");
					
				?>
					<tr>
                        <td><?=$r['acode']?></td>
                        <td><?=$r['gchart']?> | <?=$project_name?></td>
                        <td><?=number_format($amount,2,'.',',')?></td>
                        <td>&nbsp;</td>
                    </tr>
                    <?php 
					$total_debit += $amount ?>
				<?php 
				
				} 
				$vatable = (($total) / (1 + ($vat/100)));
				$vat_amount = $vatable * ($vat/100);
				$tax_amount = $vatable * ($wtax/100);
				$cash_amount = $total - $chargable_amount - $retention_amount - $rmy_amount - $tax_amount;
				
				$total_vat_amount += $vat_amount;
				$total_wtax_amount += $tax_amount;
				$total_cash_amount += $cash_amount;
				
				?>
                
                <?php if($retention_amount > 0){ ?>
                <tr>
                    <td><?=$retention_acode?></td>
                    <td><?=$retention_account?></td>
                    <td>&nbsp;</td>
                    <td><?=number_format($retention_amount,2,'.',',')?></td>
            	</tr>
                <?php $total_credit += $retention_amount ?>
                <?php } ?>
                
                <?php if($chargable_amount > 0){ ?>
                <tr>
                    <td><?=$chargable_acode?></td>
                    <td><?=$chargable_account?></td>
                    <td>&nbsp;</td>
                    <td><?=number_format($chargable_amount,2,'.',',')?></td>
            	</tr>
                <?php $total_credit += $chargable_amount ?>
                <?php } ?>
                              
                <?php if($total_wtax_amount > 0){ ?>
                <tr>
                    <td><?=$wtax_acode?></td>
                    <td><?=$wtax_account?></td>
                    <td>&nbsp;</td>
                    <td><?=number_format($total_wtax_amount,2,'.',',')?></td>
            	</tr>
                <?php $total_credit += $total_wtax_amount ?>
                <?php } ?>
                
                <?php if($rmy_amount > 0){ ?>
                <tr>
                    <td><?=$rmy_acode?></td>
                    <td><?=$rmy_account?></td>
                    <td>&nbsp;</td>
                    <td><?=number_format($rmy_amount,2,'.',',')?></td>
            	</tr>
                <?php $total_credit += $rmy_amount ?>
                <?php } ?>
                
                <?php if($total_cash_amount > 0){ ?>
                <tr>
                    <td><?=$cash_acode?></td>
                    <td><?=$cash_account?></td>
                    <td>&nbsp;</td>
                    <td><?=number_format($total_cash_amount,2,'.',',')?></td>
            	</tr>
                <?php $total_credit += $total_cash_amount ?>
                <?php } ?>
                <?php else: $display_accounts = 1; echo "<tr><td colspan='4'>Please see attached page for reference.</td></tr>"; endif; ?>
            <?php } ?>
            
            <tr>
            	<td style="border-top:1px solid #000;"></td>
                <td style="border-top:1px solid #000;"></td>
                <td style="border-top:1px solid #000;"><?=number_format($total_debit,2,'.',',')?></td>
                <td style="border-top:1px solid #000;"><?=number_format($total_credit,2,'.',',')?></td><!-- 11 -->
            </tr>
        </table>
		</td>
	</tr>
	
	<tr>
		<td style="border-bottom: 1px solid black;" colspan="2"><br /></td>
	</tr>
	<tr>
		<td colspan="2">
			<table width="100%" border="0">
			<tr>
				<td width="50%" style="">CHECK NO. : <span style="font-size: 12px; text-decoration: underline;"><?=$check_no?></span></td>				
				<td width="50%">RECEIVED FROM : <span style="font-size: 12px; text-decoration: underline;">MKU CONSTRUCTION AND SUPPLY</span></td>
			</tr>
			<tr>
				<td>AMOUNT : <span style="font-size: 12px; text-decoration: underline;"><?=number_format($_cash_amount,2)?></span></td>
				<td>PESOS : <span style="font-size: 12px; text-decoration: underline;"><?=$words?> ONLY</span></td>
			</tr>
			<tr>
				<td>CHECK DATE : <span style="font-size: 12px; text-decoration: underline;"><?=$cv_date?></span></td>
				<td>(PHP <?=number_format($_cash_amount,2)?>) in full payment of the amount described above.</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td><br /><div style="border-top: 1px solid black; font-size: 12px; width: 50%; text-align: center;">SIGNATURE & PRINTED NAME<div></td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2"><br /></td>
	</tr>
	<tr>
		<td colspan="2">
			<table cellspacing="0" cellpadding="5" align="center" width="80%" style="border:none; font-size: 12px; text-align:center; margin-top:30px; margin: 0 auto;" class="summary">
             <tr>
				<td>Prepared By:<p style="font-size: 12px;">
                    <input type="text" class="line_bottom" /><br><?=$options->getUserName($user_id);?></p></td>
				<!--<td>Checked By:<p style="font-size: 12px;">
                    <input type="text" class="line_bottom"  /><br>Kate Dequena</p></td>    -->            
                <td>Noted By:<p style="font-size: 12px;">
                    <input type="text" class="line_bottom"  /><br>Marian Joyce Camille Ku</p></td>
                <td>Approved By:<p style="font-size: 12px;">
                    <input type="text" class="line_bottom" /><br>Michael John S. Ku</p></td>				
            </tr>
        </table>
		</td>
	</tr>
	</table>
    
    
</div>
<div class="page-break" style="page-break-after:always;"></div>
<!--
<?php if($display_accounts){ ?>
<div>
	<table style="width:80%; border-collapse:collapse;" class="entry">
        <tr>
            <td style="width:10%; border-bottom:1px solid #000;">Account Code</td>
            <td style="border-bottom:1px solid #000;">Account Name</td>
            <td style="width:10%; border-bottom:1px solid #000;">Debit</td>
            <td style="width:10%; border-bottom:1px solid #000;">Credit</td>
        </tr>

	<?php
	$result = mysql_query("select gchart,acode,amount,project_id from cv_detail as d, gchart as g where d.gchart_id = g.gchart_id and cv_header_id = '$cv_header_id'") or die(mysql_error());
	$total = 0;
	
    $total_vat_amount  = 0;
    $total_wtax_amount = 0;
    $total_cash_amount = 0;
    $total_debit       = 0;
    $total_credit      = 0;
	
	while($r=mysql_fetch_assoc($result)){
		$amount = $r['amount'];
		$total += $r['amount'];
		$project_name = $options->getAttribute("projects","project_id",$r['project_id'],"project_name");
		
		 if($amount != 0){ 
		 $tax_amount = $vatable * ($wtax/100);
		 
	?>
		<tr>
			<td><?=$r['acode']?></td>
			<td><?=$r['gchart']?> | <?=$project_name?></td>
			<td><?=number_format($amount,2,'.',',')?></td>
			<td>&nbsp;</td>
		</tr>
		<?php $total_debit += $amount ?>
		<?php } ?>
	<?php } ?>
    <?php
	
    $vatable = (($total) / (1 + ($vat/100)));
    $vat_amount = $vatable * ($vat/100);
    $tax_amount = $vatable * ($wtax/100);
	
    $cash_amount = $total - $chargable_amount - $retention_amount - $rmy_amount;
    $total_vat_amount += $vat_amount;
    $total_wtax_amount += $tax_amount;
    $total_cash_amount += $cash_amount;
    ?>
    
    <?php if($retention_amount > 0){ ?>
    <tr>
        <td><?=$retention_acode?></td>
        <td><?=$retention_account?></td>
        <td>&nbsp;</td>
        <td><?=number_format($retention_amount,2,'.',',')?></td>
    </tr>
    <?php $total_credit += $retention_amount ?>
    <?php } ?>
    
    <?php if($chargable_amount > 0){ ?>
    <tr>
        <td><?=$chargable_acode?></td>
        <td><?=$chargable_account?></td>
        <td>&nbsp;</td>
        <td><?=number_format($chargable_amount,2,'.',',')?></td>
    </tr>
    <?php $total_credit += $chargable_amount ?>
    <?php } ?>
    
    
    <?php if($total_wtax_amount > 0){ ?>
    <tr>
        <td><?=$wtax_acode?></td>
        <td><?=$wtax_account?></td>
        <td>&nbsp;</td>
        <td><?=number_format($total_wtax_amount,2,'.',',')?></td>
    </tr>
    <?php $total_credit += $total_wtax_amount ?>
    <?php } ?>
    
    <?php if($rmy_amount > 0){ ?>
    <tr>
        <td><?=$rmy_acode?></td>
        <td><?=$rmy_account?></td>
        <td>&nbsp;</td>
        <td><?=number_format($rmy_amount,2,'.',',')?></td>
    </tr>
    <?php $total_credit += $rmy_amount ?>
    <?php } ?>
    
    
    <?php if($total_cash_amount > 0){ ?>
    <tr>
        <td><?=$cash_acode?></td>
        <td><?=$cash_account?></td>
        <td>&nbsp;</td>
        <td><?=number_format($total_cash_amount,2,'.',',')?></td>
    </tr>
    <?php $total_credit += $total_cash_amount ?>
    <?php } ?>
	
    <tr>
        <td style="border-top:1px solid #000;"></td>
        <td style="border-top:1px solid #000;"></td>
        <td style="border-top:1px solid #000;"><?=number_format($total_debit,2,'.',',')?></td>
        <td style="border-top:1px solid #000;"><?=number_format($total_credit,2,'.',',')?></td><!-- 12 -->
    </tr>
	</table>
</div>
<div class="page-break" style="page-break-after:always;"></div>
<?php } ?>
-->
</body>
</html>
