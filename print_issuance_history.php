<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options              = new options();	
	$from_date            = $_REQUEST['from_date'];
	$to_date              = $_REQUEST['to_date'];
	$project_id           = $_REQUEST['project_id'];
	$project              = $options->getAttribute("projects","project_id",$project_id,"project_name");
	
	$stock_id             = $_REQUEST['stock_id'];
	
	$driverID             = $_REQUEST['driverID'];
	$equipment_id         = $_REQUEST['equipment_id'];
	$work_category_id     = $_REQUEST['work_category_id'];
	$sub_work_category_id = $_REQUEST['sub_work_category_id'];
	$account_id           = $_REQUEST['account_id'];
	$categ_id1	    	= $_REQUEST['categ_id1'];
	$categ_id2	    	= $_REQUEST['categ_id2'];
	#echo "DRIVER ID : $driverID ; $equipment_id";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>JOB ORDER</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<style type="text/css">
 a{
	color:#000;
	text-decoration:none; 
 }
 a:hover{
	text-decoration:underline; 
 }
</style>

<link rel="stylesheet" type="text/css" href="css/print.css"/>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	
     	<div style="font-weight:bolder;">
        	ISSUANCE HISTORY REPORT <br />
            <?=$project?> <br />
            From <?=date("m/d/Y",strtotime($from_date))?> to <?=date("m/d/Y",strtotime($to_date))?>
        </div>           
        <div class="content" style="">
        	<table cellpadding="6">
            	<tr>
                	<th style="width:5%;">DATE</th>
                	<th style="width:5%;">TIME ENCODED</th>
                    <th style="width:5%;">RIS#</th>
                    <th style="width:15%;">SCOPE OF WORK</th>
                    <th style="width:5%;">REFERENCE</th>
                    <th>ITEM</th>
                    <th>EQPT</th>
                    <th>DRIVER</th>
                    <th style="width:5%;">QTY</th>
                    <th style="width:5%;">UNIT</th>
                    
                    <th style="width:5%;">KG/PC</th>
                    <th style="width:5%;">TOTAL KG</th>
                    
                    <th style="width:5%;">PRICE</th>
                    <th style="width:5%;">AMOUNT</th>
                    <th>CHARGED TO</th>
                </tr>	
                
             	<?php
					$query="
						select
							*
						from
							issuance_header as h, issuance_detail as d, productmaster as p
						where
							h.issuance_header_id = d.issuance_header_id
						and
							d.stock_id = p.stock_id
						and
							h.status != 'C'
						and
							h.date between '$from_date' and '$to_date'
						and
							h.project_id = '$project_id'
					";
					
					if(!empty($driverID)){
					$query.="
						and 
							d.driverID = '$driverID'
					";	
					}
					
					if(!empty($equipment_id)){
					$query.="
						and 
							d.equipment_id = '$equipment_id'
					";	
					}
					
					if(!empty($stock_id)){
					$query.="
						and 
							d.stock_id = '$stock_id'
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
					
					if($account_id){
					$query.="
						and
							d.account_id = '$account_id'
					";	
					}
					if($categ_id1){
					$query.="
						and
							p.categ_id1 = '$categ_id1'
					";	
					}
					if($categ_id2){
					$query.="
						and
							p.categ_id2 = '$categ_id2'
					";	
					}
					$query.="
						order by
							h.date asc, h.issuance_header_id asc
					";
					$result=mysql_query($query) or die(mysql_error());
					$total_quantity  = $total_amount = 0;
					$t_kg = 0;
					while($r=mysql_fetch_assoc($result)){
						
						set_time_limit(30);
						$total_quantity += $r['quantity'];
						$total_amount += $r['amount'];
						$t_kg		+= $r['quantity'] * $r['kg'];
						
						$account = (!empty($r['account_id'])) ? "(". $options->getAttribute('account','account_id',$r['account_id'],'account') . ")" : "";
						$eq = ($r['equipment_id']) ? $options->getAttribute('equipment','eqID',$r['equipment_id'],'eq_name') : "";
				?>	
                        <tr>
                            <td><?=date("m/d/Y",strtotime($r['date']))?></td>
                            <td><?=$r['encoded_datetime']?></td>
                            <td><a target="_parent" href="admin.php?view=02bb738f4e1ab460dd47&issuance_header_id=<?=$r['issuance_header_id']?>"><?=str_pad($r['issuance_header_id'],7,0,STR_PAD_LEFT)?></a></td>                       
                            <td><?=$options->getAttribute('work_category','work_category_id',$r['work_category_id'],'work')?> <?=$options->getAttribute('work_category','work_category_id',$r['sub_work_category_id'],'work')?></td>
                            <td><?=$r['_reference']?></td>                       
                            <td><?=htmlentities($r['stock'])?> <?=$account?></td>                       
                            <td><?=$eq?></td>
                            <td><?=$options->getAttribute('drivers','driverID',$r['driverID'],'driver_name')?></td>                       
                            <td style="text-align:right;"><?=$r['quantity']?></td>                       
                            <td><?=$r['unit']?></td>                       
                            
                            <td style="text-align:right;"><?=number_format($r['kg'],2)?></td>                       
                            <td style="text-align:right;"><?=number_format($r['kg'] * $r['quantity'],2)?></td>                       
                            
                            <td style="text-align:right;"><?=number_format($r['price'],2,'.',',')?></td>                       
                            <td style="text-align:right;"><?=number_format($r['amount'],2,'.',',')?></td>                       
                            <td><?=$options->getAttribute('account','account_id',$r['account_id'],'account')?></td>                       
                      	</tr>
				<?php } ?>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>                       
                    <td>&nbsp;</td>                       
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>                       
                    <td style="text-align:right;"><span style="border-bottom:1px solid #000; font-weight:bold;"><?=number_format($total_quantity,2,'.',',')?></span></td>                       
                    <td><?=$r['unit']?></td>                       
                    <td></td>                       
                    
                    <td style="text-align:right;"><span style="border-bottom:1px solid #000; font-weight:bold;"><?=number_format($t_kg,2,'.',',')?></span></td>                       
                    <td style="text-align:right;">&nbsp;</td>                       
                    
                    <td style="text-align:right;"><span style="border-bottom:1px solid #000; font-weight:bold;"><?=number_format($total_amount,2,'.',',')?></span></td>                       
                    <td>&nbsp;</td>                       
                </tr>
            </table>
        
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>