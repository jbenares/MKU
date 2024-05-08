<?php	
require_once(dirname(__FILE__).'/../library/lib.php');
require_once(dirname(__FILE__).'/../conf/ucs.conf.php');

$eur_header_id = $_REQUEST['id'];

$aStatus = array(
    'F' => 'Finished',
    'S' => 'Saved',
    'C' => 'Cancelled'
);


$equipment = DB::conn()->query("
        select 
            stock
        from
            eur_header as h 
            inner join productmaster as p on h.stock_id = p.stock_id
        where
            eur_header_id = '$eur_header_id'
    ")->fetch_object()->stock;


$result = DB::conn()->query("
    select 
        d.* , project_name
    from 
        eur_detail as d 
        inner join po_header as h on d.po_header_id = h.po_header_id
        inner join projects as p on h.project_id = p.project_id
    where 
        eur_header_id = '$eur_header_id'
");
if( $result->num_rows > 0 ){
    $obj = $result->fetch_object();
    $project_name = $obj->project_name;
}


$sql = "
	select
        h.*, concat(employee_lname,', ',employee_fname) as name, project_name
    from
        gatepass as h
        left join employee as e on h.employee_id = e.employeeID         
        left join projects as p on h.project_id = p.project_id
    where gatepass_id = '$gatepass_id'
";

$aTrans = lib::getTableAttributes($sql);

function displayDetails($id){


    $aHeader = DB::conn()->query("select *, ( after_filling - before_filling ) as liters_consumed from eur_header where eur_header_id = '$id'")->fetch_assoc();


    
    $sql = "
        select
            *
        from 
            eur_detail 
        where
            eur_header_id = '$id'
        and eur_void = '0'        
    ";

    $arr = lib::getArrayDetails($sql);
    echo "
        <table class='table-css' style='vertical-align:top; width:100%; display:inline-table;' >
            
            <thead>
                <tr>
                    <td rowspan='2'>Date</td>                    
                    <td rowspan='2'>Start</td>                    
                    <td rowspan='2'>End</td>                                    
                    <td rowspan='2'>Total # of Hours</td>       
                    <td colspan='3'>REFUEL</td>
                    <td colspan='3'>ACCOMPLISHMENT</td>
                    <td rowspan='2'>REMARKS</td>                
                </tr>
                <tr>                    
                    <td>FVS No.</td>                    
                    <td>Liters Consumed</td>                    
                    <td>Ave. fuel Consumed per km./hr.</td>                    

                    <td>KM.</td>                    
                    <td>CU.M.</td>                    
                    <td>SQ.M.</td>

                    
                </tr>
            </thead>
            <tbody>
        ";

    $t_quantity = $t_amount = 0;
    if( count($arr) ){
        $i = 1;

        foreach ($arr as $r) {

            $aHeader['liters_consumed'] = $aHeader['after_filling'] - $aHeader['before_filling'];

            echo "
                <tr>                                    
                    <td>".lib::ymd2mdy($r['release_date'])."</td>                    
                    <td>".$r['start_time']."</td>                    
                    <td>".$r['end_time']."</td>                    
                    <td>".number_format($r['computed_time'],2)."</td>

                    <td>".$aHeader['fvs_no']."</td>                  
                    <td>".number_format($aHeader['liters_consumed'],2)."</td>  
                    <td>".number_format($aHeader['liters_consumed'] / $r['km'],2)."</td>  

                    <td>".number_format($r['km'],2)."</td>  
                    <td>".number_format($r['cum'],2)."</td>  
                    <td>".number_format($r['sqm'],2)."</td>  

                    <td>".$r['remarks']."</td>                    
                </tr>
            ";

            $i++;
        }

        for( ; $i <= 8 ; $i++ ){
            echo "
                <tr>                                    
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    

                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>

                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>

                    <td>&nbsp;</td>
                </tr>
            ";
        }
    }

    echo "            
            </tbody>            
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
    border: 1px solid #000;    
    text-align: center;
}
.table-css tfoot tr:first-child td{
    border-top: 1px solid #000;
    font-weight: bold;
}

.table-css caption{ font-weight: bold;}

.table-css tbody td{
    border:1px solid #000;
    text-align: center;
}

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
}
</style>
</head>
<body>
<div class="container">	    
        <p style="text-align:center; font-size:14px; font-weight:bold;">
            <?=$title?> <br>
            Equipment Utilization Report
        </p>     

        <div class="header" style="">
        	<table class='table-header'>
                <tbody>                    
                    <tr>
                        <td>Project</td>
                        <td><?=$project_name?></td>
                        <td>Type of Equipment</td>
                        <td><?=$equipment?></td>                    
                    </tr>                       
                </tbody>
                
            </table>
     	</div><!--End of header-->
        
    
        <?=displayDetails($eur_header_id);?>    
        
        <p style="text-align:right; font-weight:bold; font-size:14px;">
            E.U.R.No.: <?=$eur_header_id?>
        </p>
        

        <table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:10px;" class="summary">
            <tr>
                <td>&nbsp;<p>
                    <input type="text" class="line_bottom" /><br>Operator's Name</p></td>
                <td>Prepared By:<p>
                    <input type="text" class="line_bottom" /><br>Timekeeper/Warehouseman</p></td>
                <td>Conformed By:<p>
                    <input type="text" class="line_bottom" /><br>Authorized Representative/P.I.C.</p></td>
            </tr>
        </table>
</div>
<div class="divFooter">
    F-EMN-010<br>
    Rev. 010/07/13
</div>
</body>
</html>
<script>
    printPage();
</script>