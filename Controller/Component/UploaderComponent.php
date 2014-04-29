<?php
/**
 * UploaderComponent
 * 
 *
 * @package upload.controller.component
 **/

App::uses( 'Component', 'Controller');
App::uses( 'UploadUtil', 'Upload.Lib');

class UploaderComponent extends Component 
{

  public $components = array();

  public function startup( Controller $controller) 
  {
    $this->Controller = $controller;
    $this->Upload = ClassRegistry::init( 'Upload.Upload');
  }
  
  public function uploadForMongo()
  {
    // La configuración del upload
    $config = Configure::read( 'Upload.'. $this->Controller->request->query ['key']);
    
    $config ['config']['fields'] = array(
		    'dir' => 'path',
		    'type' => 'mimetype'
		);
		
    // Lee el Behavior Upload.Upload para el model asociado, pasándole los datos indicados en la configuración anteriormente leída
    $this->Upload->Behaviors->load( 'Upload.Upload', array( 
        'filename' => $config ['config'],
    ));
    
    // Los datos a guardar
    $data = $this->Controller->request->params ['form'];
    $data ['content_type'] = $this->Controller->request->query ['key'];
    $data ['model'] = $this->Controller->request->query ['model'];
    
    if( isset( $this->Controller->request->query ['content_id']))
    {
      $data ['content_id'] = $this->Controller->request->query ['content_id'];
    }
    
    if( $this->Upload->save( $data))
    {
      $last = $this->Upload->read( null);
      $last ['Upload']['id'] = new MongoId();
      $last ['Upload']['id'] = $last ['Upload']['id']->__toString(); 
      
      if( isset( $config ['thumbnailSizes']))
      {
        $last ['Upload']['thumbail'] = UploadUtil::thumbailPathMulti( $last);
      }
      
      $this->Controller->set( 'success', true);
      
      $this->Controller->set( 'upload', $last ['Upload']);
      $this->Controller->set( '_serialize', array( 'success', 'upload'));
    }
    else
    {
      $errors = $this->Upload->invalidFields();
      
      if( isset( $errors ['filename']))
      {
        $this->Controller->set( 'error', current( $errors ['filename']));
      }
      else
      {
        $this->Controller->set( 'error', false);
      }

      $this->Controller->set( 'success', false);
      $this->Controller->set( '_serialize', array( 'success', 'error'));
    }
  }
}
?>