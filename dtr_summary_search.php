<?php

	$b = $_REQUEST['b'];
	$from_date = $_REQUEST['from_date'];
	$to_date = $_REQUEST['to_date'];
	$checkList = $_REQUEST['checkList'];
	$dtr_header_id	= $_REQUEST['dtr_header_id'];
	$user_id = $_SESSION['userID'];
	
?>
<script type="text/javascript">
function printIframe(id)
{
    var iframe = document.frames ? document.frames[id] : document.getElementById(id);
    var ifWin = iframe.contentWindow || iframe;
    iframe.focus();
    ifWin.printPage();
    return false;
}
</script>
<form name="_form" id="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">       
        <div style="display:inline-block;">
        	From : <br />
            <input type="text" class="datepicker textbox3"  name="from_date" readonly='readonly'  value="<?=$from_date?>">            
        </div>
		
		<div style="display:inline-block;">
        	To : <br />
            <input type="text" class="datepicker textbox3"  name="to_date" readonly='readonly'  value="<?=$to_date?>">            
        </div>

        
        <input type="submit" name="b" value="Search" />
        <!--<input type="button" name="b" value="Generate APV" onclick="j('#_dialog').dialog('open');" class="buttons" />             -->
        <!--<input type="submit" name="b" value="Cancel" onclick="return approve_confirm();"  /> -->
        <input type="hidden" id="dtr_header_id" value="" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <?php
	if($b!="Print"){
    ?>
    <div style="padding:3px; text-align:center;" id="content">
        <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
            <?php
                $page = $_REQUEST['page'];
                if(empty($page)) $page = 1;
                 
                $limitvalue = $page * $limit - ($limit);
            
				if($from_date && $to_date){
                $sql = "select
                              *
                         from
                              dtr_header
                         where
						 	from_date = '$from_date' and to_date = '$to_date' and status != 'C'
                    ";
				}else{
					$sql = "select
                              *
                         from
                              dtr_header where status != 'C'";
				}	

                $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                        
                $i=$limitvalue;
                $rs = $pager->paginate();
            ?>
            <tr bgcolor="#C0C0C0">				
                <th width="20">#</th>               
                <th width="20"></th>    
                <th>DTR Summary #</th>
                <th>From</th>     
                <th>To</th>     
                <th>Remarks</th>     
                <th>Date Created</th>
                <th>Status</th>
            </tr>  
            <?php								
                while($r=mysql_fetch_assoc($rs)) {
                    $dtr_header_id 	= $r['dtr_header_id'];

                    echo '<tr bgcolor="'.$transac->row_color($i++).'">';
                    echo '<td width="20">'.$i.'</td>';                  
                    echo '<td width="15"><a href="admin.php?view=f93c9551e5315a9c630e&dtr_header_id='.$r[dtr_header_id].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
                    echo '<td>'.str_pad($r['dtr_header_id'],8,"0",STR_PAD_LEFT).'</td>';	              
                    echo '<td>'.date("F j, Y",strtotime($r['from_date'])).'</td>';	
                    echo '<td>'.date("F j, Y",strtotime($r['to_date'])).'</td>';	
					echo '<td>'.$r['remarks'].'</td>';		
					echo '<td>'.$r['date_added'].'</td>';		
                    echo '<td>'.$options->getTransactionStatusName($r[status]).'</td>';	
                    echo '</tr>';
                }
            ?>
            </table>
            <table cellspacing="2" cellpadding="5" width="100%" class="search_table">
            <tr>
                <td colspan="5" align="left">
                    <?php
                        echo $pager->renderFullNav("$view");
                    ?>                
                </td>
            </tr>
        </table>
    </div>
    <?php
	}else if($b=="Print"){
    ?>
    <iframe id='JOframe' name='JOframe' frameborder='0' src='print_dtr_summary.php?id=<?=$dtr_header_id?>' width='100%' height='500'>
        	</iframe>
    <?php
	}
    ?>
</div>
</form>
<div id="_dialog" style="padding:0px;">
    <div id="ap_dialog_content">
    
    	<div style="margin:10px;">
        	PO #:<br />
            <input type="text" name="po_header_id" class="textbox" autocomplete="off" />
        </div>
           
        <input type="submit" name="b" value="Generate" class="buttons" style="margin:10px;" onclick="return approve_confirm();" />
    </div>
</div>
<script type="text/javascript">
	j(function(){
		j(function(){
			var dlg = j("#_dialog").dialog({autoOpen: false , modal:true , show: 'slide' , hide : 'slide' , width : 'auto', resizable : false, height : 'auto', title : "AP Voucher Details"});
			dlg.parent().appendTo(jQuery("form:first"));
		});
		
		j("#work_category_id").change(function(){
			xajax_display_subworkcategory(this.value);
		});
	});
</script>