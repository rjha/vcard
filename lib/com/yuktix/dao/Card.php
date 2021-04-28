<?php

namespace com\yuktix\dao {

    use \com\indigloo\Configuration as Config;
    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\Logger as Logger;
    use com\indigloo\exception\DBException as DBException;

    class Card {

        private $dbh ;
        private $page_size;

        function __construct() {
            $this->dbh = PDOWrapper::getHandle();
            $this->page_size = 100;
        }

        function store($cardObj) {

            try {

                if(!property_exists($cardObj, "source")) {
                    throw new APIException(400, "source is required");
                }

                if(!property_exists($cardObj, "name")) {
                    throw new APIException(400, "name is required");
                }

                if(!property_exists($cardObj, "email")) {
                    throw new APIException(400, "email is required");
                }
                
                $cc = property_exists($cardObj, "cc") ? $cardObj->cc : 0;
                $phone = property_exists($cardObj, "cc") ? $cardObj->phone : "--" ;

                // @todo 
                // email in trash list?

                // start Tx
                $this->dbh->beginTransaction();

                $sql = "insert INTO card_master(source, name, email, "
                ." country_code, phone, created_on, updated_on) "
                ." VALUES(:source, :name, :email, :cc, :phone, now(), now()) "
                ." ON DUPLICATE KEY UPDATE name = VALUES(name), phone = VALUES(phone), "
                ." country_code = VALUES(country_code), source = VALUES(source), "
                ." version = version+1 " ;

                $stmt = $this->dbh->prepare($sql);
                $stmt->bindParam(":name", $cardObj->name, \PDO::PARAM_STR);
                $stmt->bindParam(":email", $cardObj->email, \PDO::PARAM_STR);
                $stmt->bindParam(":source", $cardObj->source, \PDO::PARAM_STR);
                $stmt->bindParam(":cc", $cc, \PDO::PARAM_INT);
                $stmt->bindParam(":phone", $cardObj->phone, \PDO::PARAM_STR);
                $stmt->execute();

                //end Tx
                $this->dbh->commit();

            } catch (\PDOException $e) {
                $this->dbh->rollBack();
                $this->dbh = null;
                throw new DBException($e->getMessage(), $e->getCode());

            } catch(\Exception $ex) {
                $this->dbh->rollBack();
                $this->dbh = null;
                throw new DBException($ex->getMessage(), $ex->getCode());
            }
            
        }

        function getAllMainItems() {

            $start = $page * $this->page_size;
            $sql = "select name, email from card_master order by email asc " ;
            
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;

        }

        function getMainItems($page) {

            $start = $page * $this->page_size;
            $sql = "select name, email from card_master "
            ." order by email asc limit %d, %d " ;
            $sql = sprintf($sql, $start, $this->page_size);

            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;

        }

        function getTrashItems($page) {

            $start = $page * $this->page_size;
            $sql = "select name, email from card_trash "
            ." order by created_on desc limit %d, %d " ;
            $sql = sprintf($sql, $start, $this->page_size);

            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;

        }

        function getTotalMainPages() {

            $sql = "select count(id) as total from card_master" ;
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $total = intval($result["total"]);
            
            $num_pages = ceil($total / $this->page_size);
            return $num_pages;

        }

        function getTotalTrashPages() {

            $sql = "select count(id) as total from card_trash" ;
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $total = intval($result["total"]);
            
            $num_pages = ceil($total / $this->page_size);
            return $num_pages;

        }

        function trash($items) {

            try { 
                // start Tx
                $this->dbh->beginTransaction();
                $sql1 = "insert INTO card_trash(name, email, created_on, updated_on) "
                ." VALUES(:name, :email, now(), now()) ON DUPLICATE KEY UPDATE version = version+1 ";

                $sql2 = "delete from card_master where email  = :email ";
                $stmt1 = $this->dbh->prepare($sql1);
                $stmt2 = $this->dbh->prepare($sql2);

                foreach($items as $item) {

                    $stmt1->bindParam(":email", $item->email, \PDO::PARAM_STR);
                    $stmt1->bindParam(":name", $item->name, \PDO::PARAM_STR);
                    $stmt1->execute();

                    $stmt2->bindParam(":email", $item->email, \PDO::PARAM_STR);
                    $stmt2->execute();

                }

                //end Tx
                $this->dbh->commit();

            } catch (\PDOException $e) {
                $this->dbh->rollBack();
                $this->dbh = null;
                throw new DBException($e->getMessage(), $e->getCode());

            } catch(\Exception $ex) {
                $this->dbh->rollBack();
                $this->dbh = null;
                throw new DBException($ex->getMessage(), $ex->getCode());
            }

        }

        function checkInTrash($email) {

            $flag = 0;
            $sql = "select id from card_trash where email = :email" ;

            $stmt = $this->dbh->prepare($sql);
            $stmt->bindParam(":email", $email, \PDO::PARAM_STR);
            $stmt->execute();

            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            if($result) {
                $flag = 1 ;
            }
           
            return $flag;

        }

    }
}

?>
