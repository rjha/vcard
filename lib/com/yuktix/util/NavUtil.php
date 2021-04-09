<?php 

namespace com\yuktix\util {
		
		use \com\indigloo\Logger as Logger ;
		
		class NavUtil {
	        
            private static $keen_menu = 
                '<li class="k-nav__item">'.
                ' <a href="{link}" class="k-nav__link">'.
                ' <span class="k-nav__link-text">{name} </span>'.
                '</a> </li> ';
            
            private static $keen_divider = '<li class="divider"></li>';
            private static $bs4_menu = '<a class="dropdown-item" href="{link}"> {name}</a>';
            private static $bs4_divider = '<div class="dropdown-divider"> </div>';
            
            
            static function getUserInitials($login) {
                
                $first = empty($login->firstName) ? "-" : $login->firstName;
                $second = empty($login->lastName) ? "-" : $login->lastName;
                
                $token = mb_substr($first, 0, 1). mb_substr($second, 0, 1);
                $token = strtoupper($token);
                return $token;
                
            }
            
            static function getAlertCount($login) {
                
                $count = 0;
                $gWeb = \com\indigloo\core\Web::getInstance();
                $navigationData = $gWeb->find("yuktix.site.navigation.data");
                
                if(!empty($navigationData)) {
                    $navigationDataObj = json_decode($navigationData);
                    $count = $navigationDataObj->alertCount;
                }
                
                return $count;
                
            }
            
            static function getAccountName($login) {
                
                $gWeb = \com\indigloo\core\Web::getInstance();
                $account_name = $login->accountName;
                $impersonated_account_name = $gWeb->find("yuktix.oauth2.impersonated.account.name");
                
                if(!empty($impersonated_account_name)) {
                    $account_name = $impersonated_account_name;
                }
                
                return $account_name;
                
            }
            
			static function renderToolbar($login) {
				
                $dashboard = "ankidb";
                $gWeb = \com\indigloo\core\Web::getInstance();
                $navigationData = $gWeb->find("yuktix.site.navigation.data");
                
                if(!empty($navigationData)) {
                    $navigationDataObj = json_decode($navigationData);
                    $dashboard = $navigationDataObj->defaultDashboard->name;
                }
                
                $menus = array();
                $menu = array("name" => "Alerts", "link" => "/app/account/alerts.php", "type" => 1);
                array_push($menus, $menu);

                
                $divider = array("name" => "divider", "link" => "#", "type" => 2);
                array_push($menus, $divider);
                
                if($login->superAdmin || $login->customerAdmin) {
                    $menu = array("name" => "Admin", "link" => "/admin", "type" => "1");
                    array_push($menus, $menu);
                }

				if(isset($login->superAdmin) && $login->superAdmin) {
					
                    
                	$account_name = $login->accountName;
                    $account_code = "&bigstar;";
                    
    				$redirect = base64_encode($_SERVER["REQUEST_URI"]);
                    $impersonated_account_name = $gWeb->find("yuktix.oauth2.impersonated.account.name");
                    
    			    if(!empty($impersonated_account_name)) {
                        $account_name = $impersonated_account_name;
                        $account_code =  "&star;" ;
                    }
                    
                    $name = "switch account &nbsp;{code}";
                    $name = str_replace(array("{name}", "{code}"), array($account_name, $account_code), $name);

                    $link = "/admin/account/list.php?redirect_to={redirect}&operation=impersonate";
                    $link = str_replace(array("{redirect}"), array($redirect), $link);

                    $menu = array("name" => $name, "link" => $link, "type" => "1");
                    array_push($menus, $menu);
                    
				} 
				
                $menu = array("name" => "Settings", "link" => "/app/user/setting.php", "type" => 1);
                array_push($menus, $menu);
                
                
                $menu = array("name" => "Logout", "link" => "/app/logout.php", "type" => "1");
                array_push($menus, $menu);
                
                // render all the menus 
                // php string concatenation should be fast enough!
                $size = sizeof($menus);
                $content = "" ;

                for($i =0; $i < $size; $i++) {
                    $content = $content.NavUtil::renderMenu($menus[$i], $dashboard);
                }
            
				return $content;
				
			}

            private static function getTemplate($name, $type) {
                
                $template = NULL;
                
                if(strcmp($name, "ankidb") == 0){
                    switch($type) {
                        case 1:
                            $template = NavUtil::$keen_menu;
                            break ;
                        case 2:
                            $template = NavUtil::$keen_divider;
                            break;
                        default:
                            break;
                        
                    }
                } else {
                    switch($type) {
                        case 1:
                            $template = NavUtil::$bs4_menu;
                            break ;
                        case 2:
                            $template = NavUtil::$bs4_divider;
                            break;
                        default:
                            break;
                    }
                }
                
                return $template;
            }
            
            private static function renderMenu($menu, $dashboard) {

                $template = NavUtil::getTemplate($dashboard, $menu["type"]);
                if(empty($template)) {
                    return "";
                }
                
                $content = str_replace(array("{name}", "{link}"), array($menu["name"], $menu["link"]), $template);
                return $content;

            }
	}
}
				
				
?>
