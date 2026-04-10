<?php
require_once __DIR__."/vendor/autoload.php";

Class SendGridApi{
	var $apikey;
	var $from_email;
	var $from_name;
	var $replyTo;
	
	var $domain;
	public function __construct($apikey,$from_email,$from_name,$domain,$replyTo='')
    {
        $this->apikey    = $apikey;
		$this->from_email    = $from_email;
		$this->from_name    = $from_name;
		$this->domain    = $domain;
		// if($replyTo != ''){
       	//  	$this->replyTo = $replyTo;
		// }else{
		// 	$this->replyTo =  '';
		// }
		$this->replyTo =  '';

    }

    public function sendMail($to,$to_name,$subject,$message,$attachment,$data=array()){
    	
		$email = new \SendGrid\Mail\Mail(); 
		$email->setFrom($this->from_email, $this->from_name);
		$email->setSubject($subject);
		$email->addTo($to, $to_name);
		$email->addCustomArg('domain', $this->domain);
		$email->addContent(
			"text/html", $message
		);
		if ($this->replyTo != '') {
            $email->setReplyTo($this->replyTo);
        }

		// if($attachment != ''){
		// 	$file_encoded = base64_encode(file_get_contents($attachment));
		// 	$email->addAttachment(
		// 		$file_encoded,
		// 	);
		// }
		if (!empty($attachment)) {
			if(is_array($attachment)){
				foreach($attachment as $attach){
					if(file_exists($attach)){
						$file_encoded = base64_encode(file_get_contents($attach));
						$mime_type = mime_content_type($attach);
						$filename = basename($attach);
			
						$email->addAttachment(
							$file_encoded,
							$mime_type,
							$filename,
							"attachment"
						);
					}
				}
			}else{
				if(file_exists($attachment)){
					$file_encoded = base64_encode(file_get_contents($attachment));
					$mime_type = mime_content_type($attachment);
					$filename = basename($attachment);
		
					$email->addAttachment(
						$file_encoded,
						$mime_type,
						$filename,
						"attachment"
					);
				}
			}
        }
		$sendgrid = new \SendGrid($this->apikey);
		try {
			$response = $sendgrid->send($email);
			
			if($response->statusCode() == '202'){
				$return['status'] = "success";
				$return['result'] = $response;
				$return['message'] = "Mail sent successfully";
			}else{
				$return['status'] = "error";
				$errors = json_decode($response->body(),true);        
				$return['message'] = $errors['errors'][0]['message'];
			}
		} catch (Exception $e) {
			$return['status'] = "error";
			$return['message'] = $e->getMessage();
		}
		return $return;
    }
}
