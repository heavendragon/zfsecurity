<?php

class Security_InstallController extends Security_Controller_Action_Backend
{
    protected $_parts = array();
    
	public function indexAction()
	{
	   if (Security_System::getInstance()->isInstalled()) {
	       $this->_redirect('/security');
	   }
	   
	   $form = $this->_getForm();
	   
	   if ($this->getRequest()->isPost()) {
	       
	       if ($this->getRequest()->getPost('submit') != 'Begin') {
	       
	       } else {
	       
	           $subForm = "stepOne";
	       }
	   } else {
	   
	       $subForm = "intro";
	   }
	   
	   $this->view->form = $form->getSubForm($subForm);
	}
	
	public function justDoItAction()
	{
	    if (Security_System::getInstance()->isInstalled()) {
	        $this->_forward('index');
	        return;
	    }
	    
	    $exporter = new Doctrine_Export();
	    $tables = Security_System::getInstance()->getLoadedModels();
	    
	    if ($queries = $exporter->exportSortedClassesSql($tables, false)) {
	        
	        $conn = Doctrine_Manager::connection()->getDbh();
	        
	        try {
	            
	            foreach ($queries as $query) {
	                
	                $conn->exec($query);
	            }
	        } catch (Exception $e) {
	            var_dump($e);
	            return;
	        }
	        
	    }
	    
	    $conn->exec("INSERT INTO `security_option` VALUES ('acl_enabled', 'ACL System', '1', 'Enables/Disables ACL')");
        $conn->exec("INSERT INTO `security_option` VALUES ('installed', 'Installed', '1', 'Wether or not Security System has been installed')");
        $conn->exec("INSERT INTO `security_option` VALUES ('system_enabled', 'Security System', '0', 'Enables/Disables the entire system.  This overrides all other enabled values.')");
        $conn->exec("INSERT INTO `security_option` VALUES ('use_security_error_controller', 'Security Error Controller', '1', 'Enables/Disables the use of the Security module''s error controller for security restrictions.')");

	    $this->_forward('index');
	}
	
	protected function _generateAcl()
	{
	    $gen = new Security_Acl_Generator();
		foreach ($gen->getResources() as $module => $resources) {
		    foreach ($resources as $resource) {
		        foreach ($gen->getActions($resource) as $action) {
		            echo $module ." ". $resource ." ". $action ."<br>";
	            }
		    }
		}
	}
	
	public function updateAclAction()
	{
	    $gen = new Security_Acl_Generator();
	    
	    if (!$this->getRequest()->isPost()) {
	        
	        $modules = array();
	        
	        foreach ($gen->getResources() as $genModule => $genResources) {
	            
	            foreach ($genResources as $genResource) {
    	            
    	            foreach ($gen->getActions($genResource) as $genAction) {
    	                
    	                if (!$this->_aclExists($genModule, $genResource, $genAction)) {

        	                $modules[$genModule]['resources'][$genResource]['privileges'][$genAction]['new'] = true;
        	            }
    	            }
	            }
	        }
	        
	        if (!empty($modules)) {
	            $this->view->acl = $modules;
	        }
        } else {
            
            $parts = Doctrine_Query::create()
                                     ->select('ap.name')
                                     ->from('AclPart ap INDEXBY ap.name')
                                     ->execute()
                                     ->toArray();
	        
	        foreach ($gen->getResources() as $genModule => $genResources) {
	            
	            $module = $this->_addPart($genModule);
	            
	            foreach ($genResources as $genResource) {
	                
	                $resource = $this->_addPart($genResource);
    	            
    	            foreach ($gen->getActions($genResource) as $genAction) {
    	                
    	                $privilege = $this->_addPart($genAction);
        	            
        	            if (!$this->_aclExists($module->name, $resource->name, $privilege->name)) {
        	               
        	               $acl = new Acl();
        	               $acl->module_id = $module->id;
        	               $acl->resource_id = $resource->id;
        	               $acl->privilege_id = $privilege->id;
        	               $acl->save();
        	            }
    	            }
	            }
	        }
	    }
	}
	
	protected function _addPart($name)
	{
	    if (!isset($this->_parts[$name])) {
	        if (!$aclPart = Doctrine::getTable('AclPart')->findOneByName($name)) {
	            $aclPart = new AclPart();
	            $aclPart->name = $name;
	            $aclPart->save();
	        }
	        $this->_parts[$name] = $aclPart;
        }
	    return $this->_parts[$name];
	}
	
	protected function _aclExists($module, $resource, $privilege)
	{
	    if (!Security_System::getInstance()->isInstalled()) {
	       return false;
	    }
	    // This could be time tested against looping through Security_Acl::getInstance->getAcl()
	    return (Doctrine_Query::create()
	                            ->from('Acl a')
	                            ->innerJoin('a.Module m')
  	                            ->innerJoin('a.Resource r')
  	                            ->innerJoin('a.Privilege p')
  	                            ->addWhere('m.name = ?')
  	                            ->addWhere('r.name = ?')
  	                            ->addWhere('p.name = ?')
	                            ->fetchOne(array($module, $resource, $privilege))) ? true : false;
	}
	
	protected function _generateForm()
	{
	   return new Security_Form_Install();
	}
}