<?php
	class query{
		
		function row_color($row) {			
			if($row%2!=0)
				$color="#FFFFCC";
			else
				$color="#EEE8AA";
				
			return $color.'"  onMouseOver="javascript:trackTableHighlight(event, '."'#B5E2FE'".');" onMouseOut="javascript:highlightTableRow(0);';
		}
		
		function getTotalRows($table) {
			$query = mysql_query("select * from $table");
			$r = mysql_num_rows($query);
			
			return $r;
		}

		function get_msg($id, $sender, $receiver) {
			$Gid = $this->GUID();

			$get_msg = mysql_query("select content from messages where id='$id'");
			$r_msg = mysql_fetch_array($get_msg);
		
			$message = addslashes($r_msg[content]);
		
			$put_msg = mysql_query("insert into ozekismsout set
										id='$Gid',
										sender='$sender',
										receiver='$receiver',
										msg='$message',
										senttime=SYSDATE(),
										reference='$id',
										status='send',
										operator='SMART'");
										
		}
		
		function get_station_reply($id, $sender, $receiver) {
			$Gid = $this->GUID();
		
			$get_msg = mysql_query("select reply_message from stations where id='$id'");
			$r_msg = mysql_fetch_array($get_msg);
			
			if(!empty($r_msg[reply_message])) {
				$message = addslashes($r_msg[reply_message]);
				
				$put_msg = mysql_query("insert into ozekismsout set
										id='$Gid',
										sender='$sender',
										receiver='$receiver',
										msg='$message',
										senttime=SYSDATE(),
										reference='$id',
										status='send',
										operator='SMART'");
			}
			else {
				$this->get_msg(5, $sender, $receiver);
			}										
		}
		
		function alert_engineers($substation_id, $sender, $message) {
			$Gid = $this->GUID();
			
			$getEngineerNum = mysql_query("select mobile_num from engineers where substation_id='$substation_id'");
			
			while($rEngineerNum=mysql_fetch_array($getEngineerNum)) {
				$put_msg = mysql_query("insert into ozekismsout set
										id='$Gid',
										sender='$sender',
										receiver='$rEngineerNum[mobile_num]',
										msg='$message',
										senttime=SYSDATE(),
										reference='$id',
										status='send',
										operator='SMART'");
			}
			
		}
		
		function sendSMS($message, $sender, $receiver) {
			$Gid = $this->GUID();
		
			$put_msg = mysql_query("insert into ozekismsout set
										id='$Gid',
										sender='$sender',
										receiver='$receiver',
										msg='$message',
										senttime=SYSDATE(),
										reference='$id',
										status='send',
										operator='SMART'");				
		}
		
		function GUID() {
			return date("Ymdhis");
		}
		
		function getLatestIGPS($IMEI) {
			$db = mysql_select_db('igps');
		
			$query = mysql_query("select
									log.IMEI,
									log.DATETIME,
									log.EVENTID,
									log.LATITUDE,
									log.LONGITUDE,
									log.SPEED,
									log.EVENTID,
									ini.VAL
								from
									log,
									ini
								where
									log.IMEI='$IMEI' and
									log.EVENTID=ini.PARAM
								order by
									log.DATETIME desc");
									
			$r = mysql_fetch_array($query);
			
			return $r;
		}
		
		function getIGPS($IMEI, $date) {
			$db = mysql_select_db('igps');
			
			$query = mysql_query("select
									log.IMEI,
									log.DATETIME,
									log.EVENTID,
									log.LATITUDE,
									log.LONGITUDE,
									log.SPEED,
									log.EVENTID,
									ini.VAL
								from
									log,
									ini
								where
									log.IMEI='$IMEI' and
									log.EVENTID=ini.PARAM and
									log.DATETIME like '$date%'
								order by
									log.DATETIME desc");
			
			$i = 0;					
			while($r=mysql_fetch_array($query)) {
				$igps[$i] = $r;
				
				$i++;
			}
			
			return $igps;
		}
		
		function include_files($registered_access, $view) {
			if($registered_access==1) { 
				$getFiles = mysql_query("select
											PCode,
											Pfilename,
											view_keyword
										from
											programs
										where											
											view_keyword='$view'");
			}
			else {
				$getFiles = mysql_query("select
											PCode,
											Pfilename,
											view_keyword
										from
											programs
										where
											enabled='1' and
											view_keyword='$view'");
			}
			
			$rFiles = mysql_fetch_array($getFiles);
			
			if(mysql_num_rows($getFiles)==0)
				return 'file_down.php';
			else			
				return $rFiles[Pfilename];
		}
		
		function include_xajaxF($view) {
			$getF = mysql_query("select
									Fname
								from
									programs as p,
									my_functions as m
								where
									p.view_keyword='$view' and
									p.PCode=m.PCode");
									
			$i = 0;
			while($rF=mysql_fetch_array($getF)) {
				 $farray[$i] = $rF[Fname];
				 
				 $i++;
			}
			
			return $farray;
		}
		
		function getMname($view) {
			$getMn = mysql_query("select
										m.Mname
									from
										menu as m,
										programs as p
									where
										p.view_keyword='$view' and
										p.PCode=m.PCode");
										
			$rMn = mysql_fetch_array($getMn);
		
			return ' '.strtoupper($rMn[Mname]);
		}
		
	}
?>