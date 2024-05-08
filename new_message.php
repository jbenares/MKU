<?php
	include("fckeditor/fckeditor.php") ;

	$recipients = $_REQUEST['recipients'];
	$subject = $_REQUEST['subject'];
	$pmmessage = $_REQUEST['pmmessage'];
	$attach = $_FILES['attach'];
	$b = $_REQUEST['b'];	
		
	if(!empty($b)) {
		if(!empty($recipients) && !empty($subject) && !empty($pmmessage)) {
			
			$recipient = explode(";", $recipients);
		
			$id = date("Ymd-his");
			$date_exec = date("Y-m-d H:i:s");
		
			$sql = "insert into private_messages set
						id='$id',
						subject='$subject',
						privatemsg='$pmmessage',
						date_sent='$date_exec',
						sent_by='$registered_userID'";
				
			$query = mysql_query($sql);
		
			$i=1;
			foreach($recipient as $rcp) {
				$rcpID = explode("(", $rcp);
				
				if(!empty($rcpID[0])) {
					$pmid = $id.$i;
					$i++;
					
					$sql = "insert into pmrecipients set
								id='$pmid',
								userID='$rcpID[0]',
								pmID='$id'";
					
					$query = mysql_query($sql);
				}
			}
			
			for($i=0;$i<count($attach);$i++) {
				$attach_filename =  $upload->upload_img($attach[size][$i], $attach[type][$i], $attach[tmp_name][$i], $attach[name][$i], "attachments", "");
				
				if(!empty($attach_filename)) {
					$attachmentID = $id.$i;
					
					$query = mysql_query("insert into attachments set
											attachmentID='$attachmentID',
											Afilename='$attach_filename',
											pmid='$id'");
				}
			}
			
			if($query) {
				$msg = "Your message has been sent. Message sent status may be checked on the \"Sent Messages Module\"";
				
				$recipients = '';
				$subject = '';
				$pmmessage = '';
			}					
			else
				$msg = mysql_error();
		}
		else {
			$msg = "Fill in all fields!";
		}
	}

?>
<form name="newpmform" method="post" action="" enctype="multipart/form-data">
<div class=form_layout>
	<div class="module_title"><img src='images/email.png'> NEW MESSAGE</div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;">
    <table class=form_table align="center">
        <tr>
            <td><textarea readonly name='recipients' id='recipients' style='overflow:hidden;width:730px;height:50px;font-size:11px;font-family:Arial;'><?=$recipients;?></textarea><br><a  href="javascript:void(0);" style="cursor:pointer;" onclick="document.getElementById('recipients').value=''">Clear Recipients</a></td>
            <td>
                <div id='demodiv2' class='demo2'></div>
                <div style='padding:3px;'>
                    <img src='images/user_add.png' style='cursor:pointer;'  onmouseover="Tip('Select Receiving Users');" onclick=xajax_address_book();toggleBox('demodiv2',1);></div>
                <div style='padding:3px;'>
                    <img src='images/group_add.png' style='cursor:pointer;' onmouseover="Tip('Select Receiving Groups');" onclick=xajax_show_groups();toggleBox('demodiv2',1);></div>								
            </td>
        </tr>
        <tr>
            <td colspan="2"><img src='images/comment_blue.gif'> Subject : <br /><input type=text name='subject' style='width:730px;;font-size:11px;' value="<?=$subject;?>"></td>
        </tr>
        <tr>
            <td colspan="2">
                <?php
                    $sValue = stripslashes($pmmessage);	   
                           
                    $oFCKeditor = new FCKeditor('pmmessage') ;
                    
                    $oFCKeditor->BasePath = 'fckeditor/';
                    
                    $oFCKeditor->ToolbarSet = 'Default'; // Default | Basic Toolbars
                    $oFCKeditor->Width  = '770px' ;
                    $oFCKeditor->Height = '300px' ;
                    $oFCKeditor->Value = $sValue; //Default text in editor
                    
                    $oFCKeditor->Create() ;
                ?>
        	</td>
        </tr>
        <tr>
            <td colspan="2">
            	<img src='images/email_attach.png'> Attach Files : <br />
            	<input type=file name='attach[]' class="textbox">
                <input type=file name='attach[]' class="textbox">
                <input type=file name='attach[]' class="textbox"><br />
                <input type=file name='attach[]' class="textbox">
                <input type=file name='attach[]' class="textbox">
                <input type=file name='attach[]' class="textbox">
            </td>
        </tr>  
        <tr>
        <td>						  	  
          <input type=submit name=b value='Submit' class=buttons>
        </td>
        </tr>
        </table>
    </div>
</div>
</form>