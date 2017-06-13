<meta charset="utf-8" />

<?php 
error_reporting(0);

	$basla=microtime(); 
	ini_set('memory_limit', '-1');
	ini_set('xdebug.max_nesting_level', '-1');


	set_time_limit(0);	
	
	include "simple_html_dom.php";
	
	$soldakiDizi 	= array();
	$sagdakiDizi 	= array();
	$siteAdresi 	 	 	= "http://www.hepsiburada.com";
	$kategori_id 	 		= 1;
	$kategori_adi	 	 	= "";
	$kategori_url	 	 	= "";
	$kategori_kime_bagli 	= 0;  // Eğer anasayfa kategorisi ise bağlı değeri 0 olacak. Çünkü kimseye bağlı değil.
	$kategori_seviye 	 	= 1;  
	$kategori_turu			= 1;  // 1 anasayfa demek, 2 alt kategori demek, 3 kategori sonu
	$indis					= 0;  // sağdaki dizi için
	$siraNo					= 0;  // soldaki dizi için
	$devam					= true;
	$islemsayisi			= -1;
	
	function kategorileriAl($adres,$kategori_turumuz,$kimeBagli)
	{
		echo "<hr>";
		echo "<br>gelen id: $kimeBagli<br>";
		global $islemsayisi;
		
		$islemsayisi ++;
		//if ($islemsayisi>100) return null;
		
		global $soldakiDizi;
		global $sagdakiDizi;
		global $siteAdresi;
		global $kategori_id;
		global $kategori_adi;
		global $kategori_url;
		global $kategori_kime_bagli; 
		global $kategori_seviye;
		global $kategori_turu;
		global $indis;
		global $siraNo;		
		global $devam;
		
		if ($kategori_turumuz == 1) // Yani işlemin ana sayfada, ana kategoride işlem gerçekleştirileceğini bildiriyor.
		{
			$context = stream_context_create(
				array(
					'http' => array(
						'follow_location' => false
					)
				)
			);
			//$content = file_get_contents('http://example.org/', false, $context);

			//$html 		= file_get_html($adres);
			$html 		= file_get_html($adres, false, $context);
			$anaKategoriler =  $html->find('div[class=footer-middle-left] section',1)->find('ul li');
			$kategori_kime_bagli = $kimeBagli;
			foreach($anaKategoriler as $kategori)
			{
				$kategori_url = $kategori->find('a',0)->href;
				
				$tire_C_Konum  = strpos($kategori_url,"-c-");
				if ($tire_C_Konum !== false)
				{
					if (0 !== strpos($kategori_url, 'http'))  // Yani bağlantının içinde http yok ise
						$kategori_url = $siteAdresi  . "/" . ltrim($kategori_url,"/");
						
					//echo $kategori->plaintext. "  " . $kategori_url . "<br>";
					
					$kategori_adi = trim($kategori->plaintext);
					
					$sagdakiDizi[$indis]["kategori_id"]			= $kategori_id;
					$sagdakiDizi[$indis]["kategori_adi"] 		= $kategori_adi;
					$sagdakiDizi[$indis]["kategori_url"] 		= $kategori_url;
					$sagdakiDizi[$indis]["kategori_kime_bagli"] = $kategori_kime_bagli;
					$sagdakiDizi[$indis]["kategori_seviye"] 	= $kategori_seviye;
					$sagdakiDizi[$indis]["kategori_turu"]		= $kategori_turumuz;
				
					array_push($soldakiDizi,$sagdakiDizi[$indis]);
					
					$indis++;
					$kategori_id++;
				}
			}
			
			$kategori_seviye++;
			$kategori_turumuz = 2; // artık ana sayfa olmadığını alt kategori olduğunu belirtiyorum.
			
			$siraNo 			= count($soldakiDizi)-1;
			$gidecekId  		= $soldakiDizi[$siraNo]["kategori_id"];
			$gidecekKategoriAdi = $soldakiDizi[$siraNo]["kategori_adi"];
			$gidecekUrl 		= $soldakiDizi[$siraNo]["kategori_url"];
			
			array_pop($soldakiDizi);
			
			kategorileriAl($gidecekUrl,$kategori_turumuz,$gidecekId); // gidecekId değeri bu kategoriye ait alt soldakiDiziin kimeBagli olduğunu gösteriyor.
			
		}
		else
		{
			
			$msHtml = new simple_html_dom();
			$msHtml->load_file($adres);
			$altKategoriler =  $msHtml->find('ul[class="items"] li');
			
			if ($altKategoriler)
			{
				
				$kategori_kime_bagli = $kimeBagli;
				foreach($altKategoriler as $alt_kategori)
				{
					$kategori_url = $alt_kategori->find('a',0)->href;
					
					$tire_C_Konum  = strpos($kategori_url,"-c-");
					if ($tire_C_Konum !== false)
					{
						if (0 !== strpos($kategori_url, 'http'))  // Yani bağlantının içinde http yok ise
							$kategori_url = $siteAdresi  . "/" . ltrim($kategori_url,"/");
							
						// echo $alt_kategori->plaintext. " = " . $kategori_url . "<br>";
						
						$kategori_adi = trim($alt_kategori->plaintext);
						
						$sagdakiDizi[$indis]["kategori_id"]			= $kategori_id;
						$sagdakiDizi[$indis]["kategori_adi"] 		= $kategori_adi;
						$sagdakiDizi[$indis]["kategori_url"] 		= $kategori_url;
						$sagdakiDizi[$indis]["kategori_kime_bagli"] = $kategori_kime_bagli;
						$sagdakiDizi[$indis]["kategori_seviye"] 	= $kategori_seviye;
						$sagdakiDizi[$indis]["kategori_turu"]		= $kategori_turumuz;
						
						
						array_push($soldakiDizi,$sagdakiDizi[$indis]);
						
						$indis++;
						$kategori_id++;
					}
				}
				
				$kategori_seviye++;
				$kategori_turumuz = 2; // alt kategori olduğunu belirtiyorum.
								
				$siraNo 			= count($soldakiDizi)-1;
				$gidecekId  		= $soldakiDizi[$siraNo]["kategori_id"];
				$gidecekKategoriAdi = $soldakiDizi[$siraNo]["kategori_adi"];
				$gidecekUrl			= $soldakiDizi[$siraNo]["kategori_url"];
						
				array_pop($soldakiDizi);
				
				kategorileriAl($gidecekUrl,$kategori_turumuz,$gidecekId);	
				
			}
			else // demek ki alt kategori yok.
			{
				
				echo $kimeBagli . " -demek ki alt kategori yok .<br>";
								
				foreach ($sagdakiDizi as $key => $val) {
				   if ( $val["kategori_id"] === $kimeBagli ) 
					   $sagdakiDizi[$key]["kategori_turu"] = 3;
			    }
				
				$gidecekUrl = $soldakiDizi[count($soldakiDizi)-1]["kategori_url"];
				$gidecekId  = $soldakiDizi[count($soldakiDizi)-1]["kategori_id"];
				$gidecekKategoriAdi = $soldakiDizi[count($soldakiDizi)-1]["kategori_adi"];
						
				array_pop($soldakiDizi);
				
				kategorileriAl($gidecekUrl,$kategori_turumuz,$gidecekId);	
				
				
			}
		}
		
		echo "<br>İşlem sayısı :" + islemsayisi + "<br>";
	}
	
	kategorileriAl($siteAdresi,1,0);
	
	$son=microtime(); 
	echo "Örümcek :" . abs($basla-$son)." saniyede işlemi tamamladı."; 
	
?>

<div style="float:left;width:850px; border:1px red solid;">
<?php 
	print "<pre>soldakiDizi\n";
	print_r($soldakiDizi);
	print "</pre>";
?>
</div>
<div style="float:left; border:1px blue solid;">
<?php 
	print "<pre>sagdakiDizi\n";
	print_r($sagdakiDizi);
	print "</pre>";
?>
</div>
