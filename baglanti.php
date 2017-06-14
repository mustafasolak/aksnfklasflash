<?php
	/* 
	*	Mysql bağlantısı	
	*/
	$connx = mysql_connect("localhost", "projeyorum","xGYcNzfWqLdGA3cW");
	if($connx){
		$dbConnx = mysql_select_db("projeyorum", $connx);
		mysql_query("SET NAMES utf8");
		mysql_query("SET CHARACHTER SET	UTF-8");
	}else{
		die("Veritabanına Bağlanılamadı");
	}
	/*   **************    ************ ******** */
	
?>