<?php
	class process{

		function save_report($code_array, $from) {
			include_once("my_Classes/generate_GUID.php");			
			
			$Guid = new Guid();
			
			$Gid = $Guid->toString();
		
			$name = strtoupper($code_array[1]);
			
			$Rmessage = '';
			for($i=2;$i<count($code_array);$i++) {
				$Rmessage .= $code_array[$i]." ";
			}
			
			mysql_query("insert into sms_reports set
							id='$Gid',
							name='$name',
							sms_message='$Rmessage',
							mobileno='$from',
							received=SYSDATE()");					
		}
		
		function check_station_status($id, $to, $from) {	
			include_once("my_Classes/query.class.php");
			
			$transac = new query();
				
			$get_station_stat = mysql_query("select
												status
											from
												stations
											where
												id='$id'");
													
			$r_station_stat = mysql_fetch_array($get_station_stat);
			
			$stat = $r_station_stat[status];
			
			if($stat==1) {
				$transac->get_msg(4, $to, $from); // AC Power On
			}
			else {
				$transac->get_station_reply($id, $to, $from); // Specific Reply of Station Status
			}
		}
		
		function sensor_alerts($code_array, $station_code) {
			include_once("my_Classes/generate_GUID.php");		
			include_once("my_Classes/query.class.php");
			
			$transac = new query();
			$Guid = new Guid();
			
			$Gid = $Guid->toString();
			
			if($code_array[7]=='off') {
				$sensed = 0;
				
				$subStation = mysql_query("select
												sb.id,
												s.station_name,
												sb.name
											from
												stations as s,
												substations as sb
											where
												s.station_code='$station_code' and
												s.substation_id=sb.id");
												
				$rSubstation = mysql_fetch_array($subStation);
				
				$message = addslashes('Substation: '.$rSubstation[name].'\\0x0AMobile #: ('.$station_code.')\\0x0AAC Power Off');
				
				$transac->alert_engineers($rSubstation[id], $station_code, $message); // substation_id, gateway_num, message
			}
			else $sensed = 1;
			
			mysql_query("insert into sensor_alerts set
							id='$Gid',
							station_code='$station_code',
							date_sensed='$code_array[2]',
							ref_date=SYSDATE(),
							sensed='$sensed'");			
							
			mysql_query("update stations set
							status='$sensed'
						where
							station_code='$station_code'");
							
		}
		
	}
?>