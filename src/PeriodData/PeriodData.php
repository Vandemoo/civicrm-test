<?php
namespace Ademola\PeriodData;

use Ademola\PeriodData\PeriodDataInterface;
use Ademola\PeriodData\PeriodDataException;

class PeriodData implements PeriodDataInterface
{
    private $operation;
    
    private $startDate;
    
    private $endDate;
    
    private $membershipId;
    
    public function __construct(string $operation, &$objectRef) {
            $this->membershipId = $objectRef->id;
            $this->startDate = $objectRef->start_date;
            $this->endDate = $objectRef->end_date;
            $this->operation = $operation;
    }
    
    public function recordPeriod() {
        switch ($this->operation){
            case 'create':
            case 'edit':
                $this->saveData();
                break;
        }
    }
    
    public function performEntityCheck() {
        try {
            $this->hasNotExistingTable();
        } catch (PeriodDataException $exception){
            $this->createTable();
        }
    }
    
    public function hasNotExistingTable() {
        $result = CRM_CORE_DAO::executeQuery("SELECT 1 FROM extended_membership_data LIMIT 1");
        if(!$result){
            throw new PeriodDataException('A database Table has not been 
            setup for Additional data');
        }
    }
    
    public function saveData() {
        $this->performEntityCheck();
        $query = "INSERT INTO table_name (membership_id, start_date, end_date ) VALUES (%1, %2, %3)";
        $result = CRM_CORE_DAO::executeQuery($query, 
                                    array(
                                        1=> array($this->membershipId, "Integer"),
                                        2=> array($this->startDate),
                                        3=> array($this->endDate,)
                                        )
                                    );
        if(!$result){
            throw new PeriodDataException('Error saving extended membership details');
        }
    }
    
    public function createTable() {
        $initQuery = "CREATE TABLE extended_membership_data ( id INT(10) AUTO_INCREMENT NOT NULL, membership_id INT(10) UNSIGNED NOT NULL,
            start_date  DATETIME , end_date  DATETIME, contribution_id INT(10) UNSIGNED, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
            ALTER TABLE extended_membership_data
            ADD CONSTRAINT FK_TO_MEMBERSHIP FOREIGN KEY (membership_id) REFERENCES
            civicrm_membership (id),
            ADD CONSTRAINT FK_TO_CONTRIBUTION FOREIGN KEY (contribution_id) REFERENCES
            civicrm_contribution (id)";
        $resultConf = CRM_Core_DAO::executeQuery($initQuery);
        if(!$resultConf){
            throw new PeriodDataException('An error occured with the creation of SQL tables');
        }
    }
}