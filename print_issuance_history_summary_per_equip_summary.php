<?php
require_once('my_Classes/options.class.php');
include_once("conf/ucs.conf.php");

$options=new options();	
$from_date				= $_REQUEST['from_date'];
$to_date				= $_REQUEST['to_date'];
$stock_id				= $_REQUEST['stock_id'];

$project_id           = $_REQUEST['project_id'];

$driverID				= $_REQUEST['driverID'];
$equipment_id			= $_REQUEST['equipment_id'];
$work_category_id		= $_REQUEST['work_category_id'];
$sub_work_category_id	= $_REQUEST['sub_work_category_id'];

$fvs = $_REQUEST['fvs']; #PASUNOD

function getEquipmentsCategories(){
	$result = mysql_query("
		select * from equipment_categories order by eq_cat_name asc
	") or die(mysql_error());	
	
	$a = array();
	while($r = mysql_fetch_assoc($result)){
		$a[] = $r;	
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
            <?php
			if($stock_id){
				echo $options->getAttribute("productmaster",'stock_id',$stock_id,'stock')."<br>";
			}else{
				echo "All Items <br>";	
			}
            ?>
            From <?=date("m/d/Y",strtotime($from_date))?> to <?=date("m/d/Y",strtotime($to_date))?>
        </div>           
        
        <?php foreach(getEquipmentsCategories() as $categ){ ?>
        <div class="content" style="">
            <?php
			$query="
				select
					sum(quantity) as quantity,
					sum(amount) as amount,
					equipment_id,
					eq_name,
					eqModel
				from
					issuance_header as h, issuance_detail as d, productmaster as p, equipment as e
				where
					h.issuance_header_id = d.issuance_header_id
				and
					d.stock_id = p.stock_id
				and
					d.equipment_id = e.eqID
				and
					e.eq_catID = '$categ[eq_catID]'
			";
			if(!$fvs){
			$query .="
				and
					h.status != 'C'
			";
			}
			$query.="
				and
					h.date between '$from_date' and '$to_date'
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
			
			if($project_id){
					$query.="
						and
							h.project_id = '$project_id'
					";	
			}
			/*$query.="
				order by
					h.date asc, h.issuance_header_id  asc, _reference asc
			";*/
			$query.="
				group by equipment_id
			";
			
			$result = mysql_query($query) or die(mysql_error());
			if(mysql_num_rows($result) <= 0){
				continue;	
			}
            ?>
        	<table cellpadding="3">
            	<caption style="text-align:left; font-weight:bold;"><?=$categ['eq_cat_name']?></caption>
            	<tr>
                    <th style="text-align:left;">EQPT</th>
                    <th style="width:10%; text-align:right;">QTY</th>
                    <th style="width:10%; text-align:right;">AMOUNT</th>
                </tr>	
                
             	<?php
					$total_amount = 0;
					$total_quantity = 0;
					while($r=mysql_fetch_assoc($result)){
						$total_amount += $r['amount'];
						$total_quantity += $r['quantity'];
						
						$account = (!empty($r['account_id'])) ? "(". $options->getAttribute('account','account_id',$r['account_id'],'account') . ")" : "";
						$eq = ($r['equipment_id']) ? $options->getAttribute('equipment','eqID',$r['equipment_id'],'eq_name') : "";
				?>	
                        <tr>
                            <td><?=$r['eq_name']?></td>
                            <td style="text-align:right;"><?=number_format(($r['status'] == "C") ? 0 : $r['quantity'],4,'.',',')?></td>                       
                            <td style="text-align:right;"><?=number_format(($r['status'] == "C") ? 0 : $r['amount'],4,'.',',')?></td>   
                      	</tr>
				<?php } ?>
                <tr>
                	<td colspan="1" style="border-top:1px solid #000;"></td>                  
                    <td style="text-align:right; border-top:1px solid #000; font-weight:bold;"><span style="border-bottom:3px double #000;"><?=number_format($total_quantity,2,'.',',')?></span></td>       
                    <td style="text-align:right; border-top:1px solid #000; font-weight:bold;"><span style="border-bottom:3px double #000;"><?=number_format($total_amount,2,'.',',')?></span></td>       
                </tr>
            </table>
        </div><!--End of content-->
        <?php } ?>
    </div><!--End of Form-->
</div>
</body>
</html>