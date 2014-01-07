<?php
App::uses('UploadUtil', 'Upload.Lib/');

class UploadableBehavior extends ModelBehavior 
{

	public function afterSave( Model $model, $created) 
	{
	  foreach( $model->hasOne as $alias => $info)
	  {
	    if( $info ['className'] == 'Upload.Upload')
	    {
    		$this->saveRelated( $model, $alias, $info);
	    }
	  }
	  
	  foreach( $model->hasMany as $alias => $info)
	  {
	    if( $info ['className'] == 'Upload.Upload')
	    {
    		$this->saveRelated( $model, $alias, $info);
	    }
	  }
	  
		return true;
	}
	
	
	public function saveRelated( Model $model, $alias, $info)
	{
	  $olds = $model->$alias->find( 'list', array(
	      'conditions' => array(
	          'content_id' => $model->id,
	          'model' => $model->alias,
	          'content_type' => $info ['conditions'][$alias .'.content_type']
	      ),
	      'fields' => array(
	          'id'
	      )
	  ));
	  
	  $saves = array();
	  	  	  
	  if( !empty( $model->data [$alias]))
	  {
	    foreach( $model->data [$alias] as $data)
	    {
  	    if( isset( $data [$alias]))
  	    {
  	      $data = $data [$alias];
  	    }
  	    
	      $data ['content_id'] = $model->id;
	      $model->$alias->id =  $data ['id'];
	      $model->$alias->save( $data);
	      $saves [] = $data ['id'];
	    }
	  }
	  
	  
	  $deleteds = array_diff( $olds, $saves);

	  if( !empty( $deleteds))
	  {
	    foreach( $deleteds as $id)
  	  {
  	    $model->$alias->delete( $id);
  	  }
	  }
	  
	}
}