<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	$from_date		= $_REQUEST['from_date'];
	$to_date		= $_REQUEST['to_date'];
	$project_id		= $_REQUEST['project_id'];
	$project = $options->getAttribute("projects","project_id",$project_id,"project_name");
	$categ_id		= $_REQUEST['categ_id'];
	$stock_id		= $_REQUEST['stock_id'];
	$work_category_id		= $_REQUEST['work_category_id'];
	$sub_work_category_id	= $_REQUEST['sub_work_category_id'];
	$approved = $_REQUEST['approved'];
	$po_status			= $_REQUEST['po_status'];
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
        	PO HISTORY REPORT <br />
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
			foreach($projects as $project_id){
            ?>       
            	 <?php
					$query="
						select
							h.date,d.quantity,d.amount,d.cost,h.po_header_id,account,stock,unit,h.work_category_id,h.sub_work_category_id
						from
							po_header as h, po_detail as d, productmaster as p, supplier as s
						where
							h.po_header_id = d.po_header_id
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
					";
					if($approved){
					$query.="
						and
							approval_status = 'A'
					";	
					}
					
					if($stock_id){
					$query.="
						and
							d.stock_id= '$stock_id'
					";	
					}
					
					if($categ_id){
					$query.="
						and
							p.categ_id1 = '$categ_id'
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
					
					if(!empty($po_status)){
					$query.="
						and h.status = '$po_status'
					";	
					}
					
					$query.="
						order by
							h.date asc, h.po_header_id asc
					";
					$result=mysql_query($query) or die(mysql_error());
					
					if(mysql_num_rows($result) <= 0 ){ continue; }
				?>
             
                <table cellpadding="6">
                	<caption style="text-align:left; font-weight:bold;"><?=$options->getAttribute("projects","project_id",$project_id,"project_name");?></caption>
                    <tr>
                        <th style="width:5%;">DATE</th>
                        <th style="width:5%;">PO#</th>
						<th style="width:15%;">SCOPE OF WORK</th>
                        <th style="width:15%;">SUPPLIER</th>
                        <th>ITEM</th>
                        <th style="width:10%;">QTY</th>
                        <th style="width:10%;">UNIT</th>
                        <th style="width:10%;">PRICE</th>
                        <th style="width:10%;">AMOUNT</th>
                    </tr>	
                    
                   <?php
					$total_quantity = 0;
					$total_amount = 0;
					while($r=mysql_fetch_assoc($result)){
						$total_quantity += $r['quantity'];
						$total_amount += $r['amount'];
                    ?>	
                    <tr>
                        <td><?=date("m/d/Y",strtotime($r['date']))?></td>
                        <td><?=str_pad($r['po_header_id'],7,0,STR_PAD_LEFT)?></td> 
						<td><?=$options->getAttribute('work_category','work_category_id',$r[work_category_id],'work').'<br/>'.$options->getAttribute('work_category','work_category_id',$r[sub_work_category_id],'work')?></td> 						
                        <td nowrap="nowrap"><?=$r['account']?></td>                       
                        <td><?=$r['stock']?></td>                       
                        <td style="text-align:right;"><?=number_format($r['quantity'],4,'.',',')?></td>                       
                        <td><?=$r['unit']?></td>             
                        <td style="text-align:right;"><?=number_format($r['cost'],4,'.',',')?></td>                       
                        <td style="text-align:right;"><?=number_format($r['amount'],2,'.',',')?></td>                       
                    </tr>
                    <?php } ?>
                    <?php $grand_total_amount += $total_amount ?>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td style="text-align:right; font-weight:bold; border-bottom:1px solid #000;"><?=number_format($total_quantity,2,'.',',')?></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td style="text-align:right; font-weight:bold; border-bottom:1px solid #000;"><?=number_format($total_amount,2,'.',',')?></td>
                    </tr>
                </table>
            <?php } ?>
            <table style="margin-top:5px;">
                <tr>
                    <td style="text-align:right; font-weight:bolder; border-top:1px solid #000;" >GRAND TOTAL :</td>
                    <td style="width:10%; text-align:right; font-weight:bolder; border-top:1px solid #000; border-bottom:4px double #000;"><?=number_format($grand_total_amount,2,'.',',')?></td>
                </tr>
            </table>
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>