<?php
	/*
		Author: Michael Francis C. Catague, ECE, MIT and Michael Salvio,CpE
	*/

	class options {

		function program_keyword($filename){
			$result=mysql_query("
				select
					*
				from
					programs
				where
					Pfilename = '$filename'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);

			return $r['view_keyword'];

		}

			function option_parent_chart_of_accounts($selectedid=NULL,$name='gchart_id'){
			$content="
				<select name='$name' id='$name'>
					<option value=''>Select GChart:</option>
			";
			$query="
				select
					*
				from
					gchart
				where
					enable='Y' and
					parent_gchart_id = '0'
				order by gchart asc
			";
			$result=mysql_query($query);
			while($r=mysql_fetch_assoc($result)):
				$select=($selectedid==$r[gchart_id])?"selected='selected'":"";
				$content.="
					<option value='$r[gchart_id]' $select >$r[gchart]</option>
				";
			endwhile;
			$content.="
				</select>
			";
			return $content;	
		}
		
			function option_all_chart_of_accounts($selectedid=NULL,$name='gchart_id'){
			$content="
				<select name='$name' id='$name'>
					<option value=''>Select GChart:</option>
			";
			$query="
				select
					*
				from
					gchart
				where
					enable='Y'
				order by gchart asc
			";
			$result=mysql_query($query);
			while($r=mysql_fetch_assoc($result)):
				$select=($selectedid==$r[gchart_id])?"selected='selected'":"";
				$content.="
					<option value='$r[gchart_id]' $select >$r[gchart]</option>
				";
			endwhile;
			$content.="
				</select>
			";
			return $content;	
		}		
		

		function option_chart_of_accounts_limited($selectedid=NULL,$name='gchart_id'){
			$content="
				<select name='$name' id='$name'>
					<option value=''>Select GChart:</option>
			";
			$query="
			select
					*
				from
					gchart
				where
					enable='Y'
				and 
            	gchart_id = '75' or parent_gchart_id = '75'		
				order by gchart asc
			";
			$result=mysql_query($query);
			while($r=mysql_fetch_assoc($result)):
				$select=($selectedid==$r[gchart_id])?"selected='selected'":"";
				$content.="
					<option value='$r[gchart_id]' $select >$r[gchart]</option>
				";
			endwhile;
			$content.="
				</select>
			";
			return $content;
		}		
		
		function option_supplier_accounts($selectedid=NULL,$name='account_id'){
			$content="
				<select name='$name' id='$name'>
					<option value=''>Select Supplier:</option>
			";
			$query="
				Select * from supplier order by account ASC
			";
			$result=mysql_query($query);
			while($r=mysql_fetch_assoc($result)):
				$select=($selectedid==$r[account_id])?"selected='selected'":"";
				$content.="
					<option value='$r[account_id]' $select >$r[account]</option>
				";
			endwhile;
			$content.="
				</select>
			";
			return $content;	
		}
		
		function company_options($id) {
			$sql = "select * from companies order by companyID";
			$query = mysql_query($sql);

			while($r=mysql_fetch_array($query)) {
				if($id==$r[companyID]) continue;

				$string_row.='<option value="'.$r[companyID].'">'.$r[company_abbrevation].' - '.$r[company_name].'</option>';
			}

			if(!empty($id)) {
			  $sql = "select * from companies where companyID='$id'";
			  $query = mysql_query($sql);
			  $result = mysql_fetch_array($query);

			  return '<select name=company class=select><option value="'.$result[companyID].'">'.$result[company_abbrevation].' - '.$result[company_name].'</option><option>= = = = = = = = = = = = = = = = = = = = </option>'.$string_row.'</select>';
		    }
			else
			  return '<select name=company class=select><option value=0>Please Select Company...</option>'.$string_row.'</select>';

		}

		function access_options($id) {
			$sql = "select id,name from access_type where id!='1' order by id";
			$query = mysql_query($sql);

			while($r=mysql_fetch_array($query)) {
				if($id==$r[id]) continue;

				$string_row.='<option value="'.$r[id].'">'.$r[name].'</option>';
			}

			if(!empty($id)) {
			  $sql = "select id, name from access_type where id='$id'";
			  $query = mysql_query($sql);
			  $result = mysql_fetch_array($query);

			  return '<select name=access_type class=select><option value="'.$result[id].'">'.$result[name].'</option><option>= = = = = = = = = = = = = = = = = = = = </option>'.$string_row.'</select>';
		    }
			else
			  return '<select name=access_type class=select><option value=0>Please Select Access...</option>'.$string_row.'</select>';
		}

		function branch_options($id) {
			$sql = "select branchID, name from branches order by name";
			$query = mysql_query($sql);

			while($r=mysql_fetch_array($query)) {
				if($id==$r[branchID]) continue;

				$string_row.='<option value="'.$r[branchID].'">'.$r[name].'</option>';
			}

			if(!empty($id)) {
			  $sql = "select branchID, name from branches where branchID='$id'";
			  $query = mysql_query($sql);
			  $result = mysql_fetch_array($query);

			  return '<select name=branch class=select><option value="'.$result[branchID].'">'.$result[name].'</option><option>= = = = = = = = = = = = = = = = = = = = </option>'.$string_row.'</select>';
		    }
			else
			  return '<select name=branch class=select><option value=0>Please Select Branch...</option>'.$string_row.'</select>';
		}

		function option_all_accounts($selectedid=NULL,$name='gchart_id'){
			$content="
				<select name='$name' id='$name'>
					<option value=''>Select GChart:</option>
			";
			$query="
				select
					*
				from
					gchart
				where
					enable='Y'
				order by gchart asc
			";
			$result=mysql_query($query);
			while($r=mysql_fetch_assoc($result)):
				$select=($selectedid==$r[gchart_id])?"selected='selected'":"";
				$content.="
					<option value='$r[gchart_id]' $select >$r[gchart]</option>
				";
			endwhile;
			$content.="
				</select>
			";
			return $content;	
		}

		function file_options($id) {
			$sql = "select PCode, Pfilename, view_keyword from programs order by Pfilename";
			$query = mysql_query($sql);

			while($r=mysql_fetch_array($query)) {
				if($id==$r[PCode]) continue;

				$string_row.='<option value="'.$r[PCode].'">'.$r[Pfilename].'</option>';
			}

			if(!empty($id)) {
			  $sql = "select PCode, Pfilename, view_keyword from programs where PCode='$id'";
			  $query = mysql_query($sql);
			  $result = mysql_fetch_array($query);

			  return '<select name=file_ class=select><option value="'.$result[PCode].'">'.$result[Pfilename].'</option><option>= = = = = = = = = = = = = = = = = = = = </option><option>None</option>'.$string_row.'</select>';
		    }
			else
			  return '<select name=file_ class=select><option value=0>Leave Blank If Parent...</option>'.$string_row.'</select>';
		}

		function parentMenu_options($id) {
			$sql = "select M_id, Mname, level from menu  where level='1' or level='2' order by level, Mname";
			$query = mysql_query($sql);

			while($r=mysql_fetch_array($query)) {
				if($id==$r[M_id]) continue;

				$string_row.='<option value="'.$r[M_id].'"> (Level '.$r[level].') - '.$r[Mname].'</option>';
			}

			if(!empty($id)) {
			  $sql = "select M_id, Mname, level from menu where M_id='$id'";
			  $query = mysql_query($sql);
			  $result = mysql_fetch_array($query);

			  return '<select name=mCode class=select><option value="'.$result[M_id].'"> (Level '.$result[level].') - '.$result[Mname].'</option><option>= = = = = = = = = = = = = = = = = = = = </option>'.$string_row.'</select>';
		    }
			else
			  return '<select name=mCode class=select><option value=#>Leave Blank If Parent...</option>'.$string_row.'</select>';
		}

		function strTime($s) {
			$d = intval($s/86400);
			$s -= $d*86400;

			$h = intval($s/3600);
			$s -= $h*3600;

			$m = intval($s/60);
			$s -= $m*60;

			if ($d) $str = $d . 'd ';
			if ($h) $str .= $h . 'h ';
			if ($m) $str .= $m . 'm ';
			if ($s) $str .= $s . 's';

			return $str;
		}

		function convert_sysdate($datetime) {
			return date("F d, Y h:i:s A", strtotime($datetime));
		}

		function getID()
		{
			$id = date("Ymd-his");
			return $id;
		}


		function getUserName($userID){
			$query="
				select
					*
				from
					admin_access
				where
					userID='$userID'
			";
			$result=mysql_query($query);
			$r=mysql_fetch_assoc($result);

			return "$r[user_fname] $r[user_lname]";
		}

		function getTypeJob($selectedid=NULL,$name='type'){
			$lists=array(
					'M' => "Maintenance",
					'A' => "Accident"
				);

			$content="
				<select name='$name' id='$name' >
					<option value=''>Select Type:</option>
			";

			foreach($lists as $value => $list):
				$selected=($selectedid==$value)?"selected='selected'":"";
				$content.="
					<option value='$value' $selected >$list</option>
				";
			endforeach;


			$content.="
				</select>
			";

			return $content;

		}

		function getMonthOptions($selectedid=NULL,$name='month'){
			$lists=array(
					1 => "Jan",
					2 => "Feb",
					3 => "Mar",
					4 => "Apr",
					5 => "May",
					6 => "Jun",
					7 => "Jul",
					8 => "Aug",
					9 => "Sept",
					10=> "Oct",
					11=> "Nov",
					12=> "Dec"
				);

			$content="
				<select name='$name' id='$name' >
					<option value=''>Select Month:</option>
			";

			foreach($lists as $value => $list):
				$selected=($selectedid==$value)?"selected='selected'":"";
				$content.="
					<option value='$value' $selected >$list</option>
				";
			endforeach;


			$content.="
				</select>
			";

			return $content;

		}

    function getTirePosition($selectedid=NULL,$name='position'){
			$sql = "select * from tire_position order by tire_pos_id";
			$query = mysql_query($sql);

			while($r=mysql_fetch_array($query)) {
				if($selectedid==$r[tire_pos_id]) continue;
				$string_row.='<option value="'.$r[tire_pos_id].'" >'.$r[position].'</option>';
			}

			if(!empty($selectedid)) {
			  $sql = "select * from tire_position where tire_pos_id='$selectedid'";
			  $query = mysql_query($sql);
			  $result = mysql_fetch_array($query);

			  return '<select name="'.$name.'" class=select><option value="'.$result[tire_pos_id].'">'.$result[position].'</option><option>= = = = = = = = = = = = = = = = = = = = </option>'.$string_row.'</select>';
		    }
			else
			  return '<select name="'.$name.'" class=select><option value="">- - - - - Choose Position - - - - -</option>'.$string_row.'</select>';

		}

		function getTirePositionValue($selectedid=NULL){
		         $sql=mysql_query("SELECT * FROM tire_position WHERE tire_pos_id = '$selectedid'");
                         $r=mysql_fetch_assoc($sql);

			return $r[position];

		}

		function insertAudit($header_id,$header,$transaction){
			mysql_query("
				insert into
					audit
				set
					user_id 	= '$_SESSION[userID]',
					header_id 	= '$header_id',
					header		= '$header',
					transaction	= '$transaction'
			") or die(mysql_error());

		}


		/*PRODUCTMASTER OPTIONS*/

		function new_stockcode($categ_id1){

		$result=mysql_query("
			select
				stock_id,
				stockcode
			from
				productmaster
			where
				categ_id1 = '$categ_id1'
			order by
				stockcode DESC
			limit 0,1
		") or die(mysql_error());

		if(mysql_num_rows($result)>=1){

			$r=mysql_fetch_assoc($result);

			$last_stockcode = $r['stockcode'];

			$category_code = $this->attr_Category($categ_id1,'category_code');
			$trimmed_category_code  = trim($category_code,0);

			$last_num = explode($trimmed_category_code,$last_stockcode);

			$pad_len = strlen($last_num[1]);

			$inc_last_num = $last_num[1] + 1;

			$new_pad = str_pad($inc_last_num,$pad_len,0,STR_PAD_LEFT);

			$new_stockcode = $trimmed_category_code.$new_pad;
		}else{
			$category_code = $this->attr_Category($categ_id1,'category_code');
			$trimmed_category_code  = trim($category_code,0);

			$stock_num = 1;

			$last_stockcode = $r['stockcode'];

			$last_num = explode($trimmed_category_code,$category_code);

			$pad_len = strlen($last_num[1]);

			$new_pad = str_pad($stock_num,$pad_len,0,STR_PAD_LEFT);

			$new_stockcode = $trimmed_category_code.$new_pad;
		}

		return $new_stockcode;
	}



		function getMaterialName($id)
		{
			$query="select * from productmaster where stock_id='$id'";
			$result=mysql_query($query) or die(mysql_error());
			$row=mysql_fetch_assoc($result);
			return $row[stock];
		}

		function getMaterialOptions($id=NULL,$name='material')
		{
			$content="
				<select name='$name' id='$name'>
			";

			$query="select * from productmaster where type='RM' order by stock";
			$result=mysql_query($query);
			$content.="
				<option value=''>Select Material:</option>
			";
			while($row=mysql_fetch_assoc($result))
			{
				if($id==$row[stock_id])
				{
					$content.="
						<option value='$row[stock_id]' selected='selected'>$row[stock]</option>
					";
				}else{
					$content.="
						<option value='$row[stock_id]' >$row[stock]</option>
					";
				}
			}
			$content.="
				</select>
			";
			return $content;

		}

		function getCostOfStock($stock_id){
			$query="
				select
					*
				from
					productmaster
				where
					stock_id='$stock_id'
			";
			$result=mysql_query($query);
			$r=mysql_fetch_assoc($result);
			return $r[cost];
		}
		function getPrice1OfStock($stock_id){
			$query="
				select
					*
				from
					productmaster
				where
					stock_id='$stock_id'
			";
			$result=mysql_query($query);
			$r=mysql_fetch_assoc($result);
			return $r[price1];
		}

		function getStockCode($stock_id){
			$query="
				select
					*
				from
					productmaster
				where
					stock_id='$stock_id'
			";
			$result=mysql_query($query);
			$r=mysql_fetch_assoc($result);
			return $r[stockcode];
		}


		function getStockType($stock_id){
			$query="
				select
					*
				from
					productmaster
				where
					stock_id='$stock_id'
			";
			$result=mysql_query($query);
			$r=mysql_fetch_assoc($result);
			return $r[type];
		}

		function getAllMaterialOptions($id=NULL,$name='stock_id',$exclude=NULL,$js=NULL)
		{
			$content="
				<select name='$name' id='$name' style='width:300px;' $js >
			";

			$query="select * from productmaster";

			if(!empty($exclude)){
				$query.=" where type!='$exclude' ";
			}

			$query.=" order by stock";
			$result=mysql_query($query);
			$content.="
				<option value=''>Select Stock : </option>
			";
			while($row=mysql_fetch_assoc($result))
			{
				if($id==$row[stock_id])
				{
					$content.="
						<option value='$row[stock_id]' selected='selected'>$row[stock]</option>
					";
				}else{
					$content.="
						<option value='$row[stock_id]' >$row[stock]</option>
					";
				}
			}
			$content.="
				</select>
			";
			return $content;

		}

		function getStockOptions($id=NULL,$name='stock_id',$js=NULL)
		{
			$content="
				<select name='$name' id='$name' style='width:300px;' $js >
			";

			$query="select * from productmaster order by stock  ASC";

			$result=mysql_query($query) or die(mysql_error());
			$content.="
				<option value=''>Select Stock : </option>
			";
			while($row=mysql_fetch_assoc($result))
			{
				if($id==$row[stock_id])
				{
					$content.="
						<option value='$row[stock_id]' selected='selected'>$row[stock]</option>
					";
				}else{
					$content.="
						<option value='$row[stock_id]' >$row[stock]</option>
					";
				}
			}
			$content.="
				</select>
			";
			return $content;

		}

		function getProductMasterLatestAudit()
		{
			$query="select * from productmaster order by stock_id DESC";
			$result=mysql_query($query) or die(mysql_error());
			$row=mysql_fetch_assoc($result);

			return $row[audit];

		}

		function getTypeName($value)
		{
			$type=array('RM'=>'Raw Materials', 'FR'=>'Finished Raw', 'FP'=>'Finished Product/Package', 'PM' => 'Packaging Material' ,'NI' => 'None Inventory' );
			return $type[$value];

		}


		function getBANK($id=NULL,$exclude=NULL)
		{
			$type=array('1'=>'BDO', '2'=>'CHINA BANK', '3' => 'METROBANK', '4' => 'PNB' );
			$content="
				<select name='printing_type'>
					<option value=''>Select Printing Type:</option>
			";

			foreach($type as $key=>$value):
				if($exclude!=$key):
					if($id==$key){
						$content.="
							<option value='$key' selected='selected'>$value</option>
						";
					}else{
						$content.="
							<option value='$key'>$value</option>
						";
					}

				endif;
			endforeach;

			$content.="</select>";

			return $content;
		}

        function getTypeOptions($id=NULL,$exclude=NULL)
		{
			$type=array('RM'=>'Raw Materials', 'FP'=>'Finished Product/Package', 'PM' => 'Packaging Material' ,'NI' => 'None Inventory' );
			$content="
				<select name='type'>
					<option value=''>Select Type:</option>
			";

			foreach($type as $key=>$value):
				if($exclude!=$key):
					if($id==$key){
						$content.="
							<option value='$key' selected='selected'>$value</option>
						";
					}else{
						$content.="
							<option value='$key'>$value</option>
						";
					}

				endif;
			endforeach;

			$content.="</select>";

			return $content;
		}

		function getTypeOptionsEdit($value)
		{
			$type=array('RM'=>'Raw Materials', 'FR'=>'Finished Raw', 'FP'=>'Finished Product/Package', 'PM' => 'Packaging Material' ,'NI' => 'None Inventory' );
			$content="
				<select name='type'>
					<option value=''></option>
			";

			foreach($type as $key=>$desc)
			{
				if($value==$key)
				{
					$content.="
						<option value='$key' selected=selected >$desc</option>
					";
				}
				else
				{
					$content.="
						<option value='$key' >$desc</option>
					";
				}
			}

			$content.="
				</select>
			";

			return $content;
		}

		function getStatusOptions($value=NULL)
		{
			$status=array('S'=>'Saved','D'=>'Disabled');
			$content="
				<select name='status'>
			";

			foreach($status as $key=>$keyValue)
			{
				if($value==$key)
				{
					$content.="
						<option value=$key selected='selected'>$keyValue</option>
					";
				}
				else
				{
					$content.="
						<option value=$key>$keyValue</option>
					";
				}

			}
			$content.="
				</select>
			";

			return $content;

		}

		function getStatusName($status)
		{
			$content=array('S'=>'Saved', 'D'=>'Disabled');
			return $content[$status];

		}

		function getCategoryOptions()
		{
			$query="select * from categories where level='1'";
			$result=mysql_query($query);

			$content="
				<select name='categ_id1' id='categ_id1' onchange=xajax_getCategory2Options(xajax.getFormValues('newareaform')) >
					<option value=''></option>
			";


			while($row=mysql_fetch_assoc($result))
			{
				$content.="
					<option value='$row[categ_id]'>$row[category]</option>";
			}

			$content.="
				</select>";

			$content.="
				<select name='categ_id2' id='categ_id2' onchange=xajax_getCategory3Options(xajax.getFormValues('newareaform')) >
				</select>";
			$content.="
				<select name='categ_id3' id='categ_id3' onchange=xajax_getCategory4Options(xajax.getFormValues('newareaform')) >
				</select>";
			$content.="
				<select name='categ_id4' id='categ_id4'>
				</select>";
			return $content;
		}

		function getCategoryOptionsEdit($cat1,$cat2,$cat3,$cat4)
		{
			$content=$this->getCategory1OptionsEdit($cat1).$this->getCategory2OptionsEdit($cat2).$this->getCategory3OptionsEdit($cat3).$this->getCategory4OptionsEdit($cat4);
			return $content;
		}

		function getCategory1OptionsEdit($value)
		{
			$query="select * from categories where level='1'";
			$result=mysql_query($query);

			$content="
				<select name='categ_id1' id='categ_id1' onchange=xajax_getCategory2Options(xajax.getFormValues('newareaform')) >
					<option value=''></option>
			";

			while($row=mysql_fetch_assoc($result))
			{
				if($value==$row[categ_id])
				{
					$content.="
						<option value='$row[categ_id]' selected=selected >$row[category]</option>";
				}
				else
				{

					$content.="
					<option value='$row[categ_id]'>$row[category]</option>";
				}
			}

			$content.="
				</select>";

			return $content;
		}
		function getCategory2OptionsEdit($value)
		{
			$query="select * from categories where level='2'";
			$result=mysql_query($query);

			$content="
				<select name='categ_id2' id='categ_id2' onchange=xajax_getCategory3Options(xajax.getFormValues('newareaform')) >
					<option value=''></option>
			";


			while($row=mysql_fetch_assoc($result))
			{
				if($value==$row[categ_id])
				{
					$content.="
						<option value='$row[categ_id]' selected=selected >$row[category]</option>";
				}
				else
				{

					$content.="
					<option value='$row[categ_id]'>$row[category]</option>";
				}
			}

			$content.="
				</select>";

			return $content;
		}
		
		function getCategoryHE($value)
		{
			$query="select * from categories where level='2' AND subcateg_id = '25'";
			$result=mysql_query($query);

			$content="
				<select name='categ_id2' id='categ_id2' onchange=xajax_getCategory3Options(xajax.getFormValues('newareaform')) >
					<option value=''></option>
			";


			while($row=mysql_fetch_assoc($result))
			{
				if($value==$row[categ_id])
				{
					$content.="
						<option value='$row[categ_id]' selected=selected >$row[category]</option>";
				}
				else
				{

					$content.="
					<option value='$row[categ_id]'>$row[category]</option>";
				}
			}

			$content.="
				</select>";

			return $content;
		}
		function getCategory3OptionsEdit($value)
		{
			$query="select * from categories where level='3'";
			$result=mysql_query($query);

			$content="
				<select name='categ_id3' id='categ_id3' onchange=xajax_getCategory4Options(xajax.getFormValues('newareaform')) >
					<option value=''></option>
			";


			while($row=mysql_fetch_assoc($result))
			{
				if($value==$row[categ_id])
				{
					$content.="
						<option value='$row[categ_id]' selected=selected >$row[category]</option>";
				}
				else
				{

					$content.="
					<option value='$row[categ_id]'>$row[category]</option>";
				}
			}

			$content.="
				</select>";

			return $content;
		}
		function getCategory4OptionsEdit($value)
		{
			$query="select * from categories where level='4'";
			$result=mysql_query($query);

			$content="
				<select name='categ_id4' id='categ_id4'  >
					<option value=''></option>
			";


			while($row=mysql_fetch_assoc($result))
			{
				if($value==$row[categ_id])
				{
					$content.="
						<option value='$row[categ_id]' selected=selected >$row[category]</option>";
				}
				else
				{

					$content.="
					<option value='$row[categ_id]'>$row[category]</option>";
				}
			}

			$content.="
				</select>";

			return $content;
		}
		/*CATEGORIES OPTIONS*/
		function getLevelOptions()
		{
			$content="
				<select name='level'>
					<option value=1 >1</option>
					<option value=2 >2</option>
					<option value=3 >3</option>
					<option value=4 >4</option>
				</select>
			";
			return $content;
		}
		function getLevelOptionEdit($level)
		{
			$levelArray=array(1,2,3,4);
			$content="
				<select name='level'>
			";
			foreach($levelArray as $key)
			{
				if($key==$level)
				{
					$content.="
						<option value=$key selected='selected'>$key</option>
					";
				}
				else
				{
					$content.="
						<option value=$key>$key</option>
					";
				}

			}

			$content.="
				</select>
			";
			return $content;
		}

		function getSubCategoryOptions()
		{
			$content="
				<select name='subcateg_id' >
			";
			$query="select * from categories order by level asc, category asc";
			$result=mysql_query($query);
			$content.="
				<option value=''></option>
				<option value='0'>No Subcategory</option>
			";
			while($row=mysql_fetch_assoc($result))
			{
				$content.="
					<option value='$row[categ_id]' >(Level $row[level]) - $row[category]</option>
				";
			}
			$content.="
				</select>
			";
			return $content;
		}

		function getSubCategoryOptionsEdit($subcateg_id)
		{
			$content="
				<select name=subcateg_id >
			";
			$query="select * from categories order by level asc, category asc";
			$result=mysql_query($query);
			$content.="
				<option value=''></option>";

			if(empty($subcateg_id))
			{
				$content.="
					<option value='0' selected=selected>No Subcategory</option>
				";
			}

			while($row=mysql_fetch_assoc($result))
			{
				if($subcateg_id==$row[categ_id])
				{
					$content.="
					<option value='$row[categ_id]' selected='selected' >(Level $row[level]) - $row[category]</option>
					";

				}


				else
				{
					$content.="
					<option value='$row[categ_id]' >(Level $row[level]) - $row[category]</option>
					";

				}
			}
			$content.="
				</select>
			";
			return $content;
		}

		/*PURCHASE_HEADER OPTIONS*/
		function getpaymentTypeOptions()
		{
			$content="
				<select name='payment_type'>
					<option value=''></option>
					<option value='A'>Cash</option>
					<option value='H'>Charge</option>
				</select>
			";
			return $content;
		}

		function getPurchaseHeaderStatusOptions()
		{
			$content="
				<select name='status'>
					<option value=''></option>
					<option value='S'>Saved</option>
					<option value='C'>Cancelled</option>
					<option value='P'>Printed</option>
				</select>
			";
			return $content;
		}

		/*DISPLAYS*/

		function getCategory($subcateg_id)
		{
			$query="select * from categories where categ_id='$subcateg_id'";
			$result=mysql_query($query);
			$row=mysql_fetch_assoc($result);

			return $row[category];
		}

		/*SUPPLIERS FOR PRODUCT MASTER*/
		function getSupplierOptions($selected_id=NULL)
		{
			$content="
				<select name='supplier_id' >
			";

			$query="select * from supplier order by account asc";
			$result=mysql_query($query);
			$content.="
				<option value=''></option>
			";
			while($row=mysql_fetch_assoc($result))
			{
				$selected=($row[account_id]==$selected_id)?"selected='selected'":"";
				$content.="
					<option value='$row[account_id]' $selected >$row[account]</option>
				";
			}
			$content.="
				</select>
			";
			return $content;
		}
		function getSupplierOptionsEdit($id)
		{
			$content="
				<select name='supplier_id' >
			";

			$query="select * from supplier order by account asc";
			$result=mysql_query($query);
			$content.="
				<option value=''></option>
			";
			while($row=mysql_fetch_assoc($result))
			{
				if($row[account_id]==$id)
				{
					$content.="
						<option value='$row[account_id]' selected='selected'>$row[account]</option>
					";
				}
				else
				{
					$content.="
						<option value='$row[account_id]' >$row[account]</option>
					";
				}
			}
			$content.="
				</select>
			";
			return $content;
		}

		function getFormulationTypeOptions($id=NULL,$name='type')
		{
			$arrayType=array("Micro","Macro");

			$content="
				<select name='$name' id='$name'>
			";
			foreach($arrayType as $key)
			{
				if($key==$id)
				{
					$content.="<option value='$key' selected='selected'>$key</option>";
				}else{
					$content.="<option value='$key'>$key</option>";
				}
			}

			$content.="</select>
			";

			return $content;
		}

		function getFormulationTypeOptionsWithJs($id=NULL)
		{
			$arrayType=array("Micro","Macro");

			$content="
				<select id='formulationtype' onchange='xajax_display_formulationdetail($formulation_id)'>
			";
			foreach($arrayType as $key)
			{
				if($key==$id)
				{
					$content.="<option value='$key' selected='selected'>$key</option>";
				}else{
					$content.="<option value='$key'>$key</option>";
				}
			}

			$content.="</select>
			";

			return $content;
		}

		function getAccountOptions($id=NULL,$name='customername')
		{
			$content="
				<select name='$name'>
			";

			$query="select * from account order by account";
			$result=mysql_query($query);
			$content.="
				<option value='0'>All Customers</option>
			";

			while($row=mysql_fetch_assoc($result))
			{
				if($id==$row[account_id])
				{
					$content.="
						<option value='$row[account_id]' selected='selected' >$row[account]</option>
					";
				}else{
					$content.="
						<option value='$row[account_id]' >$row[account]</option>
					";
				}

			}
			$content.="
				</select>
			";
			return $content;

		}

		function getSpecificAccountOptions($id=NULL,$name='account_id')
		{
			$content="
				<select name='$name' id='$name' class='required' title='Please Select Account'>
			";

			$query="select * from account order by account";
			$result=mysql_query($query);
			$content.="
				<option value=''>Select Customer:</option>
			";

			while($row=mysql_fetch_assoc($result))
			{
				if($id==$row[account_id])
				{
					$content.="
						<option value='$row[account_id]' selected='selected' >$row[account]</option>
					";
				}else{
					$content.="
						<option value='$row[account_id]' >$row[account]</option>
					";
				}

			}
			$content.="
				</select>
			";
			return $content;

		}

		function getSpecificSupplierOptions($id=NULL,$name='account_id')
		{
			$content="
				<select name='$name' id='$name' class='required' title='Please Select Supplier'>
			";

			$query="select * from supplier order by account";
			$result=mysql_query($query);
			$content.="
				<option value=''>Select Supplier:</option>
			";

			while($row=mysql_fetch_assoc($result))
			{
				if($id==$row[account_id])
				{
					$content.="
						<option value='$row[account_id]' selected='selected' >$row[account]</option>
					";
				}else{
					$content.="
						<option value='$row[account_id]' >$row[account]</option>
					";
				}

			}
			$content.="
				</select>
			";
			return $content;
		}

		function getAccountFormulationOptions($id=NULL)
		{
			$content="
				<select name='keyword'>
			";

			$query="select * from account order by account";
			$result=mysql_query($query);
			$content.="
				<option value=''>All Customers</option>
			";

			while($row=mysql_fetch_assoc($result))
			{
				if($id==$row[account_id])
				{
					$content.="
						<option value='$row[account_id]' selected='selected' >$row[account]</option>
					";
				}else{
					$content.="
						<option value='$row[account_id]' >$row[account]</option>
					";
				}

			}
			$content.="
				</select>
			";
			return $content;

		}

		function getAccountName($id)
		{
			$query="select * from account where account_id='$id'";
			$result=mysql_query($query);

			$row=mysql_fetch_assoc($result);
			if($id){
				return $row[account];
			}else{
				return "All Customers";
			}
		}

		function getAccountAddress($id)
		{
			$query="select * from account where account_id='$id'";
			$result=mysql_query($query);

			$row=mysql_fetch_assoc($result);
			return $row[address];

		}

		function getSupplierName($id)
		{
			$query="select * from supplier where account_id='$id'";
			$result=mysql_query($query);

			$row=mysql_fetch_assoc($result);
			if($id){
				return $row[account];
			}else{
				return "All Suppliers";
			}
		}


		function getAllCategoryOptions($id=NULL,$name='category')
		{
			$content="
				<select name='$name' >
			";
			$query="select * from categories order by level asc, category asc";
			$result=mysql_query($query);
			$content.="
				<option value=''>Select Category:</option>
			";
			$selectcontent='';
			while($row=mysql_fetch_assoc($result))
			{
				$level=$row[level];
				$categ_id=$row[categ_id];
				$category=$row[category];
				$subcateg_id=$row[subcateg_id];

				$selectcontent="$row[category]";

				if($level==1){

					if($id==$categ_id){
						$content.="
							<option value='$categ_id' selected='selected'>$category</option>
						";
					}else{
						$content.="
							<option value='$categ_id'>$category</option>
						";
					}
				}
				else{

					while($level!=1){
						$query="
							select
								*
							from
								categories
							where categ_id='$subcateg_id'
						";

						$result2=mysql_query($query);
						$r=mysql_fetch_assoc($result2);

						$level=$r[level];
						$category=$r[category];
						$subcateg_id=$r[subcateg_id];
						$selectcontent="$category - $selectcontent";
					}
					if($id==$categ_id){
						$content.="
							<option value='$categ_id' selected='selected'>$selectcontent</option>
						";
					}else{
						$content.="
							<option value='$categ_id'>$selectcontent</option>
						";
					}
				}
			}
			$content.="
				</select>
			";
			return $content;
		}

		function getCategoryName($id=NULL){
			$query="
				select
					category
				from
					categories
				where
					categ_id='$id'
			";
			$result=mysql_query($query);
			$r=mysql_fetch_assoc($result);

			return $r[category];
		}


		function getCategoryNameWithLevel($id=NULL)
		{
			$query="select * from categories order by level asc, category asc";
			$result=mysql_query($query);
			$selectcontent='';
			while($row=mysql_fetch_assoc($result))
			{
				$level=$row[level];
				$categ_id=$row[categ_id];
				$category=$row[category];
				$subcateg_id=$row[subcateg_id];

				$selectcontent="$row[category]";

				if($level==1){

					if($id==$categ_id){
						$content.="
							$category
						";
					}
				}
				else{

					while($level!=1){
						$query="
							select
								*
							from
								categories
							where categ_id='$subcateg_id'
						";

						$result2=mysql_query($query);
						$r=mysql_fetch_assoc($result2);

						$level=$r[level];
						$category=$r[category];
						$subcateg_id=$r[subcateg_id];
						$selectcontent="$category - $selectcontent";
					}
					if($id==$categ_id){
						$content.="
							$selectcontent
						";
					}
				}
			}

			return $content;
		}


		function getAllCategoryFilterOptions($id=NULL)
		{
			$content="
				<select name='keyword' >
			";
			$query="select * from categories order by level asc, category asc";
			$result=mysql_query($query);
			$content.="
				<option value=''>All Category</option>
			";
			while($row=mysql_fetch_assoc($result))
			{
				if($id==$row[categ_id]){
					$content.="
						<option value='$row[categ_id]' selected='selected' >(Level $row[level]) - $row[category]</option>
					";
				}else{
					$content.="
						<option value='$row[categ_id]' >(Level $row[level]) - $row[category]</option>
					";
				}
			}
			$content.="
				</select>
			";
			return $content;
		}
		
		function getAllCategoryFilterOptions2($id=NULL)
		{
			$content="
				<select name='categorykeyword' >
			";
			$query="select * from categories order by level asc, category asc";
			$result=mysql_query($query);
			$content.="
				<option value=''>All Category</option>
			";
			while($row=mysql_fetch_assoc($result))
			{
				if($id==$row[categ_id]){
					$content.="
						<option value='$row[categ_id]' selected='selected' >(Level $row[level]) - $row[category]</option>
					";
				}else{
					$content.="
						<option value='$row[categ_id]' >(Level $row[level]) - $row[category]</option>
					";
				}
			}
			$content.="
				</select>
			";
			return $content;
		}

		function getAllCategoryName($id)
		{
			$query="select * from categories where categ_id='$id'";
			$result=mysql_query($query);
			$r=mysql_fetch_assoc($result);
			$content="(Level $r[level] ) - $r[category]";
			return $content;
		}
		function getFormulationFilterOptions($formulationfilter)
		{
			$options=array( 'formulationcode'=>'Formulation Code','customername' => 'Customer Name', 'category' => 'Category');
			$content="
				<select name='formulationfilter' onchange='xajax_changekeywordfield(this.value);'>
			";
			foreach($options as $value => $display)
			{
				if($value==$formulationfilter){
					$content.="
						<option value='".$value."' selected='selected'>".$display."</option>
					";
				}else{
					$content.="
						<option value='".$value."'>".$display."</option>
					";
				}
			}
			$content.="
				</select>
			";

			return $content;
		}
		function getFinishedProductOptions($id=NULL,$name='finishedproduct',$required='required')
		{
			$content="
				<select name='$name' id='$name' class='$required' title='Please select Finished Product' >
			";
			$query="select * from productmaster where type='FP' order by stock asc";
			$result=mysql_query($query);
			$content.="
				<option value=''>Select Finished Product:</option>
			";
			while($row=mysql_fetch_assoc($result))
			{
				if($id==$row[stock_id]){
					$content.="
						<option value='$row[stock_id]' selected='selected' >$row[stock]</option>
					";
				}else{
					$content.="
						<option value='$row[stock_id]' >$row[stock]</option>
					";
				}
			}
			$content.="
				</select>
			";
			return $content;
		}

		function orderCategory($categ_id1=NULL,$categ_id2=NULL,$categ_id3=NULL,$categ_id4=NULL){
			if(empty($categ_id1)){
				return "no category";
			}else{
				$category=$this->getCategoryName($categ_id1);
				if(empty($categ_id2)){
					return $category;
				}else{
					$category.="-".$this->getCategoryName($categ_id2);
					if(empty($categ_id3)){
						return $category;
					}else{
						$category.="-".$this->getCategoryName($categ_id3);
						if(empty($categ_id4)){
							return $category;
						}else{
							$category.="-".$this->getCategoryName($categ_id4);
							return $category;
						}
					}
				}
			}
		}

		function getFinishedProductOptionsForJO($id=NULL)
		{
			$content="
				<select name='finishedproduct' id='finishedproduct' <!--onchange='xajax_jofinishedproductonchange(this.value);--> '>
			";
			$query="select * from productmaster where type='FP' order by stock asc";
			$result=mysql_query($query);
			$content.="
				<option value=''>Select Finished Product:</option>
			";
			while($row=mysql_fetch_assoc($result))
			{

				if($id==$row[stock_id]){
					$content.="
						<option value='$row[stock_id]' selected='selected' >$row[stock] (".$this->orderCategory($row[categ_id1],$row[categ_id2],$row[categ_id3],$row[categ_id4]).")</option>
					";
				}else{
					$content.="
						<option value='$row[stock_id]' >$row[stock] (".$this->orderCategory($row[categ_id1],$row[categ_id2],$row[categ_id3],$row[categ_id4]).")</option>
					";
				}
			}
			$content.="
				</select>
			";
			return $content;
		}

		function getFormulationOptions($id=NULL,$name='formulation_id',$js=NULL){
			$query="
				select * from formulation_header
			";
			$result=mysql_query($query);

			$content="
				<select name='$name' onchange='$js'>
					<option value=''>Select Formulation:</option>
			";
			while($r=mysql_fetch_assoc($result)){
				if($id==$r[formulation_id]){
					$content.="
						<option value='$r[formulation_id]' selected='selected'>$r[formulationcode]</option>
					";
				}else{
					$content.="
						<option value='$r[formulation_id]'>$r[formulationcode]</option>
					";
				}
			}
			$content.="
				</select>
			";
			return $content;

		}

		function getFormulationCode($id){
			$query="
				select
					*
				from
					formulation_header
				where
					formulation_id='$id'
			";
			$result=mysql_query($query);
			$r=mysql_fetch_assoc($result);
			return $r[formulationcode];
		}

		function getAllLocationOptions($id=NULL,$name='locale_id'){
			$query="
				select
					*
				from
					location
			";
			$result=mysql_query($query);
			$content="
				<select name='$name' id='$name' class='required' title='Please select a Location'>
					<option value=''>Location : </option>
			";
			while($r=mysql_fetch_assoc($result)){

				if($r[locale_id]==$id){
					$content.="
						<option value='$r[locale_id]' selected='selected'>$r[location]</option>
					";
				}else{
					$content.="
						<option value='$r[locale_id]'>$r[location]</option>
					";
				}
			}

			$content.="
				</select>
			";
			return $content;
		}
		function getLocationName($id){
			if ($id ==0)
				return 'All Locations';
			else
			{
			$query="
				select
					*
				from
					location
				where
					locale_id='$id'
			";
			$result=mysql_query($query);
			$r=mysql_fetch_assoc($result);
			return $r[location];
			}
		}






		function getPayTypeOptions($selectedid=NULL,$name='paytype'){
			$type=array("Cash"=>"C","Charge"=>"H" );
			$content="
				<select name='$name' id='$name' title='Please Enter Payment Type' class='required'>
					<option value=''>Select Payment Type:</option>
			";

			foreach($type as $key=>$value){

				if($selectedid==$value){
					$content.="
						<option value='$value' selected='selected'>$key</option>
					";
				}else{
					$content.="
						<option value='$value'>$key</option>
					";
				}
			}
			$content.="
				</select>
			";

			return $content;
		}

		function getPayTypeName($selectedid=NULL){
			$type=array("Cash"=>"C","Charge"=>"H" );
			foreach($type as $key=>$value){
				if($selectedid==$value){
					return $key;
				}
			}
			return $content;
		}


		function getTransactionStatus($selectedid=NULL,$name='status'){
			$type=array("Saved"=>"S","Printed"=>"P","Cancelled"=>"C","Finished" => "F" );
			$content="
				<select name='$name' id='$name' title='Please Enter Status' class='required'>
					<option value=''>Select Status :</option>
			";

			foreach($type as $key=>$value){

				if($selectedid==$value){
					$content.="
						<option value='$value' selected='selected'>$key</option>
					";
				}else{
					$content.="
						<option value='$value'>$key</option>
					";
				}
			}
			$content.="
				</select>
			";
			return $content;
		}
		
		function getProjectStatus($selectedid=NULL,$name='status'){
			$type=array("F"=>"Finished","O"=>"Ongoing","Fu"=>"Future");
			$content="
				<select name='$name' id='$name' title='Please Enter Status' class='required'>
					<option value=''>Select Status :</option>
			";

			foreach($type as $key=>$value){

				if($selectedid==$key){
					$content.="
						<option value='$key' selected='selected'>$value</option>
					";
				}else{
					$content.="
						<option value='$key'>$value</option>
					";
				}
			}
			$content.="
				</select>
			";
			return $content;
		}

		function getTransactionStatusName($selectedid=NULL){
			$type=array("Saved"=>"S","Printed"=>"P","Cancelled"=>"C" ,"Finished" => "F");
			foreach($type as $key=>$value){
				if($selectedid==$value){
					return $key;
				}
			}
			return $content;
		}
		
		function getProjectStatusName($selectedid=NULL){
			$type=array("F"=>"Finished","O"=>"Ongoing","Fu"=>"Future");
			foreach($type as $key=>$value){
				if($selectedid==$key){
					return $value;
				}
			}
			return $content;
		}
		function getVattype($selectedid=NULL,$name='vat_type'){
			$type=array("VAT"=>"VATABLE","NONVAT"=>"NON-VATABLE");
			$content="
				<select name='$name' id='$name' title='Please Select Vat' class='required'>
					<option value=''>Select Vat-Type :</option>
			";

			foreach($type as $key=>$value){

				if($selectedid==$key){
					$content.="
						<option value='$key' selected='selected'>$value</option>
					";
				}else{
					$content.="
						<option value='$key'>$value</option>
					";
				}
			}
			$content.="
				</select>
			";
			return $content;
		}




		function getFormulationTable($formulation_header_id,$dialog=FALSE){
			$result=mysql_query("
				select
					status
				from
					formulation_header
				where
					formulation_header_id='$formulation_header_id'
			") or die(mysql_error());

			$r=mysql_fetch_assoc($result);
			$status = $r[status];


			$content='
				<table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
					<tr bgcolor="#C0C0C0">
						<th width="20"><b>#</b></th>
						<th width="20"></th>
						<th><b>Item</b></th>
						<th><b>Quantity</b></th>
					</tr>
				';

				$query="
					select
						*
					from
						formulation_details
					where
						formulation_header_id='$formulation_header_id'
				";

				$result=mysql_query($query);

				$i=1;
				while($r=mysql_fetch_assoc($result)):
					$formulation_detail_id 		= $r[formulation_detail_id];
					$stock_id					= $r[stock_id];
					$quantity					= $r[quantity];

					if(!$dialog){
						$onclick_js="
							xajax_removeFormulationDetail('$formulation_detail_id','$formulation_header_id');
						";
					}else{
						$onclick_js="
							xajax_removeFormulationDetailPM('$formulation_detail_id','$formulation_header_id');
						";
					}


					$content.='
						<tr>
							<td>'.$i++.'</td>
							<td><img src="images/trash.gif" style="cursor:pointer;" onclick="'.$onclick_js.'"  /></td>
							<td>'.$this->getMaterialName($r[stock_id]).'</td>
							<td><div align="right"><input type="text" class="textbox3" name="quantity[]" value="'.$r[quantity].'" ></div></td>
							<input type="hidden" name="formulation_detail_id[]" value="'.$formulation_detail_id.'" >
						</tr>
					';
				endwhile;

				$content.='
					</table>
				';

				return $content;
		}

		function getAccountDetails($account_id)
		{
			$query="
				select
					*
				from
					account
				where
					account_id='$account_id'
			";
			$result=mysql_query($query);
			$r=mysql_fetch_assoc($result);
			return $r;

		}

		function getCheckStatusOptions($selected_id=NULL,$name='checkstatus'){
			$trans=array(
						"Post Dated",
						"Cleared",
						"Replaced",
						"Replacement",
						"Bounced",
						"Cashed"
					);

			$content="
				<select name='$name' id='$name'>
					<option value=''>Select Check Status: </option>
			";

			foreach($trans as $key):
				if($selected_id==$key){
					$content.="
						<option value='$key' selected='selected'>$key</option>
					";
				}else{
					$content.="
						<option value='$key'>$key</option>
					";
				}
			endforeach;

			$content.="
				</select>
			";

			return $content;
		}


		function cancelGL($id,$header,$journal_code){
			$result=mysql_query("
				select
					*
				from
					posted_headers
				where
					header_id='$id'
				and
					header='$header'
				and
					journal_code='$journal_code'
			") or die(mysql_error());

			while($r=mysql_fetch_assoc($result)){
				$gltran_header_id = $r[gltran_header_id];
				mysql_query("
					update
						gltran_header
					set
						status='C'
					where
						gltran_header_id='$gltran_header_id'
				");
			}
		}

		function getAccountBalance($account_id,$date){

			$result=mysql_query("
				select
					sum(netamount) as netamount
				from
					dr_header
				where
					account_id='$account_id'
				and
					date<='$date'
				and
					status!='C'
			");
			$r=mysql_fetch_assoc($result);
			$netamount=$r[netamount];


			$result=mysql_query("
					select
						total_amount
					from
						pay_header
					where
						account_id='$account_id'
					and
						status!='C'
					and
						date <= '$date'
				");
			$r=mysql_fetch_assoc($result);
			$total_amount=$r[total_amount];

			return $netamount  - $total_amount;
		}

		function getAccountBalanceForAR($account_id,$date){
			/*
				PAYMENT DETAILS

			$query="
				SELECT
					netamount
				FROM
					payment_detail,pay_header
				WHERE
					pay_header.pay_header_id=payment_detail.payment_header_id
				and
					status!='C'
				and
					account_id='$account_id'
				and
					date<='$date'
			";
			$amount=0;
			$netamount=0;

			$result=mysql_query($query);
			while($r=mysql_fetch_assoc($result)):
				$netamount+=$r[amount];
			endwhile;
			*/

			$query="
				SELECT
					sum(netamount)  as netamount
				FROM
					dr_header
				WHERE
					status!='C'
				and
					account_id='$account_id'
				and
					date<='$date'
				and
					paytype='H'
			";

			$result=mysql_query($query);
			$r=mysql_fetch_assoc($result);
			$netamount=$r[netamount];


			/*
				PAYED AMOUNT
			*/

			$query="
				SELECT
					*
				FROM
					pay_checks,pay_header
				WHERE
					pay_header.pay_header_id=pay_checks.pay_header_id
				and
					status!='C'
				and
					account_id='$account_id'
				and
					date<='$date'
			";

			$result=mysql_query($query);
			$checkamount=0;
			while($r=mysql_fetch_assoc($result)):
				$checkamount+=$r[checkamount];
			endwhile;

			$amount=$netamount-$checkamount;

			return $amount;

		}

		function getAccountBalanceBetweenDates($project_id,$startdate,$enddate){

			$result=mysql_query("
				select
					sum(amount) as amount
				from
					sales_invoice
				where
					project_id = '$project_id'
				and
					date between '$startdate' and '$enddate'
			");
			$r=mysql_fetch_assoc($result);

			$amount = $r['amount'];


			$result=mysql_query("
					select
						sum(checkamount) as checkamount
					from
						sales_invoice
					where
						project_id = '$project_id'
					and
						payment_date between '$startdate' and '$enddate'
				");
			$r=mysql_fetch_assoc($result);
			$checkamount = $r['checkamount'];

			return $amount - $checkamount;

		}

		function getAccountBalanceOnAndOverDate($project_id,$date){

			$result=mysql_query("
				select
					sum(amount) as amount
				from
					sales_invoice
				where
					project_id = '$project_id'
				and
					date <= '$date'
			");
			$r=mysql_fetch_assoc($result);

			$amount = $r['amount'];


			$result=mysql_query("
					select
						sum(checkamount) as checkamount
					from
						sales_invoice
					where
						project_id = '$project_id'
					and
						payment_date <= '$date'
				");
			$r=mysql_fetch_assoc($result);
			$checkamount = $r['checkamount'];

			return $amount - $checkamount;

		}

		function getDeliveryAmount($account_id,$startdate,$enddate){
			$query="
				select
					*
				from
					dr_header
				where
					date between '$startdate' and '$enddate'
				and
					status!='C'
				and
					account_id='$account_id'
			";

			$result=mysql_query($query);
			$netamount=0;
			while($r=mysql_fetch_assoc($result)):
				$netamount+=$r[netamount];
			endwhile;

			return $netamount;
		}

		function getBalanceForwarded($date,$project_id){

			$query="
				select
					sum(amount) as charges
				from
					sales_invoice
				where
					date < '$date'
				and
					project_id = '$project_id'
			";

			$result=mysql_query($query) or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			$charges 	= $r['charges'];

			$query="
				select
					sum(checkamount) as payments
				from
					sales_invoice
				where
					payment_date < '$date'
				and
					project_id = '$project_id'
			";

			$result=mysql_query($query) or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			$payments 	= $r['payments'];


			return $charges - $payments;

		}

		function getBalancePayableForwarded($date,$supplier_id){
			$result=mysql_query("
				select
					sum(total_amount) as total_amount
				from
					accounts_payable
				where
					due_date < '$date'
				AND
					supplier_id = '$supplier_id'
				and
					status!='C'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			$charges = $r['total_amount'];

			$result=mysql_query("
						select
							sum(amount) as amount
						FROM
							ap_payment
						where
							status!='C'
						and
							date < '$date'
						and
							supplier_id = '$supplier_id'
					") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			$payments = $r['amount'];


			return $charges-$payments;

		}

		function getPriceRequestOption($selectedid=NULL,$name='priceoption'){
			$array=array(
					"y" => "Yes",
					"n" => "No"
				);

			$content="";
			foreach($array as $key => $value){
				if($selectedid==$key){
					$content.="
						<input type='radio' name='$name' value='$key' checked='checked'> - $value
					";
				}else{
					$content.="
						<input type='radio' name='$name' value='$key'> - $value
					";
				}
			}

			return $content;
		}

		function getReturnOptionsFromDelivery($dr_header_id,$name='dr_return'){
			$content="
				<select name='$name' id='$name'>
					<option value=''>Select Materials:</option>
			";

			$query="
				select
					*
				from
					dr_detail
				where
					dr_header_id='$dr_header_id'
				and
					type='D'
			";
			$result=mysql_query($query);
			while($r=mysql_fetch_assoc($result)):
				$content.="
					<option value='$r[dr_detail_id]'>".$this->getMaterialName($r[stock_id])."</option>
				";
			endwhile;

			$content.="</select>";
			return $content;
		}
		function getStockFromDeliveryDetailID($dr_detail_id){
			$query="
				select
					stock_id
				from
					dr_detail
				where
					dr_detail_id='$dr_detail_id'
			";
			$result=mysql_query($query);
			$r=mysql_fetch_assoc($result);
			return $r[stock_id];
		}
		/****************************
			SALES
		*****************************/
		function getSalesReportOptions($selectedid=NULL,$name='reporttype'){

			$lists=array(
					"Summary" => "S",
					"Detailed" => "D"
				);

			$content="
				<select name='$name' id='$name' >
					<option value=''>Select Report Type:</option>
			";

			foreach($lists as $list => $value):
				$selected=($selectedid==$value)?"selected='selected'":"";
				$content.="
					<option value='$value' $selected >$list</option>
				";
			endforeach;


			$content.="
				</select>
			";

			return $content;
		}


		function getTotalSalesBetweenDate($fromdate,$todate){
			$query="
				select
					sum(total_amount) as total_amount
				from
					accounts_receivable
				where
					status != 'C'
				and
					date between '$fromdate' and '$todate'
			";

			$result = mysql_query($query) or die(mysql_error());
			$r=mysql_fetch_assoc($result);

			$totalamount = $r['total_amount'];


			return $totalamount;

		}

		function getSalesOfAccount($fromdate,$todate,$account_id,$locale_id){
			$query="
				select
					amount
				from
					dr_header,dr_detail
				where
					dr_header.dr_header_id=dr_detail.dr_header_id
				and
					date between '$fromdate' and '$todate'
				and
					status!='C'
				and
					type='D'
				and
					account_id='$account_id'
			";

			if(!empty($locale_id)){
				$query.="
					and locale_id='$locale_id'
				";
			}

			$totalamount=0;
			$result=mysql_query($query) or die(mysql_error);
			while($r=mysql_fetch_assoc($result)){
				$totalamount+=$r[amount];
			}

			return $totalamount;
		}

		function getTotalSalesPerDay($date)
		{

			$result = mysql_query("
				select
					sum(total_amount) as total_amount
				from
					accounts_receivable
				where
					status != 'C'
				and
					date = '$date'
			") or die(mysql_error());

			$r=mysql_fetch_assoc($result);
			$total_amount	= $r['total_amount'];

			return $total_amount;
		}

		function getTotalSalesOfItem($date,$stock_id,$amount_option=TRUE){

			$query="
				SELECT
					sum(quantity) as quantity,
					sum(amount) as amount
				FROM
					issuance_header as h, issuance_detail as d
				where
					h.issuance_header_id = d.issuance_header_id
				and
					status != 'C'
				and
					date = '$date'
				and
					stock_id = '$stock_id'
			";

			$result=mysql_query($query) or die(mysql_error);
			$r=mysql_fetch_assoc($result);
				$totalamount 	=$r['amount'];
				$totalquantity 	=$r['quantity'];



			if($amount_option){
				return $totalamount;
			}else{
				return $totalquantity;
			}
		}

		function getTotalSalesOfItemBetweenDate($fromdate,$todate,$stock_id,$locale_id){
			$query="
				SELECT
					dr_detail.amount
				FROM
					dr_detail,dr_header
				where
					dr_header.dr_header_id=dr_detail.dr_header_id
				and
					dr_header.date between '$fromdate' and '$todate'
				AND
					dr_header.status!='C'
				AND
					dr_detail.type='D'
				AND
					stock_id='$stock_id'
			";
			if(!empty($locale_id)){
				$query.="
					and locale_id='$locale_id'
				";
			}

			$totalamount=0;
			$result=mysql_query($query) or die(mysql_error);
			while($r=mysql_fetch_assoc($result)){
				$totalamount+=$r[amount];
			}

			$query="
				SELECT
					dr_detail.amount
				FROM
					dr_detail,dr_header
				where
					dr_header.dr_header_id=dr_detail.dr_header_id
				and
					dr_header.date between '$fromdate' and '$todate'
				AND
					dr_header.status!='C'
				AND
					dr_detail.type='R'
				AND
					stock_id='$stock_id'
			";
			if(!empty($locale_id)){
				$query.="
					and locale_id='$locale_id'
				";
			}

			$result=mysql_query($query) or die(mysql_error);
			while($r=mysql_fetch_assoc($result)){
				$totalamount-=$r[amount];
			}

			return $totalamount;
		}


		/****************************
			ACCOUNTING
		*****************************/
		function getClassificationOptions($selectedid=NULL,$name='mclass'){
			$classifications=array(
					'A' => 'ASSET',
					'L' => 'LIABILITIES',
					'I' => 'INCOME',
					'E' => 'EXPENSE',
					'R' => 'RETAINED EARNINGS'
				);

			$content="
				<select name='$name'>
					<option value=''>Select classifications:</option>
			";
			foreach($classifications as $key => $value):
				$selected=($key==$selectedid)?"selected='selected'":'';

				$content.="
					<option value='$key' $selected>$value</option>
				";
			endforeach;
			$content.="
				</select>
			";
			return $content;
		}

		function getClassificationName($selectedid=NULL){
			$classifications=array(
					'A' => 'ASSET',
					'L' => 'LIABILITIES',
					'I' => 'INCOME',
					'E' => 'EXPENSE',
					'R' => 'RETAINED EARNINGS'
				);

			foreach($classifications as $key => $value):
				if($selectedid==$key){
					return	$value;
				}
			endforeach;
		}


		function getSubClassificationOptions($selectedid=NULL,$name='sub_mclass'){
			$classifications=array(
					'INC' => 'SALES',
					'COG' => 'COST OF SALES',
					'EXP' => 'EXPENSES',
					'EOI' => 'OTHER INCOME',
					'TAX' => 'TAXATION',
					'BCA' => 'CURRENT ASSETS',
					'BFA' => 'FIXED ASSETS',
					'BLA' => 'LONG TERM ASSETS',
					'BCL' => 'CURRENT LIABILITIES',
					'BLL' => 'LONG TERM LIABITILITES',
					'BOL' => 'OTHER LIABILITIES',
					'BEQ' => 'EQUITY'
				);

			$content="
				<select name='$name'>
					<option value=''>Select classifications:</option>
			";
			foreach($classifications as $key => $value):
				$selected=($key==$selectedid)?"selected='selected'":'';

				$content.="
					<option value='$key' $selected>$value</option>
				";
			endforeach;
			$content.="
				</select>
			";
			return $content;
		}

		function getSubClassificationName($selectedid=NULL){
			$classifications=array(
					'INC' => 'SALES',
					'COG' => 'COST OF SALES',
					'EXP' => 'EXPENSES',
					'EOI' => 'OTHER INCOME',
					'TAX' => 'TAXATION',
					'BCA' => 'CURRENT ASSETS',
					'BFA' => 'FIXED ASSETS',
					'BLA' => 'LONG TERM ASSETS',
					'BCL' => 'CURRENT LIABILITIES',
					'BLL' => 'LONG TERM LIABITILITES',
					'BOL' => 'OTHER LIABILITIES',
					'BEQ' => 'EQUITY'
				);

			foreach($classifications as $key => $value):
				if($selectedid==$key){
					return	$value;
				}
			endforeach;
		}

		function getGLAccountOptions($selectedid=NULL,$name='account_id'){
			$account=explode('-',$selectedid);
			$type=$account[0];
			$account_id=$account[1];

			$content="
				<select name='$name' id='$name'>
					<option value=''>Select Account:</option>
			";

			$query="
				select
					*
				from
					projects
				order by project_name asc
			";
			$result=mysql_query($query);


			$content.="
				<option value='' style='font-weight:bold; border-top:1px solid #000; border-bottom:1px solid #000;'>PROJECTS</option>
			";
			while($r=mysql_fetch_assoc($result)):
			$selected=(($type == "p") && ($r[project_id]==$account_id))?"selected='selected'":"";
				$content.="
					<option style='margin-left:10px;'  value='p-$r[project_id]' $selected>$r[project_name] - $r[project_code] ( Project )</option>
				";

			endwhile;

			$query="
				select
					*
				from
					supplier
				order by account asc
			";
			$result=mysql_query($query);
			$content.="
				<option value='' style='font-weight:bold; border-top:1px solid #000; border-bottom:1px solid #000;'>SUPPLIERS</option>
			";
			while($r=mysql_fetch_assoc($result)):
			$selected=(($type == "s") && ($r[account_id]==$account_id))?"selected='selected'":"";

				$content.="
					<option style='margin-left:10px;'  value='s-$r[account_id]' $selected >$r[account] ( Supplier )</option>
				";

			endwhile;

			$query="
				select
					*
				from
					contractor
			";
			$result=mysql_query($query);
			while($r=mysql_fetch_assoc($result)):
			$selected=(($type == "c") && ($r[contractor_id]==$account_id))?"selected='selected'":"";

				$content.="
					<option value='c-$r[contractor_id]' $selected >$r[contractor] ( Contractor )</option>
				";

			endwhile;
			$content.="
				</select>
			";

			return $content;
		}

		function getGLAccountName($selectedid){
			$account=explode('-',$selectedid);
			$type=$account[0];
			$account_id=$account[1];

			$query="
				select
					*
				from
					projects
			";
			$result=mysql_query($query);

			while($r=mysql_fetch_assoc($result)):
				if(($type == "p") && ($r[project_id]==$account_id)){
					return "$r[project_name] - $r[project_code]";
				}
			endwhile;

			$query="
				select
					*
				from
					supplier
			";
			$result=mysql_query($query);
			while($r=mysql_fetch_assoc($result)):
				if(($type == "s") && ($r[account_id]==$account_id)){
					return $r[account];
				}

			endwhile;

			$query="
				select
					*
				from
					contractor
			";
			$result=mysql_query($query);
			while($r=mysql_fetch_assoc($result)):
				if(($type == "c") && ($r[contractor_id]==$account_id)){
					return $r[contractor];
				}

			endwhile;

		}

		function getJournalOptions($selectedid=NULL,$name='journal_id'){
			$content="
				<select name='$name' id='journal_id'>
					<option value=''>Select Journal:</option>
			";

			$query="
				select
					*
				from
					journal
				where
					enable='Y'
			";
			$result=mysql_query($query);
			while($r=mysql_fetch_assoc($result)):
				$selected=($selectedid==$r[journal_id])?"selected='selected'":"";

				$content.="
					<option value='$r[journal_id]' $selected >$r[journal]</option>
				";
			endwhile;

			$content.="
				</select>
			";

			return $content;
		}

		function getJournalName($selectedid){
			$query="
				select
					*
				from
					journal
				where
					enable='Y'
			";
			$result=mysql_query($query);
			while($r=mysql_fetch_assoc($result)):
				if($selectedid==$r[journal_id]){
					return $r[journal];
				}
			endwhile;

			return $content;
		}

		function getJournalCode($journal_id){
			$query="
				select
					journal_code
				from
					journal
				where
					journal_id='$journal_id'
			";
			$result=mysql_query($query);
			$r=mysql_fetch_assoc($result);

			return $r[journal_code];
		}

		function getJournalID($journal_code){
			$query="
				select
					journal_id
				from
					journal
				where
					journal_code='$journal_code'
			";
			$result=mysql_query($query);
			$r=mysql_fetch_assoc($result);

			return $r[journal_id];
		}

		function option_chart_of_accounts($selectedid=NULL,$name='gchart_id'){
			$content="
				<select name='$name' id='$name'>
					<option value=''>Select GChart:</option>
			";
			$query="
				select
					*
				from
					gchart
				where
					enable='Y' and
					gchart_void = '0'
				order by gchart asc
			";
			$result=mysql_query($query);
			while($r=mysql_fetch_assoc($result)):
				$select=($selectedid==$r[gchart_id])?"selected='selected'":"";
				$content.="
					<option value='$r[gchart_id]' $select >$r[gchart]</option>
				";
			endwhile;
			$content.="
				</select>
			";
			return $content;
		}

		function option_projects($selectedid=NULL,$name='project_id'){
			$content="
				<select name='$name' id='$name'>
					<option value=''>Select Project:</option>
			";
			$query="
				select
					*
				from
					projects
					order by project_name asc
			";
			$result=mysql_query($query);
			while($r=mysql_fetch_assoc($result)):
				$select=($selectedid==$r[project_id])?"selected='selected'":"";
				$content.="
					<option value='$r[project_id]' $select >$r[project_name]</option>
				";
			endwhile;
			$content.="
				</select>
			";
			return $content;
		}

		function getGchartName($selectedid){
			$query="
				select
					*
				from
					gchart
				where
					enable='Y'
			";
			$result=mysql_query($query);
			while($r=mysql_fetch_assoc($result)):
				if($selectedid==$r[gchart_id]){
					return $r[gchart];
				}
			endwhile;
		}

		function getGcharts($selectedid=NULL,$name="advances_gchart_id"){
			$content="
				<select name='$name' id='$name'>
					<option value=''>Select Advances Account:</option>
			";
			$query="
				select
					*
				from
					gchart
				where
					gchart_void ='0'
                order by gchart asc
			";
			$result=mysql_query($query);
			while($r=mysql_fetch_assoc($result)):
				$select=($selectedid==$r[gchart_id])?"selected='selected'":"";
				$content.="
					<option value='$r[gchart_id]' $select >$r[gchart] - $r[acode]</option>
				";
			endwhile;
			$content.="
				</select>
			";
			return $content;
		}

        function getGcharts2($selectedid=NULL,$name="payable_gchart_id"){
			$content="
				<select name='$name' id='$name'>
					<option value=''>Select A/P Account:</option>
			";
			$query="
				select
					*
				from
					gchart
				where
					gchart_void ='0'
                order by gchart asc
			";
			$result=mysql_query($query);
			while($r=mysql_fetch_assoc($result)):
				$select=($selectedid==$r[gchart_id])?"selected='selected'":"";
				$content.="
					<option value='$r[gchart_id]' $select >$r[gchart] - $r[acode]</option>
				";
			endwhile;
			$content.="
				</select>
			";
			return $content;
		}

		function getGChartIDFromMClass($mclass){
			$query="
				select
					*
				from
					gchart
				where
					enable='Y'
				and
					mclass='$mclass'

			";
			$result=mysql_query($query);
			while($r=mysql_fetch_assoc($result)):
				if($selectedid==$r[mclass]){
					return $r[gchart_id];
				}
			endwhile;
		}

		function getUpdatedGLTransacTable($gltran_header_id){
			$result=mysql_query("
				select
					status
				from
					gltran_header
				where
					gltran_header_id='$gltran_header_id'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			$status=$r['status'];


			$content='
				<table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
					<tr bgcolor="#C0C0C0">
					  <th width="20"><b>#</b></th>';
			if($status=="S"){
			$content.='
					  <th width="20" align="center"></th>
					  ';
			}
			$content.='
					  <th><b>Account</b></th>
					  <th width="60"><b>Description</b></th>
					  <th width="60"><b>Project</b></th>
					  <th width="60"><b>Debit</b></th>
					  <th width="60"><b>Credit</b></th>
					</tr>
				';

				$query="
					select
						*
					from
						gltran_detail
					where
						gltran_header_id='$gltran_header_id'
					order by
						gltran_detail_id asc
				";

				$result=mysql_query($query);
				$debit_total=0;
				$credit_total=0;

				$counter=1;
				while($r=mysql_fetch_assoc($result)):

					$debit_total+=$r[debit];
					$credit_total+=$r[credit];

					$enable=($r[enable]=="Y")?"checked='checked'":"";

					$project_select =  $this->getTableAssoc($r['project_id'],'update_project_id[]','Select Project',"select * from projects order by project_name",'project_id','project_name');

					$content.='
						<tr>
							<input type="hidden" name="d_gltran_detail_id[]" value="'.$r[gltran_detail_id].'">
							<td>'.$counter++.'</td>
					';
					if($status=="S"){
					$content.='
							<td align="center"><img src="images/trash.gif" style="cursor:pointer;" onclick="xajax_removeParent(\''.$r[gltran_header_id].'\',\''.$r[gltran_detail_id].'\');"/></td>
					';
					}

					$content.='
							<td>'.$this->getGchartName($r[gchart_id]).'</td>
							<td><input type="text" class="textbox"  name="d_description[]" value="'.$r[description].'"></td>
							<td>'.$project_select.'</td>
							<td><div align="right"><input type="text" class="textbox align-right" name="d_debit[]" value="'.$r[debit].'"></td>
							<td><div align="right"><input type="text" class="textbox align-right" name="d_credit[]" value="'.$r[credit].'"></td>
						</tr>
					';
				endwhile;

				$content.="
					<tfoot>
						<tr>
							<td style='background-color:#FFF; border-top:1px solid #000;'>&nbsp;</td>
							<td style='background-color:#FFF; border-top:1px solid #000;'>&nbsp;</td>
							<td style='background-color:#FFF; border-top:1px solid #000;'>&nbsp;</td>
							<td style='background-color:#FFF; border-top:1px solid #000;'>&nbsp;</td>
							<td style='background-color:#FFF; border-top:1px solid #000;'>&nbsp;</td>
							<td style='text-align:right; background-color:#FFF; font-weight:bold; color:#F00; border-top: 1px solid #000;'>".number_format($debit_total,2,'.',',')."</td>
							<td style='text-align:right; background-color:#FFF; font-weight:bold; color:#F00; border-top: 1px solid #000;''>".number_format($credit_total,2,'.',',')."</td>
						</tr>
					</tfoot>
				";

				$content.="
					</table>
				";

				return $content;
		}

		function getACodeFromGChartID($gchart_id){
			$query="
				select
					acode
				from
					gchart
				where
					gchart_id='$gchart_id'
			";
			$result=mysql_query($query);

			$r=mysql_fetch_assoc($result);
			return $r[acode];
		}

		function getGChartIDFromACode($acode){
			$query="
				select
					gchart_id
				from
					gchart
				where
					acode='$acode'
			";
			$result=mysql_query($query);
			$r=mysql_fetch_assoc($result);

			return $r[gchart_id];
		}

		function getAuditFromGLTransac($gltran_header_id){
			$query="
				select
					audit
				from
					gltran_header
				where
					gltran_header_id='$gltran_header_id'
			";
			$result=mysql_query($query);
			$r=mysql_fetch_assoc($result);
			return $r[audit];
		}

		function getInventoryACodeFromStockID($stock_id){
			$query="
				select
					categ_id1,
					categ_id2,
					categ_id3,
					categ_id4
				from
					productmaster
				where
					stock_id='$stock_id'
			";
			$result=mysql_query($query);
			$r=mysql_fetch_assoc($result);

			$category_array=array();

			$categ_id1=$r[categ_id1];
			$categ_id2=$r[categ_id2];
			$categ_id3=$r[categ_id3];
			$categ_id4=$r[categ_id4];


			$counter=0;
			if($categ_id1){
				$category_array[]=$r[categ_id1];
				$counter++;
			}
			if($categ_id2){
				$category_array[]=$r[categ_id2];
				$counter++;
			}
			if($categ_id3){
				$category_array[]=$r[categ_id3];
				$counter++;
			}
			if($categ_id4){
				$category_array[]=$r[categ_id4];
				$counter++;
			}

			switch($counter){
				case 1:
					$category_id=$categ_id1;
					break;
				case 2:
					$category_id=$categ_id2;
					break;
				case 3:
					$category_id=$categ_id3;
					break;
				case 4:
					$category_id=$categ_id4;
					break;
			}


			$acode=$this->getACodeFromCategory($category_id);
			$inv_acode=$acode['inventory_acode'];
			return $inv_acode;
		}

		function getIncomeACodeFromStockID($stock_id){
			$query="
				select
					categ_id1,
					categ_id2,
					categ_id3,
					categ_id4
				from
					productmaster
				where
					stock_id='$stock_id'
			";
			$result=mysql_query($query);
			$r=mysql_fetch_assoc($result);

			$category_array=array();

			$category_array[]=$r[categ_id1];
			$category_array[]=$r[categ_id2];
			$category_array[]=$r[categ_id3];
			$category_array[]=$r[categ_id4];

			$acode_array=array();
			foreach($category_array as $category){
				$acode=$this->getACodeFromCategory($category);
				$acode_array[]=$acode['income_acode'];
			}
			$acode_array=array_reverse($acode_array);

			return $acode_array[0];
		}
		function getACodeFromCategory($categ_id){
			$query="
				select
					inventory_acode,
					income_acode
				from
					categories
				where
					categ_id='$categ_id'
			";
			$acode=array();
			$result=mysql_query($query);
			$r=mysql_fetch_assoc($result);
			$acode['inventory_acode']=$r[inventory_acode];
			$acode['income_acode']=$r[income_acode];

			return $acode;
		}
		/***********************************
		ACCOUNTING QUERIES
		***********************************/
		function insertIntoGLDetails($gltran_header_id,$gchart_id,$debit=NULL,$credit=NULL,$project_id = NULL){


			$query="
				insert into
					gltran_detail
				set
					debit='$debit',
					credit='$credit',
					enable='Y',
					gltran_header_id='$gltran_header_id',
					gchart_id='$gchart_id',
					project_id = '$project_id'
			";

			mysql_query($query);
		}


		function solveForDebitCreditForTheMonth($gchart_id,$startingdate,$endingdate,$project_id=NULL,$normal_balance = "Debit", $forwarding = TRUE){

			$query ="
				select
					sum(debit) as debit,
					sum(credit) as credit
				from
					gltran_header as h, gltran_detail as d
				where
					h.gltran_header_id = d.gltran_header_id
				and
					gchart_id = '$gchart_id'
				and
					date < '$startingdate'
				and
					status != 'C'
			";
			if($project_id){
			$query.="and account_id = 'p-$project_id'";
			}
			$result = mysql_query($query) or die(mysql_error());
			$debit_forwarded = 0;
			$credit_forwarded = 0;
			$r = mysql_fetch_assoc($result);

			if($normal_balance == "Debit"){
				$debit_forwarded = $r['debit'] - $r['credit'];
				$credit_forwarded = 0;
			}else{
				$credit_forwarded = $r['credit'] - $r['debit'];
				$debit_forwarded = 0;
			}

			 $query="
				select
					sum(debit) as debit,
					sum(credit) as credit
				FROM
					gltran_header as h,gltran_detail as d
				where
					h.gltran_header_id=d.gltran_header_id
				and
					gchart_id='$gchart_id'
				and
					date between '$startingdate' and '$endingdate'
				and
					status!='C'
			";
			if($project_id){
			$query.="and account_id = 'p-$project_id'";
			}
			$result=mysql_query($query);
			$totaldebit=0;
			$totalcredit=0;
			$r=mysql_fetch_assoc($result);
		 	$debit	= $r['debit'];
			$credit	= $r['credit'];

			if($forwarding){
				$totaldebit += $debit + $debit_forwarded;
				$totalcredit += $credit + $credit_forwarded;
			}else{
				$totaldebit += $debit;
				$totalcredit += $credit;
			}

			$array=array();

			$array['debit_forwarded'] = $debit_forwarded;
			$array['credit_forwarded'] = $credit_forwarded;
			$array['debit'] = $totaldebit;
			$array['credit'] = $totalcredit;

			return $array;
		}
		function solveForDebitCreditForTheMonthUsingMonthYear($gchart_id,$month,$year){

			$startingdate="$year-$month-01";
			$endingdate=date("Y-m-d",strtotime("+1 month -1 day",strtotime($startingdate)));

			$query="
				select
					*
				FROM
					gltran_header as h,gltran_detail as d
				where
					h.gltran_header_id=d.gltran_header_id
				and
					gchart_id='$gchart_id'
				and
					date between '$startingdate' and '$endingdate'
				and
					status!='C'
			";
			$result=mysql_query($query);
			 $totaldebit=0;
			 $totalcredit=0;
			while($r=mysql_fetch_assoc($result)){
				 $debit=$r[debit];
				 $credit=$r[credit];

				$totaldebit+=$debit;
				$totalcredit+=$credit;
			}

			$array=array();

			$array['debit']=$totaldebit;
			$array['credit']=$totalcredit;

			return $array;
		}


		function solveForYearToDate($gchart_id,$endingdate,$option=TRUE){
			//1 - Debit - Credit else Credit - Debit
			$year=explode("-",$endingdate);
			$startingdate=date("$year[0]-01-01");

			$query="
				select
					*
				FROM
					gltran_header as h,gltran_detail as d
				where
					h.gltran_header_id=d.gltran_header_id
				and
					gchart_id='$gchart_id'
				and
					date between '$startingdate' and '$endingdate'
				and
					status!='C'
			";
			$result=mysql_query($query);
			 $totaldebit=0;
			 $totalcredit=0;
			while($r=mysql_fetch_assoc($result)){
				 $debit=$r[debit];
				 $credit=$r[credit];

				$totaldebit+=$debit;
				$totalcredit+=$credit;
			}

			if($option){
				return $totaldebit-$totalcredit;
			}else{
				return $totalcredit-$totaldebit;
			}
		}

		function solveForYearToDateUsingMonthYear($gchart_id,$month,$year,$debitpositive=TRUE){
			$startingdate="$year-01-01";
			$endingdate=date("Y-m-d",strtotime("+1 month -1 day",strtotime("$year-$month-01")));

			$query="
				select
					*
				FROM
					gltran_header as h,gltran_detail as d
				where
					h.gltran_header_id=d.gltran_header_id
				and
					gchart_id='$gchart_id'
				and
					date between '$startingdate' and '$endingdate'
				and
					status!='C'
			";


			$result=mysql_query($query);
			 $totaldebit=0;
			 $totalcredit=0;
			while($r=mysql_fetch_assoc($result)){
				 $debit=$r[debit];
				 $credit=$r[credit];

				$totaldebit+=$debit;
				$totalcredit+=$credit;
			}
			if($debitpositive){
				return $totaldebit-$totalcredit;
			}else{
				return $totalcredit - $totaldebit;
			}
		}

		function solveForLastYear($gchart_id,$endingdate,$option=TRUE){
			$year=explode("-",$endingdate);
			$startingdate=date(($year[0]-1)."-01-01");
			$endingdate=date(($year[0]-1)."-12-31");

			$query="
				select
					*
				FROM
					gltran_header as h,gltran_detail as d
				where
					h.gltran_header_id=d.gltran_header_id
				and
					gchart_id='$gchart_id'
				and
					date <= '$endingdate'
				and
					status!='C'
			";
			$result=mysql_query($query);
			 $totaldebit=0;
			 $totalcredit=0;
			while($r=mysql_fetch_assoc($result)){
				 $debit=$r[debit];
				 $credit=$r[credit];

				$totaldebit+=$debit;
				$totalcredit+=$credit;
			}

			if($option){
				return $totaldebit-$totalcredit;
			}else{
				return $totalcredit-$totaldebit;
			}
		}

		function generateJournalReference($journal_id){
			$journal_code=$this->getJournalCode($journal_id);
			$query="
				SELECT
					*
				FROM
					gltran_header
				where
					journal_id='$journal_id'
				order by
					gltran_header_id desc
			";
			$result=mysql_query($query);
			$rows=mysql_num_rows($result);
			if($rows==0){
				$generalreference=$journal_code."-".date("Y")."-0001";
			}else{
				$r=mysql_fetch_assoc($result);
				$num=$r[generalreference];
				$num=explode('-',$num);
				$num=$num[2];
				$num=intval($num);
				$num+=1;
				$num=str_pad($num,4,"0",STR_PAD_LEFT);
				$generalreference=$journal_code."-".date("Y")."-".$num;
			}

			return $generalreference;
		}
		function solveLastYearNetProfit($endingdate){
			 $query="
                    select
                        *
                    FROM
                       gchart
					where
						mclass='I'
                ";
			$gchart_result=mysql_query($query) or die(mysql_error());
			$income=0;
			while($gchart_row=mysql_fetch_assoc($gchart_result)):
				$income+=$this->solveForLastYear($gchart_row['gchart_id'],$endingdate);
			endwhile;

			$query="
                    select
                        *
                    FROM
                       gchart
					where
						mclass='E'
                ";

	        $gchart_result=mysql_query($query) or die(mysql_error());
			$expense=0;
			while($gchart_row=mysql_fetch_assoc($gchart_result)):
					$expense+=$this->solveForLastYear($gchart_row['gchart_id'],$endingdate);
			endwhile;

			return $income-$expense;
		}


		function getPOOptions($selectedid=NULL,$name='po_header_id',$js=NULL){
			$content="
				<select name='$name' id='$name' $js>
					<option value=''>Select PO #:</option>
			";
			$query="
				select
					po_header_id
				from
					po_header
				where
					status!='C'
				order by
					po_header_id desc
			";
			$result=mysql_query($query);
			while($r=mysql_fetch_assoc($result)){
				$selected=($selectedid==$r[po_header_id])?"selected='selected'":"";
				$content.="
					<option value='$r[po_header_id]' $selected>".str_pad($r[po_header_id],8,"0",STR_PAD_LEFT)."</option>
				";
			}
			$content.="
				</select>
			";

			return $content;
		}
		
		function getMRRBalance($from,$to,$supplier_id){
		$total_rr = 0;	
			
			$result=mysql_query("
					select po.po_header_id, h.rr_header_id, po.date, h.date, h.netamount
					from
					po_header as po,
					rr_header as h
					where
					po.date between '$from' and '$to' and
					po.po_type = 'M' and
					h.po_header_id = po.po_header_id and
					h.`status` != 'C' and
					po.`status` != 'C' and
					h.supplier_id = '$supplier_id'
					") or die(mysql_error());
			while($r = mysql_fetch_assoc($result)){
				$total_rr += $r['netamount'];
				
				set_time_limit(120);
			}
			return $total_rr;
			
			
		}		
		
		function getPoCharges($from,$to,$supplier_id){
		$total_po = 0;	
			
			$result=mysql_query("
					SELECT
					h.po_header_id, sum(d.amount) as po_amount, h.date
					from
					po_header as h,
					po_detail as d
					where
					h.po_header_id = d.po_header_id and
					h.`status` != 'C' and
					h.po_type = 'M' and
					h.date between '$from' and '$to' and
					h.supplier_id = '$supplier_id'
					group by h.po_header_id order by h.date ASC
					") or die(mysql_error());
			while($r = mysql_fetch_assoc($result)){
				$total_po += $r['po_amount'];
				
				set_time_limit(120);
			}
			return $total_po;
		}

		function getBalanceAPV2($from,$to,$supplier_id){
			$sql = mysql_query("select po.po_header_id, h.apv_header_id, d.apv_detail_id, po.date, h.date, d.amount from
						po_header as po,
						apv_header as h,
						apv_detail as d
						where
						po.date between '$from' and '$to' and
						po.po_type = 'M' and
						h.po_header_id = po.po_header_id and
						h.apv_header_id = d.apv_header_id and
						h.`status` != 'C' and
						po.`status` != 'C' and
						h.supplier_id = '$supplier_id'") or die(mysql_error());
					
			while($r = mysql_fetch_assoc($sql)){
				$apv += $r['amount'];
				
				set_time_limit(120);
			}
			
			return $apv;
		}

		function getDVBalance($from,$to,$supplier_id){
			$total_ev = 0;
			
			$sql = mysql_query("select po.po_header_id, h.ev_header_id, d.ev_detail_id ,po.date, h.date, d.amount 
							from
							po_header as po,
							ev_header as h,
							ev_detail as d
							where
							po.date between '$from' and '$to' and
							po.po_type = 'M' and
							h.po_header_id = po.po_header_id and
							h.ev_header_id = d.ev_header_id and
							h.`status` != 'C' and
							po.`status` != 'C' and
							h.supplier_id = '$supplier_id'") or die(mysql_error());
					
			while($r = mysql_fetch_assoc($sql)){
				$total_ev += $r['amount'];
				
				set_time_limit(120);
			}
			
			return $total_ev;
		}

		function getCVBalance($from,$to,$supplier_id){
		$total_cv = 0;	
			
			$result=mysql_query("
					select 
					po.po_header_id, po.date, po.supplier_id, apv.apv_header_id, cd.amount, cd.cv_header_id, cd.cv_detail_id, ch.`status`
					from
					po_header as po,
					apv_header as apv,
					cv_detail as cd,
					cv_header as ch
					where
					po.po_header_id = apv.po_header_id and
					apv.apv_header_id = cd.apv_header_id and
					po.date between '$from' and '$to' and
					po.po_type = 'M' and
					po.`status` != 'C' and
					ch.cv_header_id = cd.cv_header_id and
					po.supplier_id = '$supplier_id' and
					ch.`status` != 'C'
					") or die(mysql_error());
			while($r = mysql_fetch_assoc($result)){
				$total_cv += $r['amount'];
				
				set_time_limit(120);
			}
			return $total_cv;
			
		}			

		function getAPBalanceBetweenDates($supplier_id,$fromdate,$todate){

			# MATERIALS RECEIVED
			$result=mysql_query("
						select
							sum(netamount) as total_amount
						from
							rr_header
						where
							date between '$fromdate' and '$todate'
						AND
							supplier_id = '$supplier_id'
						and
							status!='C'
					");
			$r=mysql_fetch_assoc($result);
			$rr_amount =  $r['total_amount'];


			# EXPENSES
			/*$result = mysql_query("
				select
					sum(amount) as amount
				from
					cv_header as h, cv_detail as d
				where
					h.cv_header_id = d.cv_header_id
				and
					status != 'C'
				and
					cv_date between '$fromdate' and '$todate'
				and
					supplier_id = '$supplier_id'
				and
					type = 'E'
			") or die(mysql_error());
			$r = mysql_fetch_assoc($result);
			$expense_amount = $r['amount'];*/

			$result=mysql_query("
						select
							sum(amount) as amount
						FROM
							cv_header as h, cv_detail as d
						where
							h.cv_header_id = d.cv_header_id
						and
							status!='C'
						and
							cv_date between '$fromdate' and '$todate'
						and
							supplier_id = '$supplier_id'
					") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			$payment_amount  = $r['amount'];


			return $rr_amount + $expense_amount - $payment_amount;

		}


		function getAPBalanceOnAndOverDate($supplier_id,$from,$to){
			$payables = $this->getBalanceFromSupplier($from,$to,$supplier_id);
			$payments	= $this->getDisbursementForSupplier($from,$to,$supplier_id);

			return $payables - $payments;
		}


		function getBalanceFromSupplier($from,$to,$supplier_id){
			# MATERIALS RECEIVED
			$result=mysql_query("
						select
							sum(netamount) as total_amount
						from
							rr_header
						where
							date between '$from' and '$to'
						AND
							supplier_id = '$supplier_id'
						and
							status!='C'
					");
			$r=mysql_fetch_assoc($result);
			$rr_amount =  $r['total_amount'];

			# EXPENSES
			/*$result = mysql_query("
				select
					sum(amount) as amount
				from
					cv_header as h, cv_detail as d, gchart as g
				where
					h.cv_header_id = d.cv_header_id
				and
					d.gchart_id = g.gchart_id
				and
					status != 'C'
				and
					cv_date <= '$date'
				and
					supplier_id = '$supplier_id'
				and
					type = 'E'
				and
					mclass != 'A'
			") or die(mysql_error());
			$r = mysql_fetch_assoc($result);
			$expense_amount = $r['amount'];*/

			return $rr_amount + $expense_amount;

		}

		function getDisbursementForSupplier($from,$to,$supplier_id){

			$result=mysql_query("
						select
							sum(amount) as amount
						FROM
							cv_header as h, cv_detail as d
						where
							h.cv_header_id = d.cv_header_id
						and
							status!='C'
						and
							cv_date between '$from' and '$to'
						and
							supplier_id = '$supplier_id'
					") or die(mysql_error());
			$r=mysql_fetch_assoc($result);

			return $r['amount'];
		}

		function getPayedAmountToAccountID($date,$account_id,$rr_header_id){
			$journal_id=$this->getJournalID("DV");
			$gchart_id=$this->getGChartIDFromACode(3000);

			$query="
				select
					sum(debit) as debit
				from
					gltran_header as h, gltran_detail as d
				WHERE
					h.gltran_header_id=d.gltran_header_id
				and
					date <= '$date'
				AND
					journal_id='$journal_id'
				and
					account_id='$account_id'
				and
					gchart_id='$gchart_id'
				and
					status!='C'
				and
					rr_header_id='$rr_header_id'
			";

			$result=mysql_query($query) or die(mysql_error());
			$r=mysql_fetch_assoc($result);

			return $r[debit];
		}

		function getTotalDebitOfGL($gltran_header_id,$journal_id){
			$query="
				select
					sum(debit) as debit
				from
					gltran_header as h, gltran_detail as d
				WHERE
					h.gltran_header_id=d.gltran_header_id
				AND
					journal_id='$journal_id'
				and
					status!='C'
				and
					h.gltran_header_id='$gltran_header_id'
			";

			$result=mysql_query($query) or die(mysql_error());
			$r=mysql_fetch_assoc($result);

			return $r[debit];
		}

		function getPOQuantity($date,$stock_id,$locale_id){
			$query= "
							select
								sum(qty) as qty
							from
								po_header as h, po_details as d
							where
								h.po_header_id=d.po_header_id
							and
								status!='C'
							and
								date <= '$date'
							and
								stock_id='$stock_id'
							and
								locale_id='$locale_id'
							and
								h.po_header_id not in (
								select
									rr_header.po_header_id
								from
									rr_header
								where
									status='F'
							)

					";

			$result=mysql_query($query) or die(mysql_error());
			$r=mysql_fetch_assoc($result);


			$po_qty=($r[qty])?$r[qty]:0;

			$result=mysql_query("
						select
							sum(quantity) as quantity
						from
							rr_header as h, rr_detail as d
						where
							h.rr_header_id=d.rr_header_id
						and
							status='S'
						and
							date <= '$date'
						and
							stock_id='$stock_id'
						and
							locale_id='$locale_id'
					") or die(mysql_error());


			$r=mysql_fetch_assoc($result);

			$return_qty=$r[quantity];

			return $po_qty-$return_qty;
		}

		function getDataFromInvAdjust($invadjust_header_id,$column){
			$result=mysql_query("
				select
					*
				from
					invadjust_header
				where
					invadjust_header_id='$invadjust_header_id'
			");

			$r=mysql_fetch_assoc($result);
			return $r[$column];
		}

		function getDataFromARAdjust($aradjust_header_id,$column){
			$result=mysql_query("
				select
					*
				from
					aradjust_header
				where
					aradjust_header_id='$aradjust_header_id'
			");

			$r=mysql_fetch_assoc($result);
			return $r[$column];
		}


		function postIssuance($issuance_header_id,$contractor_id = NULL){

			$issuance_header_id_pad = str_pad($issuance_header_id,7,0,STR_PAD_LEFT);

			$result=mysql_query("
				select
					*
				from
					issuance_header
				where
					issuance_header_id = '$issuance_header_id'

			") or die(mysql_error());

			$r=mysql_fetch_assoc($result);

			$project_id		= $r['project_id'];
			$date			= $r['date'];

			/*INSERT TO GL_HEADER*/
			$journal_id=$this->getJournalID("JV");
			$generalreference=$this->generateJournalReference($journal_id);

			/*
			if(!empty($contractor_id)){
				$gl_account_id="c-".$contractor_id;
			}else{
				$gl_account_id="p-".$project_id;
			}
			*/
			$gl_account_id="p-".$project_id;

			$query="
				insert into
					gltran_header
				set
					generalreference='$generalreference',
					date='$date',
					journal_id='$journal_id',
					status='S',
					user_id='$_SESSION[userID]',
					account_id='$gl_account_id',
					xrefer = 'R.I.S. # : $issuance_header_id_pad',
					header = 'issuance_header_id',
					header_id = '$issuance_header_id'
			";
			mysql_query($query) or die(mysql_error());
			$gltran_header_id=mysql_insert_id();

			/*
				COST OF GOODS SOLD AND MERCHANDISE INVENTORY
			*/

			#	326	 	- DIRECT MATERIALS		DEBIT
			#	119 	- MATERIALS INVENTORY				CREDIT


			$result = mysql_query("
				SELECT
					SUM(amount) as amount
				from
					issuance_header as h, issuance_detail AS d
				where
					h.issuance_header_id = d.issuance_header_id
				and
					h.issuance_header_id = '$issuance_header_id'
			") or die(mysql_error());


			$r = mysql_fetch_assoc($result);
			$amount = $r['amount'];

			$inventory_account = 119;
			$direct_materials_account = 326;

			$this->insertIntoGLDetails($gltran_header_id,$inventory_account,0,$amount,$project_id);
			$this->insertIntoGLDetails($gltran_header_id,$direct_materials_account,$amount,0,$project_id);

			#NOT YET APPLICABLE
			/*#84 - ADVANCES TO SUPPLIERS
			$advances_to_officers_account = 84;

			$result = mysql_query("
				select
					sum(amount) as amount
				from
					issuance_header as h, issuance_detail as d
				where
					h.issuance_header_id = d.issuance_header_id
				and
					h.issuance_header_id = '$issuance_header_id'
				and
					account_id != ''
			") or die(mysql_error());
			$r = mysql_fetch_assoc($result);
			$issued_amount = $r['amount'];
			#CREDIT DIRECT MATERIALS
			#DEBIT A/R
			if($issued_amount > 0){
				$this->insertIntoGLDetails($gltran_header_id,$direct_materials_account,0,$issued_amount);
				$this->insertIntoGLDetails($gltran_header_id,$advances_to_officers_account,$issued_amount,0);
			}*/

			mysql_query("
				insert into
					posted_headers
				set
					header_id='$issuance_header_id',
					journal_code='JV',
					gltran_header_id='$gltran_header_id',
					header='issuance_header_id'
			") or die(mysql_error());

		}

		function hasAdvancePayment($po_header_id){
			$result = mysql_query("select * from ev_header where po_header_id = '$po_header_id' and status != 'C'") or die(mysql_error());
			if(mysql_num_rows($result) > 0 ){
				return true;
			}else{
				return false;
			}
		}


	function postRR($rr_header_id){

				
			$rr_header_id_pad = str_pad($rr_header_id,7,0,STR_PAD_LEFT);
			//$advances_to_suppliers = 677;

			$result=mysql_query("
				select
					sum(amount) as amount,
					sum(discount * quantity) as discount_amount
				from
					rr_detail
				where
					rr_header_id='$rr_header_id'

			") or die(mysql_error());

			$r=mysql_fetch_assoc($result);
			$total_amount = $r['amount'];
			$discount_amount = $r['discount_amount'];


			$result=mysql_query("
				select
					*
				from
					rr_header
				where
					rr_header_id='$rr_header_id'

			") or die(mysql_error());

			$r=mysql_fetch_assoc($result);
			$paytype = $r[paytype];
			$supplier_id            = $r['supplier_id'];
			$date                   = $r['date'];
			$po_header_id           = $r['po_header_id'];
			$advance_payment_amount = $r['advance_payment_amount'];
			$rr_type                = $r['rr_type'];
			$ppe_gchart_id          = $r['ppe_gchart_id'];
			$project_id             = $r['project_id'];
			$payable_to_suppliers  = $this->getAttribute("supplier","account_id",$supplier_id,"payable_gchart_id");

			if($payable_to_suppliers == 0){
				$payable_to_suppliers = 72;
			}

			/*INSERT TO GL_HEADER*/
			$journal_id=$this->getJournalID("AP");
			$generalreference=$this->generateJournalReference($journal_id);
			$gl_account_id="s-".$supplier_id; //not yet used

			$query="
				insert into
					gltran_header
				set
					generalreference='$generalreference',
					date='$date',
					journal_id='$journal_id',
					status='S',
					user_id='$_SESSION[userID]',
					account_id='$gl_account_id',
					xrefer = 'M.R.R # : $rr_header_id_pad',
					header = 'rr_header_id',
					header_id = '$rr_header_id'
			";
			mysql_query($query) or die(mysql_error());
			$gltran_header_id=mysql_insert_id();

			/*
			DETAILS
			*/

			/*
				insert inventory
			*/
			//$accounts_payable  = 729;
			
			$sql = mysql_query("Select * from po_header where po_header_id = '$po_header_id'") or die (mysql_error());
			$rp = mysql_fetch_assoc($sql);
			$vat_percentage = $rp['vat'];
			$wtax_percentage = $rp['wtax'];
			
			//formulas
			$vatable = $total_amount / 1.12; // VATABLE
			
			if($vat_percentage != 0){
				$vat = $vatable * ($vat_percentage/100); //12% vat
				$debit_amount = $total_amount - $vat;
			}else{
				$debit_amount = $total_amount;
			}
			
			/*if($wtax_percentage != 0){
				$wtax = $vatable * ($wtax_percentage/100);
				
				if($wtax_percentage == 1){
					$wtax_account = 924; //EWT 1% PAYABLE GOODS
				}else if($wtax_percentage == 2){
					$wtax_account = 925; //EWT 2% PAYABLE SERVICES	
				}
				$credit_amount = $total_amount - $wtax;
			}else{*/
				$credit_amount = $total_amount;
			//}
		
			$vat_account = 922; //CREDITABLE WITHOLDING VAT(12%)
			$inventory_account = 703; //MATERIALS INVENOTRY
			$ppe = 943; // PROPERTY, PLANT AND EQUIPMENT
			
			//debit side ===================================
			if($rr_type == "A"){
				if(!empty($ppe_gchart_id)){
					//$this->insertIntoGLDetails($gltran_header_id,$ppe_gchart_id,$total_amount-$discount_amount,0,$project_id);
					$this->insertIntoGLDetails($gltran_header_id,$ppe_gchart_id,$debit_amount,0,$project_id);					
				}else{
					//$this->insertIntoGLDetails($gltran_header_id,$ppe,$total_amount-$discount_amount,0,$project_id);
					$this->insertIntoGLDetails($gltran_header_id,$ppe,$debit_amount,0,$project_id);
				}
			}else{
				#$this->insertIntoGLDetails($gltran_header_id,$inventory_account,$total_amount-$discount_amount,0,$project_id);
                $this->insertIntoGLDetails($gltran_header_id,$inventory_account,$debit_amount,0,$project_id);
			}	
			
			//vat
			if($vat_percentage != 0){
				$this->insertIntoGLDetails($gltran_header_id,$vat_account,$vat,0,$project_id);
			}

			/*if($advance_payment_amount > 0) {
				$this->insertIntoGLDetails($gltran_header_id,$advances_to_suppliers,0,$advance_payment_amount,$project_id);
				if(($total_amount-$discount_amount - $advance_payment_amount) > 0){
					$this->insertIntoGLDetails($gltran_header_id,$accounts_payable,0,$total_amount-$discount_amount - $advance_payment_amount,$project_id);
				}
			}else{
				$this->insertIntoGLDetails($gltran_header_id,$accounts_payable,0,$total_amount-$discount_amount,$project_id);
			}
			*/
			
			//credit side ===================================
			// A/P Account
            $this->insertIntoGLDetails($gltran_header_id,$payable_to_suppliers,0,$credit_amount,$project_id);
			
			//For EWT
			//if($wtax_percentage != 0){
			//	$this->insertIntoGLDetails($gltran_header_id,$wtax_account,0,$wtax,$project_id);
			//}
			
			mysql_query("
				insert into
					posted_headers
				set
					header_id='$rr_header_id',
					journal_code='AP',
					gltran_header_id='$gltran_header_id',
					header='rr_header_id'
			") or die(mysql_error());

		}
		
		function postAPV($apv_header_id){
			
			$materials_gchart_id = 119;
			$vat_account = 120;
			$apv_header_id = str_pad($apv_header_id,7,0,STR_PAD_LEFT);
			
			$sql = mysql_query("Select * from apv_header as h
								where
								h.apv_header_id = '$apv_header_id'") or die (mysql_error());
								
			$r = mysql_fetch_assoc($sql);

			$ap_gchart_id = $r['ap_gchart_id'];
			$date = $r['date'];
			$ppe_gchart_id = $r['ppe_gchart_id'];
			$advances_gchart_id = $r['advances_gchart_id'];
			$advance_payment_amount = $r['advance_payment_amount'];
			$vatable = $r['vatable'];
			$project_id = $r['project_id'];
			
			
			/*INSERT TO GL_HEADER*/
			$journal_id=$this->getJournalID("AP");
			$generalreference=$this->generateJournalReference($journal_id);
			$gl_account_id="s-".$supplier_id; //not yet used

			$query="
				insert into
					gltran_header
				set
					generalreference='$generalreference',
					date='$date',
					journal_id='$journal_id',
					status='S',
					user_id='$_SESSION[userID]',
					account_id='$gl_account_id',
					xrefer = 'A.P.V # : $apv_header_id',
					header = 'apv_header_id',
					header_id = '$apv_header_id'
			";
			mysql_query($query) or die(mysql_error());
			$gltran_header_id=mysql_insert_id();
			
			$s2 = mysql_query("select
								sum(d.amount) as amount
								from
								apv_detail as d
								where
								d.apv_header_id = '$apv_header_id'") or die (mysql_error());
			$r2 = mysql_fetch_assoc($s2);

			$debit_amount = $r2['amount'];
			$credit_amount = $r2['amount'];
			
			
			
			
			if($ap_gchart_id != NULL && $ap_gchart_id != 0){
				$payable_to_suppliers = $ap_gchart_id;
			}else if($ppe_gchart_id != NULL && $ppe_gchart_id != 0){
				$payable_to_suppliers = $ppe_gchart_id;
			}else{
				$payable_to_suppliers = 72;
			}
			
			//debit side
			
			if($vatable == 1){
				$vatable = $debit_amount / 1.12;
				
				$vat_amount = $debit_amount - $vatable;
				
				$this->insertIntoGLDetails($gltran_header_id,$materials_gchart_id,$vatable,0,$project_id);
				$this->insertIntoGLDetails($gltran_header_id,$vat_account,$vat_amount,0,$project_id);
				
			}else{
				$this->insertIntoGLDetails($gltran_header_id,$materials_gchart_id,$debit_amount,0,$project_id);	
			}						
			
			//credit side
			
			if($advances_gchart_id != NULL && $advances_gchart_id != 0 && $advance_payment_amount != NULL && $advance_payment_amount != 0){
				$advances_gchart_id = $advances_gchart_id;
				
				$this->insertIntoGLDetails($gltran_header_id,$advances_gchart_id,0,$advance_payment_amount,$project_id);
				
				$credit_amount = $credit_amount - $advance_payment_amount;
				
				$this->insertIntoGLDetails($gltran_header_id,$payable_to_suppliers,0,$credit_amount,$project_id);
				
			}else{
			
				$this->insertIntoGLDetails($gltran_header_id,$payable_to_suppliers,0,$credit_amount,$project_id);
			}	
			
			mysql_query("
				insert into
					posted_headers
				set
					header_id='$apv_header_id',
					journal_code='AP',
					gltran_header_id='$gltran_header_id',
					header='apv_header_id'
			") or die(mysql_error());
		}

		function postCV($cv_header_id){
			$cv_header_id_pad = str_pad($cv_header_id,7,0,STR_PAD_LEFT);
			$result=mysql_query("
				select
					*
				from
					cv_header
				where
					cv_header_id = '$cv_header_id'

			") or die(mysql_error());

			$r=mysql_fetch_assoc($result);
			$type 			= $r['type'];
			$supplier_id	= $r['supplier_id'];

			$cv_date		= $r['cv_date'];
			$check_date		= $r['check_date'];
			$check_no		= $r['check_no'];
			$cv_no		    = str_pad($r['cv_no'],7,0,STR_PAD_LEFT);
			$cash_gchart_id	= $r['cash_gchart_id'];
			$ap_gchart_id	= $r['ap_gchart_id'];

			$wtax_gchart_id	= $r['wtax_gchart_id'];
			$wtax			= $r['wtax'];
			$vat_gchart_id	= $r['vat_gchart_id'];
			$vat			= $r['vat'];

			$retention_project_id	= $r['retention_project_id'];
			$retention_gchart_id	= $r['retention_gchart_id'];
			$chargable_gchart_id	= $r['chargable_gchart_id'];
			$retention_amount		= $r['retention_amount'];
			$chargable_amount		= $r['chargable_amount'];
			$rmy_gchart_id			= $r['rmy_gchart_id'];
			$rmy_amount				= $r['rmy_amount'];

			$materials_gchart_id = 703;

			/*INSERT TO GL_HEADER*/
			$journal_id=$this->getJournalID("DV");
			$generalreference=$this->generateJournalReference($journal_id);
			$gl_account_id="s-".$supplier_id; //not yet used
			$advances_gchart_id = $this->getAttribute("supplier","account_id",$supplier_id,"advances_gchart_id");
			$query="
				insert into
					gltran_header
				set
					generalreference='$generalreference',
					date='$cv_date',
					journal_id='$journal_id',
					status='S',
					user_id='$_SESSION[userID]',
					account_id='$gl_account_id',
					xrefer = 'CV # : $cv_no',
					header = 'cv_header_id',
					header_id = '$cv_header_id',
					checkdate = '$check_date',
					mcheck = '$check_no'
			";
			mysql_query($query) or die(mysql_error());
			$gltran_header_id=mysql_insert_id();

			if($type == "M"){
				$result = mysql_query("
					select
						amount,apv_header_id
					from
						cv_detail
					where
						cv_header_id = '$cv_header_id'
				") or die(mysql_error());
				$amount = 0;
				$discount_ap = 0;
				while($r = mysql_fetch_assoc($result)):
					$amount += $r['amount'];
					$discount_ap += $this->getAttribute("apv_header","apv_header_id",$r['apv_header_id'],"discount_amount");
				endwhile;

				$total_vat_amount  = 0;
				$total_wtax_amount = 0;
				$total_cash_amount = 0;

				$vatable     = ($amount / (1 + ($vat/100)));
				$vat_amount  = $vatable * ($vat/100);
				$tax_amount  =  $vatable * ($wtax/100);
				$cash_amount = $amount - $tax_amount;

				/*subtract to cash amount retention and chargables*/
				$cash_amount -= $retention_amount;
				$cash_amount -= $chargable_amount;
				$cash_amount -= $rmy_amount;

				$total_vat_amount  += $vat_amount;
				$total_wtax_amount += $tax_amount;
				$total_cash_amount += $cash_amount;

                /*if($total_vat_amount > 0){
					$this->insertIntoGLDetails($gltran_header_id,$materials_gchart_id,0,$total_vat_amount);
                } */
				
                /*if($total_vat_amount > 0){
					$this->insertIntoGLDetails($gltran_header_id,$vat_gchart_id,$total_vat_amount,0);
				}*/
				
                if($amount > 0){
					$this->insertIntoGLDetails($gltran_header_id,$ap_gchart_id,$amount,0);
                }

				if($discount_ap > 0){
					$this->insertIntoGLDetails($gltran_header_id,$advances_gchart_id,0,$discount_ap);
				}

                if($wtax != 0){
					
					//echo 'hello';
					//exit; 
					$this->insertIntoGLDetails($gltran_header_id,$wtax_gchart_id,0,$total_wtax_amount);
                }

                /*post retention*/
                if( $retention_amount > 0  && !empty($retention_gchart_id) ){
	                $this->insertIntoGLDetails($gltran_header_id,$retention_gchart_id,0,$retention_amount);
                }

                /*post chargables*/
                if( $chargable_amount > 0 && !empty($chargable_gchart_id) ) {
	                $this->insertIntoGLDetails($gltran_header_id,$chargable_gchart_id,0,$chargable_amount);
                }

	            if( $rmy_amount > 0 && !empty($rmy_gchart_id) ) {
	                $this->insertIntoGLDetails($gltran_header_id,$rmy_gchart_id,0,$rmy_amount);
                }			
				
                if($total_cash_amount > 0){
	                $this->insertIntoGLDetails($gltran_header_id,$cash_gchart_id,0,$total_cash_amount);
                }

			}else{
				$result = mysql_query("
					select
						*
					from
						cv_detail
					where
						cv_header_id = '$cv_header_id'
				") or die(mysql_error());

				$total_vat_amount = 0;
				$total_wtax_amount = 0;
				$total_cash_amount = 0;
				$total_amount = 0;
				while($r = mysql_fetch_assoc($result)){
					$gchart_id 		= $r['gchart_id'];
					$amount 		= $r['amount'];
					$project_id		= $r['project_id'];
					$total_amount += $amount;

					if($amount > 0){
						$this->insertIntoGLDetails($gltran_header_id,$gchart_id,$amount,0,$project_id);
                    }
				}


				$vatable = (($total_amount)/ (1 + ($vat/100)));
				$tax_amount = $vatable * ($wtax/100);
				$cash_amount = ($total_amount - $chargable_amount - $retention_amount) - $tax_amount - $rmy_amount;

				$total_wtax_amount += $tax_amount;
				$total_cash_amount += $cash_amount;


				if($retention_amount != 0){
					$this->insertIntoGLDetails($gltran_header_id,$retention_gchart_id,0,$retention_amount,$retention_project_id);
                }

				if($chargable_amount != 0){
					$this->insertIntoGLDetails($gltran_header_id,$chargable_gchart_id,0,$chargable_amount);
                }

                if($total_wtax_amount != 0){
					$this->insertIntoGLDetails($gltran_header_id,$wtax_gchart_id,0,$total_wtax_amount);
                }

                if($total_cash_amount != 0){
	                $this->insertIntoGLDetails($gltran_header_id,$cash_gchart_id,0,$total_cash_amount);
                }

				if($rmy_amount != 0){
	                $this->insertIntoGLDetails($gltran_header_id,$rmy_gchart_id,0,$rmy_amount);
                }
			}

			mysql_query("
				insert into
					posted_headers
				set
					header_id='$cv_header_id',
					journal_code='DV',
					gltran_header_id='$gltran_header_id',
					header='cv_header_id'
			") or die(mysql_error());

			return $gltran_header_id;
		}

		function postAP($ap_payment_id,$account_id){

			$result=mysql_query("
				select
					*
				from
					ap_payment
				where
					ap_payment_id = '$ap_payment_id'
			") or die(mysql_error());

			$r = mysql_fetch_assoc($result);

		    $ap_payment_id		= $r['ap_payment_id'];
			$ap_payment_id_pad	= str_pad($ap_payment_id,7,0,STR_PAD_LEFT);
			$date		= $r['date'];

			$bank		= $r['bank'];
			$checkno	= $r['checkno'];
			$checkdate	= $r['checkdate'];
			$amount		= $r['amount'];

			$supplier_id	= $r['supplier_id'];
			$status			= $r['status'];

			/*INSERT TO GL_HEADER*/
			$journal_id=$this->getJournalID("DV");
			$generalreference=$this->generateJournalReference($journal_id);
			$gl_account_id="s-".$supplier_id; //not yet used

			$query="
				insert into
					gltran_header
				set
					generalreference='$generalreference',
					date='$date',
					journal_id='$journal_id',
					status='S',
					user_id='$_SESSION[userID]',
					account_id='$gl_account_id',
					xrefer = 'A.P. # : ".str_pad($ap_payment_id,7,0,STR_PAD_LEFT)."',
					checkdate = '$checkdate',
					mcheck = '$checkno',
					bank = '$bank',
					header = 'ap_payment_id'
			";

			mysql_query($query) or die(mysql_error());
			$gltran_header_id=mysql_insert_id();

			/*
			DETAILS
			*/

			$cash_account = $account_id;
			$accounts_payable  = 72;

			$this->insertIntoGLDetails($gltran_header_id,$accounts_payable,$amount,0);
			$this->insertIntoGLDetails($gltran_header_id,$cash_account,0,$amount);


			mysql_query("
				insert into
					posted_headers
				set
					header_id='$ap_payment_id',
					journal_code='DV',
					gltran_header_id='$gltran_header_id',
					header='ap_payment_id'
			") or die(mysql_error());

		}

		function postSalesInvoice($sales_invoice_id){

			$result=mysql_query("
				select
					*
				from
					sales_invoice
				where
					sales_invoice_id = '$sales_invoice_id'
			") or die(mysql_error());

			$r = mysql_fetch_assoc($result);

		    $sales_invoice_id		= $r['sales_invoice_id'];
			$sales_invoice_id_pad	= str_pad($sales_invoice_id,7,0,STR_PAD_LEFT);


			$date					= $r['date'];
			$project_id				= $r['project_id'];
			$amount					= $r['amount'];
			$invoice_no				= $r['invoice_no'];
			$sales_gchart_id		= $r['sales_gchart_id'];
			$ar_gchart_id			= $r['ar_gchart_id'];
			$status					= $r['status'];
			$user_id				= $r['user_id'];

			/*INSERT TO GL_HEADER*/
			$journal_id=$this->getJournalID("SJ");
			$generalreference=$this->generateJournalReference($journal_id);
			$gl_account_id="p-".$project_id; //not yet used

			$query="
				insert into
					gltran_header
				set
					generalreference='$generalreference',
					date='$date',
					journal_id='$journal_id',
					status='S',
					user_id='$_SESSION[userID]',
					account_id='$gl_account_id',
					xrefer = 'S.I. # : ".$sales_invoice_id_pad."',
					checkdate = '',
					mcheck = '',
					bank = '',
					header = 'sales_invoice_id',
					header_id = '$sales_invoice_id'
			";

			mysql_query($query) or die(mysql_error());
			$gltran_header_id=mysql_insert_id();

			/*
			DETAILS
			*/

			$this->insertIntoGLDetails($gltran_header_id,$sales_gchart_id,0,$amount,$project_id);
			$this->insertIntoGLDetails($gltran_header_id,$ar_gchart_id,$amount,0,$project_id);


			mysql_query("
				insert into
					posted_headers
				set
					header_id='$sales_invoice_id',
					journal_code='SJ',
					gltran_header_id='$gltran_header_id',
					header='sales_invoice_id'
			") or die(mysql_error());

		}

		function postCashReceipts($cr_header_id){

			$result=mysql_query("
				select
					*
				from
					cr_header
				where
					cr_header_id = '$cr_header_id'
			") or die(mysql_error());

			$r = mysql_fetch_assoc($result);

		    $cr_header_id		= $r['cr_header_id'];
			$cr_header_id_pad	= str_pad($cr_header_id,7,0,STR_PAD_LEFT);


			$date					= $r['date'];
			$project_id				= $r['project_id'];
			$cash_amount			= $r['amount'];
			$invoice_no				= $r['invoice_no'];
			$cash_gchart_id			= $r['cash_gchart_id'];
			$ar_gchart_id			= $r['ar_gchart_id'];
			$status					= $r['status'];
			$user_id				= $r['user_id'];
			$bank					= $r['bank'];
			$check_date				= $r['check_date'];
			$check_no				= $r['check_no'];
			$particulars			= $r['particulars'];

			/*INSERT TO GL_HEADER*/
			$journal_id=$this->getJournalID("CR");
			$generalreference=$this->generateJournalReference($journal_id);
			$gl_account_id="p-".$project_id; //not yet used


			$query="
				insert into
					gltran_header
				set
					generalreference='$generalreference',
					date='$date',
					journal_id='$journal_id',
					status='S',
					user_id='$_SESSION[userID]',
					account_id='$gl_account_id',
					xrefer = 'C.R. # : ".$cr_header_id_pad."',
					checkdate = '$check_date',
					mcheck = '$check_no',
					bank = '$bank',
					header = 'cr_header_id',
					header_id = '$cr_header_id',
					particulars = '$particulars'
			";

			mysql_query($query) or die(mysql_error());
			$gltran_header_id=mysql_insert_id();

			/*DETAILS*/
			$result = mysql_query("
				select * from cr_detail where cr_header_id = '$cr_header_id'
			") or die(mysql_error());
			$deductions = 0;
			while($r = mysql_fetch_assoc($result)){
				$deductions += $r['_amount'];
				$this->insertIntoGLDetails($gltran_header_id,$r['gchart_id'],$r['_amount'],0,$project_id);
			}

			$amount = $cash_amount - $deductions;
			$this->insertIntoGLDetails($gltran_header_id,$cash_gchart_id,$amount,0,$project_id);
			$this->insertIntoGLDetails($gltran_header_id,$ar_gchart_id,0,$cash_amount,$project_id);


			mysql_query("
				insert into
					posted_headers
				set
					header_id='$cr_header_id',
					journal_code='CR',
					gltran_header_id='$gltran_header_id',
					header='cr_header_id'
			") or die(mysql_error());

			return $gltran_header_id;
		}

		function postAR($sales_invoice_id){

			$result=mysql_query("
				select
					*
				from
					sales_invoice
				where
					sales_invoice_id = '$sales_invoice_id'

			") or die(mysql_error());

			$r = mysql_fetch_assoc($result);

		    $sales_invoice_id		= $r['sales_invoice_id'];
			$sales_invoice_id_pad	= str_pad($sales_invoice_id,7,0,STR_PAD_LEFT);
            $date					= $r['date'];
            $project_id				= $r['project_id'];
			$project				= $this->attr_Project($project_id,'project_name');
            $or_no					= $r['or_no'];

			$bank					= $r['bank'];
			$checkno				= $r['checkno'];
            $checkdate				= $r['checkdate'];
			$checkdate_display		= ($checkdate == "0000-00-00")?"":date("F j, Y",strtotime($checkdate));

            $amount					= $r['amount'];
			$checkamount			= $r['checkamount'];

            $status					= $r['status'];
            $user_id				= $r['user_id'];


			/*INSERT TO GL_HEADER*/
			$journal_id=$this->getJournalID("SJ");
			$generalreference=$this->generateJournalReference($journal_id);


			$gl_account_id="p-".$project_id;


			$query="
				insert into
					gltran_header
				set
					generalreference='$generalreference',
					date='$date',
					journal_id='$journal_id',
					status='S',
					user_id='$user_id',
					account_id='$gl_account_id',
					xrefer = 'Invoice # : ".str_pad($sales_invoice_id,7,0,STR_PAD_LEFT)."',
					checkdate = '',
					mcheck = '',
					bank = '',
					header = 'sales_invoice_id',
					header_id = '$sales_invoice_id'
			";

			mysql_query($query) or die(mysql_error());
			$gltran_header_id=mysql_insert_id();

			/*
			DETAILS
			*/

			$progress_billing = 5;
			$ar  = 75;

			$this->insertIntoGLDetails($gltran_header_id,$ar,$amount,0);
			$this->insertIntoGLDetails($gltran_header_id,$progress_billing,0,$amount);


			mysql_query("
				insert into
					posted_headers
				set
					header_id='$sales_invoice_id',
					journal_code='SJ',
					gltran_header_id='$gltran_header_id',
					header='sales_invoice_id'
			") or die(mysql_error());
		}


		function postAR_Payment($sales_invoice_id,$account_id,$deduction_gchart_id,$deduction_amount){

			$total_deduction_amount	= 0;
			foreach($deduction_amount as $amount){
				$total_deduction_amount += $amount;
			}

			$result=mysql_query("
				select
					*
				from
					sales_invoice
				where
					sales_invoice_id = '$sales_invoice_id'

			") or die(mysql_error());

			$r = mysql_fetch_assoc($result);

		    $sales_invoice_id		= $r['sales_invoice_id'];
			$sales_invoice_id_pad	= str_pad($sales_invoice_id,7,0,STR_PAD_LEFT);
            $date					= $r['date'];
            $project_id				= $r['project_id'];
			$project				= $this->attr_Project($project_id,'project_name');
            $or_no					= $r['or_no'];

			$bank					= $r['bank'];
			$checkno				= $r['checkno'];
            $checkdate				= $r['checkdate'];
			$checkdate_display		= ($checkdate == "0000-00-00")?"":date("F j, Y",strtotime($checkdate));

            $amount					= $r['amount'];
			$checkamount			= $r['checkamount'];

            $status					= $r['status'];
            $user_id				= $r['user_id'];


			/*INSERT TO GL_HEADER*/
			$journal_id=$this->getJournalID("CR");
			$generalreference=$this->generateJournalReference($journal_id);


			$gl_account_id="p-".$project_id;


			$query="
				insert into
					gltran_header
				set
					generalreference='$generalreference',
					date='$date',
					journal_id='$journal_id',
					status='S',
					user_id='$user_id',
					account_id='$gl_account_id',
					xrefer = 'Invoice # : ".str_pad($sales_invoice_id,7,0,STR_PAD_LEFT)."',
					checkdate = '$checkdate',
					mcheck = '$checkno',
					bank = '$bank',
					header = 'sales_invoice_id',
					header_id = '$sales_invoice_id'
			";

			mysql_query($query) or die(mysql_error());
			$gltran_header_id=mysql_insert_id();

			/*
			DETAILS
			*/

			$cash_account = $account_id;
			$ar  = 75;

			$this->insertIntoGLDetails($gltran_header_id,$cash_account,$checkamount,0);
			$this->insertIntoGLDetails($gltran_header_id,$ar,0,$checkamount+$total_deduction_amount);

			$i = 0;
			foreach($deduction_gchart_id as $gchart_id){
				$gchart_amount = $deduction_amount[$i++];

				if($gchart_amount > 0 && !empty($gchart_id)){
					$this->insertIntoGLDetails($gltran_header_id,$gchart_id,$gchart_amount,0);
				}
			}

			mysql_query("
				insert into
					posted_headers
				set
					header_id='$sales_invoice_id',
					journal_code='CR',
					gltran_header_id='$gltran_header_id',
					header='sales_invoice_id'
			") or die(mysql_error());
		}


		function postInventoryAdjustments($invadjust_hdr_id){

			$invadjust_hdr_id_pad = str_pad($invadjust_hdr_id,7,0,STR_PAD_LEFT);

			$result=mysql_query("
				select
					*
				from
					invadjust_header
				where
					invadjust_hdr_id = '$invadjust_hdr_id'
			") or die(mysql_error());

			$r=mysql_fetch_assoc($result);
			$date		= $r['date'];

			$journal_id=$this->getJournalID("JV");
			$generalreference=$this->generateJournalReference($journal_id);

			$result=mysql_query("
				insert into
					gltran_header
				set
					date='$date',
					generalreference='$generalreference',
					journal_id='$journal_id',
					status='S',
					xrefer = '$invadjust_hdr_id_pad'
			") or die(mysql_error());

			$gltran_header_id=mysql_insert_id();

			$accounts_payable 		= 1;
			$cash_on_hand 			= 2;
			$cash_on_bank 			= 3;
			$accounts_receivable 	= 4;
			$sales 					= 5;
			$sales_discount 		= 6;
			$merchandise_inventory  = 7;
			$cost_of_goods_sold 	= 8;


			$result=mysql_query("
				select
					sum(quantity) as quantity,
					stock_id
				from
					invadjust_header as h, invadjust_detail as d
				where
					h.invadjust_hdr_id = d.invadjust_hdr_id
				and
					h.invadjust_hdr_id='$invadjust_hdr_id'
				group by
					stock_id

			") or die(mysql_error());

			while($r=mysql_fetch_assoc($result)){
				$quantity			= $r[quantity];
				$stock_id			= $r[stock_id];
				$cost				= $this->attr_stock($stock_id,"cost");

				$stock_amount 		= $cost * $quantity;

				if($quantity>=0){
					$this->insertIntoGLDetails($gltran_header_id,$merchandise_inventory,$stock_amount,0);
					$this->insertIntoGLDetails($gltran_header_id,$sales,0,$stock_amount);
				}else{
					$this->insertIntoGLDetails($gltran_header_id,$cost_of_goods_sold,0-$stock_amount,0);
					$this->insertIntoGLDetails($gltran_header_id,$merchandise_inventory,"",0-$stock_amount);
				}
			}

			mysql_query("
				insert into
					posted_headers
				set
					header_id='$invadjust_hdr_id',
					journal_code='JV',
					gltran_header_id='$gltran_header_id',
					header='invadjust_hdr_id'
			") or die(mysql_error());

		}

		function unpost($gltran_header_id){
			mysql_query("
				delete from
					posted_headers
				where
					gltran_header_id='$gltran_header_id'
			") or die(mysql_error());
		}

		function checkGLEntry($header_id,$header){

			$sql=mysql_query("select * from gltran_header where header = '$header' AND header_id = '$header_id' AND status != 'C'");

			if(mysql_num_rows($sql)){
					return 1;
			}else{
					return 0;
			}
		}

		function checkGLIfBalance($gltran_header_id){
			$result=mysql_query("
				select
					sum(debit) as debit,
					sum(credit) as credit
				from
					gltran_detail
				where
					gltran_header_id='$gltran_header_id'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			$debit=$r[debit];
			$credit=$r[credit];

			if($debit==$credit){
				return 1;
			}else{
				return 0;
			}
		}

		function solveWeightedAvg($product_convert_id,$date,$quantity,$finish=NULL){
			$result=mysql_query("
				select
					*
				from
					product_convert
				where
					product_convert_id='$product_convert_id'
			") or die(mysql_error());

			$r=mysql_fetch_assoc($result);


			$date = date('Y-m-d',strtotime ('+1 day' , strtotime ( $date)));

			$stock_id				= $r[stock_id];
			$finishedproduct_id		= $r[finishedproduct_id];

			$oldcost				= $this->getCostOfStock($stock_id);
			$inventorybalance		= $this->getCurrentBalanceOfStock($stock_id,$date);
			//$quantity				= $r[quantity];
			$cost					= $this->getCostOfStock($finishedproduct_id);

			$newcost	= ( ( ( $inventorybalance - $quantity ) * $oldcost ) + ( $quantity * $cost ) ) / ( ( $inventorybalance - $quantity ) + $quantity );
			//echo " ( ( ( $inventorybalance - $quantity ) * $oldcost ) + ( $quantity * $cost ) ) / ( ( $inventorybalance - $quantity ) + $quantity ) - $newcost";
			if($finish){
				mysql_query("
					update
						productmaster
					set
						cost='$newcost'
					where
						stock_id='$stock_id'
				") or die(mysql_error);
			}
		}

		function getDistributionTypeOptions($selectedid=NULL,$name='distributiontype',$js=NULL){
			$content="
				<select name='$name' id='$name' $js>
					<option value=''>Select Distribution Type:</option>
			";

			$types=array(
						"Branch",
						"Dealer",
						"Special Order"
					);


			foreach($types as $type){
				$selected=($selectedid==$type)?"selected='selected'":"";
				$content.="
					<option value='$type' $selected>$type</option>
				";
			}
			$content.="
				</select>
			";
			return $content;

		}

		function getOrdersFromAccount($date,$account_id,$stock_id){
			$result=mysql_query("
				select
					sum(quantity) as quantity
				from
					order_header as h,order_details as d
				where
					h.order_header_id = d.order_header_id
				and
					date = '$date'
				and
					account_id = '$account_id'
				and
					status!='C'
				and
					stock_id = '$stock_id'
			") or die(mysql_error());

			$r=mysql_fetch_assoc($result);

			return $r[quantity];
		}

		function getDeliveriesFromAccount($date,$account_id,$stock_id){
			$result=mysql_query("
				select
					sum(quantity) as quantity
				from
					dr_header as h,dr_detail as d
				where
					h.dr_header_id = d.dr_header_id
				and
					date = '$date'
				and
					account_id = '$account_id'
				and
					status!='C'
				and
					stock_id = '$stock_id'
			") or die(mysql_error());

			$r=mysql_fetch_assoc($result);

			return $r[quantity];
		}

		function getAllAccounts(){
			$result=mysql_query("
				select
					*
				from
					account
				order by
					account
			") or die(mysql_error());

			$account_array=array();
			while($r=mysql_fetch_assoc($result)){
				$account_id = $r[account_id];
				array_push($account_array,$account_id);
			}

			return $account_array;
		}

		function formulationAttr($formulation_header_id,$column){
			$result=mysql_query("
				select
					*
				from
					formulation_header
				where
					formulation_header_id='$formulation_header_id'
			");
			$r=mysql_fetch_assoc($result);

			return $r[$column];
		}

		function attr_stock($stock_id,$column){
			$result=mysql_query("
				select
					*
				from
					productmaster
				where
					stock_id='$stock_id'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);

			return $r[$column];
		}


		function getStockReturnStatusOptions($selectedid=NULL,$name='sr_status[]',$js=NULL){

			$content="
				<select name='$name' id='$name' $js>
					<option value=''>Select Status:</option>
			";

			$types=array(
						"Good" => "Good",
						"Damaged" => "Damaged",
						"Recyclable" => "Recyclable"
					);


			foreach($types as $type => $key){
				$selected=($selectedid==$key)?"selected='selected'":"";
				$content.="
					<option value='$key' $selected>$type</option>
				";
			}
			$content.="
				</select>
			";
			return $content;

		}

		function table_deliveryForReturns($dr_header_id){

			$content='
				<table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
					<tr bgcolor="#C0C0C0">
						<th width="20"><b>#</b></th>
						<th><b>Stock</b></th>
						<th><b>Quantity</b></th>
						<th><b>SRP</b></th>
						<th><b>Discount ( % )</b></th>
						<th><b>Price</b></th>
						<th><b>Amount</b></th>
						<th><b>Status</b></th>
					</tr>
				';

				$query="
					select
						*
					from
						dr_detail
					where
						dr_header_id='$dr_header_id'
				";

				$result=mysql_query($query);

				$netamount=0;
				$grossamount=0;
				$totaldiscount=0;
				$i=1;
				while($r=mysql_fetch_assoc($result)):
					$dr_detail_id		= $r[dr_detail_id];
					$quantity			= $r[quantity];
					$stock_id			= $r[stock_id];
					$stock			= $this->attr_stock($stock_id,'stock');;
					$srp				= $r[srp];
					$discount			= $r[discount];
					$price 				= $r[price];
					$amount				= $r[amount];


					$netamount+=$r[amount];
					$grossamount+=($r[srp]*$r[quantity]);

					$discount=($r[discount]/100)*$r[quantity]*$r[srp];
					$totaldiscount+=$discount;


					$content.='
						<tr>
							<td>'.$i++.'</td>
							<td>'.$stock.'</td>
							<td><div align="right"><input type="text" class="textbox3" name="quantity[]" value="" ></div></td>
							<td><div align="right">P '.number_format($srp,2,'.',',').' </div></td>
							<td><div align="right">'.$discount.'</div></td>
							<td><div align="right">P '.number_format($price,2,'.',',').'</div></td>
							<td><div align="right">P '.number_format($amount,2,'.',',').'</div></td>
							<td>'.$this->getStockReturnStatusOptions().'</td>

							<input type="hidden" name="srp[]" value="'.$srp.'">
							<input type="hidden" name="discount[]" value="'.$discount.'">
							<input type="hidden" name="stock_id[]" value="'.$stock_id.'">
						</tr>
					';
				endwhile;

				$content.='
					</table>
					<table style="color:#F00; font-weight:bolder; width:100%;" >
						<tr>
							<td width="90%"><div align="right">Gross Amount:</div></td>
							<td><div align="right">'.number_format($grossamount,2,'.',',').'</div></td>
						</tr>
						<tr>
							<td width="90%"><div align="right">Total Discount:</div></td>
							<td><div align="right">'.number_format($totaldiscount,2,'.',',').'</div></td>
						</tr>
						<tr>
							<td width="90%"><div align="right">Net Amount:</div></td>
							<td><div align="right">'.number_format($netamount,2,'.',',').'</div></td>
						</tr>
					</table>
				';

				return $content;
		}

		function table_orderForJO($order_header_id){

			$content='
				<table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
					<tr bgcolor="#C0C0C0">
						<th width="20"><b>#</b></th>
						<th><b>Stock</b></th>
						<th><b>Formulation</b></th>
						<th><b>Formulation Quantity</b></th>
						<th><b>Actual Output</b></th>
					</tr>
				';

				$query="
					select
						*
					from
						order_details
					where
						order_header_id='$order_header_id'
				";

				$result=mysql_query($query) or die(mysql_error());

				$i=1;
				while($r=mysql_fetch_assoc($result)):

					$order_detail_id		= $r['order_detail_id'];
					$formulation_header_id	= $r['formulation_header_id'];
					$formulation_code		= $this->formulationAttr($formulation_header_id,'formulation_code');
					$formulation_quantity	= $this->formulationAttr($formulation_header_id,'output');
					$stock_id				= $r['stock_id'];
					$stock					= $this->attr_stock($stock_id,'stock');


					$content.='
						<tr>
							<td>'.$i++.'</td>
							<td>'.$stock.'</td>
							<td>'.$formulation_code.'</td>
							<td align="right">'.number_format($formulation_quantity,2,'.',',').'</td>
							<td><input type="text" class="textbox" name="actualoutput[]" ></td>

							<input type="hidden" name="formulation_header_id[]" value="'.$formulation_header_id.'">
							<input type="hidden" name="quantity[]" value="'.$quantity.'">
							<input type="hidden" name="stock_id[]" value="'.$stock_id.'">
						</tr>
					';
				endwhile;


				return $content;
		}

		function table_stockReturns($return_header_id){

			$result=mysql_query("
				select
					status
				from
					return_header
				where
					return_header_id = '$return_header_id'
			") or die(mysql_error());

			$r=mysql_fetch_assoc($result);
			$status = $r[status];

			$content='
				<table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
					<tr bgcolor="#C0C0C0">
						<th width="20"><b>#</b></th>
				';
			if($status=='S'){
			$content.='
						<th width="20"></th>
					';
			}
			$content.='
						<th><b>Quantity</b></th>
						<th><b>Stock</b></th>
						<th><b>Price</b></th>
						<th><b>Amount</b></th>
						<th>Status</th>
					</tr>
				';

				$query="
					select
						*
					from
						return_details
					where
						return_header_id = '$return_header_id'
				";

				$result=mysql_query($query);

				$netamount=0;
				$grossamount=0;
				$totaldiscount=0;
				$totalquantity=0;
				$i=1;
				while($r=mysql_fetch_assoc($result)):
					$return_detail_id		= $r[return_detail_id];
					$stock_id				= $r[stock_id];
					$srp					= $r[srp];
					$amount					= $r[amount];
					$quantity				= $r[quantity];
					$discount				= $r[discount];
					$sr_status				= $r[sr_status];

					$price = $srp - ( $srp * ($discount / 100) );

					$totalquantity+=$quantity;
					$netamount+=$amount;
					$grossamount+= $srp * $quantity;
					$totaldiscount+=$srp * $quantity *( $discount / 100 );

					$content.='
						<tr>
							<td>'.$i++.'</td>
					';
					if($status=='S'){
					$content.='
							<td><img src="images/trash.gif" style="cursor:pointer;" onclick="xajax_removeStockReturnsDetail(\''.$return_detail_id.'\');"/></td>
					';
					}

					$content.='
							<td><input type="text" class="textbox" name="detail_quantity[]" value="'.$quantity.'" ></td>
							<td>'.$this->getMaterialName($r[stock_id]).'</td>
							<td><div align="right">P '.number_format($srp,2,'.',',').' </div></td>
							<td><div align="right">P '.number_format($amount,2,'.',',').'</td>
							<td>'.$this->getStockReturnStatusOptions($sr_status,'detail_sr_status[]').'</td>
							<input type="hidden" name="detail_srp[]" value="'.$srp.'" />
							<input type="hidden" name="return_detail_id[]" value="'.$return_detail_id.'" />
						</tr>
					';
				endwhile;

				$vatsales 	= $netamount / 1.12;
				$vat		= $netamount - $vatsales;

				$content.='
					</table>
					<table style="color:#F00; font-weight:bolder; width:100%;" >
						<tr>
							<td width="90%"><div align="right">Total Quantity:</div></td>
							<td><div align="right">'.number_format($totalquantity,2,'.',',').'</div></td>
						</tr>
						<tr>
							<td width="90%"><div align="right">Vat Sales:</div></td>
							<td><div align="right">'.number_format($vatsales,2,'.',',').'</div></td>
						</tr>
						<tr>
							<td width="90%"><div align="right">12% VAT:</div></td>
							<td><div align="right">'.number_format($vat,2,'.',',').'</div></td>
						</tr>
						<tr>
							<td width="90%"><div align="right">Total Sales:</div></td>
							<td><div align="right">'.number_format($netamount,2,'.',',').'</div></td>
						</tr>
					</table>
				';

				if($status=="S"){

					mysql_query("
						update
							return_header
						set
							totalamount='$netamount'
						where
							return_header_id='$return_header_id'
					") or die(mysql_error());
				}

				return $content;
		}

		function getStockPrices($stock_id,$name='price',$js=NULL){
			$result=mysql_query("
				select
					*
				from
					productmaster
				where
					stock_id = '$stock_id'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			$prices = array();
			for($x=1 ; $x<=10 ; $x++){
				$price_name = price.$x;
				$price = $r[$price_name];
				array_push($prices,$price);
			}

			$content="
				<select name='$name' id='$name' $js >
			";

			foreach($prices as $price){
				if(!empty($price) && $price > 0){
					$content.="
						<option value='$price'>$price</option>
					";
				}
			}
			$content.="
				</select>
			";
			return $content;
		}

		function table_orderForDR($order_header_id){

			$result=mysql_query("
				select
					status
				from
					order_header
				where
					order_header_id='$order_header_id'
			") or die(mysql_error());

			$r=mysql_fetch_assoc($result);
			$status = $r[status];

			$content='
				<table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
					<tr bgcolor="#C0C0C0">
						<th width="20"><b>#</b></th>
				';
			if($status=='S'){
			$content.='
						<th width="20"></th>
					';
			}
			$content.='
						<th><b>Quantity</b></th>
						<th><b>Stock</b></th>
						<th><b>Price</b></th>
						<th><b>Discount (%) </b></th>
					</tr>
				';

				$query="
					select
						*
					from
						order_details
					where
						order_header_id='$order_header_id'
				";

				$result=mysql_query($query);

				$netamount=0;

				$i=1;
				while($r=mysql_fetch_assoc($result)):
					$order_detail_id		= $r[order_detail_id];
					$stock_id				= $r[stock_id];
					$price					= $r[price];
					$amount					= $r[amount];
					$quantity				= $r[quantity];

					$netamount+=$amount;

					$content.='
						<tr>
							<td>'.$i++.'</td>
					';
					if($status=='S'){
					$content.='
							<td><img src="images/trash.gif" style="cursor:pointer;" onclick="xajax_removeOrderDetails(\''.$order_detail_id.'\' , \''.$order_header_id.'\');"/></td>
					';
					}

					$content.='
							<td><input type="text" class="textbox" name="detail_quantity[]" ></td>
							<td>'.$this->getMaterialName($r[stock_id]).'</td>
							<td><div align="right">P '.number_format($price,2,'.',',').' </div></td>
							<td><input type="text" class="textbox" name="detail_discount[]"  value="0.00"  ></td>
							<input type="hidden" name="detail_price[]" value="'.$price.'" />
							<input type="hidden" name="detail_stock_id[]" value="'.$stock_id.'" />
						</tr>
					';
				endwhile;

				return $content;
		}

		function deliveryStatus($order_header_id,$stock_id){

			$result=mysql_query("
				select
					sum(quantity) as quantity
				from
					dr_header as h, dr_detail as d
				where
					h.dr_header_id = d.dr_header_id
				and
					status!='C'
				and
					order_header_id='$order_header_id'
				and
					stock_id = '$stock_id'
			") or die(mysql_error());

			$r=mysql_fetch_assoc($result);
			$dr_quantity = $r['quantity'];

			$result=mysql_query("
				select
					sum(quantity) as quantity
				from
					order_header as h, order_details as d
				where
					h.order_header_id = d.order_header_id
				and
					status!='C'
				and
					h.order_header_id='$order_header_id'
				and
					stock_id = '$stock_id'
			") or die(mysql_error());

			$r=mysql_fetch_assoc($result);
			$order_quantity = $r['quantity'];

			return $order_quantity - $dr_quantity;
		}

		function totalOrders($date,$stock_id){
			$date = date('Y-m-d',strtotime ('-1 day' , strtotime ( $date )));

			$result=mysql_query("
				select
					sum(quantity) as quantity
				from
					order_header as h, order_details as d
				where
					h.order_header_id = d.order_header_id
				and
					date = '$date'
				and
					status != 'C'
				and
					stock_id = '$stock_id'
			") or die(mysql_error());
			$r = mysql_fetch_assoc($result);
			return $r[quantity];
		}


		function getFormulationsFromProduction($production_id){
			$formulations = array();
			$result = mysql_query("
				select
					*
				from
					production_formulations
				where
					production_id = '$production_id'
			") or die(mysql_error());

			while($r=mysql_fetch_assoc($result)){
				$formulation_header_id = $r['formulation_header_id'];
				array_push($formulations,$formulation_header_id);
			}
			return $formulations;
		}
		function option_scopeofwork($id=NULL,$project_id,$name='scope_of_work',$label='Select Scope of Work:'){

			$content="
				<select name='$name' id='$name' class='select'>
					<option value=''>$label</option>
			";

			$result=mysql_query("
				select
					scope_of_work
				from
					budget_header
				where
					status != 'C'
				and
					project_id = '$project_id'
				group by
					scope_of_work
				order by
					scope_of_work asc
			") or die(mysql_error());

			while($r=mysql_fetch_assoc($result)){
				$scope_of_work = $r['scope_of_work'];

				$selected = ($id==$scope_of_work)?"selected='selected'":"";
				$content.="
					<option value='$scope_of_work' $selected >$scope_of_work</option>
				";
			}

			$content.="
				</select>
			";

			return $content;
		}


		function attr_workcategory($id,$column){
			$result = mysql_query("
				select
					*
				from
					work_category
				where
					work_category_id = '$id'
			") or die(mysql_error());
			$r = mysql_fetch_assoc($result);
			return $r[$column];
		}

		function option_workcategory($id=NULL,$name='work_subcategory_id',$label="No Parent Category",$arg=array('level' => '1')){
			$query = "
				select
					*
				from
					work_category
			";

			if(!empty($arg)){
				$i=1;
				foreach($arg as $col => $val){
					if($i==1){
						$query.="
							where $col = '$val'
						";
					}else{
						$query.="
							and $col = '$val'
						";
					}
					$i++;
				}
			}
			$query.="order by work asc";

			$result=mysql_query($query) or die(mysql_error());
			$content="
				<select name='$name' id='$name' class='select'>
					<option value=''>$label</option>
			";
			while($r=mysql_fetch_assoc($result)){
				$work_category_id	= $r['work_category_id'];
				$work				= $r['work'];

				$selected = ($id==$work_category_id)?"selected='selected'":"";
				$content.="
					<option value='$work_category_id' $selected >$work</option>
				";
			}
			$content.="
				</select>
			";

			return $content;
		}


		function option_category1($id,$name='categ_id1',$label='All Categories'){
			$result=mysql_query("
				select
					category,
					categ_id
				from
					categories
			") or die(mysql_error());
			$content="
				<select name='$name' id='$name'>
					<option value=''>$label</option>
			";
			while($r=mysql_fetch_assoc($result)){
				$categ_id = $r['categ_id'];
				$category = $r['category'];

				$selected = ($id==$categ_id)?"selected='selected'":"";
				$content.="
					<option value='$categ_id' $selected >$category</option>
				";
			}
			$content.="
				</select>
			";

			return $content;
		}

		function option_rr_in($id,$name='rr_in'){
			$list = array("W" =>"Warehouse" , "P" => "Project");
			$content="
				<select name='$name' id='$name'>
			";

			foreach($list as $list_value => $list_item){
				$selected = ($id==$list_value)?"selected='selected'":"";
				$content.="
					<option value='$list_value' $selected >$list_item</option>
				";
			}

			$content.="
				</select>
			";

			return $content;
		}


		function attr_Category($category_id,$column){
			$result=mysql_query("
				select
					*
				from
					categories
				where
					categ_id = '$category_id'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			return $r[$column];
		}

		function attr_Supplier($account_id,$column){
			$result=mysql_query("
				select
					*
				from
					supplier
				where
					account_id = '$account_id'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			return $r[$column];
		}

		function getAttribute($table,$search_column,$search_id,$column){
			$result = mysql_query("
				select
					*
				from
					$table
				where
					$search_column = '$search_id'
			") or die(mysql_error());
			$r =  mysql_fetch_assoc($result);
			return $r[$column];
		}

		function attr_AccountType($account_id,$column){
			$result=mysql_query("
				select
					*
				from
					account_type
				where
					account_type_id = '$account_type_id'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			return $r[$column];
		}

		function attr_Project($id,$column){
			$result=mysql_query("
				select
					*
				from
					projects
				where
					project_id = '$id'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			return $r[$column];
		}

		function attr_Contractor($id,$column){
			$result=mysql_query("
				select
					*
				from
					contractor
				where
					contractor_id = '$id'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			return $r[$column];
		}

		function attr_Account($account_id,$column){
			$result=mysql_query("
				select
					*
				from
					account
				where
					account_id = '$account_id'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			return $r[$column];
		}


		function report_accountsWithOrders($date){
			$result=mysql_query("
				select
					account_id
				from
					order_header
				where
					status != 'C'
				and
					date = '$date'
				group by
					account_id
			") or die(mysql_error());
			$customers = array();
			while($r=mysql_fetch_assoc($result)){
				$account_id = $r['account_id'];
				array_push($customers,$account_id);
			}
			return $customers;
		}

		function report_accountsWithOrdersBetweenDates($fromdate,$todate){
			$result=mysql_query("
				select
					account_id
				from
					order_header
				where
					status != 'C'
				and
					date between '$fromdate' and '$todate'
				group by
					account_id
			") or die(mysql_error());
			$customers = array();
			while($r=mysql_fetch_assoc($result)){
				$account_id = $r['account_id'];
				array_push($customers,$account_id);
			}
			return $customers;
		}


		function report_accountsWithDelivery($date){
			$result=mysql_query("
				select
					account_id
				from
					dr_header
				where
					status != 'C'
				and
					date = '$date'
				group by
					account_id
			") or die(mysql_error());
			$customers = array();
			while($r=mysql_fetch_assoc($result)){
				$account_id = $r['account_id'];
				array_push($customers,$account_id);
			}
			return $customers;
		}

		function report_accountsWithDeliveryBetweenDates($fromdate,$todate){
			$result=mysql_query("
				select
					account_id
				from
					dr_header
				where
					status != 'C'
				and
					date '$fromdate' and '$todate'
				group by
					account_id
			") or die(mysql_error());
			$customers = array();
			while($r=mysql_fetch_assoc($result)){
				$account_id = $r['account_id'];
				array_push($customers,$account_id);
			}
			return $customers;
		}

		function report_stocksDelivered($fromdate,$todate,$stock_id,$account_id){
			$result=mysql_query("
				select
					sum(quantity) as quantity
				from
					dr_header as h, dr_detail as d
				where
					h.dr_header_id = d.dr_header_id
				and
					date between '$fromdate' and '$todate'
				and
					stock_id = '$stock_id'
				and
					account_id = '$account_id'
				and
					status != 'C'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);

			return $r['quantity'];
		}


		function report_stocksOrdered($date,$stock_id,$account_id){
			$result=mysql_query("
				select
					order_header_id
				from
					dr_header
				where
					status != 'C'
				and
					date = '$date'
				and
					account_id = '$account_id'
				group by
					order_header_id
			") or die(mysql_error());
			$orders = array();
			while($r=mysql_fetch_assoc($result)){
				$order_header_id = $r['order_header_id'];
				array_push($orders,$order_header_id);
			}

			$query="
				select
					sum(quantity) as quantity
				from
					order_header as h, order_details as d
				where
					h.order_header_id = d.order_header_id
				and
					status !=  'C'
				and
					account_id = '$account_id'
				and
					date = '$date'
				and
					stock_id = '$stock_id'
				and
					(
			";
			$i=0;
			foreach($orders as $order_header_id){
				if($i==0){
					$query.="
						h.order_header_id = '$order_header_id'
					";
				}else{
					$query.="
					or
						h.order_header_id = '$order_header_id'
					";
				}
				$i++;
			}
			$query.="
				)
				group by stock_id
			";

			//echo $query;
			$result=mysql_query($query) or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			return $r['quantity'];
		}
		
		function list_employee_type($employee_type_id){
			$result = mysql_query("
				select
					*
				from
					employee_type
				where
					employee_type_id = '$employee_type_id'
				order by
					employee_type asc
			") or die(mysql_error());

			$list = array();
			while($r=mysql_fetch_assoc($result)){
				$employee_type_id		= $r['employee_type_id'];
				$employee_type					= $r['employee_type'];

				$array = array();
				$array['employee_type_id'] = $employee_type_id;
				$array['employee_type'] = $employee_type;


				array_push($list,$array);
			}

			return $list;
		}
		
		function list_dtr_entries($dtrID){
			$result = mysql_query("
				select
					*
				from
					dtr
				where
					dtrID = '$dtrID'
				order by
					dtrID desc
			") or die(mysql_error());

			$list = array();
			while($r=mysql_fetch_assoc($result)){
				$dtrID							= $r['dtrID'];
				$employeeID						= $r['employeeID'];
				$remarks						= $r['remarks'];
				$period_from					= $r['period_from'];
				$period_to						= $r['period_to'];
				$overtime_hr					= $r['overtime_hr'];
				$legal_holiday					= $r['legal_holiday'];
				$special_holiday				= $r['special_holiday'];

				$array = array();
				$array['dtrID'] = $dtrID;
				$array['employeeID'] = $employeeID;
				$array['remarks'] = $remarks;
				$array['period_from'] = $period_from;
				$array['period_to'] = $period_to;
				$array['overtime_hr'] = $overtime_hr;
				$array['legal_holiday'] = $legal_holiday;
				$array['special_holiday'] = $special_holiday;

				array_push($list,$array);
			}

			return $list;
		}
		
		function report_productionFormulation($production_id){
			$result=mysql_query("
				select
					f.formulation_header_id,
					f.formulation_code,
					f.output
				from
					production_formulations as p, formulation_header as f
				where
					production_id = '$production_id'
				and
					p.formulation_header_id = f.formulation_header_id
			") or die(mysql_error());

			$content="";
			$i=0;
			while($r=mysql_fetch_assoc($result)){
				$formulation_header_id 	= $r['formulation_header_id'];
				$formulation_code		= $r['formulation_code'];

				if($i==0){
					$content.="
						$formulation_code
					";
				}else{
					$content.="
						, $formulation_code
					";
				}
				$i++;
			}
			return $content;
		}

		function report_productionFormulationOutput($production_id){
			$result=mysql_query("
				select
					f.formulation_header_id,
					f.formulation_code,
					f.output
				from
					production_formulations as p, formulation_header as f
				where
					production_id = '$production_id'
				and
					p.formulation_header_id = f.formulation_header_id
			") or die(mysql_error());

			$total_output = 0;
			while($r=mysql_fetch_assoc($result)){
				$formulation_header_id 	= $r['formulation_header_id'];
				$formulation_code		= $r['formulation_code'];
				$output					= $r['output'];

				$total_output += $output;
			}

			return $total_output;
		}

		function budget_stock($stock_id,$project_id,$scope_of_work,$work_category_id,$sub_work_category_id){
			$result=mysql_query("
				select
					sum(quantity) as quantity
				from
					budget_detail as d,
					budget_header as h
				where
					h.budget_header_id = d.budget_header_id
				and
					project_id = '$project_id'
				and
					stock_id = '$stock_id'
				and
					status != 'C'
				and
					work_category_id = '$work_category_id'
				and
					sub_work_category_id = '$sub_work_category_id'
				and
					scope_of_work = '$scope_of_work'
			") or die(mysql_error());

			$r=mysql_fetch_assoc($result);

			return $r['quantity'];
		}

		function in_budget($project_id,$scope_of_work,$work_category_id,$sub_work_category_id,$stock_id,$added_quantity){
			$result = mysql_query("
				select
					sum(quantity) as quantity
				from
					budget_header as h, budget_detail as d
				where
					h.budget_header_id = d.budget_header_id
				and
					project_id = '$project_id'
				and
					scope_of_work = '$scope_of_work'
				and
					work_category_id = '$work_category_id'
				and
					sub_work_category_id = '$sub_work_category_id'
				and
					stock_id = '$stock_id'
				and
					status != 'C'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			$total_budget = $r['quantity'];


			if($total_budget == 0 || empty($total_budget)){
				return 0;
			}

			/*sum(quantity) as quantity*/
			$result=mysql_query("
				select
					sum(request_quantity) as quantity
				from
					pr_header as h, pr_detail as d
				where
					h.pr_header_id = d.pr_header_id
				and
					project_id = '$project_id'
				and
					scope_of_work = '$scope_of_work'
				and
					work_category_id = '$work_category_id'
				and
					sub_work_category_id = '$sub_work_category_id'
				and
					stock_id = '$stock_id'
				and
					allowed = '1'
				and h.status != 'C'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			$total_pr = $r['quantity'] + $added_quantity;

			if($total_budget >= $total_pr){
				return 1;
			}else{
				return 0;
			}

		}

		function in_stock($stock_id){

			$total_warehouse_quantity = $this->inventory_warehouse(NULL,$stock_id);

			if($total_warehouse_quantity == 0 || empty($total_warehouse_quantity)){
				return 0;
			}

			$result=mysql_query("
				select
					sum(quantity) as quantity
				from
					pr_header as h, pr_detail as d
				where
					h.pr_header_id = d.pr_header_id
				and
					project_id = '$project_id'
				and
					stock_id = '$stock_id'
				and
					allowed = '1'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			$total_pr = (empty($r['quantity']))?0:$r['quantity'];

			//echo "Total Warehouse qty : $total_warehouse_quantity <br> Total Pr Qty : $total_pr";

			if($total_warehouse_quantity >= $total_pr){
				return 1;
			}else{
				return 0;
			}

		}

		function service_budget($project_id,$scope_of_work,$work_category_id,$sub_work_category_id,$stock_id){
			$result = mysql_query("
				select
					sum(amount) as amount
				from
					budget_header as h, budget_service_detail as d
				where
					h.budget_header_id = d.budget_header_id
				and
					project_id = '$project_id'
				and
					scope_of_work = '$scope_of_work'
				and
					work_category_id = '$work_category_id'
				and
					sub_work_category_id = '$sub_work_category_id'
				and
					stock_id = '$stock_id'
				and
					status != 'C'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			return  $r['amount'];

		}

		function equipment_budget($project_id,$scope_of_work,$work_category_id,$sub_work_category_id,$stock_id){
			$result = mysql_query("
				select
					sum(amount) as amount
				from
					budget_header as h, budget_equipment_detail as d
				where
					h.budget_header_id = d.budget_header_id
				and
					project_id = '$project_id'
				and
					scope_of_work = '$scope_of_work'
				and
					work_category_id = '$work_category_id'
				and
					sub_work_category_id = '$sub_work_category_id'
				and
					stock_id = '$stock_id'
				and
					status != 'C'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			return  $r['amount'];

		}

		function fuel_budget($project_id,$scope_of_work,$work_category_id,$sub_work_category_id,$fuel_id,$equipment_id){
			$result = mysql_query("
				select
					sum(amount) as amount
				from
					budget_header as h, budget_fuel_detail as d
				where
					h.budget_header_id = d.budget_header_id
				and
					project_id = '$project_id'
				and
					scope_of_work = '$scope_of_work'
				and
					work_category_id = '$work_category_id'
				and
					sub_work_category_id = '$sub_work_category_id'
				and
					fuel_id = '$fuel_id'
				and
					equipment_id = '$equipment_id'
				and
					status != 'C'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			return  $r['amount'];

		}

		function service_approved_request($project_id,$scope_of_work,$work_category_id,$sub_work_category_id,$stock_id){
			$result = mysql_query("
				select
					sum(amount) as amount
				from
					pr_header as h, pr_service_detail as d
				where
					h.pr_header_id = d.pr_header_id
				and
					project_id = '$project_id'
				and
					scope_of_work = '$scope_of_work'
				and
					work_category_id = '$work_category_id'
				and
					sub_work_category_id = '$sub_work_category_id'
				and
					stock_id = '$stock_id'
				and
					status != 'C'
				and
					d.allowed = '1'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			return  $r['amount'];

		}

		function equipment_approved_request($project_id,$scope_of_work,$work_category_id,$sub_work_category_id,$stock_id){
			$result = mysql_query("
				select
					sum(amount) as amount
				from
					pr_header as h, pr_equipment_detail as d
				where
					h.pr_header_id = d.pr_header_id
				and
					project_id = '$project_id'
				and
					scope_of_work = '$scope_of_work'
				and
					work_category_id = '$work_category_id'
				and
					sub_work_category_id = '$sub_work_category_id'
				and
					stock_id = '$stock_id'
				and
					status != 'C'
				and
					d.allowed = '1'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			return  $r['amount'];

		}

		function fuel_approved_request($project_id,$scope_of_work,$work_category_id,$sub_work_category_id,$fuel_id,$equipment_id){
			$result = mysql_query("
				select
					sum(amount) as amount
				from
					pr_header as h, pr_fuel_detail as d
				where
					h.pr_header_id = d.pr_header_id
				and
					project_id = '$project_id'
				and
					scope_of_work = '$scope_of_work'
				and
					work_category_id = '$work_category_id'
				and
					sub_work_category_id = '$sub_work_category_id'
				and
					fuel_id = '$fuel_id'
				and
					equipment_id = '$equipment_id'
				and
					status != 'C'
				and
					d.allowed = '1'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			return  $r['amount'];

		}

		function service_received($project_id,$scope_of_work,$work_category_id,$sub_work_category_id,$stock_id){
			$result = mysql_query("
				select
					sum(amount) as amount
				from
					po_header as h, po_service_detail as d
				where
					h.po_header_id = d.po_header_id
				and
					project_id = '$project_id'
				and
					scope_of_work = '$scope_of_work'
				and
					work_category_id = '$work_category_id'
				and
					sub_work_category_id = '$sub_work_category_id'
				and
					stock_id = '$stock_id'
				and
					status != 'C'
				and
				approval_status = 'A'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			return  $r['amount'];

		}

		function equipment_received($project_id,$scope_of_work,$work_category_id,$sub_work_category_id,$stock_id){
			$result = mysql_query("
				select
					sum(amount) as amount
				from
					po_header as h, po_equipment_detail as d
				where
					h.po_header_id = d.po_header_id
				and
					project_id = '$project_id'
				and
					scope_of_work = '$scope_of_work'
				and
					work_category_id = '$work_category_id'
				and
					sub_work_category_id = '$sub_work_category_id'
				and
					stock_id = '$stock_id'
				and
					status != 'C'
				and
					approval_status = 'A'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			return  $r['amount'];

		}

		function fuel_received($project_id,$scope_of_work,$work_category_id,$sub_work_category_id,$fuel_id,$equipment_id){
			$result = mysql_query("
				select
					sum(amount) as amount
				from
					po_header as h, po_fuel_detail as d
				where
					h.po_header_id = d.po_header_id
				and
					project_id = '$project_id'
				and
					scope_of_work = '$scope_of_work'
				and
					work_category_id = '$work_category_id'
				and
					sub_work_category_id = '$sub_work_category_id'
				and
					fuel_id = '$fuel_id'
				and
					equipment_id = '$equipment_id'
				and
					status != 'C'
				and
					approval_status = 'A'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			return  $r['amount'];

		}




		function service_po($po_header_id,$stock_id){
			$result = mysql_query("
				select
					sum(amount) as amount
				from
					po_header as h, po_service_detail as d
				where
					h.po_header_id = d.po_header_id
				and
					stock_id = '$stock_id'
				and
					status != 'C'
				and
					h.po_header_id = '$po_header_id'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			return  $r['amount'];
		}

		function service_rr_po($po_header_id,$stock_id){
			$result = mysql_query("
				select
					sum(amount) as amount
				from
					service_rr_header as h, service_rr_detail as d
				where
					h.service_rr_header_id = d.service_rr_header_id
				and
					stock_id = '$stock_id'
				and
					status != 'C'
				and
					h.po_header_id = '$po_header_id'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			return  $r['amount'];
		}

		function service_pr($pr_header_id,$stock_id){
			$result = mysql_query("
				select
					sum(amount) as amount
				from
					pr_header as h, pr_service_detail as d
				where
					h.pr_header_id = d.pr_header_id
				and
					stock_id = '$stock_id'
				and
					status != 'C'
				and
					h.pr_header_id = '$pr_header_id'
				and
					approval_status = 'A'
				and
					allowed = '1'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			return  $r['amount'];
		}

		function service_po_pr($pr_header_id,$stock_id){
			$result = mysql_query("
				select
					sum(amount) as amount
				from
					po_header as h, po_service_detail as d
				where
					h.po_header_id = d.po_header_id
				and
					stock_id = '$stock_id'
				and
					status != 'C'
				and
					h.pr_header_id = '$pr_header_id'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			return  $r['amount'];
		}

		function equipment_pr($pr_header_id,$stock_id){
			$result = mysql_query("
				select
					sum(amount) as amount
				from
					pr_header as h, pr_equipment_detail as d
				where
					h.pr_header_id = d.pr_header_id
				and
					stock_id = '$stock_id'
				and
					status != 'C'
				and
					h.pr_header_id = '$pr_header_id'
				and
					approval_status = 'A'
				and
					allowed = '1'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			return  $r['amount'];
		}

		function equipment_po_pr($pr_header_id,$stock_id){
			$result = mysql_query("
				select
					sum(amount) as amount
				from
					po_header as h, po_equipment_detail as d
				where
					h.po_header_id = d.po_header_id
				and
					stock_id = '$stock_id'
				and
					status != 'C'
				and
					h.pr_header_id = '$pr_header_id'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			return  $r['amount'];
		}

		function fuel_pr($pr_header_id,$fuel_id,$equipment_id){
			$result = mysql_query("
				select
					sum(amount) as amount
				from
					pr_header as h, pr_fuel_detail as d
				where
					h.pr_header_id = d.pr_header_id
				and
					fuel_id = '$fuel_id'
				and
					equipment_id = '$equipment_id'
				and
					status != 'C'
				and
					h.pr_header_id = '$pr_header_id'
				and
					approval_status = 'A'
				and
					allowed = '1'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			return  $r['amount'];
		}

		function fuel_warehouse_pr($pr_header_id,$fuel_id,$equipment_id){
			$result = mysql_query("
				select
					sum(warehouse_quantity) as quantity,
					cost_per_litter
				from
					pr_header as h, pr_fuel_detail as d
				where
					h.pr_header_id = d.pr_header_id
				and
					fuel_id = '$fuel_id'
				and
					equipment_id = '$equipment_id'
				and
					status != 'C'
				and
					h.pr_header_id = '$pr_header_id'
				and
					approval_status = 'A'
				and
					allowed = '1'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			$quantity 	= $r['quantity'];
			$cost		= $r['cost_per_litter'];

			$amount		= $quantity * $cost;

			return  $amount;
		}

		function fuel_po_pr($pr_header_id,$fuel_id,$equipment_id,$warehouse=0){
			$query="
				select
					sum(amount) as amount
				from
					po_header as h, po_fuel_detail as d
				where
					h.po_header_id = d.po_header_id
				and
					fuel_id = '$fuel_id'
				and
					equipment_id = '$equipment_id'
				and
					status != 'C'
				and
					h.pr_header_id = '$pr_header_id'
			";
			if($warehouse != 0){
			$query.="
				and
					supplier_id = '0'
			";
			}else{
			$query.="
				and
					supplier_id != '0'
			";
			}

			$result = mysql_query($query) or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			return  $r['amount'];
		}

		function getApprovalStatus($selectedid=NULL){
			$type=array("Approved"=>"A","Disapproved"=>"D","Pending"=>"P");
			foreach($type as $key=>$value){
				if($selectedid==$value){
					return $key;
				}
			}
			return $content;
		}

		function getProjectIDFromPR($pr_header_id){
			$result=mysql_query("
				select
					*
				from
					pr_header
				where
					pr_header_id = '$pr_header_id'
			") or die(mysql_error());

			$r=mysql_fetch_assoc($result);
			return $r['project_id'];
		}

		function list_work_sub_category($work_category_id){
			$result = mysql_query("
				select
					*
				from
					work_category
				where
					work_subcategory_id = '$work_category_id'
				order by
					work asc
			") or die(mysql_error());

			$list = array();
			while($r=mysql_fetch_assoc($result)){
				$work_category_id		= $r['work_category_id'];
				$work					= $r['work'];

				$array = array();
				$array['work_category_id'] = $work_category_id;
				$array['work'] = $work;


				array_push($list,$array);
			}

			return $list;
		}

		function option_category_type($id=NULL,$name='category_type'){

			$list = array("M" =>"Material" , "S" => "Service", "E" => "Equipment");
			$content="
				<select name='$name' id='$name' class='select'>
					<option value=''>Select Category Type : </option>
			";

			foreach($list as $list_value => $list_item){
				$selected = ($id==$list_value)?"selected='selected'":"";
				$content.="
					<option value='$list_value' $selected >$list_item</option>
				";
			}

			$content.="
				</select>
			";

			return $content;

		}

		function category_type($id){
			$list = array("M" =>"Material" , "S" => "Service", "E" => "Equipment");

			foreach($list as $list_value => $list_item){
				if($list_value == $id){
					return $list_item;
				}
			}

		}

		function option_account_type($selectedid=NULL,$name='account_type_id'){
			$content="
				<select name='$name' id='$name'>
					<option value=''>Select Account Type:</option>
			";
			$query="
				select
					*
				from
					account_type
				order by
					account_type asc
			";
			$result=mysql_query($query);
			while($r=mysql_fetch_assoc($result)):
				$select=($selectedid==$r[account_type_id])?"selected='selected'":"";
				$content.="
					<option value='$r[account_type_id]' $select >$r[account_type]</option>
				";
			endwhile;
			$content.="
				</select>
			";
			return $content;
		}

		/*
		RR
		*/
		function rr_totalStocksReceived($po_header_id,$stock_id,$price=NULL){
			$query = "
				select
					sum(quantity) as quantity
				from
					rr_header as h, rr_detail as d
				where
					h.rr_header_id = d.rr_header_id
				and
					h.status != 'C'
				and
					h.po_header_id = '$po_header_id'
				and
				(
				d.stock_id = '$stock_id' or
				d.stock_id in  (select stock_id from productmaster where parent_stock_id = '$stock_id')
				)
				";
			if($price){
			$query.="
				and
					cost = '$price'
				group by
					stock_id, cost
			";
			}

			$result=mysql_query($query) or die(mysql_error());
			$r=mysql_fetch_assoc($result);

			return $r['quantity'];
		}

		function po_totalStocksPO($pr_header_id,$stock_id){
			#unclosed po
			$t_qty = 0;
			$result=mysql_query("
				select
					sum(quantity) as quantity
				from
					po_header as h, po_detail as d
				where
					h.po_header_id = d.po_header_id
				and h.status != 'C'
				and h.pr_header_id = '$pr_header_id'
				and
				(
				d.stock_id = '$stock_id' or
				d.stock_id in  (select stock_id from productmaster where parent_stock_id = '$stock_id')
				)
				and h.closed = '0'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			$t_qty += $r['quantity'];

			#closed po
			$result=mysql_query("
				select
					h.po_header_id,
					quantity
				from
					po_header as h, po_detail as d
				where
					h.po_header_id = d.po_header_id
				and h.status != 'C'
				and h.pr_header_id = '$pr_header_id'
				and
				(
				d.stock_id = '$stock_id' or
				d.stock_id in  (select stock_id from productmaster where parent_stock_id = '$stock_id')
				)
				and h.closed = '1'
			") or die(mysql_error());
			while($r=mysql_fetch_assoc($result)){
				$stocks_received = $this->rr_totalStocksReceived($r['po_header_id'],$stock_id);
				$t_qty += $stocks_received;
			}

			return $t_qty;
		}

		function po_warehouse_totalStocksPO($pr_header_id,$stock_id){
			$result=mysql_query("
				select
					sum(quantity) as quantity
				from
					po_header as h, po_detail as d
				where
					h.po_header_id = d.po_header_id
				and
					h.status != 'C'
				and
					h.pr_header_id = '$pr_header_id'
				and
					d.stock_id = '$stock_id'
				and
					supplier_id = '0'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);

			return $r['quantity'];

		}

		function po_getBalance($pr_header_id,$stock_id,$warehouse=0){
			$query="
				select
			";
			if($warehouse == 0){
				$query.="
					sum(quantity) as quantity
				";
			}else{
				$query.="
					sum(warehouse_quantity) as quantity
				";
			}
			$query.="
				from
					pr_header as h, pr_detail as d
				where
					h.pr_header_id = d.pr_header_id
				and
					h.status != 'C'
				and
					h.pr_header_id = '$pr_header_id'
				and
					d.stock_id = '$stock_id'
				and
					allowed = '1'
				and
					approval_status = 'A'
			";

			$result=mysql_query($query) or die(mysql_error());
			$r=mysql_fetch_assoc($result);

			$pr_quantity  = $r['quantity'];

			/*$query="
				select
					sum(quantity) as quantity
				from
					po_header as h, po_detail as d
				where
					h.po_header_id = d.po_header_id
				and
					pr_header_id = '$pr_header_id'
				and
					stock_id = '$stock_id'
			";

			if($warehouse != 0){
				$query.="
					and
						supplier_id = '0'
				";
			}else{
				$query.="
					and
						supplier_id != '0'
				";
			}

			$result=mysql_query($query) or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			$po_quantity	= $r['quantity'];*/
			$po_quantity 	= $this->po_totalStocksPO($pr_header_id,$stock_id);

			$balance = $pr_quantity - $po_quantity;
			return $balance;
		}

		function rr_getBalance($po_header_id,$stock_id){
			$result=mysql_query("
				select
					sum(quantity) as quantity
				from
					rr_header as h, rr_detail as d
				where
					h.rr_header_id = d.rr_header_id
				and
					h.status != 'C'
				and
					h.po_header_id = '$po_header_id'
				and
					d.stock_id = '$stock_id'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);

			$rr_quantity  = $r['quantity'];

			$result=mysql_query("
				select
					sum(quantity) as quantity
				from
					po_detail
				where
					po_header_id = '$po_header_id'
				and
					stock_id = '$stock_id'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			$po_quantity	= $r['quantity'];

			$balance = $po_quantity - $rr_quantity;
			return $balance;
		}

		function hasBudget($project_id){
			$result=mysql_query("
				select
					*
				from
					budget_header
				where
					project_id = '$project_id'
			") or die(mysql_errorr());

			if(mysql_num_rows($result) >= 1){
				return TRUE;
			}else{
				return FALSE;
			}

		}

		/*
			PURHCASE REQUEST
		*/

		function purchase_request_approved_stock($stock_id,$project_id,$work_category_id,$sub_work_category_id){
			$result=mysql_query("
				select
					sum(quantity) as quantity
				from
					pr_header as h, pr_detail as d
				where
					h.pr_header_id = d.pr_header_id
				and
					status != 'C'
				and
					approval_status = 'A'
				and
					work_category_id = '$work_category_id'
				and
					sub_work_category_id = '$sub_work_category_id'
				and
					stock_id = '$stock_id'
				and
					allowed = '1'
			") or die(mysql_error());

			$r=mysql_fetch_assoc($result);
			return $r['quantity'];
		}


		/*
			ISSUANCE
		*/

		function issuance_warehouseqty($stock_id,$project_id){
			$result=mysql_query("
				select
					sum(quantity) as quantity
				from
					rr_header as h, rr_detail as d
				where
					h.rr_header_id = d.rr_header_id
				and
					status != 'C'
				and
					stock_id = '$stock_id'
				and
					project_id = '$project_id'
				and
					rr_in = 'W'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);

			$rr_qty = $r['quantity'];

			$result=mysql_query("
				select
					sum(quantity) as quantity
				from
					return_header as h, return_detail as d
				where
					h.return_header_id = d.return_header_id
				and
					project_id = '$project_id'
				and
					status != 'C'
				and
					stock_id = '$stock_id'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);

			$return_qty = $r['quantity'];

			$result=mysql_query("
				select
					sum(quantity) as quantity
				from
					transfer_header as h, transfer_detail as d
				where
					h.transfer_header_id = d.transfer_header_id
				and
					status != 'C'
				and
					stock_id = '$stock_id'
				and
					project_id = '$project_id'
			") or die(mysql_error());

			$r=mysql_fetch_assoc($result);
			$transferred_qty =  $r['quantity'];

			return $rr_qty + $return_qty - $transferred_qty;
		}

		function issuance_projectwarehouseqty($stock_id,$project_id){
			$result=mysql_query("
				select
					sum(quantity) as quantity
				from
					transfer_header as h, transfer_detail as d
				where
					h.transfer_header_id = d.transfer_header_id
				and
					status != 'C'
				and
					stock_id =  '$stock_id'
				and
					project_id = '$project_id'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			$project_qty = $r['quantity'];

			$result=mysql_query("
				select
					sum(quantity) as quantity
				from
					rr_header as h, rr_detail as d
				where
					h.rr_header_id = d.rr_header_id
				and
					status != 'C'
				and
					stock_id = '$stock_id'
				and
					project_id = '$project_id'
				and
					rr_in = 'P'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);

			$rr_qty = $r['quantity'];

			return $project_qty + $rr_qty;
		}

		function issuance_issuedToProject($stock_id,$project_id,$work_category_id,$sub_work_category_id,$scope_of_work){
			$result=mysql_query("
				select
					sum(quantity) as quantity
				from
					issuance_header as h, issuance_detail as d
				where
					h.issuance_header_id = d.issuance_header_id
				and
					status != 'C'
				and
					stock_id =  '$stock_id'
				and
					project_id = '$project_id'
				and
					work_category_id = '$work_category_id'
				and
					sub_work_category_id = '$sub_work_category_id'
				and
					scope_of_work = '$scope_of_work'

			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			return  $r['quantity'];
		}

		function total_approved_stocks_requested($stock_id,$project_id,$work_category_id,$sub_work_category_id,$scope_of_work){
			$result=mysql_query("
				select
					sum(quantity) as quantity
				from
					pr_header as h, pr_detail as d
				where
					h.pr_header_id = d.pr_header_id
				and
					status != 'C'
				and
					approval_status = 'A'
				and
					project_id = '$project_id'
				and
					work_category_id = '$work_category_id'
				and
					sub_work_category_id = '$sub_work_category_id'
				and
					scope_of_work = '$scope_of_work'
				and
					stock_id = '$stock_id'
				and
					allowed = '1'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			return $r['quantity'];
		}

		function itemsTransferredToProject($stock_id,$project_id){
			$result=mysql_query("
				select
					sum(quantity) as quantity
				from
					transfer_header as h, transfer_detail as d
				where
					h.transfer_header_id = d.transfer_header_id
				and
					status != 'C'
				and
					stock_id =  '$stock_id'
				and
					project_id = '$project_id'
			") or die(mysql_error());
			$r=mysql_fetch_assoc($result);
			return $r['quantity'];
		}

		function itemsIssuedToProject($stock_id,$project_id){
			$result=mysql_query("
				select
					sum(quantity) as quantity
				from
					issuance
				where
					stock_id = '$stock_id'
				and
					project_id = '$project_id'
				and
					status != 'C'
			") or die(mysql_error());

			$r=mysql_fetch_assoc($result);
			return $r['quantity'];
		}

		function itemsReturnedFromProject($stock_id,$project_id){
			$result=mysql_query("
				select
					sum(quantity) as quantity
				from
					return_header as h, return_detail as d
				where
					h.return_header_id = d.return_header_id
				and
					stock_id = '$stock_id'
				and
					project_id = '$project_id'
				and
					status != 'C'
			") or die(mysql_error());

			$r=mysql_fetch_assoc($result);
			return $r['quantity'];
		}

		/*
			INVENTORY
		*/
		#quantity_cum
		function inventory_receiving($date,$stock_id,$project_id=NULL,$rr_in = NULL , $col_quantity = "quantity"){
			$query="
				select
					sum($col_quantity) as quantity
				from
					rr_header as h, rr_detail as d
				where
					h.rr_header_id = d.rr_header_id
				and status != 'C'
				and stock_id = '$stock_id'
				and rr_type = 'M'
			";

			if(!empty($project_id)) $query.=" and  project_id = '$project_id' ";
			if(!empty($rr_in)) $query.=" and rr_in = '$rr_in'";
			if(!empty($date)) $query.=" and  date <= '$date' ";


			$result=mysql_query($query) or die(mysql_error());

			$r=mysql_fetch_assoc($result);
			return $r['quantity'];
		}



		function inventory_stockstransfer($date,$stock_id,$project_id = NULL){

			/*transfers in*/
			$query="
				select
					sum(quantity) as quantity
				from
					transfer_header as h, transfer_detail as d
				where
					h.transfer_header_id = d.transfer_header_id
				and status != 'C'
				and stock_id = '$stock_id'
			";

			if(!empty($project_id)) $query .= " and project_id = '$project_id'";
			if(!empty($date)) $query .= " and date <= '$date'";

			$result = mysql_query($query) or die(mysql_error());

			$r = mysql_fetch_assoc($result);
			$transfers_in = $r['quantity'];

			/*transfers out*/
			$sql = "
				select
					sum(quantity) as quantity
				from
					transfer_header as h
					inner join transfer_detail as d on h.transfer_header_id = d.transfer_header_id
				where
					h.status != 'C'
				and stock_id = '$stock_id'
			";
			if(!empty($date)) $sql .= " and date <= '$date'";
			if( !empty($project_id) ) $sql .= " and from_project_id = '$project_id'";

			$transfers_out = DB::conn()->query($sql)->fetch_object()->quantity;

			return $transfers_in - $transfers_out;
		}

		function inventory_stocksreturn($date,$stock_id,$project_id=NULL){

			$query="
				select
					sum(quantity) as quantity
				from
					return_header as h, return_detail as d
				where
					h.return_header_id = d.return_header_id
				and status != 'C'
				and stock_id = '$stock_id'
			";

			if(!empty($project_id)) $query.=" and project_id = '$project_id' ";
			if(!empty($date)) $query.=" and  date <= '$date'";


			$result=mysql_query($query) or die(mysql_error());

			$r=mysql_fetch_assoc($result);
			return $r['quantity'];
		}


		/*TO BE UPDATED*/
		function inventory_production_used($date,$stock_id){
			$result=mysql_query("
				select
					sum(quantity) as quantity
				from
					production_header as h, production_detail as d
				where
					h.production_header_id = d.production_header_id
				and
					status != 'C'
				and
					date <= '$date'
				and
					d.stock_id = '$stock_id'
			") or die(mysql_error());

			$r=mysql_fetch_assoc($result);
			return $r['quantity'];

		}

		function inventory_production_produced($date,$stock_id){
			$result=mysql_query("
				select
					sum(actualoutput) as actualoutput
				from
					production_header
				where
					stock_id = '$stock_id'
				and
					status != 'C'
				and
					date <= '$date'
			") or die(mysql_error());

			$r=mysql_fetch_assoc($result);
			return $r['actualoutput'];
		}

		#quantity_cum
		function inventory_issuance($date,$stock_id,$project_id , $col_quantity = "quantity"){
			$query="
				select
					sum($col_quantity) as quantity
				from
					issuance_header as h, issuance_detail as d
				where
					h.issuance_header_id = d.issuance_header_id
				and status != 'C'
				and stock_id = '$stock_id'
			";
			if(!empty($project_id)) $query.=" and project_id = '$project_id' ";
			if(!empty($date)) $query.=" and  date <= '$date' ";


			$result=mysql_query($query) or die(mysql_error());

			$r=mysql_fetch_assoc($result);
			return $r['quantity'];

		}



		function inventory_adjustments($date,$stock_id,$project_id=NULL,$work_category_id=NULL,$sub_work_category_id=NULL){

			$query="
				select
					sum(quantity) as quantity
				from
					invadjust_header as h, invadjust_detail as d
				where
					h.invadjust_header_id = d.invadjust_header_id
				and status != 'C'
				and stock_id = '$stock_id'
			";

			if(!empty($date)) $query.=" and date <= '$date' ";
			if(!empty($project_id)) $query.=" and  project_id = '$project_id' ";
			if(!empty($work_category_id)) $query.=" and work_category_id = '$work_category_id' ";
			if(!empty($sub_work_category_id)) $query.=" and  sub_work_category_id = '$sub_work_category_id' ";


			$result=mysql_query($query) or die(mysql_error());

			$r=mysql_fetch_assoc($result);
			return $r['quantity'];
		}

		function inventory_projectqty($date=NULL,$stock_id,$project_id,$scope_of_work,$work_category_id,$sub_work_category_id){
			/*
				RR + TRANSFERS - STOCKS RETURNS
			*/
			$rr_qty			= $this->inventory_actual_received($date,$stock_id,$project_id,$scope_of_work,$work_category_id,$sub_work_category_id);
			$transfer_qty	= $this->inventory_transfers_allocated_to_project($date,$stock_id,$project_id,$scope_of_work,$work_category_id,$sub_work_category_id);
			$returns_qty	= $this->inventory_returns_allocated_to_project($date,$stock_id,$project_id,$scope_of_work,$work_category_id,$sub_work_category_id);
			$adjustments_qty  = $this->inventory_adjustments($date,$stock_id,$project_id,$work_category_id,$sub_work_category_id);

			return $rr_qty + $transfer_qty - $returns_qty + $adjustments_qty;

		}

		function inventory_actual_received($date,$stock_id,$project_id,$scope_of_work,$work_category_id , $sub_work_category_id){
			$query="
				select
					sum(quantity) as quantity
				from
					rr_header as h, rr_detail as d, po_header as po
				where
					h.rr_header_id = d.rr_header_id
				and
					h.po_header_id  = po.po_header_id
				and
					h.status != 'C'
				and
					stock_id = '$stock_id'
				and
					h.project_id = '$project_id'
				and
					po.scope_of_work = '$scope_of_work'
				and
					po.work_category_id = '$work_category_id'
				and
					po.sub_work_category_id = '$sub_work_category_id'
			";

			if(!empty($rr_in)){
			$query.="
				and
					rr_in = '$rr_in'
			";
			}

			if(!empty($date)){
			$query.="
				and
					h.date <= '$date'
			";
			}

			$result=mysql_query($query) or die(mysql_error());

			$r=mysql_fetch_assoc($result);
			return $r['quantity'];

			return $rr_qty;
		}

		function total_rtp($stock_id,$project_id,$scope_of_work,$work_category_id,$sub_work_category_id){
			$result=mysql_query("
				select
					sum(quantity) as quantity
				from
					pr_header as h, pr_detail as d
				where
					h.pr_header_id = d.pr_header_id
				and
					status != 'C'
				and
					work_category_id = '$work_category_id'
				and
					sub_work_category_id = '$sub_work_category_id'
				and
					stock_id = '$stock_id'
				and
					h.project_id = '$project_id'
			") or die(mysql_error());

			$r=mysql_fetch_assoc($result);
			return $r['quantity'];

		}

		function getPurchaseReturn($date,$stock_id){
			$sql = "
				select
					ifnull( sum( quantity ) , 0 )  as quantity
				from
					preturn_header as h
					inner join preturn_detail as d on h.preturn_header_id = d.preturn_header_id
				where
					h.status != 'C'
				and preturn_void = '0'
				and date <= '$date'
				and stock_id = '$stock_id'
			";
			return DB::conn()->query($sql)->fetch_object()->quantity;
		}

		function inventory_warehouse($date,$stock_id,$quantity_type = "quantity", $project_id){
			/*
				RR (WAREHOUSE - 9)  - TRANSFERS + STOCKS RETURN
			*/

			# RECEIVED IN WAREHOUSE - STOCKS TRANSFER + STOCKS RETURN + INV ADJUSTMENTS - ISSUANCE MAN DAPAT!

			$rr_qty = $transfer_qty = $returns_qty = $adjustments = $issuance_qty = 0;

			if($quantity_type == "quantity"){

				// $rr_qty			= $this->inventory_receiving($date,$stock_id,NULL,'W');	#has cum
				// $issuance_qty 	= $this->inventory_issuance($date,$stock_id,14);	#has cum
				// $transfer_qty	= $this->inventory_stockstransfer($date,$stock_id,14);
				// $returns_qty	= $this->inventory_stocksreturn($date,$stock_id);
				// $adjustments	= $this->inventory_adjustments($date,$stock_id,14);
				// $preturn_qty    = $this->getPurchaseReturn($date,$stock_id);

				$rr_qty			= $this->inventory_receiving($date,$stock_id,NULL,'W');	#has cum
				$issuance_qty 	= $this->inventory_issuance($date,$stock_id, $project_id);	#has cum
				$transfer_qty	= $this->inventory_stockstransfer($date,$stock_id,$project_id);
				$returns_qty	= $this->inventory_stocksreturn($date,$stock_id);
				$adjustments	= $this->inventory_adjustments($date,$stock_id,$project_id);
				$preturn_qty    = $this->getPurchaseReturn($date,$stock_id);

			}else{

				$rr_qty			= $this->inventory_receiving($date,$stock_id,NULL,'W',"quantity_cum");	#has cum
				$issuance_qty 	= $this->inventory_issuance($date,$stock_id,14,"quantity_cum");	#has cum
			}

			/*
			$production_used_qty = $this->inventory_production_used($date,$stock_id);
			$production_produced_qty = $this->inventory_production_produced($date,$stock_id);
			*/

			return $rr_qty + $transfer_qty + $returns_qty + $adjustments - $issuance_qty - $preturn_qty;
		}

		function inventory_projectwarehousebalance($date,$stock_id,$project_id,$quantity_type = "quantity"){

			#RECEIVED IN PROJECT + STOCKS TRANSFER + INV ADJUSTMENTS - STOCKS RETURN - ISSUANCE
			$rr_qty = $transfer_qty = $returns_qty = $adjustments = $issuance_qty = 0;

			if($quantity_type == "quantity"){

				$rr_qty			= $this->inventory_receiving($date,$stock_id,$project_id,"P");	#has cum
				$issuance_qty 	= $this->inventory_issuance($date,$stock_id,$project_id);		#has cum
				$transfer_qty	= $this->inventory_stockstransfer($date,$stock_id,$project_id);
				$returns_qty	= $this->inventory_stocksreturn($date,$stock_id,$project_id);
				$adjustments	= $this->inventory_adjustments($date,$stock_id,$project_id);

				/*add fabrication here*/
				$fabrication_in = $this->inventory_fabrication_in($date,$stock_id,$project_id);

				/*subtract fabrication here*/
				$fabrication_out = $this->inventory_fabrication_out($date,$stock_id,$project_id);

				/*sales return*/
				$sales_return = $this->getSalesReturn($date,$stock_id,$project_id);


			} else {
				$rr_qty			= $this->inventory_receiving($date,$stock_id,$project_id,"P","quantity_cum");	#has cum
				$issuance_qty 	= $this->inventory_issuance($date,$stock_id,$project_id,"quantity_cum");		#has cum
			}

			$inventory = $rr_qty + $transfer_qty - $returns_qty - $issuance_qty + $adjustments + $fabrication_in - $fabrication_out + $sales_return;

			return $inventory;
		}


		function getSalesReturn( $date, $stock_id, $project_id ){

			$sql = "
				select
					sum(quantity) as quantity
				from
					sales_return_header as h
					inner join sales_return_detail as d on h.sales_return_header_id = d.sales_return_header_id
				where
					h.status != 'C'
				and sales_return_void = '0'
				and date <= '$date'
				and stock_id = '$stock_id'
				and project_id = '$project_id'
			";

			return DB::conn()->query($sql)->fetch_object()->quantity;
		}

		function inventory_fabrication_out( $date, $stock_id, $from_project_id ){

			$sql = "
				select
					sum(raw_mat_quantity) as quantity
				from
					fabrication as f
					inner join fabrication_raw_mat as r on f.fabrication_id = r.fabrication_id
				where
					f.status != 'C'
				and raw_mat_void = '0'
				and date <= '$date'
				and raw_mat_stock_id = '$stock_id'
				and from_project_id = '$from_project_id'
			";

			return DB::conn()->query($sql)->fetch_object()->quantity;
		}

		function inventory_fabrication_in( $date, $stock_id, $to_project_id ){

			/*product*/

			$sql = "
				select
					sum(product_quantity) as quantity
				from
					fabrication as f
					inner join fabrication_product as r on f.fabrication_id = r.fabrication_id
				where
					f.status != 'C'
				and product_void = '0'
				and date <= '$date'
				and product_stock_id = '$stock_id'
				and to_project_id = '$to_project_id'
			";



			$quantity = DB::conn()->query($sql)->fetch_object()->quantity;

			$sql = "
				select
					sum(excess_quantity) as excess_quantity
				from
					fabrication
				where
					status != 'C'
				and date <= '$date'
				and excess_stock_id = '$stock_id'
				and from_project_id = '$to_project_id'
			";


			$quantity += DB::conn()->query($sql)->fetch_object()->excess_quantity;

			return $quantity;
		}

		function inventory_rr_allocated_to_project($date=NULL,$stock_id,$project_id,$scope_of_work,$work_category_id,$sub_work_category_id){
			$query="
				select
					sum(quantity) as quantity
				from
					rr_header as h, rr_detail as d,po_header as po
				where
					h.rr_header_id = d.rr_header_id
				and
					h.po_header_id = po.po_header_id
				and
					status != 'C'
				and
					stock_id = '$stock_id'
				and
					project_id = '$project_id'
				and
					scope_of_work = '$scope_of_work'
				and
					work_category_id = '$work_category_id'
				and
					sub_work_category_id = '$sub_work_category_id'
			";

			if(!empty($date)){
			$query.="
				and
					date <= '$date'
			";
			}

			$result=mysql_query($query) or die();
			$r = mysql_fetch_assoc($result);

			return $r['quantity'];

		}

		function inventory_transfers_allocated_to_project($date=NULL,$stock_id,$project_id,$scope_of_work,$work_category_id,$sub_work_category_id){
			$query="
				select
					sum(quantity) as quantity
				from
					transfer_header as h, transfer_detail as d
				where
					h.transfer_header_id = d.transfer_header_id
				and
					status != 'C'
				and
					stock_id = '$stock_id'
				and
					project_id = '$project_id'
				and
					scope_of_work = '$scope_of_work'
				and
					work_category_id = '$work_category_id'
				and
					sub_work_category_id = '$sub_work_category_id'
			";

			if(!empty($date)){
			$query.="
				and
					date <= '$date'
			";
			}

			$result=mysql_query($query) or die();
			$r = mysql_fetch_assoc($result);

			return $r['quantity'];

		}


		function inventory_returns_allocated_to_project($date=NULL,$stock_id,$project_id,$scope_of_work,$work_category_id,$sub_work_category_id){
			$query="
				select
					sum(quantity) as quantity
				from
					return_header as h, return_detail as d
				where
					h.return_header_id  = d.return_header_id
				and
					status != 'C'
				and
					stock_id = '$stock_id'
				and
					project_id = '$project_id'
				and
					scope_of_work = '$scope_of_work'
				and
					work_category_id = '$work_category_id'
				and
					sub_work_category_id = '$sub_work_category_id'
			";

			if(!empty($date)){
			$query.="
				and
					date <= '$date'
			";
			}

			$result=mysql_query($query) or die();
			$r = mysql_fetch_assoc($result);

			return $r['quantity'];

		}




		function budgetIsPresent($project_id, $scope_of_work,$work_category_id , $sub_work_category_id){
			$result=mysql_query("
				select
					*
				from
					budget_header
				where
					status != 'C'
				and
					project_id = '$project_id'
				and
					scope_of_work = '$scope_of_work'
				and
					work_category_id = '$work_category_id'
				and
					sub_work_category_id = '$sub_work_category_id'
			") or die(mysql_error());
			$row = mysql_num_rows($result);

			if($row >= 1){
				return 1;
			}else{
				return 0;
			}

		}

		function getBudgetId($project_id, $scope_of_work,$work_category_id , $sub_work_category_id){
			$result=mysql_query("
				select
					*
				from
					budget_header
				where
					status != 'C'
				and
					project_id = '$project_id'
				and
					scope_of_work = '$scope_of_work'
				and
					work_category_id = '$work_category_id'
				and
					sub_work_category_id = '$sub_work_category_id'
			") or die(mysql_error());

			$r = mysql_fetch_assoc($result);
			$budget_header_id = $r['budget_header_id'];

			return $budget_header_id;
		}

		function option_price_list($stock_id,$name='price'){
			$content="
				<select name='$name' id='$name' >
					<option value=''>Select Price : </option>
			";

			$result = mysql_query("
				select
					*
				from
					productmaster
				where
					stock_id  = '$stock_id'
			") or die(mysql_error());

			$r = mysql_fetch_assoc($result);
			for( $i=1 ; $i<=10 ; $i++ ){
				$price = $r["price$i"];
				if($price > 0){
					$content.="
						<option value='$price'>$price</option>
					";
				}
			}
			$content.="
				</select>
			";

			return $content;
		}

		function option_Contractor($id=NULL,$name='account_id',$label='Select Subcontractor : '){
			$result=mysql_query("
				select
					*
				from
					account
				where
					account_type_id = '2'
			") or die(mysql_error());

			$content="
				<select name='$name' id='$name'>
					<option value=''>$label</option>
			";
			while($r=mysql_fetch_assoc($result)){
				$account_id 	= $r['account_id'];
				$account		= $r['account'];

				$selected = ($id==$account_id)?"selected='selected'":"";
				$content.="
					<option value='$account_id' $selected >$account</option>
				";
			}
			$content.="
				</select>
			";

			return $content;
		}

		function getAPBalance($ap_header_id){

			$result=mysql_query("
			select
				sum(p.total_amount) as total_amount
			from
				ap_detail as d, accounts_payable as p
			where
				d.ap_id = p.ap_id
			and
				ap_header_id = '$ap_header_id'
			") or die(mysql_error());

			$r=mysql_fetch_assoc($result);
			$payables = $r['total_amount'];

			$result = mysql_query("
				select
					sum(amount) as amount
				from
					ap_payment
				where
					ap_header_id = '$ap_header_id'
			") or die();

			$r=mysql_fetch_assoc($result);
			$payment = $r['amount'];

			$balance = $payables - $payment;

			return $balance;
		}

		function getARBalance($ar_header_id){

			$result=mysql_query("
			select
				sum(p.total_amount) as total_amount
			from
				ar_detail as d, accounts_receivable as p
			where
				d.ar_id = p.ar_id
			and
				ar_header_id = '$ar_header_id'
			") or die(mysql_error());

			$r=mysql_fetch_assoc($result);
			$payables = $r['total_amount'];

			$result = mysql_query("
				select
					sum(amount) as amount
				from
					ar_payment
				where
					ar_header_id = '$ar_header_id'
			") or die();

			$r=mysql_fetch_assoc($result);
			$payment = $r['amount'];

			$balance = $payables - $payment;

			return $balance;
		}

		function getAPTotalAmount($ap_header_id){
			$result=mysql_query("
			select
				sum(p.total_amount) as total_amount
			from
				ap_detail as d, accounts_payable as p
			where
				d.ap_id = p.ap_id
			and
				ap_header_id = '$ap_header_id'
			") or die(mysql_error());

			$r=mysql_fetch_assoc($result);
			$payables = $r['total_amount'];

			return $payables;
		}

		function getARTotalAmount($ar_header_id){
			$result=mysql_query("
			select
				sum(p.total_amount) as total_amount
			from
				ar_detail as d, accounts_receivable as p
			where
				d.ar_id = p.ar_id
			and
				ar_header_id = '$ar_header_id'
			") or die(mysql_error());

			$r=mysql_fetch_assoc($result);
			$payables = $r['total_amount'];

			return $payables;
		}

		function acctg_credit_normal_balance($mclass){
			if($mclass == "L" || $mclass == "I" || $mclass == "R" ){
				return 1;
			}else{
				return 0;
			}

		}

		function acctg_debit_normal_balance($mclass){
			if($mclass == "A" || $mclass == "E" ){
				return 1;
			}else{
				return 0;
			}
		}

		function hasPayment($apv_header_id){
			$result=mysql_query("
				select
					apv_header_id
				from
					ap_payment
				where
					apv_header_id = '$apv_header_id'
			") or die(mysql_error());

			$num = mysql_num_rows($result);

			if($num > 0){
				return 1;
			}else{
				return 0;
			}

		}

		function hasFinancialBudget($project_id){
			$result=mysql_query("
				select
					*
				from
					financial_budget_header
				where
					project_id	 = '$project_id'
			") or die(mysql_error());

			$num = mysql_num_rows($result);

			if($num > 0){
				return 1;
			}else{
				return 0;
			}
		}

		function getFinancialBudgetId($project_id){
			$result=mysql_query("
				select
					*
				from
					financial_budget_header
				where
					project_id = '$project_id'
			") or die(mysql_error());

			$r = mysql_fetch_assoc($result);
			$financial_budget_header_id = $r['financial_budget_header_id'];

			return $financial_budget_header_id;
		}

		function hasPOMaterials($po_header_id){
			$query="
				select
					d.stock_id,
					p.stockcode,
					p.stock,
					p.unit,
					d.quantity,
					d.cost,
					d.amount,
					d.details
				from
					po_detail as d, productmaster as p
				where
					d.stock_id = p.stock_id
				and
					po_header_id='$po_header_id'
			";
			$result=mysql_query($query) or die(mysql_error());

			$rows = mysql_num_rows($result);

			if($rows>0){
				return 1;
			}else{
				return 0;
			}
		}

		function hasPOService($po_header_id){
			$result=mysql_query("
				select
					d.stock_id,
					p.stock,
					p.unit,
					d.quantity,
					d.days,
					d.rate_per_day,
					d.amount
				from
					po_service_detail as d, productmaster as p
				where
					d.stock_id = p.stock_id
				and
					po_header_id='$po_header_id'

			") or die(mysql_error());

			$rows = mysql_num_rows($result);

			if($rows>0){
				return 1;
			}else{
				return 0;
			}
		}

		function hasPORentals($po_header_id){
			$result=mysql_query("
				select
					d.stock_id,
					p.stock,
					p.unit,
					d.quantity,
					d.days,
					d.rate_per_day,
					d.amount
				from
					po_equipment_detail as d, productmaster as p
				where
					d.stock_id = p.stock_id
				and
					po_header_id='$po_header_id'

			") or die(mysql_error());

			$rows = mysql_num_rows($result);

			if($rows>0){
				return 1;
			}else{
				return 0;
			}
		}
		function option_price_issuance($name,$stock_id,$project_id=NULL,$work_category_id=NULL,$sub_work_category_id=NULL){
			$content="
				<select name='$name' id='$name'>
					<option value=''>Select Price:</option>
			";

			$query="
				select
					cost
				from
					po_header as h, po_detail as d
				where
					h.po_header_id = d.po_header_id
				and
					status != 'C'
				and
					stock_id = '$stock_id'
			";
			/*if($project_id){
			$query.="
				and
					project_id = '$project_id'
			";
			}
			if($work_category_id){
			$query.="
				and
					work_category_id = '$work_category_id'
			";
			}
			if($sub_work_category_id){
			$query.="
				and
					sub_work_category_id = '$sub_work_category_id'
			";
			}*/

			$result=mysql_query($query);
			if(mysql_num_rows($result) > 0){
				$content .= "<option style='font-weight:bold; border-top:1px solid #000;'>PURCHASE ORDER PRICE</option>";
				while($r=mysql_fetch_assoc($result)):
					$content.="
						<option style='padding-left:10px;'>$r[cost]</option>
					";
				endwhile;
			}

			#/**from inventory adjustments
			$result = mysql_query("
						select
							cost
						from
							invadjust_header as h, invadjust_detail as d
						where
							h.invadjust_header_id = d.invadjust_header_id
						and
							stock_id = '$stock_id'
						group by cost
			") or die(mysql_error());
			if(mysql_num_rows($result) > 0){
				$content .= "<option style='font-weight:bold; border-top:1px solid #000;'>INVENOTRY ADJUSTMENTS PRICE</option>";
				while($r=mysql_fetch_assoc($result)):
					if($r['cost'] > 0){
						$content.="
							<option style='padding-left:10px;'>$r[cost]</option>
						";
					}
				endwhile;
			}
			/**/

			#/**from PRODUCT MASTER
			$result = mysql_query("
						select
							*
						from
							productmaster
						where
							stock_id = '$stock_id'
			") or die(mysql_error());
			if(mysql_num_rows($result) > 0){
				$content .= "<option style='font-weight:bold; border-top:1px solid #000;'>PRODUCT MASTER PRICE</option>";
				$r=mysql_fetch_assoc($result);

				for($x=1 ; $x<=10 ; $x++){
					if($r["price$x"] > 0){
						$content.="
							<option style='padding-left:10px;'>".$r["price$x"]."</option>
						";
					}
				}
			}

			$content.="
				</select>
			";
			return $content;
		}

		function stockIsPresent($table,$stock_id){
			$result = mysql_query("
				select
					*
				from
					$table
				where
					stock_id = '$stock_id'
				limit 0,1
			") or die(mysql_error());
			return (mysql_num_rows($result) > 0)?1:0;
		}

		function poIsAccomplished($po_header_id){
			$result = mysql_query("
				select
					sum(quantity) as quantity,
					stock_id
				from
					po_header as h, po_detail as d
				where
					h.po_header_id = d.po_header_id
				and
					h.po_header_id = '$po_header_id'
				group by
					stock_id
			") or die(mysql_error());

			$accomplished = 1;
			while($r = mysql_fetch_assoc($result)){
				$stock_id = $r['stock_id'];
				$po_quantity = $r['quantity'];

				$rr_quantity = $this->rr_totalStocksReceived($po_header_id,$stock_id);
				if($po_quantity > $rr_quantity){
					$accomplished = 0;
					break;
				}
			}

			return $accomplished;
		}

		function getMRR($po_header_id,$stock_id,$cost){
			$result = mysql_query("
				select
					h.rr_header_id
				from
					rr_header as h, rr_detail as d
				where
					h.rr_header_id = d.rr_header_id
				and
					h.status != 'C'
				and
					h.po_header_id = '$po_header_id'
				and
					d.stock_id = '$stock_id'
				and
					cost = '$cost'
				and
					h.rr_header_id not in(
						select
							rr_header_id
						from
							apv_header as h, apv_detail as d, apv_mrr_detail as mrr
						where
							h.apv_header_id = d.apv_header_id
						and
							d.apv_detail_id = mrr.apv_detail_id
						and
							h.status != 'C'
					)
			") or die(mysql_error());

			/*echo "select
					h.rr_header_id
				from
					rr_header as h, rr_detail as d
				where
					h.rr_header_id = d.rr_header_id
				and
					h.status != 'C'
				and
					h.po_header_id = '$po_header_id'
				and
					d.stock_id = '$stock_id'
				and
					h.rr_header_id not in(
						select
							rr_header_id
						from
							apv_header as h, apv_detail as d, apv_mrr_detail as mrr
						where
							h.apv_header_id = d.apv_header_id
						and
							d.apv_detail_id = mrr.apv_detail_id
						and
							h.status != 'C'
					) ";*/
			$array =  array();
			while($r = mysql_fetch_assoc($result)){
				array_push($array,str_pad($r['rr_header_id'],7,0,STR_PAD_LEFT));
			}
			return $array;
		}

		function getMRRDetails($po_header_id,$stock_id,$cost){
			$result = mysql_query("
				select
					sum(quantity) as quantity,
					cost
				from
					rr_header as h, rr_detail as d
				where
					h.rr_header_id = d.rr_header_id
				and
					h.status != 'C'
				and
					h.po_header_id = '$po_header_id'
				and
					d.stock_id = '$stock_id'
				and
					cost = '$cost'
				and
					h.rr_header_id not in(
						select
							rr_header_id
						from
							apv_header as h, apv_detail as d, apv_mrr_detail as mrr
						where
							h.apv_header_id = d.apv_header_id
						and
							d.apv_detail_id = mrr.apv_detail_id
						and
							h.status != 'C'
					)
			") or die(mysql_error());

			$r = mysql_fetch_assoc($result);
			$details = array();
			$details['quantity'] = $r['quantity'];
			$details['price'] = $r['cost'];

			return $details;
		}

		function hasAPV($po_header_id){
			$result = mysql_query("
				select
					*
				from
					apv_header
				where
					po_header_id = '$po_header_id'
				and
					status != 'C'
			") or die(mysql_error());

			if(mysql_num_rows($result) > 0){
				return 1;
			}else{
				return 0;
			}
		}

		function getOptions($name,$label,$query,$value,$key,$selected){
			$content="
				<select name='$name' id='$name'>
					<option value=''>Select $label:</option>
			";

			$result=mysql_query($query);
			while($r=mysql_fetch_assoc($result)):
				$select=($selected==$r[$value])?"selected='selected'":"";
				$content.="
					<option value='$r[$value]' $select >$r[$key]</option>
				";
			endwhile;
			$content.="
				</select>
			";
			return $content;
		}

		function computeAPV($apv_header_id,$percent){
			$discount_amount = $this->getAttribute('apv_header','apv_header_id',$apv_header_id,'discount_amount');
			
			$result = mysql_query("
				select
					sum(amount) as amount
				from	
					apv_header as h, apv_detail as d
				where
					h.apv_header_id = d.apv_header_id
				and
					h.apv_header_id = '$apv_header_id'
			") or die(mysql_error()); 
			
			$r = mysql_fetch_assoc($result);
			$amount = $r['amount'] - $discount_amount;
			return $amount * ($percent / 100);
		}

		function totalMRR($stock_id,$project_id,$work_category_id,$sub_work_category_id){
			$result = mysql_query("
				select
					sum(d.quantity) as quantity,
					sum(d.amount) as amount
				from
					rr_header as h, rr_detail as d , po_header as po
				where
					h.rr_header_id = d.rr_header_id
				and
					po.po_header_id = h.po_header_id
				and
					po.project_id = '$project_id'
				and
					po.work_category_id = '$work_category_id'
				and
					po.sub_work_category_id = '$sub_work_category_id'
				and
					d.stock_id = '$stock_id'
				and
					h.status != 'C'
			") or die(mysql_error());

			$r = mysql_fetch_assoc($result);
			$data = array();
			$data['quantity'] = $r['quantity'];
			$data['amount'] = $r['amount'];

			return $data;
		}

		function totalPR($stock_id,$project_id,$work_category_id,$sub_work_category_id){
			$result = mysql_query("
				select
					sum(d.quantity) as quantity
				from
					pr_header as h, pr_detail as d
				where
					h.pr_header_id = d.pr_header_id
				and
					h.project_id = '$project_id'
				and
					h.work_category_id = '$work_category_id'
				and
					h.sub_work_category_id = '$sub_work_category_id'
				and
					d.stock_id = '$stock_id'
				and
					h.status != 'C'
				and
					d.allowed = '1'
			") or die(mysql_error());

			$r = mysql_fetch_assoc($result);

			return $r['quantity'];

		}

		function getTableAssoc($selectedid=NULL,$name='name',$label = NULL,$query,$id_column,$display_column,$aDisplay = NULL){
			$content="
				<select name='$name' id='$name'>
					<option value=''>$label:</option>
			";

			$result=mysql_query($query);
			while($r=mysql_fetch_assoc($result)):
				$selected=($selectedid==$r[$id_column])?"selected='selected'":"";

				$display_content = $r[$display_column];
				if(!empty($aDisplay)){
					$display_content = "";
					foreach($aDisplay as $a){
						$display_content .="$r[$a] ";
					}
				}

				$content.="
					<option value='$r[$id_column]' $selected >".addslashes(htmlentities($display_content))."</option>
				";
			endwhile;

			$content.="
				</select>
			";

			return $content;
		}

		function getWitholdingTaxAttributes($supplier_id,$from_date,$to_date,$attr_date,$cleared,$cv_no = NULL){
			$options = new options();
			$query="
				select
					*
				from
					cv_header as h, supplier as s
				where
					h.supplier_id = s.account_id
				and
					status != 'C'
				and
					cleared = '$cleared'
				and
					$attr_date between '$from_date' and '$to_date'
				and
					wtax != '0'
				and
					account_id = '$supplier_id'
			";

			if(!empty($cv_no)){
			$query.="
				and
					h.cv_no = '$cv_no'
			";
			}

			$query.="
				order by
					date_cleared asc, check_no asc
			";

			$result = mysql_query($query) or die(mysql_error());

			$a = array();
			while($r = mysql_fetch_assoc($result)){
				$a[] = $r['cv_header_id'];
			}

			$total_cash_amount = $total_vatable_amount = $total_vat_amount = $total_tax_amount = 0;
			if(!empty($a)){
				foreach($a as $cv_header_id){
					$total_cash_amount 		+= $this->getCashAmount($cv_header_id);
					$total_vatable_amount 	+= $this->getVatableAmount($cv_header_id);
					$total_vat_amount		+= $this->getVatAmount($cv_header_id);
					$total_tax_amount		+= $this->getTaxAmount($cv_header_id);
				}
			}

			$r = array();
			$r['cash'] 		= $total_cash_amount;
			$r['vatable'] 	= $total_vatable_amount;
			$r['vat']		= $total_vat_amount;
			$r['tax']		= $total_tax_amount;

			return $r;
		}

		function getBIRWitholdingTaxAttributes($supplier_id,$from_date,$to_date,$attr_date,$cleared,$cv_no = NULL){
			$options = new options();
			$query="
				select
					*
				from
					cv_header as h, supplier as s
				where
					h.supplier_id = s.account_id
				and
					status != 'C'
				and
					$attr_date between '$from_date' and '$to_date'
				and
					wtax != '0'
				and
					account_id = '$supplier_id'
			";

			if(!empty($cv_no)){
			$query.="
				and
					h.cv_no = '$cv_no'
			";
			}

			$query.="
				order by
					date_cleared asc, check_no asc
			";

			$result = mysql_query($query) or die(mysql_error());

			$a = array();
			while($r = mysql_fetch_assoc($result)){
				$a[] = $r['cv_header_id'];
			}

			$total_cash_amount = $total_vatable_amount = $total_vat_amount = $total_tax_amount = 0;
			if(!empty($a)){
				foreach($a as $cv_header_id){
					$total_cash_amount 		+= $this->getCashAmount($cv_header_id);
					$total_vatable_amount 	+= $this->getVatableAmount($cv_header_id);
					$total_vat_amount		+= $this->getVatAmount($cv_header_id);
					$total_tax_amount		+= $this->getTaxAmount($cv_header_id);
				}
			}

			$r = array();
			$r['cash'] 		= $total_cash_amount;
			$r['vatable'] 	= $total_vatable_amount;
			$r['vat']		= $total_vat_amount;
			$r['tax']		= $total_tax_amount;

			return $r;
		}
		
		function getBIR($supplier_id,$from_date,$to_date,$attr_date,$cleared,$cv_no = NULL,$tax_val){
			$options = new options();
			$query="
				select
					*
				from
					cv_header as h, supplier as s
				where
					h.supplier_id = s.account_id
				and
					status != 'C'
				and
					$attr_date between '$from_date' and '$to_date'
				and
					wtax = '$tax_val'
				and
					account_id = '$supplier_id'
			";

			if(!empty($cv_no)){
			$query.="
				and
					h.cv_no = '$cv_no'
			";
			}

			$query.="
				order by
					date_cleared asc, check_no asc
			";

			$result = mysql_query($query) or die(mysql_error());

			$a = array();
			while($r = mysql_fetch_assoc($result)){
				$a[] = $r['cv_header_id'];
			}

			$total_cash_amount = $total_vatable_amount = $total_vat_amount = $total_tax_amount = 0;
			if(!empty($a)){
				foreach($a as $cv_header_id){
					$total_cash_amount 		+= $this->getCashAmount($cv_header_id);
					$total_vatable_amount 	+= $this->getVatableAmount($cv_header_id);
					$total_vat_amount		+= $this->getVatAmount($cv_header_id);
					$total_tax_amount		+= $this->getTaxAmount($cv_header_id);
				}
			}

			$r = array();
			$r['cash'] 		= $total_cash_amount;
			$r['vatable'] 	= $total_vatable_amount;
			$r['vat']		= $total_vat_amount;
			$r['tax']		= $total_tax_amount;

			return $r;
		}		
		


		function getCashAmount($cv_header_id){
			$result = mysql_query("
				select
					sum(amount) as amount
				from
					cv_detail
				where
					cv_header_id = '$cv_header_id'
			") or die(mysql_error());

			$r = mysql_fetch_assoc($result);

			$vat = $this->getAttribute('cv_header','cv_header_id',$cv_header_id,'vat');
			$wtax = $this->getAttribute('cv_header','cv_header_id',$cv_header_id,'wtax');
			$retention_amount 	= $this->getAttribute('cv_header','cv_header_id',$cv_header_id,'retention_amount');
			$chargable_amount	= $this->getAttribute('cv_header','cv_header_id',$cv_header_id,'chargable_amount');
			$rmy_amount			= $this->getAttribute('cv_header','cv_header_id',$cv_header_id,'rmy_amount');

			$amount = $r['amount'];
			$vatable = (($amount) / (1 + ($vat/100)));
			$vat_amount = $vatable * ($vat/100);
			$tax_amount =  $vatable * ($wtax/100);
			$cash_amount = $amount - $retention_amount - $chargable_amount - $tax_amount - $rmy_amount;

			return $cash_amount;
		}

		function getTaxAmount($cv_header_id){
			$options = new options();
			$result = mysql_query("
				select
					sum(amount) as amount
				from
					cv_detail
				where
					cv_header_id = '$cv_header_id'
			") or die(mysql_error());

			$r = mysql_fetch_assoc($result);

			$vat = $options->getAttribute('cv_header','cv_header_id',$cv_header_id,'vat');
			$wtax = $options->getAttribute('cv_header','cv_header_id',$cv_header_id,'wtax');
			$retention_amount 	= $options->getAttribute('cv_header','cv_header_id',$cv_header_id,'retention_amount');
			$chargable_amount	= $options->getAttribute('cv_header','cv_header_id',$cv_header_id,'chargable_amount');

			$amount = $r['amount'];
			$vatable = (($amount) / (1 + ($vat/100)));
			$vat_amount = $vatable * ($vat/100);
			$tax_amount =  $vatable * ($wtax/100);
			$cash_amount = $amount - $retention_amount - $chargable_amount - $tax_amount;

			return $tax_amount;
		}

		function getVatableAmount($cv_header_id){
			$options = new options();
			$result = mysql_query("
				select
					sum(amount) as amount
				from
					cv_detail
				where
					cv_header_id = '$cv_header_id'
			") or die(mysql_error());

			$r = mysql_fetch_assoc($result);

			$vat 				= $options->getAttribute('cv_header','cv_header_id',$cv_header_id,'vat');
			$wtax 				= $options->getAttribute('cv_header','cv_header_id',$cv_header_id,'wtax');
			$retention_amount 	= $options->getAttribute('cv_header','cv_header_id',$cv_header_id,'retention_amount');
			$chargable_amount	= $options->getAttribute('cv_header','cv_header_id',$cv_header_id,'chargable_amount');

			$amount = $r['amount'];
			$vatable = (($amount) / (1 + ($vat/100)));
			$vat_amount = $vatable * ($vat/100);
			$tax_amount =  $vatable * ($wtax/100);
			$cash_amount = $amount - $retention_amount - $chargable_amount - $tax_amount;

			return $vatable;
		}

		function getVatAmount($cv_header_id){
			$options = new options();
			$result = mysql_query("
				select
					sum(amount) as amount
				from
					cv_detail
				where
					cv_header_id = '$cv_header_id'
			") or die(mysql_error());

			$r = mysql_fetch_assoc($result);

			$vat = $options->getAttribute('cv_header','cv_header_id',$cv_header_id,'vat');
			$wtax = $options->getAttribute('cv_header','cv_header_id',$cv_header_id,'wtax');
			$retention_amount 	= $options->getAttribute('cv_header','cv_header_id',$cv_header_id,'retention_amount');
			$chargable_amount	= $options->getAttribute('cv_header','cv_header_id',$cv_header_id,'chargable_amount');

			$amount = $r['amount'];
			$vatable = (($amount) / (1 + ($vat/100)));
			$vat_amount = $vatable * ($vat/100);
			$tax_amount =  $vatable * ($wtax/100);
			$cash_amount = $amount - $retention_amount - $chargable_amount - $tax_amount;

			return $vat_amount;
		}

		function eqcat_options($id) {
			$sql = "select * from equipment_categories order by eq_catID";
			$query = mysql_query($sql);

			while($r=mysql_fetch_array($query)) {
				if($id==$r[eq_catID]) continue;

				$string_row.='<option value="'.$r[eq_catID].'">'.$r[eq_cat_name].'</option>';
			}

			if(!empty($id)) {
			  $sql = "select * from equipment_categories where eq_catID='$id'";
			  $query = mysql_query($sql);
			  $result = mysql_fetch_array($query);

			  return '<select name=eq_cat class=select><option value="'.$result[eq_catID].'">'.$result[eq_cat_name].'</option><option>= = = = = = = = = = = = = = = = = = = = </option>'.$string_row.'</select>';
		    }
			else
			  return '<select name=eq_cat class=select><option value="">- - - - -  Choose Category - - - - -  </option>'.$string_row.'</select>';
		}

		function getCheckStatusAmount($cleared){
			#1 - cleared
			#0 - uncleared
			$result = mysql_query("
				select
					cv_header_id
				from
					cv_header
				where
					status != 'C'
				and
					cleared = '$cleared'
			") or die(mysql_error());
			$total_cash_amount = 0;
			while($r = mysql_fetch_assoc($result)){
				$total_cash_amount += $this->getAttribute('cv_header','cv_header_id',$r['cv_header_id'],'cash_amount');
			}
			return $total_cash_amount;
		}

		function insertAuditTrail($description,$user_id,$header_id,$trans){
			$datetime = date("Y-m-d H:i:s");

			$description = mysql_escape_string($description);

			mysql_query("
				insert into
					audit_trail
				set
					description = '$description',
					user_id = '$user_id',
					header_id = '$header_id',
					trans = '$trans',
					time_entry = '$datetime'
			") or die(mysql_error());
		}

		function jobtype_options($id) {
			$sql = "select * from dynamic.job_types order by job_type";
			$query = mysql_query($sql);

			while($r=mysql_fetch_array($query)) {
				if($id==$r[job_typeID]) continue;

				$string_row.='<option value="'.$r[job_typeID].'">'.$r[job_type].'</option>';
			}

			if(!empty($id)) {
			  $sql = "select * from dynamic.job_types where job_typeID='$id'";
			  $query = mysql_query($sql);
			  $result = mysql_fetch_array($query);

			  return '<select name=job_type class=select><option value="'.$result[job_typeID].'">'.$result[job_type].'</option><option>= = = = = = = = = = = = = = = = = = = = = = = = = = = </option>'.$string_row.'</select>';
		    }
			else
			  return '<select name=job_type class=select><option value="">- - - Choose Job Type - - -</option>'.$string_row.'</select>';
		}

		function daysList(){
			$content='
					<select name="day" style="font-size:8px;line-height:-100px;letter-spacing:1px;padding:0;">
						<option value="" selected disabled>Day</option>
						<option value="01">01</option>
						<option value="02">02</option>
						<option value="03">03</option>
						<option value="04">04</option>
						<option value="05">05</option>
						<option value="06">06</option>
						<option value="07">07</option>
						<option value="08">08</option>
						<option value="09">09</option>
						<option value="10">10</option>
						<option value="11">12</option>
						<option value="14">13</option>
						<option value="15">16</option>
						<option value="17">17</option>
						<option value="18">18</option>
						<option value="19">19</option>
						<option value="20">20</option>
						<option value="21">21</option>
						<option value="22">22</option>
						<option value="23">23</option>
						<option value="24">24</option>
						<option value="25">25</option>
						<option value="26">26</option>
						<option value="27">27</option>
						<option value="28">28</option>
						<option value="29">29</option>
						<option value="30">30</option>
						<option value="31">31</option>
					</select>
					';

			return $content;
		}
		function daysList2(){
			$content='
					<select name="day2" style="font-size:8px;line-height:-100px;letter-spacing:1px;padding:0;">
						<option value="" selected disabled>Day</option>
						<option value="01">01</option>
						<option value="02">02</option>
						<option value="03">03</option>
						<option value="04">04</option>
						<option value="05">05</option>
						<option value="06">06</option>
						<option value="07">07</option>
						<option value="08">08</option>
						<option value="09">09</option>
						<option value="10">10</option>
						<option value="11">12</option>
						<option value="14">13</option>
						<option value="15">16</option>
						<option value="17">17</option>
						<option value="18">18</option>
						<option value="19">19</option>
						<option value="20">20</option>
						<option value="21">21</option>
						<option value="22">22</option>
						<option value="23">23</option>
						<option value="24">24</option>
						<option value="25">25</option>
						<option value="26">26</option>
						<option value="27">27</option>
						<option value="28">28</option>
						<option value="29">29</option>
						<option value="30">30</option>
						<option value="31">31</option>
					</select>
					';

			return $content;
		}
		function monthsList(){
			$content='
					<select name="month" style="font-size:8px;line-height:-100px;letter-spacing:1px;padding:0;">
						<option value="" selected disabled>Month</option>
						<option value="01">Jan</option>
						<option value="02">Feb</option>
						<option value="03">Mar</option>
						<option value="04">Apr</option>
						<option value="05">May</option>
						<option value="06">Jun</option>
						<option value="07">Jul</option>
						<option value="08">Aug</option>
						<option value="09">Sept</option>
						<option value="10">Oct</option>
						<option value="11">Nov</option>
						<option value="12">Dec</option>
					</select>
					';

			return $content;
		}
		function monthsList2(){
			$content='
					<select name="month2" style="font-size:8px;line-height:-100px;letter-spacing:1px;padding:0;">
						<option value="" selected disabled>Month</option>
						<option value="01">Jan</option>
						<option value="02">Feb</option>
						<option value="03">Mar</option>
						<option value="04">Apr</option>
						<option value="05">May</option>
						<option value="06">Jun</option>
						<option value="07">Jul</option>
						<option value="08">Aug</option>
						<option value="09">Sept</option>
						<option value="10">Oct</option>
						<option value="11">Nov</option>
						<option value="12">Dec</option>
					</select>
					';

			return $content;
		}
		function yearList(){
			$content='
					<select name="year" style="font-size:8px;line-height:-100px;letter-spacing:1px;padding:0;">
						<option value="" selected disabled>Year</option>
						<option value="2004">2004</option>
						<option value="2005">2005</option>
						<option value="2006">2006</option>
						<option value="2007">2007</option>
						<option value="2008">2008</option>
						<option value="2009">2009</option>
						<option value="2010">2010</option>
						<option value="2011">2011</option>
						<option value="2012">2012</option>
						<option value="2013">2013</option>
						<option value="2014">2014</option>
						<option value="2015">2015</option>
						<option value="2016">2016</option>
						<option value="2017">2017</option>
						<option value="2018">2018</option>
						<option value="2019">2019</option>
						<option value="2020">2020</option>
						<option value="2021">2021</option>
						<option value="2022">2022</option>
						<option value="2023">2023</option>
						<option value="2024">2024</option>
					</select>
					';

			return $content;
		}

		function yearList2(){
			$content='
					<select name="year2" style="font-size:8px;line-height:-100px;letter-spacing:1px;padding:0;">
						<option value="" selected disabled>Year</option>
						<option value="2004">2004</option>
						<option value="2005">2005</option>
						<option value="2006">2006</option>
						<option value="2007">2007</option>
						<option value="2008">2008</option>
						<option value="2009">2009</option>
						<option value="2010">2010</option>
						<option value="2011">2011</option>
						<option value="2012">2012</option>
						<option value="2013">2013</option>
						<option value="2014">2014</option>
						<option value="2015">2015</option>
						<option value="2016">2016</option>
						<option value="2017">2017</option>
						<option value="2018">2018</option>
						<option value="2019">2019</option>
						<option value="2020">2020</option>
						<option value="2021">2021</option>
						<option value="2022">2022</option>
						<option value="2023">2023</option>
						<option value="2024">2024</option>
					</select>
					';

			return $content;
		}

		 function type_options($id) {
			$sql = "select * from tire_type order by type_id";
			$query = mysql_query($sql);

			while($r=mysql_fetch_array($query)) {
				if($id==$r[type_id]) continue;

				$string_row.='<option value="'.$r[type_id].'">'.$r[type_name].'</option>';
			}

			if(!empty($id)) {
			  $sql = "select * from tire_type where type_id='$id'";
			  $query = mysql_query($sql);
			  $result = mysql_fetch_array($query);

			  return '<select name=tire_type class=select><option value="'.$result[type_id].'">'.$result[type_name].'</option><option>= = = = = = = = = = = = = = = = = = = = </option>'.$string_row.'</select>';
		    }
			else
			  return '<select name=tire_type class=select><option value="">- - - - -  Choose Category - - - - -  </option>'.$string_row.'</select>';
		}
		function eq_name($id) {
			$sql = "select * from equipment order by eqID";
			$query = mysql_query($sql);

			while($r=mysql_fetch_array($query)) {
				if($id==$r[eqID]) continue;

				$string_row.='<option value="'.$r[eqID].'">'.$r[eq_name].'</option>';
			}

			if(!empty($id)) {
			  $sql = "select * from equipment where eqID='$id'";
			  $query = mysql_query($sql);
			  $result = mysql_fetch_array($query);

			  return '<select name=equipment class=select><option value="'.$result[eqID].'">'.$result[eq_name].'</option><option>= = = = = = = = = = = = = = = = = = = = </option>'.$string_row.'</select>';
		    }
			else
			  return '<select name=equipment class=select><option value="">- - - - - Select Equipment - - - - -</option>'.$string_row.'</select>';
		}
		function getSize($id) {
			$sql = "select * from tire_size order by size_id";
			$query = mysql_query($sql);

			while($r=mysql_fetch_array($query)) {
				if($id==$r[size_id]) continue;

				$string_row.='<option value="'.$r[size_id].'">'.$r[sizes].'</option>';
			}

			if(!empty($id)) {
			  $sql = "select * from tire_size where size_id='$id'";
			  $query = mysql_query($sql);
			  $result = mysql_fetch_array($query);

			  return '<select name=sizes class=select><option value="'.$result[size_id].'">'.$result[sizes].'</option><option>= = = = = = = = = = = = = = = = = = = = </option>'.$string_row.'</select>';
		    }
			else
			  return '<select name=sizes class=select><option value="">- - - - - Choose Size - - - - -</option>'.$string_row.'</select>';
		}
		function generateLFnum(){
				$query="
					SELECT
						lf_num
					FROM
						leave_info
					order by
						lf_num desc
				";
				$result=mysql_query($query);
				$rows=mysql_num_rows($result);
				if($rows==0){
					$drnum="000001";
				}else{
					$r=mysql_fetch_assoc($result);
					$num=$r[lf_num];	
					/*$num=explode('-',$num);
					$num=$num[1];*/
					$num=intval($num);
					$num+=1;
					$num=str_pad($num,6,"0",STR_PAD_LEFT);
					$drnum=$num;
				}
				return $drnum;
		   }

	}
?>
