<?php

	$dbserver = "localhost";
	$dbuser = "root";
	$dbpass = "";
	$db = "tsp";

	$baglan = @mysql_connect($dbserver, $dbuser,$dbpass ) or die("HATA : " . mysql_error());
	$db_select = @mysql_select_db($db,$baglan);
	
	$sehir = array();
	$deger = array();
	
	//SATIR SÜTUN SAYISI
	--
	$queryy = @mysql_query("SELECT * FROM mesafeler");
	$numrowss = @mysql_num_rows($queryy);

	$query = @mysql_query("SELECT * FROM koordinatlar");
	$numrows = @mysql_num_rows($query);	
	
	//SATIR SÜTUN SAYISI
	
	$sqlbasla	=		"SELECT baslangic FROM mesafeler ";
	$sorgubasla	=		@mysql_query("$sqlbasla");
	
	$sqlidal	=		"SELECT adi FROM koordinatlar ";
	$sorguidal	=		@mysql_query("$sqlidal");
	
	$sqllati	=		"SELECT lati FROM koordinatlar ";
	$sorgulatial=		@mysql_query("$sqllati");
	
	$sqllon	    =		"SELECT lon FROM koordinatlar ";
	$sorgulonal	=		@mysql_query("$sqllon");
		
	$sqlbitis	=		"SELECT bitis FROM mesafeler ";
	$sorgubitis	=		@mysql_query("$sqlbitis");
		
	$sqluzaklik 	=	"SELECT mesafe FROM mesafeler ";
	$sorguuzaklik	=	@mysql_query("$sqluzaklik");	
	// VERİLERİN DİNAMİK OLARAK DİZİ İÇERİSİNE AKTARILMASI //	
	$i=0;
	$j=0;
	$k=0;
	$p=0;
	$n=0;
	$o=0;
	$lati = array();
	$lon = array();
	while ($sonucidal = @mysql_fetch_array($sorguidal))
	{
			$index[$p] = $sonucidal;
			$index[$p]['id'] = $p+1;		
			$p++;
			
	}
	while ($sonucbaslangic = @mysql_fetch_array($sorgubasla))
	{
			$baslangic[$i] = $sonucbaslangic; 
			$i++;
	}
	while ($sonucbitis = @mysql_fetch_array($sorgubitis))
	{
			$bitis[$j] = $sonucbitis; 
			$j++;
	}
	while ($sonucmesafe = @mysql_fetch_array($sorguuzaklik))
	{
			$uzaklik[$k] = $sonucmesafe; 
			$k++;
	}
	while ($sonuclati = @mysql_fetch_array($sorgulatial))
	{
			$lati[$n] = $sonuclati; 
			$n++;
	}
	while ($sonuclon = @mysql_fetch_array($sorgulonal))
	{
			$lon[$o] = $sonuclon; 
			$o++;
	}
