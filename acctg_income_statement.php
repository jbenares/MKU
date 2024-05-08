<?php
$checkList	= $_REQUEST['checkList'];
$checkList2	= $_REQUEST['checkList2'];
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
    	<table>
            <tr>
                <td>
                    <input type="checkbox"  name="checkAll" value="Comfortable with" onclick="javascript:check_all2('_form', this)" title="Check/Uncheck All" /> <label for="check_all" style="font-style:italic; font-weight:bolder;">CHECK ALL</label><br />
                </td>
            </tr>
            <?php
            $result = mysql_query("
                select	
                    *
                from
                    projects
                order by
                    project_name asc
            ") or die(mysql_error());
            
            $i = 0;
            while($r = mysql_fetch_assoc($result)){
                $project_id 	= $r['project_id'];
                $project_name	 = $r['project_name'];
            ?>	
            
            <?php
            
            if($i == 0){
                echo "<tr>";
            }
            ?>
            <?php
            if(!empty($checkList2)){
                if(in_array($project_id,$checkList2)){
                    $selected = "checked='checked'";
                }else{
                    $selected = "";	
                }
            }
            ?>
                <td>
                    <input type="checkbox" <?=$selected?> id="project_id_<?=$project_id?>" value="<?=$project_id?>" name="checkList2[]" onclick="document._form.checkAll.checked=false"/> <label for="project_id_<?=$project_id?>"><?=$project_name?></label>
                </td>
            <?php
            if($i == 4){
                echo "</tr>";
                $i = -1;	
            }
            ?>
            <?php
            $i++;
            }
            ?>
    	</table>
    </div>
    <div class="module_actions">              
        <div class="inline">
            Starting Date<br />
            <input type="text" class="textbox3 datepicker" id='startingdate' name="startingdate" value='<?php echo $_REQUEST[startingdate];?>' readonly='readonly' >
        </div>
        
        <div class="inline">
            Ending Date<br />
            <input type="text" class="textbox3 datepicker" id='endingdate' name="endingdate" value='<?php echo $_REQUEST[endingdate];?>' readonly='readonly' >
        </div>
        
     	<input type="submit" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php
		if(!empty($_REQUEST['startingdate']) && !empty($_REQUEST['endingdate']) && !empty($checkList2))
		{
	?>
		<?php
        foreach($checkList2 as $val){
            $testValues2[] = "checkList2[]=$val";
        }
        
        $link2 = join("&amp;", $testValues2);	
        ?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="printIncomeStatement2.php?startingdate=<?=$_REQUEST['startingdate'];?>&endingdate=<?=$_REQUEST['endingdate'];?>&project_id=<?=$_REQUEST['project_id']?>&<?=$link2?>" width="100%" height="500">
        </iframe>
    </div>
    <?php }?>
    </div>
</div>
</form>