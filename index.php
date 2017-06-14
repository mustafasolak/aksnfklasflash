<!DOCTYPE HTML>
<html lang="en-US">
<head>
  <meta charset="UTF-8">
	<title>Proje yorum ana sayfa</title>
	<link rel="stylesheet" href="style.css"/>
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
	<script type="text/javascript">
	$(function(){
		
		
		
		$("#sonuc").hide();
		
		/* jQuery İşlemlerim */
		function veriGonder()
		{
			/* Post Degerleri Alınsın */
			var sitelinki = $(".input").val();
			if(!sitelinki)
			{
				alert("Site linkini boş bıraktın !!!");
			}
			else
			{
				if (gelenKategoriTuru == 1)
					kategori_kime_bagli = 0;
					
				$.ajax({
				type: "POST",
				url: "isleyici.php",
				data: {link : sitelinki, kategoriTuru : gelenKategoriTuru, kategori_kime_bagli: kategori_kime_bagli},
				dataType: 'json',
				success: function(donenVeri){
					//var objData = jQuery.parseJSON(donenVeri);
					$("#sonuc").fadeIn(200).addClass("ok").html("");	
					//$("#sonuc").html(donenVeri[0].diziAdi + "-" + donenVeri[0].diziIcerik[1].kategori_adi);	
					var islemsonucu = donenVeri.islemsonucu;

					gelenKategoriTuru = donenVeri.altkategoribilgisi;
					if (gelenKategoriTuru == 3) gelenKategoriTuru = 2;
					kategori_kime_bagli = donenVeri.idsi;
					
					$("#kategoriId").text(donenVeri.idsi);
					$(".input").val(donenVeri.urlsi);
					$("input[name=kategoriTuru][value=" + gelenKategoriTuru + "]").prop('checked', true);
					
					if (islemsonucu == 1)
					{
						$("#sonuc").append("<b>" + donenVeri.gelenAdres +  "</b> url'sinde şu <u>ana kategoriler</u> bulundu.<br>");
						for(x=0;x<donenVeri.alt_kategorileri.length;x++)
						{
							$("#sonuc").append("<b>" + (x+1) + ")</b>" +  donenVeri.alt_kategorileri[x].kategori_adi + "   " + donenVeri.alt_kategorileri[x].kategori_url + "<br>" );
						}
					}
					else if (islemsonucu == 2)
					{
						$("#sonuc").append("<b>" + donenVeri.gelenAdres +  "</b> url'sinde şu <u>alt kategoriler</u> bulundu.<br>");
						for(x=0;x<donenVeri.alt_kategorileri.length;x++)
						{
							$("#sonuc").append("<b>" + (x+1) + ")</b>" +  donenVeri.alt_kategorileri[x].kategori_adi + "   " + donenVeri.alt_kategorileri[x].kategori_url + "<br>" );
						}
					}
					else if (islemsonucu == 3)
					{
						$("#sonuc").append("<b>" + donenVeri.gelenAdres +  "</b> url'sinde ait <u>alt kategori</u> bulunmamaktadır.<br>");
					}
					
					$( "#bilgilendirme" ).slideUp( 400 ).delay( 3000 ).fadeIn( 400 );
					veriGonder();
					
				},
				error:function(){
					$("#sonuc").html('There was an error updating the settings');
					$("#sonuc").addClass('hata');
					$("#sonuc").fadeIn(1500);
			  }   
			});
			}
		}
		
		
		var gelenKategoriTuru = 1;
		var kategori_kime_bagli = 0;
		
		$(".gonder").click(function(){
			veriGonder();
		});
		
		$("input[name=kategoriTuru]").live("change",function(){
			if ($(this).is(":checked"))
			{
				gelenKategoriTuru = $(this).val();
				// remove old images, and place the new ones. 
			}
		});
		
		
		//var myVar = setInterval(function(){ veriGonder() }, 3000);
		
			
			
	});
	</script>
</head>
<body>
	
	<!-- Ortala -->
	<div id="center">
			<b>URL :</b><input type="text" name="link" value="http://www.hepsiburada.com" class="input"/><span id="kategoriId"></span><br />
			<b>Kategori Türü :</b> <input type="radio" value="1" name="kategoriTuru"  id="kategoriTuru" checked /> 1-Ana Sayfa &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input type="radio" value="2" name="kategoriTuru" id="kategoriTuru"  /> 2-Alt Kategori &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input type="radio" value="3" name="kategoriTuru"  id="kategoriTuru" /> 3-Kategori Sonu
							<br />
			<input type="submit" value="Başlat" class="gonder"/>
		<div id="bilgilendirme">Kategori alınmaya devam ediliyor.</div>
		<div id="sonuc"></div>
		
	</div>
	<!--#Ortala -->
	
</body>
</html>