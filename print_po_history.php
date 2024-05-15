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
	$approved			= $_REQUEST['approved'];
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
        	<table cellpadding="6">
            	<tr>
            		<th>#</th>
                	<th>DATE</th>
                    <th>PO#</th>
					<th>SCOPE OF WORK</th>
                    <th>SUPPLIER</th>
                    <th>ITEM</th>
                    <th>QTY</th>
                    <th>UNIT</th>
                    <th>PRICE</th>
                    <th>AMOUNT</th>
                    <th>STATUS</th>
                </tr>	
                
             	<?php
					$query="
						select
							h.date,d.quantity,d.amount,d.cost,h.po_header_id,account,stock,unit,h.work_category_id,h.sub_work_category_id, h.status
						from
							po_header as h, po_detail as d, productmaster as p, supplier as s
						where
							h.po_header_id = d.po_header_id
						and
							d.stock_id = p.stock_id
						and
							s.account_id = h.supplier_id";


						  if(!empty($from_date) && !empty($to_date)){
						
							$query.=" and h.date between '$from_date' and '$to_date'";
							}
					
                    if(!empty($project_id)){
                     $query.="
                        and
							h.project_id = '$project_id'";
                    }
                    
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
							h.po_header_id, h.date asc 
					";

					//echo $query;
					$result=mysql_query($query) or die(mysql_error());
					$total_quantity = 0;
					$total_amount = 0;
					$no=1;


					while($r=mysql_fetch_assoc($result)){
						$total_quantity += $r['quantity'];
						$total_amount += $r['amount'];

						if($r['status'] == 'F'){
							$status = 'Finished';
						} else if($r['status'] == 'S'){
							$status = 'Saved';
						}  else if($r['status'] == 'C'){
							$status = 'Cancelled';
						}


				?>	
                        <tr>
                        	<td><?=$no?></td>
                            <td><?=date("m/d/Y",strtotime($r['date']))?></td>
                            <td><?=str_pad($r['po_header_id'],7,0,STR_PAD_LEFT)?></td> 
							<td><?=$options->getAttribute('work_category','work_category_id',$r[work_category_id],'work').'<br/>'.$options->getAttribute('work_category','work_category_id',$r[sub_work_category_id],'work')?></td>  								
                            <td><?=$r['account']?></td>                       
                            <td><?=$r['stock']?></td>                       
                            <td style="text-align:right;"><?=number_format($r['quantity'],4,'.',',')?></td>                       
                            <td><?=$r['unit']?></td>             
                            <td style="text-align:right;"><?=number_format($r['cost'],4,'.',',')?></td>                       
                            <td style="text-align:right;"><?=number_format($r['amount'],2,'.',',')?></td>
                            <td><?=$status?></td>                 
                      	</tr>
				<?php $no++; } ?>
                <tr>
                	<td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
					<td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td style="text-align:right; font-weight:bold; border-top:1px solid #000;"><?=number_format($total_quantity,2,'.',',')?></td>
                    <td>&nbsp;</td>
					<td>&nbsp;</td>
                    <td style="text-align:right; font-weight:bold; border-top:1px solid #000;"><?=number_format($total_amount,2,'.',',')?></td>
                </tr>
            </table>
        
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>