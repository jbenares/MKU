<?php	
require_once(dirname(__FILE__).'/../library/lib.php');
require_once(dirname(__FILE__).'/../conf/ucs.conf.php');

$fabrication_id = $_REQUEST['id'];

$aStatus = array(
    'F' => 'Finished',
    'S' => 'Saved',
    'C' => 'Cancelled'
);


$sql = "
	select 
		f.*, to_project.project_name as to_project_name
    from 
        fabrication as f 
        left join projects as to_project on f.to_project_id = to_project.project_id        
    where fabrication_id = '$fabrication_id'
";

$aTrans = lib::getTableAttributes($sql);

function displayDetails($id){


    /*RAW MATERIALS DISPLAY*/
    $sql = "
        select
            d.*, stock, stock_length
        from 
            fabrication_raw_mat as d 
        left join productmaster as p on d.raw_mat_stock_id = p.stock_id
        where
            d.fabrication_id = '$id'
        and raw_mat_void = '0'        
    ";

    $arr = lib::getArrayDetails($sql);
    echo "
        <table class='table-css' style='vertical-align:top; width:45%; display:inline-table;' >
            <caption>RAW MATERIALS</caption>
            <thead>
                <tr>
                    <td style='width:10px;'>#</td>
                    <td>ITEM</td>
                    <td style='text-align:right; width:15%;'>LENGTH</td>                    
                    <td style='text-align:right; width:15%;'>WEIGHT</td>                    
                    <td style='text-align:right; width:15%;'>QUANTITY</td>                    
                    <td style='text-align:right; width:15%;'>TOTAL WEIGHT</td>                    
                </tr>
            </thead>
            <tbody>
        ";

    $t_quantity = $t_amount = $t_total_weight = 0;
    if( count($arr) ){
        $i = 1;

        foreach ($arr as $r) {

            $t_quantity     += $r['raw_mat_quantity'];            
            $t_total_weight += $r['raw_mat_total_weight'];            

            echo "
                <tr>
                    <td>$i</td>
                    <td>$r[stock]</td>
                    <td style='text-align:right;'>".number_format($r['stock_length'],4)."</td>
                    <td style='text-align:right;'>".number_format($r['raw_mat_weight_per_unit'],4)."</td>
                    <td style='text-align:right;'>".number_format($r['raw_mat_quantity'],4)."</td>
                    <td style='text-align:right;'>".number_format($r['raw_mat_total_weight'],4)."</td>
                </tr>
            ";

            $i++;
        }
    }

    echo "            
            </tbody>
            <tfoot>
                <tr>
                    <td></td>                    
                    <td></td>                    
                    <td></td>                    
                    <td></td>                    
                    <td style='text-align:right;'><span>".number_format($t_quantity,4)."</td>
                    <td style='text-align:right;'><span>".number_format($t_total_weight,4)."</td>
                </tr>
            </tfoot>
        </table>
    ";

    /*FINISHED PRODUCT DISPLAY*/
    $sql = "
        select
            d.*, stock, stock_length
        from 
            fabrication_product as d 
        left join productmaster as p on d.product_stock_id = p.stock_id
        where
            d.fabrication_id = '$id'
        and product_void = '0'        
    ";

    $arr = lib::getArrayDetails($sql);
    echo "
        <table class='table-css' style='display:inline-table; vertical-align:top; width:45%;' >
            <caption>FINISHED PRODUCT MATERIALS</caption>
            <thead>
                <tr>
                    <td style='width:3%;'>#</td>
                    <td>CLR#</td>
                    <td>ITEM</td>
                    <td style='text-align:right;'>LENGTH</td>     
                    <td style='text-align:right; width:15%;'>WEIGHT</td>                    
                    <td style='text-align:right; width:15%;'>QUANTITY</td>     
                    <td style='text-align:right; width:15%;'>TOTAL WEIGHT</td>                                   
                </tr>
            </thead>
            <tbody>
        ";

    $t_quantity = $t_amount = $t_total_weight = 0;

    if( count($arr) ){
        $i = 1;

        foreach ($arr as $r) {

            $t_quantity     += $r['product_quantity'];
            $t_total_weight += $r['product_total_weight'];
            echo "
                <tr>
                    <td>$i</td>
                    <td>$r[clr_no]</td>
                    <td>$r[stock]</td>
                    <td style='text-align:right;'>".number_format($r['stock_length'],4)."</td>
                    <td style='text-align:right;'>".number_format($r['product_weight_per_unit'],4)."</td>
                    <td style='text-align:right;'>".number_format($r['product_quantity'],4)."</td>
                    <td style='text-align:right;'>".number_format($r['product_total_weight'],4)."</td>
                </tr>
            ";

            $i++;
        }
    }

    echo "            
            </tbody>
            <tfoot>
                <tr>
                    <td></td>                    
                    <td></td>                    
                    <td></td>                    
                    <td></td>                    
                    <td></td>                    
                    <td style='text-align:right;'><span>".number_format($t_quantity,4)."</td>
                    <td style='text-align:right;'><span>".number_format($t_total_weight,4)."</td>
                </tr>
            </tfoot>
        </table>
    ";
    

    $aFab = lib::getTableAttributes("select stock, excess_quantity, excess_weight_per_unit, excess_total_weight, unit, excess_length from fabrication as f inner join productmaster as p on f.excess_stock_id = p.stock_id and fabrication_id = '$id'");
    echo "
        <table class='waste-cut'>
            <tr>    
                <td>Waste Cut Material</td>
                <td>$aFab[stock]</td>
            </tr>

            <tr>    
                <td>Waste Cut Length</td>
                <td>".number_format($aFab['excess_length'],4)."</td>
            </tr>

            <tr>    
                <td>Waste Cut Weight</td>
                <td>".number_format($aFab['excess_weight_per_unit'],4)."</td>
            </tr>

            <tr>    
                <td>Waste Cut Quantity</td>
                <td>".number_format($aFab['excess_quantity'],4)."</td>
            </tr>

            <tr>    
                <td>Waste Cut Total Weight</td>
                <td>".number_format($aFab['excess_total_weight'],4)."</td>
            </tr>
        </table>
        
    ";
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
body
{		
	padding:0px;	
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
}


.clearfix:after {
	content: ".";
	display: block;
	clear: both;
	visibility: hidden;
	line-height: 0;
	height: 0;
}
 
.clearfix {
	display: inline-block;
}

html[xmlns] .clearfix {
	display: block;
}
 
* html .clearfix {
	height: 1%;
}

.table-header tbody td:nth-child(odd){
    padding-right: 20px;
}

.table-header tbody td:nth-child(even){
    padding-right: 40px;
}
.table-css{
    border-collapse: collapse;
    margin-top: 10px;
}

.table-css thead td{
    font-weight: bold;
    border-top: 1px solid #000;
    border-bottom: 1px solid #000;
}
.table-css tfoot tr:first-child td{
    border-top: 1px solid #000;
    font-weight: bold;
}

.table-css caption{ font-weight: bold;}

.waste-cut td:nth-child(1){
    font-weight: bold;
    text-align: left;
    padding-right: 10px;
}
.waste-cut td:nth-child(2){
    text-align: right;
    padding-right: 10px;
}

.line_bottom {
    border-bottom-width: 1px;
    border-bottom-style: solid;
    border-bottom-color: #000000;
    border-left: 0px;
    border-right: 0px;
    border-top: 0px;
    width:140px;
    font-size: 11px;
    text-align: center;
}
</style>
</head>
<body>
<div class="container">	    
        <p style="text-align:center; font-size:14px; font-weight:bold;">
            <?=$title?> <br>
            FABRICATION
        </p>     

        <div class="header" style="">
        	<table class='table-header'>
                <tbody>
                    <tr>
                        <td>Date:</td>
                        <td><?=lib::ymd2mdy($aTrans['date'])?></td>
                        <td>FAB #: </td>
                        <td><?=str_pad($aTrans['fabrication_id'], 7,0,STR_PAD_LEFT)?></td>
                    </tr>                            
                    <tr>
                        <td>To Project:</td>
                        <td><?=$aTrans['to_project_name']?></td>
                        <td>Remarks </td>
                        <td><?=$aTrans['remarks']?></td>
                    </tr>                            
                </tbody>
                
            </table>
     	</div><!--End of header-->
        
        <?=displayDetails($fabrication_id);?>    

        <table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:10px;" class="summary">
            <tr>
                <td>Prepared By:<p>
                    <input type="text" class="line_bottom" /><br><?=lib::getUserFullName($aTrans['prepared_by'])?></p></td>
                <td>Checked By:<p>
                    <input type="text" class="line_bottom" /><br>Eddie Montinola</p></td>
                <td>Received By:<p>
                    <input type="text" class="line_bottom" /><br>&nbsp;</p></td>
            </tr>
        </table>
</div>
</body>
</html>
<script>
    printPage();
</script>