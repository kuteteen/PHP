<?php 
	
	class YandexAPI
	{
		public $api_token   = "";
		
		public $domain_list = array();
		public $mail_list   = array();
		
		
		function getData($link,$headers=array(),$post=array()){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$link);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			if(!empty($post)){ curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post)); }
			$options = array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HEADER         => false,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_ENCODING       => "",  
				CURLOPT_USERAGENT      => "spider",
				CURLOPT_AUTOREFERER    => true,  
				CURLOPT_CONNECTTIMEOUT => 120,   
				CURLOPT_TIMEOUT        => 120,  
				CURLOPT_MAXREDIRS      => 10,  
				CURLOPT_SSL_VERIFYPEER => false  
			);
			curl_setopt_array( $ch, $options );
			$server_output = curl_exec ($ch);
			curl_close ($ch);
			return json_decode($server_output,true) ;
		}
		
		
		
		// Domain Listesi
		function getDomainList(){
			$devam = true; $i = 0;$c=0;
			while ($devam){
				$i++;
				$data = $this->getData("https://pddimp.yandex.ru/api2/admin/domain/domains?page=".$i."&on_page=20",array('PddToken:'.$this->api_token));
				$domains = $data['domains'];
				foreach($domains as $domain){
					$this->domain_list[] = $domain;
				}
				$c += $data['found'];
				if($c >= $data['total']){$devam=false;}
			} 
		}
		
		
		// Mail Listesi
		function getMailList($domain){
			$devam = true; $i = 0;$c=0;
			while ($devam){
				$i++;
				$data = $this->getData("https://pddimp.yandex.ru/api2/admin/email/list?domain={$domain}&page=".$i."&on_page=20",array('PddToken:'.$this->api_tokens[0]));
				$accounts = $data['accounts'];
				foreach($accounts as $account){
					$this->mail_list[] = $account;
				}
				$c += $data['found'];
				if($c >= $data['total']){$devam=false;}
			} 
		}
		// Mail Ekle
		function addMail($domain,$login,$password){
			$data = $this->getData("https://pddimp.yandex.ru/api2/admin/email/add",array('PddToken:'.$this->api_tokens[0]),array("domain"=>$domain,"login"=>$login,"password"=>$password));
			return ($data['success'] == 'ok'? true : false);
		}
		// Mail Sil
		function delMail($domain,$login){
			$data = $this->getData("https://pddimp.yandex.ru/api2/admin/email/del",array('PddToken:'.$this->api_tokens[0]),array("domain"=>$domain,"login"=>$login));
			return ($data['success'] == 'ok'? true : false);
		}
		// Mail Sil
		function editMail($domain,$login,$parameters=array()){
			// password <new password>
			// iname <first name>
			// fname <last name>
			// enabled <mailbox status>
			// birth_date <date of birth>
			// sex <gender>
			// hintq <secret question>
			// hinta <answer to secret question>
			$parameters["domain"] = $domain;
			$parameters["login"] = $login;
			$data = $this->getData("https://pddimp.yandex.ru/api2/admin/email/edit",array('PddToken:'.$this->api_tokens[0]),$parameters);
			var_dump($data);
			return ($data['success'] == 'ok'? true : false);
		}
		function getMailCount($domain,$login){
			$data = $this->getData("https://pddimp.yandex.ru/api2/admin/email/counters?domain={$domain}&login={$login}",array('PddToken:'.$this->api_tokens[0]));
			var_dump($data);
		}		
		
	}


?>





