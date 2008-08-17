<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class BaseSecurityOption extends Doctrine_Record
{
	public function setTableDefinition()
	{
		$this->setTableName('security_option');
		
		$this->hasColumn('security_option_tag as tag', 'string', 32, array(
			'fixed'			=>	false,
			'primary'		=>	true,
			'notnull'		=>	true,
			'autoincrement'	=>	false));
		
		$this->hasColumn('security_option_name as name', 'string', 32, array(
			'fixed'			=>	false,
			'primary'		=>	false,
			'notnull'		=>	true,
			'autoincrement'	=>	false));
		
		$this->hasColumn('security_option_value as value', 'string', 12, array(
			'fixed'			=>	false,
			'primary'		=>	false,
			'notnull'		=>	true,
			'autoincrement'	=>	false));
		
		$this->hasColumn('security_option_description as description', 'string', 255, array(
			'fixed'			=>	false,
			'primary'		=>	false,
			'notnull'		=>	false,
			'autoincrement'	=>	false));
	}

	public function setUp()
	{
		parent::setUp();
	}

}