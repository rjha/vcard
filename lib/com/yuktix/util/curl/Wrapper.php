<?php

	namespace com\yuktix\util\curl {
		
		use \com\indigloo\Configuration as Config;
        use \com\indigloo\core\Web as Web;
        use \com\indigloo\Logger as Logger;
        use \com\yuktix\auth\Login as Login;
		
		class Wrapper {
			
			private $ch ;
			private $headers ;
			private $contentType ;
			
			function __construct($url,$cookies=array(), $xheaders=array()) {
				
				$this->ch = curl_init ();
				$this->headers = array();
				$this->contentType = "application/json" ;
				$user_agent = "Mozilla/5.0 (Windows NT 6.1; rv:22.0) Gecko/20130405 Firefox/22.0";
				
				curl_setopt ( $this->ch, CURLOPT_URL, $url );
				curl_setopt ( $this->ch, CURLOPT_USERAGENT, $user_agent );
				curl_setopt ( $this->ch, CURLOPT_TIMEOUT, 30 );
				curl_setopt ( $this->ch, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt ( $this->ch, CURLOPT_FOLLOWLOCATION, 1 );
				curl_setopt ( $this->ch, CURLOPT_SSL_VERIFYPEER, 0);
				
			}
			
			function setDebug() {
				curl_setopt($this->ch, CURLOPT_VERBOSE, true);
			}
			
			function setCookies($cookies) {
                
				// cookie headers
				if(!empty($cookies)) {
					$cookie_header = "Cookie: ";
					foreach($cookies as $name => $value) {
						$cookie_header = $cookie_header.$name."=".$value." " ;
					}
						
					$cookie_header .= "\r\n" ;
					array_push($this->headers, $cookie_header);
				}
                
			}
			
			function setXHeaders($xheaders) {
				
				// extra headers
				if(!empty($xheaders)) {
					foreach($xheaders as $name => $value) {
						$x_header = $name.": ".$value." " ;
						array_push($this->headers, $x_header);
					}
				}
                
			}
			
			function setContentType($ct) {
				$this->contentType = $ct ;
			}
			
			function doPut($data) {
                
				// @imp Do not use the built-in PUT handling
				// instead use the  CURLOPT_CUSTOMREQUEST attribute 
				// to specify the request type we want manually
				curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "PUT");
				$this->setContentHeaders($data,true);
				$response = $this->execute();
				return $response ;
                
			}
			
			function doPost($data) {
				
                // JSON encode the PHP $data object
				$this->setContentHeaders($data, true);
				curl_setopt ($this->ch, CURLOPT_POST, true);
				$response = $this->execute();
				return $response;
                
			}
			
			function doRawPost($data) {
                
				// do not json_encode data
                // treat as string 
				$this->setContentHeaders($data, false);
				curl_setopt ($this->ch, CURLOPT_POST, true);
				$response = $this->execute();
				return $response ;
                
			}
			
			function doGet() {
                
				$ctHeader = sprintf("Content-Type: %s; charset=UTF-8", $this->contentType);
				array_push($this->headers, $ctHeader);
				$response = $this->execute();
				return $response;
                
			}
			
			function close() {
				// free resources
				curl_close($this->ch);
			}
			
			// ==============================================
			
			private function setContentHeaders($data, $encode) {
				
				// we received a JSON object 
				if($encode) {
					$data = json_encode($data);
					$data = $data."\r\n" ;
				}
				
				$length = strlen($data) ;
				curl_setopt ( $this->ch, CURLOPT_POSTFIELDS, $data );
				$ctHeader = sprintf("Content-Type: %s; charset=UTF-8", $this->contentType);
				array_push($this->headers, $ctHeader);
				array_push($this->headers, "Content-Length: ".$length) ;
			
			}
			
			private function execute() {
                
				// set curl headers
				curl_setopt($this->ch,CURLOPT_HTTPHEADER, $this->headers);
				$response = curl_exec ($this->ch);
				$code = curl_getinfo ( $this->ch, CURLINFO_HTTP_CODE);
				
				// return zero indicates network issues
				// code zero needs special handing
				
				if($code == 0) {
					$code = 502 ;
					$responseObj = new \stdClass ;
					$responseObj->code = 502 ;
					$responseObj->error = "Network error: server unreachable" ;
					$response = json_encode($responseObj);
				}
				
				return array("code" => $code , "response" => $response);
			}
			
			function setSecurityHeaders($login) {
				
                // do we have web session login?  
				if(!is_null($login)) {
					$sessionKey = $login->sessionKey;
    				$this->setXHeaders(array(
                        "Authorization" => "Signature=".$sessionKey, 
                        "x-machine-cookie" => $login->machineCookie));
                        
				} else {
                    // We did not find login session
                    // use the public API keys 
                    $client_key = Config::getInstance()->get_value("yuktix.api.public.client.key");
                    $secret_key = Config::getInstance()->get_value("yuktix.api.public.secret.key");
                    if(empty($client_key) || empty($secret_key)) {
                        trigger_error("No public API keys in configuration file.", E_USER_ERROR);
                    }
                    
                    $auth_header = Login::createAuthorizationHeader($client_key, $secret_key);
                    $this->setXHeaders(array("Authorization" => $auth_header));
                    
                }
				
			}
			
		}
		
	}

?>
