<?php

App::uses('UploadUtil', 'Upload.Lib/');
App::uses('UploadAppController', 'Upload.Controller');


class UploadsController extends UploadAppController 
{
  public $name = 'Uploads';
  
  public $uses = array();
  
  public $components = array( 'RequestHandler', 'Upload.CropImage');
  
  public function beforeFilter()
  {
    parent::beforeFilter();
    
    if( isset( $this->Auth))
    {
      $this->Auth->allow( array( 'add', 'multiple'));
    }
  }
  
  public function upload()
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
    
    if( isset( $this->request->query ['content_id']))
    {
      $data ['content_id'] = $this->request->query ['content_id'];
    }

    if( $this->Upload->save( $data))
    {
      $last = $this->Upload->read( null);

      
      $this->set( 'success', true);
      
      App::uses('JsonView', 'View');
      $View = new JsonView($this);
      
      if( isset( $config ['thumbnailSizes']))
      {
        $last ['Upload']['thumbail'] = UploadUtil::thumbailPathMulti( $last);
      }
      
      $this->set( 'upload', $last ['Upload']);
      $this->set( '_serialize', array( 'success', 'upload'));
    }
    else
    {
      $errors = $this->Upload->invalidFields();
      
      if( isset( $errors ['filename']))
      {
        $this->set( 'error', current( $errors ['filename']));
      }
      else
      {
        $this->set( 'error', false);
      }

      $this->set( 'success', false);
      $this->set( '_serialize', array( 'success', 'error'));
    }
  }

  public function admin_crop()
  {
    $this->Components->load( 'Angular.Angular');
    $content = $this->Upload->find( 'first', array(
        'conditions' => array(
            'Upload.id' => $this->request->data ['id']
        )
    ));

    $config = UploadUtil::getConfig( $content);

    $thumbails = array();

    $size = false;

    foreach( $config ['config']['thumbnailSizes'] as $_size => $thm_info)
    {
      if( is_array( $thm_info))
      {
        $thumbails [$_size] = $thm_info;

        if( !$size)
        {
          $size = $_size;
        }
      }
    }

    $configs = array();

    foreach( $thumbails as $_size => $info)
    {
      if( strpos( $info ['size'], 'x'))
      {
        list( $width, $height) = explode( 'x', $info ['size']);
      }
      else
      {
        $width = false;
        $height = false;
      }

      $configs [$_size] = array(
          'id' => $content ['Upload']['id'],
          'path' => $content ['Upload']['path'],
          'width' => $width,
          'height' => $height,
          'size' => $_size
      );
    }

    $current = $configs [$size];
    $_serialize = array( 'content', 'thumbails', 'current', 'configs');
    $this->set( compact( 'content', 'thumbails', 'size', 'current', 'configs', '_serialize'));
  }

  public function admin_proccess()
  {
    $data = $this->request->data ['upload'];

    $upload = $this->Upload->find( 'first', array(
        'conditions' => array(
            'Upload.id' => $data ['id']
        )
    ));
    // La configuración del upload
    $config = Configure::read( 'Upload.'. $upload ['Upload']['content_type']);
    $config ['config']['fields'] = array(
        'dir' => 'path',
        'type' => 'mimetype'
    );

    $this->Upload->Behaviors->load( 'Upload.Upload', array( 
        'filename' => $config ['config'],
    ));

    $file = $this->CropImage->crop( $upload, $data ['cropper']);
    $config = UploadUtil::getConfig( $upload);

    $this->Upload->data = $upload;
    $geometry = $config ['config']['thumbnailSizes'][$data ['size']]['size'];

    $pathinfo = pathinfo( $file);
    $this->Upload->resizeThmPhp( $file, 'filename', $pathinfo ['dirname'] .DS, $data ['size'], $geometry, $pathinfo ['dirname'] .DS, $upload ['Upload']['filename']);
    unlink( $file);
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
      $this->set( 'errors', array( $field => $this->request->form [$field]));
      $this->set( 'testing', 'test');
      $this->set( '_serialize', array( 'success', 'errors', 'testing'));
    }
  }
  
  public function delete()
  {  
    $model = $this->request->params ['model'];
    
    $upload = $this->Upload->find( 'first', array(
        'conditions' => array(
            'Upload.model' => $this->request->params ['model'],
            'Upload.filename' => $this->request->params ['filename'],
            'Upload.id' => $this->request->params ['id']
        )
    ));
    
    if( $upload)
    {
      $this->Upload->delete( $this->request->params ['id']);
      $this->set( 'success', true);
    }
    else
    {
      $this->set( 'success', false);
    }
    
    $this->set( '_serialize', array( 'success'));
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
    
    if( isset( $this->request->query ['content_id']))
    {
      $data ['content_id'] = $this->request->query ['content_id'];
    }
    // $data ['filesize'] = $data ['filename']['size'];

    if( $this->Upload->save( $data))
    {
      $this->set( 'success', true);
      $last = $this->Upload->read( null);
      App::uses('JsonView', 'View');
      $View = new JsonView($this);
            
      if( isset( $this->request->params ['admin']))
      {
        $body = $View->element( 'json/'. $config ['type'], array(
            'upload' => $last ['Upload'],
            'alias' => $this->request->query ['alias']
        ));
      }
      else
      {
        $body = $View->element( 'uploads/json/'. $config ['template'], array(
            'upload' => $last ['Upload'],
            'alias' => $this->request->query ['alias']
        ));
      }
      
      
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
      $errors = $this->Upload->invalidFields();
      
      if( isset( $errors ['filename']))
      {
        $this->set( 'error', current( $errors ['filename']));
      }
      else
      {
        $this->set( 'error', false);
      }

      $this->set( 'success', false);
      $this->set( '_serialize', array( 'success', 'error'));
    }    
  }
  
  public function admin_multiple()
  {
    $this->multiple();
  }
  
  public function setorder()
  {
    $this->autoRender = false;
    $this->loadModel( 'Upload.Upload');
    $els = $this->request->query ['upload'];
    
    foreach( $els as $order => $id)
    {
      $this->Upload->create();
      $this->Upload->id = $id;
      $this->Upload->saveField( 'order', $order + 1);
    }
  }
}
?>