<?php

abstract class Security_User
{
    protected static $_instance = null;

    protected $_records = array();

    protected $_tableName = "User";

    private function __clone()
    {}

    private function __construct()
    {
        
    }

    protected function _initialize()
    {
        if (($auth = Zend_Auth::getInstance()->getIdentity()) && isset($auth->{$this->getIdentityColumn()})) {
            
            if ($record = Doctrine::getTable($this->_tableName)->find($auth->{$this->getIdentityColumn()})) {}
            
            if ($record !== false) {
                
                foreach ($record as $key => $value) {
                
                    $this->_setVar($key, $value);
                }
                
                // @TODO remove this
                $this->_setVar('acl_role_id', 1);
                
                $this->_setRecord($this->_tableName, $record);
            }
        } else {
            
            $this->_setVar('acl_role_id', null);
        }
    }
    
    public function isLoggedIn()
    {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            return true;
        }
        //$role = Security_System::getInstance()->getRole();
        
        //if ($role === null || !isset($role->name) || strtolower($role->name) == 'anonymous') {
        //    return false;
        //}
        //return true;
        return false;
    }
    
    /**
     * Undocumented function.
     *
     * @todo document me
     * @return unknown
     * @author jaeisenmenger@edrivemedia.com
     **/
    public function getVar($varName)
    {
        if (isset($this->_vars[$varName]))
        {
            return $this->_vars[$varName];
        }
        return null;
    }
    
    /**
     * Undocumented function.
     *
     * @todo document me
     * @return unknown
     * @author jaeisenmenger@edrivemedia.com
     **/
    public function getRecord($rowName)
    {
        if (isset($this->_records[$rowName])) {
            return $this->_records[$rowName];
        }
        return null;
    }
    
    /**
     * Undocumented function.
     *
     * @todo document me
     * @return unknown
     * @author jaeisenmenger@edrivemedia.com
     **/
    protected function _setVar($varName, $varValue)
    {
        $this->_vars[$varName] = $varValue;
    }
    
    protected function _setRecord($recordName, $record)
    {
        $this->_records[$recordName] = $record;
    }
    
    public function getTableName()
    {
        return $this->_tableName;
    }
    
    public function getIdentityColumn()
    {
        return Doctrine::getTable($this->_tableName)->getIdentifier();
    }
    
    //final public function __set()
    //{
    //    
    //}
    //
    //final public function __get()
    //{
    //    
    //}
    //
    //final public function __isset()
    //{
    //    
    //}
    //
    //final public function __unset()
    //{
    //    
    //}
}