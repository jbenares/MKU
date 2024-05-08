<?php
	require_once('my_Classes/options.class.php');
	#require_once('my_Classes/numbertowords.class.php');
	require_once('my_Classes/numtowords.class.php');
	
	include_once("conf/ucs.conf.php");

	$options=new options();	
	
	#$c=new NumToWords();
	$convert = new num2words();
	
	$cv_header_id=$_REQUEST[id];

	
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
	$cv_header_id_pad		= str_pad($cv_header_id,7,0,STR_PAD_LEFT);
	$cv_date				= date("m/d/Y",strtotime($r['cv_date']));
	$check_date				= date("m/d/Y",strtotime($r['check_date']));
	$check_no				= $r['check_no'];
	$supplier_id 			= $r['supplier_id'];
	$supplier				= $options->getAttribute('supplier','account_id',$supplier_id,'account');
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
	
	$materials_gchart_id  	= 703;
	$materials_account		= $options->getAttribute('gchart','gchart_id',$materials_gchart_id,'gchart');
	$materials_acode		= $options->getAttribute('gchart','gchart_id',$materials_gchart_id,'acode');
	
	$vat_account			= $options->getAttribute('gchart','gchart_id',$vat_gchart_id,'gchart');
	$vat_acode				= $options->getAttribute('gchart','gchart_id',$vat_gchart_id,'acode');
	
	$wtax_account			= $options->getAttribute('gchart','gchart_id',$wtax_gchart_id,'gchart');
	$wtax_acode				= $options->getAttribute('gchart','gchart_id',$wtax_gchart_id,'acode');
	
	
	$project_id			= $r['project_id'];
	$project_name		= $options->attr_Project($project_id,'project_name');
	$project_code		= $options->attr_Project($project_id,'project_code');
	$project_name_code	= ($project_id)?"$project_name - $project_code":"";
	
	$vat_gchart_id	= $r['vat_gchart_id'];
	
	$retention_gchart_id	= $r['retention_gchart_id'];
	$chargable_gchart_id	= $r['chargable_gchart_id'];
	$retention_amount		= $r['retention_amount'];
	$chargable_amount		= $r['chargable_amount'];
	
	
	$result = mysql_query("
		select
			sum(amount) as amount
		from
			cv_detail
		where
			cv_header_id = '$cv_header_id'
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	
	$amount = $r['amount'];
	
	$vatable = ($amount / (1 + ($vat/100)));
	$vat_amount = $vatable * ($vat/100);
	$tax_amount =  $vatable * ($wtax/100);	
	$cash_amount = $amount - $tax_amount;
	
	$convert = new num2words();
	#$cash_amount = number_format($cash_amount,2,'.','');
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
<title>ORDER SHEET</title>
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
</style>
</head>
<body>
<div class="container">	    
    <div>
    	<?=$title?>
    </div>
    
    <div style="position:absolute; top:1.6cm; left:4.3cm; line-height:20px;">
    	CV#<?=$cv_no?><br />
        <?=$cv_date?><br />
        <?=number_format($_cash_amount,2,'.',',')?><br />
        <?=$supplier?>
    </div>
    
    <div style="position:absolute; top:1.5cm; right:1cm;">
    	<br />
        <br />
        <?=$check_date?>
    </div>
    
    <div style="position:absolute; top:7.80cm; left:4.2cm; line-height:20px;">
    	CV#<?=$cv_no?><br />
        <br />
        <br />
        <?=number_format($cash_amount,2,'.',',')?>
    	<div style="font-size:11px; margin-left:20px;"><?=$particulars?></div>
    </div>
    
    <div style="position:absolute; top:8.5cm; right:4cm;">
    	<?=$cv_date?>
    </div>
    
    <div style="position:absolute; top:12.5cm; left:2cm; width:100%; font-size:10px;">
    	<table style="width:80%; border-collapse:collapse;" class="entry">
        	<tr>
            	<td style="width:10%; border-bottom:1px solid #000;">A/C Code</td>
                <td style="border-bottom:1px solid #000;">A/C Name</td>
                <td style="width:10%; border-bottom:1px solid #000;">Debit</td>
                <td style="width:10%; border-bottom:1px solid #000;">Credit</td>
            </tr>
            <?php if($type == "M") { ?>
				<?php
                
                $total_vat_amount = 0;
                $total_wtax_amount = 0;
                $total_cash_amount = 0;
				$total_debit = 0;
				$total_credit = 0;
				
				$vatable = ($amount / (1 + ($vat/100)));
				$vat_amount = $vatable * ($vat/100);
				$tax_amount =  $vatable * ($wtax/100);
				$cash_amount = $amount - $tax_amount;
				
				$total_vat_amount += $vat_amount;
				$total_wtax_amount += $tax_amount;
				$total_cash_amount += $cash_amount;
                ?>
                <?php if($total_vat_amount > 0){ ?>
                <!--<tr>
                    <td><?=$materials_acode?></td>
                    <td><?=$materials_account?></td>
                    <td>&nbsp;</td>
                    <td><?=number_format($total_vat_amount,2,'.',',')?></td>
                </tr> -->
                <?php #$total_credit += $total_vat_amount ?>
                <?php } ?>
                
                <?php if($total_vat_amount > 0){ ?>
                <!--<tr>
                    <td><?=$vat_acode?></td>
                    <td><?=$vat_account?></td>
                    <td><?=number_format($total_vat_amount,2,'.',',')?></td>
                    <td>&nbsp;</td>
                </tr> -->
                <?php #$total_debit += $total_vat_amount ?>
                <?php } ?>
                
                <?php if($amount > 0){ ?>
                <tr>
                    <td><?=$ap_acode?></td>
                    <td><?=$ap_account?></td>
                    <td><?=number_format($amount,2,'.',',')?></td>
                    <td>&nbsp;</td>
                </tr>
                <?php $total_debit += $amount ?>
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
                
                <?php if($total_cash_amount > 0){ ?>
                <tr>
                    <td><?=$cash_acode?></td>
                    <td><?=$cash_account?></td>
                    <td>&nbsp;</td>
                    <td><?=number_format($total_cash_amount,2,'.',',')?></td>
                </tr>
                <?php $total_credit += $total_cash_amount ?>
                <?php } ?>
            
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
					
				
					$vatable = ($amount / (1 + ($vat/100)));
					$vat_amount = $vatable * ($vat/100);
					$tax_amount = $vatable * ($wtax/100);
					$cash_amount = $amount - $tax_amount;
					
					$total_vat_amount += $vat_amount;
					$total_wtax_amount += $tax_amount;
					$total_cash_amount += $cash_amount;
					?>
                    <?php if($vatable > 0){ ?>
                    <tr>
                        <td><?=$r['acode']?></td>
                        <td><?=$r['gchart']?> | <?=$project_name?></td>
                        <td><?=number_format($vatable,2,'.',',')?></td>
                        <td>&nbsp;</td>
                    </tr>
                    <?php $total_debit += $vatable ?>
                    <?php } ?>
            	<?php } ?>
                <?php if($total_vat_amount > 0){ ?>
                <tr>
                    <td><?=$vat_acode?></td>
                    <td><?=$vat_account?></td>
                    <td><?=number_format($total_vat_amount,2,'.',',')?></td>
                    <td>&nbsp;</td>
            	</tr>
                <?php $total_debit += $total_vat_amount ?>
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
                <td style="border-top:1px solid #000;"><?=number_format($total_credit,2,'.',',')?></td>
            </tr>
        </table>
        
        <table cellspacing="0" cellpadding="5" align="center" width="80%" style="border:none; text-align:center; margin-top:30px;" class="summary">
            <tr>
                <td>Prepared By:<p>
                    <input type="text" class="line_bottom" /><br><?=$options->getUserName($user_id);?></p></td>
                <td>Checked By:<p>
                    <input type="text" class="line_bottom" /><br>S. Lareza</p></td>
                <td>Approved By:<p>
                    <input type="text" class="line_bottom" /><br>RJR/MMY</p></td>
            </tr>
        </table>
    </div>
    
    <div style="position:absolute; top:20.3cm; left:3.7cm; width:90%; line-height:20px;font-weight:bold; font-size:12px;" >
    	<?=$supplier?> <br /><br />
        <?=$words?> ONLY<br />
        CV#<?=$cv_no?>
    </div>
    
    <div style="position:absolute; top:19.7cm; right:1.35cm; line-height:25px;font-weight:bold; font-size:12px;">
    	<?=$check_date?>
    	<br />
    	<?=number_format($_cash_amount,2,'.',',')?>
    </div>
    
    
</div>
<div class="page-break" style="page-break-after:always;"></div>
<?php if($display_accounts){ ?>
<div>
	<table style="width:80%; border-collapse:collapse;" class="entry">
        <tr>
            <td style="width:10%; border-bottom:1px solid #000;">A/C Code</td>
            <td style="border-bottom:1px solid #000;">A/C Name</td>
            <td style="width:10%; border-bottom:1px solid #000;">Debit</td>
            <td style="width:10%; border-bottom:1px solid #000;">Credit</td>
        </tr>

	<?php
	$result = mysql_query("select gchart,acode,amount,project_id from cv_detail as d, gchart as g where d.gchart_id = g.gchart_id and cv_header_id = '$cv_header_id'") or die(mysql_error());
	$total = 0;
	
	$total_vat_amount = 0;
	$total_wtax_amount = 0;
	$total_cash_amount = 0;
	$total_debit = 0;
	$total_credit = 0;
	
	while($r=mysql_fetch_assoc($result)){
		$amount = $r['amount'];
		$total += $r['amount'];
		$project_name = $options->getAttribute("projects","project_id",$r['project_id'],"project_name");
		
	
		$vatable = ($amount / (1 + ($vat/100)));
		$vat_amount = $vatable * ($vat/100);
		$tax_amount = ( $vatable / (1 + ($wtax/100)) ) * ($wtax/100);
		$cash_amount = $amount - $tax_amount;
		
		$total_vat_amount += $vat_amount;
		$total_wtax_amount += $tax_amount;
		$total_cash_amount += $cash_amount;
		?>
		<?php if($vatable > 0){ ?>
		<tr>
			<td><?=$r['acode']?></td>
			<td><?=$r['gchart']?> | <?=$project_name?></td>
			<td><?=number_format($vatable,2,'.',',')?></td>
			<td>&nbsp;</td>
		</tr>
		<?php $total_debit += $vatable ?>
		<?php } ?>
	<?php } ?>
	<?php if($total_vat_amount > 0){ ?>
	<tr>
		<td><?=$vat_acode?></td>
		<td><?=$vat_account?></td>
		<td><?=number_format($total_vat_amount,2,'.',',')?></td>
		<td>&nbsp;</td>
	</tr>
	<?php $total_debit += $total_vat_amount ?>
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
        <td style="border-top:1px solid #000;"><?=number_format($total_credit,2,'.',',')?></td>
    </tr>
	</table>
</div>
<div class="page-break" style="page-break-after:always;"></div>
<?php } ?>

</body>
</html>
