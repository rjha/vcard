<?php

namespace com\yuktix\dao {

    use \com\indigloo\Configuration as Config;
    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\Logger as Logger;

    class Card {

        private $dbh ;

        function __construct() {
            $this->dbh = PDOWrapper::getHandle();
        }

        function store($name, $email) {

            try {

                // start Tx
                $this->dbh->beginTransaction();

                $sql = "insert INTO card_master(name, email, created_on, updated_on) "
                ." VALUES(:name, :email, now(), now()) ON DUPLICATE KEY UPDATE version = version+1 " ;

                $stmt = $this->dbh->prepare($sql);
                $stmt->bindParam(":name", $name, \PDO::PARAM_STR);
                $stmt->bindParam(":email", $email, \PDO::PARAM_STR);
                $stmt->execute();

                //end Tx
                $this->dbh->commit();

            } catch (\PDOException $e) {
                $dbh->rollBack();
                $dbh = null;
                throw new DBException($e->getMessage(), $e->getCode());

            } catch(\Exception $ex) {
                $dbh->rollBack();
                $dbh = null;
                throw new DBException($ex->getMessage(), $ex->getCode());
            }
            
        }

        function get($page) {

            $this->dbh->beginTransaction();
            // page size = 50 
            $page_size = 50;
            $start = $page * $page_size;
            $sql = "select name, email from card_master order by email asc limit %d, %d " ;
            $sql = sprintf($sql, $start, $page_size);

            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;

        }

        function trash($emails) {

            try { 
                // start Tx
                $this->dbh->beginTransaction();
                $sql1 = "insert INTO card_trash(email, created_on, updated_on) "
                ." VALUES(:email, now(), now()) ON DUPLICATE KEY UPDATE version = version+1 ";

                $sql2 = "delete from card_master where email  = :email ";
                $stmt1 = $this->dbh->prepare($sql1);
                $stmt2 = $this->dbh->prepare($sql2);

                foreach($emails as $email) {

                    $stmt1->bindParam(":email", $email, \PDO::PARAM_STR);
                    $stmt1->execute();

                    $stmt2->bindParam(":email", $email, \PDO::PARAM_STR);
                    $stmt2->execute();

                }

                //end Tx
                $this->dbh->commit();

            } catch (\PDOException $e) {
                $dbh->rollBack();
                $dbh = null;
                throw new DBException($e->getMessage(), $e->getCode());

            } catch(\Exception $ex) {
                $dbh->rollBack();
                $dbh = null;
                throw new DBException($ex->getMessage(), $ex->getCode());
            }

        }


    }
}

?>
