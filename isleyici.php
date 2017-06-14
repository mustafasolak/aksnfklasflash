<?php 
	
	
	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	$basla=microtime(); 
	ini_set('memory_limit', '-1');
	ini_set('xdebug.max_nesting_level', '-1');


	set_time_limit(0);	
	
	include "simple_html_dom.php";
	include "baglanti.php";
	
	$soldakiDizi 	= array();
	$sagdakiDizi 	= array();
	$kokSiteAdresi			= "http://www.hepsiburada.com";
	$siteAdresi 	 	 	= $_POST["link"];
	$kategori_id 	 		= 1;
	$kategori_adi	 	 	= "";
	$kategori_url	 	 	= "";
	$kategori_kime_bagli 	= $_POST["kategori_kime_bagli"];  // Eğer anasayfa kategorisi ise bağlı değeri 0 olacak. Çünkü kimseye bağlı değil.
	$kategori_seviye 	 	= 1;  
	$kategori_turu			= $_POST["kategoriTuru"];  // 1 anasayfa demek, 2 alt kategori demek, 3 kategori sonu
	$indis					= 0;  // sağdaki dizi için
	$siraNo					= 0;  // soldaki dizi için
	$devam					= true;
	$islemsayisi			= -1;
	
	if ($kategori_turu == 1)
		anaKategorileriAl($siteAdresi,$kategori_turu,$kategori_kime_bagli);
	elseif ($kategori_turu == 2)
		altKategorileriAl($siteAdresi,$kategori_turu,$kategori_kime_bagli);
		
	$son=microtime(); 
	
	function anaKategorileriAl($adres,$kategori_turumuz,$kimeBagli)
	{
		//echo "<hr>";
		//echo "<br>gelen id: $kimeBagli<br>";
		global $islemsayisi;
		
		$islemsayisi ++;
		//if ($islemsayisi>100) return null;
		
		global $soldakiDizi;
		global $sagdakiDizi;
		global $siteAdresi;
		global $kokSiteAdresi;
		global $kategori_id;
		global $kategori_adi;
		global $kategori_url;
		global $kategori_kime_bagli; 
		global $kategori_seviye;
		global $kategori_turu;
		global $indis;
		global $siraNo;		
		global $devam;
		$linkin_altkategorileri = array();
		
		if ($kategori_turumuz == 1) // Yani işlemin ana sayfada, ana kategoride işlem gerçekleştirileceğini bildiriyor.
		{
			$context = stream_context_create(
				array(
					'http' => array(
						'follow_location' => false
					)
				)
			);
			$hatasayac = 0;

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
						$kategori_url = $kokSiteAdresi  . "/" . ltrim($kategori_url,"/");
					
					
					$kategori_adi = trim($kategori->plaintext);
					$sql = "insert into kategoriler(kategori_adi,kategori_url,kategori_kime_bagli,kategori_seviye,kategori_turu) 
					values('$kategori_adi','$kategori_url','$kategori_kime_bagli','$kategori_seviye','$kategori_turu')";
					if (!mysql_query($sql))
						$hatasayac ++;
					
					$linkin_altkategorileri[$indis]["kategori_adi"] = $kategori_adi;
					$linkin_altkategorileri[$indis]["kategori_url"] = $kategori_url;
					$indis++;
				}
			}
		
			// Şimdi veritabanından bakılmamış olan en son elemanın bilgisini çekicem.
			$sqlBilgiCek = "select * from kategoriler  where bakildimi=0 order by kategori_id desc limit 1";
			$sqlBilgiSonuc = mysql_fetch_assoc(mysql_query($sqlBilgiCek));
			$idsi = $sqlBilgiSonuc["kategori_id"];
			$urlsi = $sqlBilgiSonuc["kategori_url"];
			$altkategoribilgisi = 2;
			
			if ($hatasayac == 0)
				echo json_encode(
								array(
										'islemsonucu' => '1',
										'idsi'=>$idsi,
										'urlsi'=>$urlsi,
										'altkategoribilgisi'=>$altkategoribilgisi,
										'gelenAdres' => $adres,
										'alt_kategorileri' => $linkin_altkategorileri
									 )
								);
			else				 
				echo json_encode(array("hata var"));
		}
	}
	
	function altKategorileriAl($adres,$kategori_turumuz,$kimeBagli)
	{
		global $soldakiDizi;
		global $sagdakiDizi;
		global $siteAdresi;
		global $kokSiteAdresi;
		global $kategori_id;
		global $kategori_adi;
		global $kategori_url;
		global $kategori_kime_bagli; 
		global $kategori_seviye;
		global $kategori_turu;
		global $indis;
		global $siraNo;		
		global $devam;
		$linkin_altkategorileri = array();
		
		$msHtml = new simple_html_dom();
		$msHtml->load_file($adres);
		$altKategoriler =  $msHtml->find('ul[class="items"] li');
		$hatasayac = 0;
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
						$kategori_url = $kokSiteAdresi  . "/" . ltrim($kategori_url,"/");
											
					$kategori_adi = trim($alt_kategori->plaintext);
					$sql = "insert into kategoriler(kategori_adi,kategori_url,kategori_kime_bagli,kategori_seviye,kategori_turu) 
					values('$kategori_adi','$kategori_url','$kategori_kime_bagli','$kategori_seviye','$kategori_turu')";
					if (!mysql_query($sql))
						$hatasayac ++;
					
					$linkin_altkategorileri[$indis]["kategori_adi"] = $kategori_adi;
					$linkin_altkategorileri[$indis]["kategori_url"] = $kategori_url;
					$indis++;
					
				}
			}
			
			$sqlBakildimiGuncelle = "update kategoriler set bakildimi='1' where kategori_id='$kategori_kime_bagli'";
			if (!mysql_query($sqlBakildimiGuncelle))
						$hatasayac ++;
			
			// Şimdi veritabanından bakılmamış olan en son elemanın bilgisini çekicem.
			$sqlBilgiCek = "select * from kategoriler  where bakildimi=0 order by kategori_id desc limit 1";
			$sqlBilgiSonuc = mysql_fetch_assoc(mysql_query($sqlBilgiCek));
			$idsi = $sqlBilgiSonuc["kategori_id"];
			$urlsi = $sqlBilgiSonuc["kategori_url"];
			$altkategoribilgisi = 2;
			
			if ($hatasayac == 0)
				echo json_encode(
								array(
										'islemsonucu' => '2',
										'idsi'=>$idsi,
										'urlsi'=>$urlsi,
										'altkategoribilgisi'=>$altkategoribilgisi,
										'gelenAdres' => $adres,
										'alt_kategorileri' => $linkin_altkategorileri
									 )
								);
			else				 
				echo json_encode(array("hata var"));
			
		}
		else // demek ki alt kategori yok.
		{
			
			$sqlKategoriTuruGuncelle = "update kategoriler set kategori_turu='3',bakildimi='1' where kategori_id='$kategori_kime_bagli'";
			if (!mysql_query($sqlKategoriTuruGuncelle))
						$hatasayac ++;
					
					
			// Şimdi veritabanından bakılmamış olan en son elemanın bilgisini çekicem.
			$sqlBilgiCek = "select * from kategoriler  where bakildimi=0 order by kategori_id desc limit 1";
			$sqlBilgiSonuc = mysql_fetch_assoc(mysql_query($sqlBilgiCek));
			$idsi = $sqlBilgiSonuc["kategori_id"];
			$urlsi = $sqlBilgiSonuc["kategori_url"];
			$altkategoribilgisi = 3;
			
			if ($hatasayac == 0)
				echo json_encode(
								array(
										'islemsonucu' => '3',
										'idsi'=>$idsi,
										'urlsi'=>$urlsi,
										'altkategoribilgisi'=>$altkategoribilgisi,
										'gelenAdres' => $adres,
										'alt_kategorileri' => $linkin_altkategorileri
									 )
								);
			else				 
				echo json_encode(array("hata var"));
			
			
			
			
			/*
			//echo $kimeBagli . " -demek ki alt kategori yok .<br>";
							
			foreach ($sagdakiDizi as $key => $val) {
			   if ( $val["kategori_id"] === $kimeBagli ) 
				   $sagdakiDizi[$key]["kategori_turu"] = 3;
			}
			
			$gidecekUrl = $soldakiDizi[count($soldakiDizi)-1]["kategori_url"];
			$gidecekId  = $soldakiDizi[count($soldakiDizi)-1]["kategori_id"];
			$gidecekKategoriAdi = $soldakiDizi[count($soldakiDizi)-1]["kategori_adi"];
					
			array_pop($soldakiDizi);
			
			kategorileriAl($gidecekUrl,$kategori_turumuz,$gidecekId);	
			*/
			
		}
	}
	

	
?> 