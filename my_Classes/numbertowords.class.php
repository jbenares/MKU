<?php 
	/*
		AUTHOR: MICHAEL ANGELO O. SALVIO, CpE
	*/

	class NumToWords
	{
	
		private $number;
		private $numLength;
		private $numArray;
		private $ones=array('','ONE','TWO','THREE','FOUR','FIVE','SIX','SEVEN','EIGHT','NINE'); 
		private $tens=array('','ELEVEN','TWELVE','THIRTEEN','FOURTEEN','FIFTEEN','SIXTEEN','SEVENTEEN','EIGHTEEN','NINETEEN'); 
		private $tens2=array('','TEN','TWENTY','THIRTY','FORTY','FIFTY','SIXTY','SEVENTY','EIGHTY','NINETY'); 
		private $tens3=array('','HUNDRED','THOUSAND','MILLION','BILLION','TRILLION'); 
		private $decimal;
		
		public function setNumber($number)
		{
			$numbers=explode('.',$number);
			$this->decimal=$numbers[1];
			
			$this->number=$numbers[0];	
			$this->numLength=strlen($this->number); 
			$this->numArray=str_split($this->number,1);  
		}
			
		public function num_words(){ 
		
			if ($this->number<10) { 
				return $this->ones[intval($this->number)]; 
			} 
		
			//tens //11 12 13 14 15...
			if (($this->numLength==2)&&($this->number<20) && ($this->numArray[1]!=0)) { 
				return $this->tens[$this->number-10]; 
			} 
		
			//10		
			if (($this->numLength==2)&&($this->number<20) && ($this->numArray[1]==0)){ 
				return $this->tens2[$this->numArray[0]]; 
			} 
		
			//20 30 40 50 60...
			if (($this->numLength==2)&&($this->number>19)&& ($this->numArray[1]==0)){ 
				return $this->tens2[$this->numArray[0]]; 
			} 
		
			//21 - 99
			if (($this->numLength==2)&&($this->number>19)&&($this->numArray[1]<>0)) { 
				return $this->tens2[$this->numArray[0]]." ".$this->ones[$this->numArray[1]]; 
			} 
		
		
			//hundreds 
			//999
			if ($this->numLength==3) { 
				$x=$this->numArray[1].$this->numArray[2]; 
				return $this->ones[$this->numArray[0]]." HUNDRED ".$this->tens($x); 
			} 
		
			//THOUSANDS 9999 1234
			if ($this->numLength==4){ 
				$y=$this->numArray[1].$this->numArray[2].$this->numArray[3]; 
				$z=$this->numArray[2].$this->numArray[3]; 
				if (intval($y)>99){ 
					return $this->ones[$this->numArray[0]]." THOUSAND ".$this->hundreds($y); 
				}else{ 
					return $this->ones[$this->numArray[0]]." THOUSAND ". $this->tens($z); 
				} 
			} 
		
		
			//tensthousand 
			//12,345
			if ($this->numLength==5){ 
				$v=$this->numArray[0].$this->numArray[1]; 
				$y=$this->numArray[2].$this->numArray[3].$this->numArray[4]; 
				$z=$this->numArray[3].$this->numArray[4]; 
		
				if (intval($y)>99){ 
					return $this->tens($v)." THOUSAND ".$this->hundreds($y); 
				}else{ 
					return $this->tens($v)." THOUSAND ". $this->tens($z); 
				} 
			} 
			
			//hundredthousand
			if($this->numLength==6){
				$x=$this->numArray[0].$this->numArray[1].$this->numArray[2];	
				$y=$this->numArray[3].$this->numArray[4].$this->numArray[5];	
				return $this->hundreds($x)." THOUSAND ".$this->hundreds($y);	
			}
			
			if($this->numLength==7){
				$x=$this->numArray[0];
				$y=$this->numArray[1].$this->numArray[2].$this->numArray[3];	
				$z=$this->numArray[4].$this->numArray[5].$this->numArray[6];	
				return $this->tens($x)." MILLION ".$this->hundreds($y)." THOUSAND ".$this->hundreds($z);
			}
		} 
		
		private function tens($number) { 
		    //1-99
		
			$numLength=strlen($number); 
			$numArray=str_split($number,1); 
		
			if ($number<10){ 
				return $this->ones[intval($number)]; 
			} 
			if ($numLength==2&&$number<20 && $numArray[1]<>0){ 
				return $this->tens[$number-10]; 
			} 
		
			if ($numLength==2&&$number<20 && $numArray[1]==0){ 
				return $this->tens2[$numArray[0]]; 
			} 
		
			if ($numLength==2&&$number>19 && $numArray[1]==0){ 
				return $this->tens2[$numArray[0]]; 
			} 
		
			if ($numLength==2&&$number>19 && $numArray[1]<>0){ 
				return $this->tens2[$numArray[0]]." ".$this->ones[$numArray[1]]; 
			} 
		} 
		
		private function hundreds($number) { 
			//up to 999
		
			$numLength=strlen($number); 
			$numArray=str_split($number,1); 
		
			if ($number<10){ 
				return $this->ones[intval($number)]; 
			} 
		
			if ($numLength==2&&$number<20 && $numArray[1]<>0){ 
				return $this->tens[$number-10]; 
			} 
			if ($numLength==2&&$number<20 && $numArray[1]==0){ 
				return $this->tens2[$numArray[0]]; 
			} 
		
			if ($numLength==2&&$number>19 && $numArray[1]==0){ 
				return $this->tens2[$numArray[0]]; 
			} 
		
			if ($numLength==2&&$number>19 && $numArray[1]<>0){ 
				return $this->tens2[$numArray[0]]." ".$this->ones[$numArray[1]]; 
			} 
		
			//hundreds 
			if ($numLength==3){ 
			
				$x=$numArray[1].$numArray[2]; 
				return $this->ones[$numArray[0]]." HUNDRED ".$this->tens($x); 
			} 
		} 
		
		public function appendDecimal()
		{
			if($this->decimal==0 || empty($this->decimal)){
				$dec = "0";	
			}else{
				$dec = $this->decimal;	
			}
			return " & $dec/100 ONLY";
		}
	}
	
	
	
	/*$c=new NumToWords();
	$c->setNumber(9999999.99);
	echo $c->num_words().$c->appendDecimal();*/
?>
