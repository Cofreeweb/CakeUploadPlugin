<?php

App::uses('UploadUtil', 'Upload.Lib/');
App::uses('UploadAppController', 'Upload.Controller');


class UploadsController extends UploadAppController 
{
  public $name = 'Uploads';
  
  public $uses = array();
  
  public $components = array( 'RequestHandler');
  
  public function beforeFilter()
  {
    parent::beforeFilter();
    
    if( isset( $this->Auth))
    {
      $this->Auth->allow( array( 'add', 'multiple'));
    }
  }
  
  public function add()
  {
    $model = $model_plugin = $this->request->params ['model'];
    $id = isset( $this->request->params ['id']) ? $this->request->params ['id'] : null;
    $field = $this->request->params ['field'];
    
    if( strpos( $model, '.') !== false) 
    {
      list( $plugin, $model) = explode( '.', $model);
      ClassRegistry::removeObject( $model);
      App::import( 'Model', $plugin .'.'. $model);
      $model_plugin = $plugin .'.'. $model;
    }
    
    $this->loadModel( $model_plugin);
    
    $this->$model->id = $id;
    
    $dir = $this->$model->actsAs ['Upload.Upload'][$field]['fields']['dir'];
    
    if( $id && $this->$model->save( array( $field => $this->request->form [$field]), false))
    {
      $this->set( 'success', true);
      $this->set( 'update', $this->request->data ['update']);
      $content = $this->$model->read( null);
      $this->set( 'image_path', UploadUtil::imagePath( $content [$this->$model->alias], array(
          'size' => 'thm',
          'fields' => array(
              'dir' => $dir,
              'filename' => $field
          ),
          'model' => $model
      )));
      
      $this->set( '_serialize', array( 'success', 'update', 'image_path'));
    }
    else
    {
      // $this->set( 'errors', $this->validateErrors());
      $this->set( 'errors', array( $field => $this->request->form [$field]));
      $this->set( 'success', false);
      $this->set( '_serialize', array( 'success', 'errors'));
    }
  }
  
  public function delete()
  {
    $model = $this->request->params ['model'];
    $id = $this->request->params ['id'];
    $field = $this->request->params ['field'];
    
    if( strpos( $model, '.') !== false) 
    {
      list( $plugin, $model) = explode( '.', $model);
      ClassRegistry::removeObject( $model);
      App::import( 'Model', $plugin .'.'. $model);
    }
    
    $this->loadModel( $model);
    $this->$model->id = $id;
    $dir = $this->$model->actsAs ['Upload.Upload'][$field]['fields']['dir'];

    if( isset( $this->$model->actsAs ['Upload.Upload'][$field]['beforeDelete']))
    {
      $method = $this->$model->actsAs ['Upload.Upload'][$field]['beforeDelete'];
      $this->$model->$method( $id);
      $this->set( 'success', true);
    }
    else
    {
      if( $this->$model->save( array( $field => null), false))
      {
        $this->set( 'success', true);
      }
      else
      {
        $this->set( 'success', false);
      }
    }
    
    $content = $this->$model->read( null);
    
    if( !empty( $content [$this->$model->alias][$field]))
    {
      $this->set( 'image_path', UploadUtil::imagePath( $content [$this->$model->alias], array(
          'size' => 'thm',
          'fields' => array(
              'dir' => $dir,
              'filename' => $field
          )
      )));
    }
    else
    {
      $this->set( 'image_path', '');
    }
    
    
    $this->set( 'update', $this->request->data ['update']);
    
    $this->set( '_serialize', array( 'success', 'image_path', 'update'));
  }
  
  
/**
 * Recoge los datos de los uploads que están relacionados con un model por hasMany o por HABTM
 *
 * @return void
 */
  public function multiple()
  {
    // La configuración del upload
    $config = Configure::read( 'Upload.'. $this->request->query ['key']);
    $config ['config']['fields'] = array(
		    'dir' => 'path',
		    'type' => 'mimetype'
		);
		
    // Lee el Behavior Upload.Upload para el model asociado, pasándole los datos indicados en la configuración anteriormente leída
    $this->Upload->Behaviors->load( 'Upload.Upload', array( 
        'filename' => $config ['config'],
    ));
    
    // Los datos a guardar
    $data = $this->request->params ['form'];
    $data ['content_type'] = $this->request->query ['key'];
    $data ['model'] = $this->request->query ['model'];
    $data ['filesize'] = $this->request->data ['qqtotalfilesize'];
    
    if( $this->Upload->save( $data))
    {
      $this->set( 'success', true);
      $last = $this->Upload->read( null);
      App::uses('JsonView', 'View');
      $View = new JsonView($this);
      $body = $View->element( 'uploads/json/'. $config ['template'], array(
          'upload' => $last ['Upload'],
          'alias' => $this->request->query ['alias']
      ));
      
      if( isset( $config ['thumbnailSizes']))
      {
        $last ['Upload']['thumbail'] = UploadUtil::thumbailPathMulti( $last);
      }
      
      $this->set( 'upload', $last ['Upload']);
      $this->set( compact( 'body'));
      $this->set( '_serialize', array( 'success', 'upload', 'body'));
    }
    else
    {
      $this->set( 'success', false);
      $this->set( '_serialize', array( 'success'));
    }    
  }
}
?>