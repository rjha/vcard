<?php 

namespace com\yuktix\util {
		
		use \com\indigloo\Logger as Logger ;
		
		class TimeUtil {
		
			/**
			 * 
			 * @param $date date in dd-mm-YYYY hh:mm:ss  format
			 * @param $tz_offset timezone offset in minutes
			 * @return unix time in seconds 
			 * 
			 */
			static function convertDateToSeconds($date, $tz_offset=0) {
				// $date should be in format
				// 
				// PHP strtotime uses slash(/) for american style 
				// and dash(-) for european style dates
				// use date_default_timezone_set("UTC") inside 
				// your script
				if(empty($date)) {
					trigger_error("wrong date format", E_USER_ERROR);	
				}
				
				$date = trim($date);
				if(strlen($date) != 19) {
					trigger_error("wrong date format", E_USER_ERROR);
				}
				
				$x = strtotime($date) + ($tz_offset *60);
				return $x ;
			}
			
			static function convertMilliUnixToDate($ts) {
				
				$ts = (float) $ts * 1.0 ;
				$ts =  intval($ts/1000.0) ;
    			$x = date('d-m-Y H:i:s',$ts);
    			return $x ;
			}
			
            static function iso8601_to_unix($isoDate) {
                $x = date("U",strtotime($isoDate));
                return $x ;
            }
            
            static function iso8601_to_mysql($isoDate) {
                // @fix timezone please
                // use UTC instead of current_timezone
                $x = date("Y-m-d H:i:s",strtotime($isoDate));
                return $x ;
            }
            
            
		}
		
}
				
				
?>