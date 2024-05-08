<?php

	$sValue = addslashes($_REQUEST['message']);
	$page = $_REQUEST['page'];
	$b = $_REQUEST['b'];

	if($b=='Save') {	
	  if(!empty($sValue)) {	
	  
	    $sql = "update messages set
				content='$sValue'
				where
					id='$page'";
				
		$query = mysql_query($sql);
		
		if($query) {
		  $msg = 'Query Successful';
		}
		else {
		  $msg = mysql_error();
		}
	  }		
	}
	
	$get_message = mysql_query("select content from messages where id='$page'");
	$r_message = mysql_fetch_array($get_message);

?>
<script src="scripts/insert_toCursor.js"></script>
<script type="text/javascript">
	//Edit the counter/limiter value as your wish
	var count = "160";   //Example: var count = "175";
	function limiter(){
	var tex = document.messages_form.message.value;
	var len = tex.length;
	if(len > count){
			tex = tex.substring(0,count);
			document.messages_form.message.value =tex;
			return false;
	}
	document.messages_form.limit.value = count-len;
	}
</script>
<form name="messages_form" method="post" action="">
<div class=form_layout>
	<div class="module_title"><img src='images/phone.png'> REPLY MESSAGES</div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;">
    <table class="form_table" align="center" width="100%">
        <tr>
        <td>
            <select name="page" class="select" onChange="this.form.submit();">
                <?php
                if(!empty($page)) {
                    $get_page = mysql_query("select id, name from messages where id='$page'");
                    $r_page = mysql_fetch_array($get_page);
                    
                    echo '<option value="'.$r_page[id].'">'.$r_page[name].' (Reply Code #'.$r_page[id].')</option>';
                }
                else
                    echo '<option value="0">Please select reply message. . .</option>';
                    
                    echo '<option value="0">- - - - - - - - - - - - - - - - </option>';
                    
                    $get_page = mysql_query("select id, name from messages where id!='$page' order by id");
                    
                    while($r_page = mysql_fetch_array($get_page)) {
                        echo '<option value="'.$r_page[id].'">'.$r_page[name].' (Reply Code #'.$r_page[id].')</option>';
                    }
                ?>                   
            </select>                
            <input type="button" name="b" value="Add Next Line Characters" class="buttons" onclick="insertAtCaret('messageBody','\\0x0A');" />   
            <input type="submit" name="b" value="Save" class="buttons" />   
        </td>
        </tr>
        <tr>
          <td align="center" colspan="2">     
            <?php
                       
                echo '<textarea id="messageBody" name="message" onkeyup="limiter()" class="textarea">'.$r_message[content].'</textarea>';
                
            ?>
            <p align="left">
            <script type="text/javascript">
                document.write("Characters Left : <input type=text name=limit size=4 readonly value="+count+">");
                limiter();
            </script>
            </p>
          </td>
        </tr>
    </table>
  </div>
</div>
</form>