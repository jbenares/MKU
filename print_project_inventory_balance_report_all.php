<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	set_time_limit(0);
    $options    = new options();	
    $date       = $_REQUEST['reportdate'];
    $project_id = $_REQUEST['project_id'];
    $categ_id1  = $_REQUEST['categ_id1'];
    $categ_id2  = $_REQUEST['categ_id2'];
    $categ_id3  = $_REQUEST['categ_id3'];
    $categ_id4  = $_REQUEST['categ_id4'];
    $type       = $_REQUEST['type'];
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
	
     
     	
     	
	<?php 
    $projects = array();
    $result = mysql_query("select * from projects order by project_name asc") or die(mysql_error()) ;
    while($r = mysql_fetch_assoc($result)){
        $projects[] = $r['project_id'];
    }
    ?>
    
    <?php foreach($projects as $project_id): ?>
        <div style="font-weight:bolder;">
            PROJECT INVENTORY REPORT <br />
            As of <?php echo date("m/d/Y",strtotime($date))?><br />
            <?=$options->getAttribute('projects','project_id',$project_id,'project_name') ?>
        </div>
        <div class="content" style="">
			<?php
                $query="
                    select
                        *
                    from 
                        productmaster
                    where
                        1=1                    
                ";
                
                if(!empty($categ_id1)) $query.=" and categ_id1 = '$categ_id1'"; 
                if(!empty($categ_id2)) $query.=" and categ_id2 = '$categ_id2'"; 
                if(!empty($categ_id3)) $query.=" and categ_id3 = '$categ_id3'";
                if(!empty($categ_id4)) $query.=" and categ_id4 = '$categ_id4'";
                
                
                $query .= " order by stock asc ";
                $result = mysql_query($query);            
      	  ?>
            <table>
                <tr>
                    <th>INVENTORY ITEM</th>
                    <th style="width:10%;">BALANCE</th>
                    <?php if($type == "quantity"){ ?>
                    <th style="width:10%;">UNIT</th>
                    <?php } ?>
                    <th>COST</th>
                    <th>AMOUNT</th>
                </tr>	
                
                
                <?php    
					$total_amount = 0;
					$total_balance = 0;
                    while($r=mysql_fetch_assoc($result)):
                        $stock 		= $r['stock'];
                        $stock_id 	= $r['stock_id'];
                        $unit		= $r['unit'];
						$cost		= $r['cost'];
                        
                        $project_qty	= $options->inventory_projectwarehousebalance($date,$stock_id,$project_id,$type);
						set_time_limit(30);
                        if($project_qty > 0){
							$amount =  $project_qty * $cost;
							$total_balance += $project_qty;
							$total_amount += $amount;
                ?>	
                        <tr>
                            <td><?=$stock?></td>
                            <td style="text-align:right;"><?=number_format($project_qty,2,'.',',')?></td>
                            <?php if($type == "quantity"){ ?>
                            <td><?=$unit?></td>           
                            <?php } ?>             
                            <td style="text-align:right;"><?=number_format($cost,2)?></td>
                            <td style="text-align:right;"><?=number_format($amount,2)?></td>      
                        </tr>
                <?php
                        }
                    endwhile;
                ?>
                <tr>
                	<td style="text-align:left; border-top:1px solid #000;"></td>
                    <td style="widtd:10%; text-align:right; border-top:1px solid #000;"><?=number_format($total_balance,2)?></td>
                    <?php if($type == "quantity"){ ?>
                    <td style="widows:10%; text-align:left; border-top:1px solid #000;"></td>
                    <?php } ?>
                    <td style="border-top:1px solid #000;"></td>
                    <td style="border-top:1px solid #000; text-align:right;"><?=number_format($total_amount,2)?></td>
                </tr>
            </table>
        
        </div><!--End of content-->
   	<?php endforeach ?>
</div>
</body>
</html>