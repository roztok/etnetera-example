<?php

require_once __DIR__."/../leolos/mysqldb.php";


class SearchItem extends Leolos\MysqlDb\DBClass {

    protected $m_id;
    protected $m_search_patern;
    protected $m_ip;
    protected $m_search_date;
    
    public function getId() { return $this->m_id; }
    public function getSearchPatern() { return $this->m_search_patern; }
    public function setSearchPatern($patern) { $this->set("m_search_patern", $patern); }
    public function getSearchDate() { return $this->m_search_date; }
    public function getIp() { return $this->m_ip; }
    public function setIp($ip) { $this->set("m_ip", $ip);}

    /**
    *
    * Leolos/MysqlDb/DBClass use lazy loading
    */
    public function load($row = Null) {
        if( $row === Null ) {
            if( $this->m_id ) {
                
                //start transaction
                $this->sqlConn->begin();
                
                $res = $this->sqlConn->execute("SELECT 
                                                    id,
                                                    search_patern,
                                                    search_date,
                                                    ip
                                                FROM 
                                                    `search_log` 
                                                WHERE 
                                                    id=%s",
                                                $this->m_id);
                        
                $row = $res->fetch_object();
                $this->sqlConn->commit();
                
            } else {
                return;
            }
        }

        $this->m_id             = $row->id;
        $this->m_search_patern  = $row->search_patern;
        $this->m_search_date    = $row->search_date;
        $this->m_ip             = $row->ip;
    }
    
    public function save() {
    
        if( $this->isDirty ) {
            
            $this->sqlConn->begin();
            
            if( $this->m_id ) {
                
                 $this->sqlConn->execute("UPDATE 
                                            `search_log` 
                                          SET
                                            search_patern = %s,
                                            search_date = %s.
                                            ip = %s
                                            WHERE id = %s",
                                        $this->m_search_patern, 
                                        $this->m_search_date,
                                        $this->m_ip,
                                        $this->m_id);
            } else {
            
                $this->sqlConn->execute("INSERT INTO 
                                            `search_log`
                                         SET
                                            search_patern = %s,
                                            search_date = NOW() ,
                                            ip = %s",
                                        $this->m_search_patern, 
                                        $this->m_ip);
                                        
                if( $this->sqlConn->lastInsertId() ) {
                    $this->m_id = $this->sqlConn->lastInsertId();
                }
            }
            
            $this->sqlConn->commit();
            $this->isDirty = False;
        }
    }
    
    
    public function toHtmlTable() {
        echo "<tr>
                <td>".$this->getSearchDate()."</td>
                <td>".$this->getSearchPatern()."</td>
                <td>".$this->getIp()."</td>
              </tr>";
    }
}


class SearchLogList extends Leolos\MysqlDb\ListObject {

    public function __construct(& $sqlConn) {
        
        parent::__construct($sqlConn);
    
        $this->itemClassName = "SearchItem";
        $this->dbTableName = "search_log";
    }
    
    public function removeByHours($hours) {
    
        $this->sqlConn->begin();
        
        $this->sqlConn->execute("DELETE FROM 
                                    search_log 
                                 WHERE 
                                    search_date <= NOW() - INTERVAL %s HOUR",
                                 $hours);
        $this->sqlConn->commit();
    
    }
}