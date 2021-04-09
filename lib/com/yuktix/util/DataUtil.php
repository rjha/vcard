<?php 

namespace com\yuktix\util {
		
	use \com\indigloo\Logger as Logger ;
		
	class DataUtil {
		
	      
		static function isValidJson($url,$jsonObj,$attributes=array()) {
			$flag = true ;
			// php json_decode can return TRUE | FALSE | NULL
			 
			if($jsonObj === FALSE || $jsonObj ===  TRUE || $jsonObj == NULL ) {
				$url = urldecode($url);
				$message = sprintf("json url [%s] returned true/false/null ",$url) ;
				Logger::getInstance()->error($message);
				return false ;
			}
		
			if(is_object($jsonObj) && property_exists($jsonObj, "error")) {
				$message = sprintf("json url [%s] returned error ",$url) ;
				Logger::getInstance()->error($message);
				Logger::getInstance()->error($jsonObj->error);
				return false ;
			}
		
			if(is_object($jsonObj) && !empty($attributes)) {
				foreach($attributes as $attribute) {
					if(!property_exists($jsonObj,$attribute)) {
						$flag = false ;
						break ;
					}
				}
			}
			 
			return $flag ;
		}
		
        static function guid() {
			$value = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
					mt_rand(0, 65535),
					mt_rand(0, 65535),
					mt_rand(0, 65535),
					mt_rand(16384, 20479),
					mt_rand(32768, 49151),
					mt_rand(0, 65535),
					mt_rand(0, 65535),
					mt_rand(0, 65535));
			
			return $value ;
		}
        
        static function getYAxisDiff($data){

            $ydiff = array() ;
            $size = sizeof($data) ;

            if ($size <= 2) {
                return $ydiff ;
            }

            // first element of ydiff array
            array_push($ydiff,0);
            $last = $data[0] ;
            $index = 1 ;

            for($index = 1 ; $index < $size ; $index++) {
                array_push($ydiff,abs($data[$index] - $last)) ;
                $last = $data[$index];
            }

            return $ydiff;
        }
        
        static function isArrayOfZeros($arr) {
            
            foreach ($arr as $val) {
                if (floatval($val) != 0) {
                    return false;
                }
            }
            
            return true;
        }
        
        static function encodeDashboards($items) {

            $size = sizeof($items);
            $dashboards = array();

            for($i = 0; $i < $size; $i++) {
                $item = $items[$i];
                $line = $item["value"];
                $line = trim($line);

                // split on comma
                $dashboard = array();
                $tokens = explode("," , $line);

                foreach($tokens as $token) {

                    $token = trim($token);
                    $parts = explode("=", $token);

                    if(sizeof($parts) < 2) {
                        continue;
                    }

                    $name = $parts[0];
                    $value = $parts[1];

                    if((strcmp($name, "default") == 0) && (strcasecmp($value, "true") == 0)) {
                        $value = true ;
                        $dashboard["isDefault"] = true;
                    }

                    if((strcmp($name, "default") == 0) && (strcasecmp($value, "false") == 0)) {
                        $value = false ;
                        $dashboard["isDefault"] = false;
                    }

                    $dashboard[$name] = $value;
                }

                array_push($dashboards, $dashboard);
            }

            return $dashboards;

        }
        
        static function decodeDashboards($dashboards) {

            $items = array();
            foreach ($dashboards as $dashboard) {

                $str_default = "false";
                if(array_key_exists("isDefault", $dashboard)) {

                    if($dashboard["isDefault"] === true) {
                        $str_default = "true" ;
                    }

                    if(strcasecmp($dashboard["isDefault"], "true") == 0) {
                        $str_default = "true" ;
                    }
                }

                $line = sprintf("name=%s, label=%s, default=%s", 
                            $dashboard["name"], $dashboard["label"], $str_default);
                array_push($items, array("value" => $line, "valueType" => "dashboard"));
                
            }

            return $items;

        }

	}
		
}
				
				
?>