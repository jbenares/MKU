<?php
	function print_stockcard($id){
		
		$objResponse=new xajaxResponse();
		$options=new options();	
		
		//$objResponse->alert($id);
		
		
				
		$newContent="
			<iframe id='JOframe' name='JOframe' frameborder='0' src='printStockCard.php?id=$id' width='100%' height='500'>
        	</iframe>
		";
		$objResponse->script("hideBox();");
		$objResponse->assign("content","innerHTML", $newContent);
		$objResponse->assign("stockcard_id","value",$id);
		$objResponse->script("toggleBox('demodiv',0)");
		return $objResponse;
	}
	
	function updateStockCardStatus($id){
		$objResponse=new xajaxResponse();
		//$objResponse->alert($id);
		$query="
			select
				*
			from 
				stockcard
			where
				stockcard_id='$id'
		";
		$result=mysql_query($query);
		$r=mysql_fetch_assoc($result);
		
		$status=$r[status];
		if($status!='C'):
			$query="
				update
					dr_header
				set
					status='P'
				where
					dr_header_id='$id'
			";
			mysql_query($query);
		endif;
		
		
		
		return $objResponse;
	}
	
?>