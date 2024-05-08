<script type="text/javascript">
	<!--
	//var TSort_Data = new Array ('my_table', '','','','','','s','s', 's','s','s','s');
	//tsRegister();
	// -->
</script>
<?php

	$b = $_REQUEST['b'];
	$keyword = $_REQUEST['keyword'];
	$checkList = $_REQUEST['checkList'];
	$financial_budget_header_id	= $_REQUEST['financial_budget_header_id'];
	
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
<form name="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">       
        <div style="display:inline-block;">
            <input type='text' name='keyword' class='textbox3' value='<?=$keyword?>'>
             
            <input type="submit" name="b" value="Search" />
        </div>
      	<input type="button" value="Print" onclick="printIframe('JOframe');" />
        <input type="hidden" id="financial_budget_header_id" value="" />
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
            
                $sql = "
					select
						*
					from
						financial_budget_header as h, projects as p
					where
						h.project_id = p.project_id
					and
						financial_budget_header_id like '$keyword%'
                   ";
                              
                    
                $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
                        
                $i=$limitvalue;
                $rs = $pager->paginate();
            ?>
            <tr bgcolor="#C0C0C0">				
                <th width="20">#</th>
                <th width="20" align="center"><input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all('_form', this)" title="Check/Uncheck All" /></th>
                <th width="20"></th>
                <th width="20"></th>    
                <th>Financial Budget #</th>
                <th>Project</th>
                <th>Status</th>
            </tr>  
            <?php								
                while($r=mysql_fetch_assoc($rs)) {
                    $financial_budget_header_id 	= $r['financial_budget_header_id'];
					$financial_budget_header_id_pad	= str_pad($financial_budget_header_id,7,0,STR_PAD_LEFT);
					$project_id						= $r['project_id'];
					$project						= $r['project_name'];
					$status							= $r['status'];
					
			?>
                <tr bgcolor="<?=$transac->row_color($i)?>">
                    <td width="20"><?=++$i?></td>
                    <td><input type="checkbox" name="checkList[]" value="<?=$financial_budget_header_id?>" onclick="document._form.checkAll.checked=false"></td>
                    <td width="15"><a href="admin.php?view=2e6ffd8f1c122408e585&financial_budget_header_id=<?=$financial_budget_header_id?>" title="Show Details"><img src="images/edit.gif" border="0"></a></td>
                    <td width="15"><a href="admin.php?view=<?=$view?>&financial_budget_header_id=<?=$financial_budget_header_id?>&b=Print"><img src="images/action_print.gif" border="0"></a></td>
                    <td><?=$financial_budget_header_id_pad?></td>
                    <td><?=$project?></td>	
                    <td><?=$options->getTransactionStatusName($status)?></td>	
	            </tr>
			<?php
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
    <iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_rr.php?id=<?=$financial_budget_header_id?>' width='100%' height='500'>
        	</iframe>
    <?php
	}
    ?>
</div>
</form>