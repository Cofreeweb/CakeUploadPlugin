<?php 
class UploadSchema extends CakeSchema {

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}
  
 
	var $uploads = array(
		'id' => array(
		    'type' => 'integer', 
		    'null' => false, 
		    'default' => NULL, 
		    'length' => 10, 
		    'key' => 'primary'
		),
		'locale' => array(
		    'type' => 'string', 
		    'null' => true, 
		    'default' => NULL, 
		    'length' => 3
		),
		'content_id' => array('type' => 'string', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'content_type' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50, 'key' => 'index'),
		'model' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50, 'key' => 'index'),
		'filename' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'path' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'original_filename' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'filesize' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 6),
		'extension' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 10),
		'mimetype' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'duration' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 20),
		'title' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'subtitle' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'text' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'main' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'special' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'order' => array('type' => 'integer', 'null' => true, 'length' => 5),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array(
		    'PRIMARY' => array(
		        'column' => 'id', 
		        'unique' => 1
		    ), 
		    'content_id' => array(
		        'column' => 'content_id', 
		        'unique' => 0
		    ),
		    'content_type' => array(
		        'column' => 'content_type', 
		        'unique' => 0
		    ),
		    'model' => array(
		        'column' => 'model', 
		        'unique' => 0
		    ),
		)
	);
}
