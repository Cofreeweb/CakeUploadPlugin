<?php
App::uses('AppModel', 'Model');
App::uses( 'UploadUtil', 'Upload.Lib');

/**
 * Upload Model
 *
 */
class Upload extends UploadAppModel 
{
	public $displayField = 'title';
	
	
	public function afterFind( $results)
	{
	  if( isset( $results [0]))
	  {
	    foreach( $results as $key => $result)
	    {
	      $paths = UploadUtil::paths( $result);
	      $results [$key][$this->alias]['paths'] = $paths;
	    }
	  }
	  return $results;
	}
	
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
