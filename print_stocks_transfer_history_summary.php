<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	$from_date		= $_REQUEST['from_date'];
	$to_date		= $_REQUEST['to_date'];
	$stock_id		= $_REQUEST['stock_id'];
	$work_category_id		= $_REQUEST['work_category_id'];
	$sub_work_category_id	= $_REQUEST['sub_work_category_id'];
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
        	STOCKS TRANSFER HISTORY REPORT <br />
            <?=$project?> <br />
            From <?=date("m/d/Y",strtotime($from_date))?> to <?=date("m/d/Y",strtotime($to_date))?>
        </div>           
        <div class="content" style="">
        	<table cellpadding="3">
            	<tr>
                	<th style="text-align:left; width:5%;">DATE</th>
                    <th style="text-align:left; width:10%;">TS #</th>
                    <th style="width:10%;">SCOPE OF WORK</th>
                    <th style="width:10%;">REFERENCE</th>
					<th style="text-align:center; width:10%;">REMARKS</th>
                    <th>PROJECT</th>
                    <th style="text-align:left;">ITEM</th>
                    <th style="text-align:right; width:10%;">QTY</th>
                    <th style="text-align:left; width:10%;">UNIT</th>
                    
                    <th style="text-align:right; width:10%;">KG/PC</th>
                    <th style="text-align:left; width:10%;">TOTAL KG</th>
                    
                    <th style="text-align:right; width:10%;">PRICE</th>
                        <th style="text-align:right; width:10%;">AMOUNT</th>
                </tr>	
                
             	<?php
					$query="
						select
							*
						from
							transfer_header as h, transfer_detail as d, productmaster as p, projects as pr
						where
							h.transfer_header_id = d.transfer_header_id
						and
							d.stock_id = p.stock_id
						and
							h.project_id = pr.project_id
						and
							h.status != 'C'
						and
							h.date between '$from_date' and '$to_date'
					";
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
					
					
					$query.="
						order by
							h.date asc, h.transfer_header_id asc
					";
					$result=mysql_query($query) or die(mysql_error());
					$total_amount = 0;
					$total_quantity = 0;
					$t_kg	= 0;
					while($r=mysql_fetch_assoc($result)){
						$total_amount += $r['amount'];
						$total_quantity += $r['quantity'];
						$t_kg 	+= $r['quantity'] * $r['kg'];
				?>	
                        <tr>
                            <td><?=date("m/d/Y",strtotime($r['date']))?></td>
                            <td><?=str_pad($r['transfer_header_id'],7,0,STR_PAD_LEFT)?></td>                       
                            <td><?=$options->getAttribute('work_category','work_category_id',$r['work_category_id'],'work')?></td>                       
	                        <td><?=$r['reference']?></td> 
							<td><?=$r['remarks']?></td>
                            <td><?=$r['project_name']?></td>                      
                            <td><?=htmlentities($r['stock'])?></td>                       
                            <td style="text-align:right;"><?=$r['quantity']?></td>                       
                            <td><?=$r['unit']?></td>           
                            
						    
                            <td style="text-align:right;"><?=number_format($r['kg'],2,'.',',')?></td>                       
	                       <td style="text-align:right;"><?=number_format($r['kg'] * $r['quantity'],2,'.',',')?></td>                                
                         
                            <td style="text-align:right;"><?=number_format($r['price'],2,'.',',')?></td>                       
	                        <td style="text-align:right;"><?=number_format($r['amount'],2,'.',',')?></td> 
						
                      	</tr>
				<?php } ?>
                <tr>
                	<tr>
                    	<td style="border-top:1px solid #000;"></td>
                        <td style="border-top:1px solid #000;"></td>
                        <td style="border-top:1px solid #000;"></td>
                        <td style="border-top:1px solid #000;"></td>
						<td style="border-top:1px solid #000;"></td>
						<td style="border-top:1px solid #000;"></td>
					    <td style="border-top:1px solid #000;"></td>
                        <td style="border-top:1px solid #000; font-weight:bold; text-align:right;"><?=number_format($total_quantity,3,'.',',')?></td>
                        <td style="border-top:1px solid #000;"></td>
                        
                        <td style="border-top:1px solid #000;"></td>
					    <td style="border-top:1px solid #000;"></td>
                        <td style="border-top:1px solid #000; font-weight:bold; text-align:right;"><?=number_format($t_kg,3,'.',',')?></td>
                        
                        
                        <td style="border-top:1px solid #000; font-weight:bold; text-align:right;"><?=number_format($total_amount,3,'.',',')?></td>
                    </tr>
                </tr>
            </table>
        
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>