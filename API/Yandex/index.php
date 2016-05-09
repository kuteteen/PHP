<?php 

	require_once("api.yandex.php");
	
	$yandex = new YandexAPI();
	
	$yandex->api_tokens = "XXXXXXXXW5JVBWR7MHK2SRIDDQUKXXXXXXXXXXXXXXXXXXXXXXXX";
	
	//$yandex->getDomainList();
	//var_dump($yandex->domain_list);
	
	//$yandex->getMailList("domain.com");
	//var_dump($yandex->mail_list);
	
	//$yandex->addMail("domain.com","testing2","123456789");
	// true - false
	
	//$yandex->delMail("domain.com","testing2");
	// true - false
	
	//$yandex->getMailCount("domain.com","testing");
	
	//$yandex->editMail("domain.com","testing",array("iname"=>"A360J","birth_date"=>"1992-06-16"));

?>