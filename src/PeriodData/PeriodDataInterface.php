<?php

namespace Ademola\PeriodData;

interface PeriodDataInterface
{
    private $operation;
    
    private $startDate;
    
    private $endDate;
    
    private $membershipId;
    
    
    public function recordPeriod();
    
    public function hasNotExistingTable();
    
    public function performEntityCheck();
    
    public function saveData();
    
    public function createTable();
}