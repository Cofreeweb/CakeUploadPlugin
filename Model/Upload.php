<?php
App::uses('AppModel', 'Model');
/**
 * Upload Model
 *
 */
class Upload extends UploadAppModel 
{
	public $displayField = 'title';
	
	public function beforeDelete()
	{
    // Borra el fichero 
	  $content = $this->read();
	  $paths = UploadUtil::paths( $content);
	  
	  foreach( $paths as $path)
	  {
	    unlink( substr( WWW_ROOT, 0, -1) .$path);
	  }
	  
	  return true;
	}
  
  
}