############################################################ VERİTABANI İŞLEMLERİ #####################################################	
	$anakromozom = array();			
	for($i = 1; $i < $numrows; $i++){			
		$secim[$i] = $index[$i]['id'];					
	}	
	echo "secim=",print_R($secim),"</br>";
	for($i = 1; $i < 11; $i++){ /*	KROMOZOM SAYISI 10 OLARAK BELİRLENDİ	*/
			shuffle($secim);
			$anakromozom[$i]['ADI'] = implode('',$secim);
			$anakromozom[$i]['ADI'][$numrows-1] = $index[0]['id'];
			$tmp = $anakromozom[$i]['ADI'][$numrows-1];
			$anakromozom[$i]['ADI'][$numrows-1] = $anakromozom[$i]['ADI'][0];
			$anakromozom[$i]['ADI'][0] = $tmp;			
	}
	for($i = 0; $i < $numrows; $i++){
		$index[$i]['adi'] = trim($index[$i]['adi']," ");
		print_r($index[$i]['adi']);
	}	
	for($i = 0; $i < $numrowss; $i++) {
		for($j = 0; $j < $numrows; $j++){
			if($baslangic[$i]['baslangic'] == $index[$j]['adi']){
				$baslangic[$i]['baslangic'] = $index[$j]['id'];				
			}
		}	
	}
	for($i = 0; $i < $numrowss; $i++) {
		for($j = 0; $j < $numrows; $j++){
			if($bitis[$i]['bitis'] == $index[$j]['adi']){
				$bitis[$i]['bitis'] = $index[$j]['id'];				
			}			
		}	
	}
	for($i = 0; $i < $numrowss; $i++){
	$a = $baslangic[$i]['baslangic'];
	$b = $bitis[$i]['bitis'];
	$sehir[$a][$b] = $uzaklik[$i]['mesafe'];
	}
	
		for($j = 1; $j < 11; $j++){
		for($i = 1; $i < $numrows; $i++){
			
			$a = $anakromozom[$j]['ADI'][$i-1];
			$b = $anakromozom[$j]['ADI'][$i];						
			@$abcd[$j] += $sehir[$a][$b];
						
		}
			@$abcd[$j] +=$sehir[$anakromozom[$j]['ADI'][$numrows-1]][$anakromozom[$j]['ADI'][0]];
			$anakromozom[$j]['UZAKLIK'] = $abcd[$j];				
	}
	for($f =1; $f<101; $f++){
#########################################################/*Kromozomları sırala.*/#####################################################################################################
	for($i = 1; $i < 11; $i++){
		for($j = 1; $j < 11; $j++){	
			if($anakromozom[$i]['UZAKLIK'] < $anakromozom[$j]['UZAKLIK']){
				
				$tmp  = $anakromozom[$i]['UZAKLIK'];
				$anakromozom[$i]['UZAKLIK'] = $anakromozom[$j]['UZAKLIK'];
				$anakromozom[$j]['UZAKLIK'] = $tmp ;
				
				$tmp  = $anakromozom[$i]['ADI'];
				$anakromozom[$i]['ADI'] = $anakromozom[$j]['ADI'];
				$anakromozom[$j]['ADI'] = $tmp ;
				
			}			
		}
	}	
	$toplam =0;
	for($i = 1; $i<11; $i++){
		
		$toplam += $anakromozom[$i]['UZAKLIK'];  /* Toplam uzunluk hesaplaması */
		
	}	
	$uygunluk = array();
	$a = 0;
	for($i = 1; $i < 11; $i++){
				
		$a		+= $anakromozom[$i]['UZAKLIK'];  /*	Uygunluk değerlerinin hesaplanması	*/
		$uygunluk1[$i] = $a;
			
	}
	$uygunluk2 = array_reverse($uygunluk1);     	
	for	($k = 1; $k < 5 ; $k++){  /* EBEVEYN SAYISI 4 OLARAK OLUŞTURULDU DİNAMİK YAPILABİLİR.	 */
		
		for ($i = 1; $i < 11; $i++){
				$R = rand(0,$toplam);
								
			if($uygunluk2[$i] <= $R ){
				$ebeveyn[$k]['UZAKLIK'] = $anakromozom[$i]['UZAKLIK'];
				$ebeveyn[$k]['ADI'] = $anakromozom[$i]['ADI'];
				$ebeveyn[$k]['ID'] = $i;
				break;
			}
		}	
	}
	
	/*EBEVEYNİN HANGİ ANA KROMOZOM OLDUĞU EBEVEYN ID İLE SAKLANIYOR*/
	
### SEÇİLİM ###			
### ÇAPRAZLAMA ###					
		$cocuk= array();
		for($i = 0; $i < $numrows; $i++){
			
			$cocuk[1]['ADI'][$i] =	0;
			$cocuk[2]['ADI'][$i] =	0;
			$cocuk[3]['ADI'][$i] =	0;
			$cocuk[4]['ADI'][$i] =	0;						
		}		
		$y = (int)floor($numrows/2);	
		for($i = 0; $i < $y; $i++){
			
			$cocuk[1]['ADI'][$i] =	$ebeveyn[1]['ADI'][$i];
			$cocuk[2]['ADI'][$i] =	$ebeveyn[2]['ADI'][$i];
			$cocuk[3]['ADI'][$i] =	$ebeveyn[3]['ADI'][$i];
			$cocuk[4]['ADI'][$i] =	$ebeveyn[4]['ADI'][$i];
						
		}
	for($g = 1; $g < 5; $g++)
	{
			if($g==1)
			{$m=2;}
			if($g==2)
			{$m=1;}
			if($g==3)
			{$m=4;}
			if($g==4)
			{$m=3;}	
		$y = (int)floor($numrows/2); ## numrows 7 için y = 3
		for($i = ($y)+1; $i < $numrows; $i++){
			$a=0;
			for($j = 0; $j < ($y+1); $j++){
				/*echo "y =",$y,"---",$m,"ebeveynin  ",$i,"indeksi =",$ebeveyn[$m]['ADI'][$i],"</br>";*/
					if(strcmp($cocuk[$g]['ADI'][$j],$ebeveyn[$m]['ADI'][$i])==0)
					{						
						$a=0;
						break;
					}					
					else					/*YARIDAN SONRAKİ ÇAPRAZLAMA*/
					{					
						$a++;
					}
					if($a == $y+1){
						
						$cocuk[$g]['ADI'][$i] = $ebeveyn[$m]['ADI'][$i];
						$a=0;
						
					}			
			}				
		}		
		for($i = 0; $i < $numrows; $i++){			
			if($cocuk[$g]['ADI'][$i] == 0 ){
				for($j = $i; $j < $numrows; $j++){
					{						
						if($cocuk[$g]['ADI'][$j] != 0){
																			/*0 ları sona atma*/
							$cocuk[$g]['ADI'][$i] = $cocuk[$g]['ADI'][$j];
							$cocuk[$g]['ADI'][$j]=0;
						}						
					}					
				}
			}			
		}		
		for($i = 0; $i < $numrows; $i++){
			if( $cocuk[$g]['ADI'][$i] == 0 )
			{
				for($j = 0; $j < $numrows; $j++){
					for($k = 0; $k < $numrows; $k++){
						if(strcmp($cocuk[$g]['ADI'][$k],$ebeveyn[$m]['ADI'][$j]) == 0)
						{						
							$a=0;
							break;
						}					
							else					/*YARIDAN SONRAKİ ÇAPRAZLAMA*/
						{					
							$a++;
						}
							if($a == $numrows){
						
							$cocuk[$g]['ADI'][$i] = $ebeveyn[$m]['ADI'][$j];
							$a=0;						
						}
					}
				}				
			}			
		}	

	// Çaprazlama İşlemi bitti. Şimdi çocukların uzaklıklarını hesaplayıp daha kısaysa ebeveyni ile yer değiştireceğiz.		
		for($i = 0; $i < $numrows-1; $i++){			
			$a = $cocuk[$g]['ADI'][$i];
			$b = $cocuk[$g]['ADI'][$i+1];							
			@$cccc[$g] += $sehir[$a][$b];					
		}
		@$cccc[$g] +=$sehir[$cocuk[$g]['ADI'][$numrows-1]][$cocuk[$g]['ADI'][0]];
		$cocuk[$g]['UZAKLIK'] = $cccc[$g];					
		if ($cocuk[$g]['UZAKLIK'] < $anakromozom[$ebeveyn[$m]['ID']]['UZAKLIK'] ){					
				$anakromozom[$ebeveyn[$m]['ID']]['UZAKLIK'] = $cocuk[$g]['UZAKLIK']; /*EVEYN İLE KARŞILAŞTIRILIP DEĞİŞİM YAPILMASI*/
				$anakromozom[$ebeveyn[$m]['ID']]['ADI'] = $cocuk[$g]['ADI'];			
		}
	} 
### ÇAPRAZLAMA ###
### MUTASYON ###
		$mutant = array();
		for($k = 1; $k < 3; $k++){
			
			$q = rand(1,10);
			$r = rand(1,$numrows-1);
			$p = rand(1,$numrows-1);
		
			$mutant[$k]['ADI'] = $anakromozom[$q]['ADI'];
			$mutant[$k]['UZAKLIK'] = $anakromozom[$q]['UZAKLIK'];
		
			$tmp = $mutant[$k]['ADI'][$r];
			$mutant[$k]['ADI'][$r] = $mutant[$k]['ADI'][$p];
			$mutant[$k]['ADI'][$p] = $tmp;			
			/*Şimdi mutant i uzunluğu ile anakromozom uzunluğunu karşılaştırıp küçükse yer değiştirecez.*/						
			for($i = 1; $i < $numrows; $i++){
			
				$a = $mutant[$k]['ADI'][$i-1];
				$b = $mutant[$k]['ADI'][$i];							
				@$xxx[$k] += $sehir[$a][$b];
						
				}
				@$xxx[$k] +=$sehir[$mutant[$k]['ADI'][$numrows-1]][$mutant[$k]['ADI'][0]];
				$mutant[$k]['UZAKLIK'] = $xxx[$k];	 
			
			if ($mutant[$k]['UZAKLIK'] < $anakromozom[$q]['UZAKLIK'] ){
				
				$anakromozom[$q]['UZAKLIK'] = $mutant[$k]['UZAKLIK'];
				$anakromozom[$q]['ADI'] = $mutant[$k]['ADI'] ;				
			}			
		}	
	for($i = 1; $i < 11; $i++){
		for($j = 1; $j < 11; $j++){	
			if($anakromozom[$i]['UZAKLIK'] < $anakromozom[$j]['UZAKLIK']){
				
				$tmp  = $anakromozom[$i]['UZAKLIK'];
				$anakromozom[$i]['UZAKLIK'] = $anakromozom[$j]['UZAKLIK'];
				$anakromozom[$j]['UZAKLIK'] = $tmp ;
				
				$tmp  = $anakromozom[$i]['ADI'];
				$anakromozom[$i]['ADI'] = $anakromozom[$j]['ADI'];
				$anakromozom[$j]['ADI'] = $tmp ;
				
			}
			
		}
	}
unset($secim);
unset($xxx);
unset($cccc);
unset($abcd);
unset($uygunluk);
unset($uygunluk1);
unset($uygunluk2);

}
	for($i = 0; $i < $numrows; $i++){
			
		$sehirid[$i] = $anakromozom[1]['ADI'][$i];	
	}
	foreach($sehirid as $idsi ){
		$i=0;
		$deger[$i][$idsi]['lon']  =  $lon[$idsi-1][0]; 
		$deger[$i][$idsi]['lati']  =  $lati[$idsi-1][0];
		$deger[$i][$idsi]['adi']  =  $index[$idsi-1]['adi'];
		$deger[$i][$idsi]['id']  = $idsi;
		$i++;
	}
	$bosalt=mysql_query("truncate table sondurum");
	
	foreach($sehirid as $idsi ){
		$i=0;
		$k=0;
	$finaladi  = $deger[$i][$idsi]['adi'];
	$latilati = $deger[$i][$idsi]['lati'];
	$lonlonlon = $deger[$i][$idsi]['lon'];
	$db_select = mysql_select_db("tsp");
	
	$sqlat = "insert into sondurum(adi , lati , lon) values ('".$finaladi."','".$latilati."','".$lonlonlon."');";
	if (mysql_query($sqlat))
    echo "veriler eklendi";
	unset($finaladi);
	unset($latilati);
	unset($lonlonlon);

	$i++;
	}	
	$sqlatiki = "insert into sondurum(adi , lati , lon) values ('".$deger[0][1]['adi']."','".$deger[0][1]['lati']."','".$deger[0][1]['lon']."');";
	if (mysql_query($sqlatiki))
    echo "veriler eklendi";
	
?>