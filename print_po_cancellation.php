<?php
	require_once('my_Classes/options.class.php');
	require_once('my_Classes/numbertowords.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$n =new NumToWords();
	
	$po_header_id		= $_REQUEST[id];
	$cancellation_id	= $_REQUEST[cancellation_id];

	$user = $_SESSION['userID'];
	
	function getUser($user){
		$sql = mysql_query("Select * from admin_access where userID = '$user'") or die (mysql_error());
		$r = mysql_fetch_assoc($sql);
		
		$lname = $r['user_lname'];
		$mname = $r['user_mname'];
		$fname = $r['user_fname'];
		
		$fullname = $lname.', '.$fname.' '.$mname;
		
		return $fullname;
	}
	
	
	function getJust($cancellation_id){
		$sql = mysql_query("Select * from po_cancellation where cancellation_id = '$cancellation_id' and status != 'C'") or die (mysql_errno());
		$r = mysql_fetch_assoc($sql);
		
		return $r['justification'];
	}
	
	function getCoDate($cancellation_id){
		$sql = mysql_query("Select * from po_cancellation where cancellation_id = '$cancellation_id'") or die (mysql_errno());
		$r = mysql_fetch_assoc($sql);
		
		return $r['date'];
	}
	
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
    $description          = $r['description'];
    $status               = $r['status'];
    $date                 = $r['date'];
    $po_type              = $r['po_type'];
    $user_id              = $r['user_id'];
    $supplier_id          = $r['supplier_id'];
    $supplier             = $options->attr_Supplier($supplier_id,'account');
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
	
	function getSubDetails($po_header_id){
		$query="
				select
				*
				from 
				spo_detail as sub,
				sub_spo_detail as ss
				where
				sub.po_header_id = '$po_header_id' and
				sub.spo_detail_id = ss.spo_detail_id  ";
		$result = mysql_query($query) or die(mysql_error());
		$a = array();
		while($r = mysql_fetch_assoc($result)){
			$tmp = array();	
			$tmp['quantity'] 		= $r['quantity'];
            $tmp['unit']			= $r['unit'];
            $tmp['cost']			= $r['unit_cost'];
            $tmp['amount']			= $r['amount'];
            $tmp['details']			= $r['sub_description'];
			$tmp['chargables']		= $r['chargables'];
			$tmp['person']			= $r['person'];
			$a[] = $tmp;
		}
		return $a;
	}	
	
	if($po_type == 'M'){
		$po = getPODetails($po_header_id);
	}else if($po_type == 'S' or $po_type == 'L'){
		$po = getSubDetails($po_header_id);
	}
	


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ORDER SHEET</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<link rel="stylesheet" type="text/css" href="css/print_po.css"/>
<style type="text/css">	
	*{ font-size:14px; }
    .table-po{ border-collapse: collapse; }

    .table-po thead{ display:table-header-group; }

    
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
		
	
    <table class="table-po" border=0 width="100%">
        <thead>
            <tr>
                <td colspan="9">
                    <?php require("form_heading.php"); ?>
                    <div style="text-align:center; font-weight: bold; font-size: 20px;">
                        CANCELLATION ORDER
                    </div>
					<br />
                    <div class="header clearfix" style="">
                        <table style="width:60%; float:left;">
                            <tr>
                                <td width="24%">Supplier :</td>
                                <td width="76%" style="border-bottom:1px solid #000;"><?=$supplier?></td>
                            </tr>
                            <tr>
                                <td width="24%">Project :</td>
                                <td width="76%" style="border-bottom:1px solid #000;"><?=$project_name?></td>
                            </tr>
                            <tr>
                                <td>Scope of Work:</td>
                                <td style="border-bottom:1px solid #000;"><?=$scope_of_work." | ".$work_category." | ".$sub_work_category?></td>
                            </tr>
							
                            <tr>
                                <td style="vertical-align: top;">Justification / Reason for Cancellation</td>
                                <td style="border-bottom:1px solid #000; text-align: justify;"><?=getJust($cancellation_id)?></td>
                            </tr>							
                        </table>
                        
                        <table style="width:30%; float:right; margin-right:20px;">
                            <tr>
                                
                                <td width="40%">C.O. # :</td>
                                <td width="62%" style="border-bottom:1px solid #000;"><b style="font-size:15px;"><?=str_pad($cancellation_id,7,0,STR_PAD_LEFT)?></b></td>
                            </tr>
                            <tr>                  
                              <td>Cancellation Date :</td>
                               <td style="border-bottom:1px solid #000;"><?=date("F j, Y",strtotime(getCoDate($cancellation_id)))?></td>                             
                            </tr>
                             <tr>                  
                              <td>PO/Subcon PO Date :</td>
							  <td style="border-bottom:1px solid #000;"><?=date("F j, Y",strtotime($date))?></td>
                            </tr>                          
                            <tr>                  
                              <td>
							  <?php if($po_type == 'S' or $po_type == 'L'){ ?>
									SubCon P.O. #:
							  <?php }else{ ?>
									P.O. #
							  <?php } ?>
							 
							  </td>
							  <td width="62%" style="border-bottom:1px solid #000;"><b style="font-size:15px;"><?=$po_header_id_pad?></b></td>
                            </tr>

                        </table>
                    </div><!--End of header-->
					<br />
                </td>
            </tr>
            <tr>
                <td style="border:1px solid #000;">DESCRIPTION</td>
                <td style="width:10%; border:1px solid #000;">EQUIPT</td>
                <td style="width:10%; border:1px solid #000;">PERSON</td>
                <td style="width:10%; border:1px solid #000;">QTY</td>
                <td style="width:10%; border:1px solid #000;">UOM</td>
                <td style="width:10%; border:1px solid #000;">U.PRICE</td>
                <td style="width:10%; border:1px solid #000;">DISCOUNT</td>
                <td style="width:10%; border:1px solid #000;">AMOUNT</td>
            </tr>
        </thead>

        <tbody>
            <?php
            if( count($po) ){
            foreach( $po as $r ){                
                $quantity       = $r['quantity'];
                $stock_id       = $r['stock_id'];
                $stock          = $r['stock'];
                $stockcode      = $r['stockcode'];
                $unit           = $r['unit'];
                $cost           = $r['cost'];
                $amount         = $r['amount'];
                $details        = $r['details'];
                $chargables     = $r['chargables'];
                $person         = $r['person'];
                $barcode        = $r['barcode'];                    
                
                $total_quantity += $quantity;
                $total_items++;
                $totalamount    += $amount;            
            ?>
                <tr>
                    <td><?=$stock?> <?php if($details){ echo "($details)"; }?> </td>
                    <td><?=$chargables?></td>
                    <td><?=$person?></td>
                    <td class="align-right" style="text-align:right;"><?=$quantity?></td>
                    <td><?=$unit?></td>
                    <td class="align-right" style="text-align:right;"><?=number_format($cost,4,'.',',')?></td>
                    <td class="align-right" style="text-align:right;"><?=number_format($r['discount']*$quantity,4,'.',',')?></td>
                    <td class="align-right" style="text-align:right; "><?=number_format($amount,4,'.',',')?></td>
                </tr>
            <?php 
                }
            } 
            ?>

            <?php
            if( !empty( $remarks ) ){
                echo '<tr>';        
                echo '<td style="border-right: 1px solid #000000;" >'.$remarks.'</td>';
                echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';
                echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';    
                echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';
                echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';    
                echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';    
                echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';    
                echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';    
                echo '</tr>';
            } /*end if*/

            if($discount_amount > 0){
                echo '<tr>';        
                echo '<td style="border-right: 1px solid #000000; font-weight:bold;">Less: Discount</td>';
                echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';
                echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';    
                echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';
                echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';    
                echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';   
                echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';    
                echo '<td style="border-right: 1px solid #000000; font-weight:bold; text-align:right;" align=right>'.number_format(0-$discount_amount,4,'.',',').'</td>';   
                echo '</tr>';
            }

            echo '<tr>';        
            echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';
            echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';
            echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';
            echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';
            echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';    
            echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';    
            echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';  
            echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';     
            echo '</tr>';

            echo '<tr>';            
            echo '<td style="border-right: 1px solid #000000;"  >&nbsp;</td>';
            echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';
            echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';    
            echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';
            echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';    
            echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';    
            echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';   
            echo '<td style="border-right: 1px solid #000000;" align=right>&nbsp;</td>';    
            echo '</tr>';
                
            ?>

        </tbody>
        <tfoot>
            <?php
            $totalamount -= $discount_amount;
        
            $vatable_amount = $totalamount / ( 1 + ($vat / 100));
            $vatable_display = ($vat > 0)?$vatable_amount:0;
            $vat_amount = $vatable_amount * ( $vat / 100 );
            $wtax_amount = $vatable_amount * ($wtax / 100);
            $total_net_amount = ($vatable_amount + $vat_amount) - $wtax_amount;

            $total_net_amount = number_format($total_net_amount,2,'.','');
            $n->setNumber($total_net_amount);
            $words =  $n->num_words().$n->appendDecimal();
            $vat = $totalamount - ($totalamount / 1.12)
            ?>

            <tr class="summary">
				<td style="border-top: 1px solid #000;"></td>
                <td style="border-top:1px solid #000; border-left: none;border-right:none; font-weight:bold;" ></td>
                <td colspan="4" style=" width:20%; border-top:1px solid #000; border-bottom:1px solid #000; border-right:none; border-left:1px solid #000;"><strong>SUBTOTAL :</strong></td>
                <td style="width:10%; border-left:none; border-top:1px solid #000; border-bottom:1px solid #000; text-align:right; "><strong>PESO</strong></td>
                <td class="align-right" style="width:10%; border:1px solid #000; font-weight:bold; text-align:right;"><?=number_format($totalamount,2,'.',',')?></td>
            </tr>
            <tr class="summary">
                <td class="no-border align-right" style="border:none;" colspan="2" ></td>
                <td colspan="4" style="border-top:1px solid #000; border-bottom:1px solid #000; border-right:none;"><strong>VATABLE:</strong></td>
                <td style="border-left:none; border-top:1px solid #000; border-bottom:1px solid #000; text-align:right; "><strong>PESO</strong></td>
                <td class="align-right" style="border:1px solid #000; font-weight:bold; text-align:right;"><?=number_format($vatable_display,2,'.',',')?></td>
            </tr>
            <tr class="summary">
                <td class="no-border align-right" style="border:none;" colspan="2"></td>
                <td colspan="4" style="border-top:1px solid #000; border-bottom:1px solid #000; border-right:none;"><strong>VAT:</strong></td>
                <td style="border-left:none; border-top:1px solid #000; border-bottom:1px solid #000; text-align:right; "><strong>PESO</strong></td>
                <td class="align-right" style="border:1px solid #000; font-weight:bold; text-align:right;"><?=number_format($vat_amount,2,'.',',')?></td>
            </tr>
            <tr class="summary">
                <td class="no-border align-right" style="border:none;" colspan="2"></td>
                <td colspan="4" style="border-top:1px solid #000; border-bottom:1px solid #000; border-right:none;"><strong>WTAX :</strong></td>
                <td style="border-left:none; border-top:1px solid #000; border-bottom:1px solid #000; text-align:right; "><strong>PESO</strong></td>
                <td class="align-right" style="border:1px solid #000; font-weight:bold; text-align:right;"><?=number_format($wtax_amount,2,'.',',')?></td>
            </tr>
            <tr class="summary">
                <td class="no-border align-right" style="border:none;" colspan="2"></td>
                <td colspan="4" style="border-top:1px solid #000; border-bottom:1px solid #000; border-right:none;  border-bottom:1px solid #000;"><strong>NET AMOUNT :</strong></td>
                <td style="border-left:none; border-top:1px solid #000; border-bottom:1px solid #000;  text-align:right; border-bottom:1px solid #000;"><strong>PESO</strong></td>
                <td class="align-right" style="border:1px solid #000; font-weight:bold; text-align:right; border-bottom:1px solid #000;"><?=number_format($total_net_amount,2,'.',',')?></td>
            </tr>

        </tfoot>
    </table>
    
    <br />
    <table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:10px;" class="summary">
        <tr>
            <td style="text-align: center;">Prepared By:
			<p>
                <input type="text" style="border: none; border-bottom: 1px solid black;" value="<?=getUser($user)?>" /><br>Purchasing Staff <br>
                <span style='font-size:10px;'><?=$aTrans['datetime_encoded']?></span>
            </p>
			</td>
            <td style="text-align: center;">Checked By:<p>
                <input type="text" class="line_bottom" /><br>Purchasing Manager</p></td>
            <td style="text-align: center;">Approved By:<p>
                <input type="text" class="line_bottom" /><br>President / G.M.</p></td>
        </tr>
    </table>
    <div class="divFooter" style="font-family: calibri; font-size: 16px;">
        F-PUR-009<br>
        Rev. 0 04/20/16
    </div>
<div class="page-break"></div>
</body>
</html>
