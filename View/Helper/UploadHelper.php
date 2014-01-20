<?php
/**
 * @link https://github.com/Widen/fine-uploader/tree/master/docs
 *
 * @package plugin.upload.view.helper
 */

class UploadHelper extends AppHelper 
{
  public $helpers = array( 'Html', 'Form', 'Js');
  
  protected $_models = array();
  
/**
 * Guess the location for a model based on its name and tries to create a new instance
 * or get an already created instance of the model
 *
 * @param string $model
 * @return Model model instance
 */
	protected function _getModel($model) {
		$object = null;
		if (!$model || $model === 'Model') {
			return $object;
		}

		if (array_key_exists($model, $this->_models)) {
			return $this->_models[$model];
		}

		if (ClassRegistry::isKeySet($model)) {
			$object = ClassRegistry::getObject($model);
		} elseif (isset($this->request->params['models'][$model])) {
			$plugin = $this->request->params['models'][$model]['plugin'];
			$plugin .= ($plugin) ? '.' : null;
			$object = ClassRegistry::init(array(
				'class' => $plugin . $this->request->params['models'][$model]['className'],
				'alias' => $model
			));
		} elseif (ClassRegistry::isKeySet($this->defaultModel)) {
			$defaultObject = ClassRegistry::getObject($this->defaultModel);
			if (in_array($model, array_keys($defaultObject->getAssociated()), true) && isset($defaultObject->{$model})) {
				$object = $defaultObject->{$model};
			}
		} else {
			$object = ClassRegistry::init($model, true);
		}

		$this->_models[$model] = $object;
		if (!$object) {
			return null;
		}

		$this->fieldset[$model] = array('fields' => null, 'key' => $object->primaryKey, 'validates' => null);
		return $object;
	}
	
  
  private function __loadScripts()
  {
    $this->Html->script( array(        
        '/upload/js/all.fineuploader-3.9.1.min',
        '/upload/js/jquery.tipsy.min',
        '/upload/js/upload',
    ), array(
        'inline' => false,
        'once' => true
    ));
    
    $this->Html->css(array(
        '/upload/css/upload'
    ), null, array(
        'inline' => false
    ));
  }
 
/**
 * Devuelve un id random
 * 
 * Últil para hacer ids aleatorios que sean utilizados en jQuery
 * 
 * ´$id = Helper::randId( 'objeto');
 * ´<div id="<?= $id ?>">haz click</div>´
 * ´$("#<?= $id ?>").click(function(){})´
 *
 * @param string $prefix Un prefijo para el id
 * @return string
 */
  public function randId( $prefix = '')
	{
	  if( $prefix)
	    $prefix = $prefix .'_';

	  return $prefix . intval(mt_rand());
	}
	  
  public function setOptions( $options = array())
  {
    $_options = array(
        'inputName' => 'filename',
        'model' => null,
        'id' => false,
        'class' => false,
        'buttonLabel' => __("Subir"),
        'div'=> false,
        'limit' => 0,
        'container' => null,
        'params' => array(),
        'accept' => "image/*",
        'idButtonDelete' => 'button-delete-'. intval(mt_rand()),
    );
    
    $options = array_merge( $_options, $options);
    
    if( $options ['container'])
    {
      $options['params']['update'] = $options ['container'];
    }
    
    $this->options = $options;
  }
  
  public function input( $options = array())
  {
    $this->__loadScripts();
    
    $options = $this->options;
    
    $options ['url'] = $this->Html->url( array(
        'manager' => false,
        'admin' => false,
        'plugin' => 'upload',
        'controller' => 'uploads',
        'action' => 'add',
        'model' => $options ['model'],
        'field' => $options ['inputName'],
        'id' => $options ['id'],
        'ext' => 'json'
    ));
    
    if( empty( $options ['element']))
    {
      $options ['element'] = $this->options ['element'] = $this->randId( 'upload');
    }
    
    $script = '
      $(function(){
        new qq.FineUploader({
            element: $("#'. $options ['element'] .'")[0],
            debug: false,
            request: {
                endpoint: "'. $options ['url'] .'",
                inputName: "'. $options ['inputName'] .'",
                params: '. json_encode( $options ['params']) .'
            },
            multiple: false,
            text: {
                uploadButton: "'. $options ['buttonLabel'] .'"
            },
            callbacks: {
                onComplete: function( id, name, response){
                    $("li", el).remove();
                    var el = $("#'. $options ['element'] .'");
                    $(".qq-response", el).remove();
                    $(".qq-drop-processing", el).remove();
                    $(".qq-upload-file", el).remove();
                    $(".qq-upload-size", el).remove();
                    $(response.update).html($("<img>").attr( "src", response.image_path));
                    $("#'. $options ['idButtonDelete'] .'").show();
                }
            },
            validation: {
              itemLimit: "'. $options ['limit'] .'",
              acceptFiles: "'. $options ['accept'] .'"
            },
            retry: {
                showButton: false
            },
            fileTemplate: "<li>" +
                "<div class=\"qq-response\"></div>" +
                "<div class=\"qq-progress-bar\"></div>" +
                "<span class=\"qq-upload-spinner\"></span>" +
                "<span class=\"qq-upload-finished\"></span>" +
                "<span class=\"qq-upload-file\"></span>" +
                "<span class=\"qq-upload-size\"></span>" +
                "<a class=\"qq-upload-cancel\" href=\"#\">{cancelButtonText}</a>" +
                "<a class=\"qq-upload-retry\" href=\"#\">{retryButtonText}</a>" +
                "<a class=\"qq-upload-delete\" href=\"#\">{deleteButtonText}</a>" +
                "<span class=\"qq-upload-status-text\">{statusText}</span>" +
                "</li>"
        });
      })
    ';
    
    $this->Js->buffer( $script);
    
    return $this->Html->tag( 'ul', '', array(
        'class' => $options ['class'],
        'id' => $options ['element']
    ));
  }
  
  public function buttonDelete( $image = false)
  {
    $options = $this->options;
    
    $options ['url'] = $this->Html->url( array(
        'manager' => false,
        'admin' => false,
        'plugin' => 'upload',
        'controller' => 'uploads',
        'action' => 'delete',
        'model' => $options ['model'],
        'field' => $options ['inputName'],
        'id' => $options ['id'],
        'ext' => 'json'
    ));
    
    $id = $options ['idButtonDelete'];
    $out =  $this->Html->link( __( "Delete"), '#', array(
        'id' => $id,
        'class'=>'img-delete icon-image16 icon-close',
        'style' => empty( $image) ? 'display: none' : false
    ));
    
    $script = '$("#'. $id .'").click(function(){
        var _this = this;
        $.ajax({
          type: "post",
          url: "'. $options ['url'] .'",
          data: '. json_encode( $options ['params']) .',
          success: function( data) {
            $("'. $options ['container'] .'").html( "");
            if( data.image_path == "") {
              $(_this).hide();
            }
            $(data.update).html($("<img>").attr( "src", data.image_path));
          }
        });
        return false;
    })';
    
    $this->Js->buffer( $script);
    
    return $out;
  }
  
  public function addUploadToFineupload( $options)
  {
    if( !empty( $this->addToUploadScript))
    {
      for ($i = 0; $i < $this->addToUploadScript; $i++) 
      { 
        $script [] = '$("#'. $options ['element'] .'").data( "fineuploader").uploader._netUploadedOrQueued++';
        $script [] = '$("#'. $options ['element'] .'").data( "fineuploader").uploader._netUploaded++';
      }
      
      $this->Html->scriptBlock( '$(function(){'. implode( "\n", $script) .'})', array(
          'inline' => false
      ));
    }
  } 
   
  public function collection( $options)
  {
    $model = $this->_getModel( $options ['model']);
    $associateds = $model->getAssociated();
    $association = $associateds [$options ['alias']];    
    $method = 'get'. ucfirst( $association);
    return $this->$method( $options);
  }
  
  private function getHasOne( $options)
  {
    if( !isset( $this->request->data [$options ['alias']]))
    {
      return null;
    }
    
    $data = $this->request->data [$options ['alias']];
    
    if( !empty( $data ['id']))
    {
      return $this->elementUpload( $options, $data);
    }
    
    return null;
  }
  
  private function getHasMany( $options)
  {
    if( !isset( $this->request->data [$options ['alias']]))
    {
      return null;
    }
    
    $data = $this->request->data [$options ['alias']];
    $out = array();
    
    foreach( $data as $content)
    {
      if( !empty( $content ['id']))
      {
        $out [] = $this->elementUpload( $options, $content);
      }
    }
    
    if( !empty( $out))
    {
      return implode( "\n", $out);
    }
    
    return null;
  }
  
  public function elementUpload( $options, $data)
  {
    $config = Configure::read( 'Upload.'. $options ['key']);
    $this->addToUploadScript++;
    if( isset( $this->request->params ['admin']) || $this->request->controller == 'crud')
    {
      $template = 'Upload.json/'. $config ['type'];
    }
    else
    {
      $template = 'uploads/json/'. $config ['template'];
    }
    
    return $this->_View->element( $template, array(
        'upload' => $data,
        'alias' => $options ['alias']
    ));
  }
  
  public function multiple( $options = array())
  {
    $this->__loadScripts();
    $this->addToUploadScript = 0;
    $_options = array(
        'key' => null,
        'model' => null,
        'inputName' => 'filename',
        'url' => null,
        'class' => '',
        'buttonLabel' => __( 'Subir archivo'),
        'limit' => 0,
        'collection' => true
    );
    
    $options = array_merge( $_options, $options);
    
    if( $options ['collection'])
    {
      $collection = $this->collection( $options);

      $collection = str_replace( '"', '\"', $collection);
      $collection = str_replace( "\n", '', $collection);
    }
    else
    {
      $collection = '';
    }
    
    
    if( empty( $options ['url']))
    {
      $options ['url'] = $this->Html->url( array(
          'admin' => isset( $this->request->params ['admin']) || $this->request->controller == 'crud',
          'manager' => false,
          'plugin' => 'upload',
          'controller' => 'uploads',
          'action' => 'multiple',
          'ext' => 'json'
      )) . '?key=' . $options ['key'] . '&model=' . $options ['model'] . '&alias=' . $options ['alias'];
    }
    
    if( empty( $options ['element']))
    {
      $options ['element'] = $this->randId( 'upload');
    }
    
    $script = '
      $(function(){
        var _this = $("#'. $options ['element'] .'");
        _this.fineUploader({
           debug: false,
            request: {
                endpoint: "'. $options ['url'] .'",
                inputName: "'. $options ['inputName'] .'"
            },
            text: {
                uploadButton: "<div><i class=\"upload-icon icon-cloud-upload white icon\"></i> '. $options ['buttonLabel'] .'</div>"
            },
            callbacks: {
                onComplete: function( id, name, response, xhr){
                    $(".ace-thumbnails", _this).append( response.body);
                    $(".qq-upload-success", _this).remove();
                    var uploader = _this.data( "fineuploader").uploader;
                    if( uploader._netUploaded >= uploader._options.validation.itemLimit) {
                      $(".qq-upload-button", _this).hide();
                    }
                    if( !response.success && response.error){
                      var item = uploader.getItemByFileId(id);
                      qq(uploader._find(item, "statusText")).setText( "<span class=\"qq-upload-retry\">" + response.error + "</span>");
                    }
                }
            },
            validation: {
              itemLimit: "'. $options ['limit'] .'"
            },
            retry: {
                showButton: false
            },
            template: "<div class=\"qq-uploader btn-group\"><div class=\"qq-upload-drop-area\"><span>{dragZoneText}</span></div>" +
                "<div class=\"qq-upload-button btn btn-xs btn-success \">{uploadButtonText}</div>" + 
                "<span class=\"qq-drop-processing\"><span>{dropProcessingText}</span><span class=\"qq-drop-processing-spinner\"></span></span>" +
                "<div class=\"col-xs-12\"><div class=\"row-fluid\"><ul class=\"ace-thumbnails\">'. $collection .'</ul></div></div>" +
                "</div>",
            classes: {
                buttonHover: false,
                buttonFocus: "qq-upload-button-focus",
                list: "ace-thumbnails",
            },
            deleteFile: {
                enabled: true
            },
            camera: {
              ios: true
            },
            fileTemplate: "<li>" +
                "<div class=\"qq-progress-bar\"></div>" +
                "<span class=\"qq-upload-spinner\"></span>" +
                "<span class=\"qq-upload-finished\"></span>" +
                "<span class=\"qq-upload-image\"></span>" +
                "<span class=\"qq-upload-file\"></span>" +
                "<span class=\"qq-upload-size\"></span>" +
                "<a class=\"qq-upload-cancel\" href=\"#\">{cancelButtonText}</a>" +
                "<a class=\"qq-upload-retry\" href=\"#\">{retryButtonText}</a>" +
                "<a class=\"qq-upload-delete\" href=\"#\">{deleteButtonText}</a>" +
                "<span class=\"qq-upload-status-text\">{statusText}</span>" +
                "</li>"
        });
        _this.find( "ul").sortable({
          update: function( event, ui) {
            var els = ui.item.parent().sortable( "serialize");
            $.ajax({
              url: "'. $this->Html->url( array(
                  'plugin' => 'upload',
                  'controller' => 'uploads',
                  'action' => 'setorder'
              )) .'",
              data: els
            })
          }
        });
        // $(_this).data( "fineuploader").uploader._options.validation.itemLimit = 1;
      })
    ';
    
    $this->Html->scriptBlock( $script, array(
        'inline' => false
    ));
    
    if( $options ['collection'])
    {
      $this->addUploadToFineupload( $options);
    }
    
    if( !empty( $options ['label']))
    {
      $out [] = '<h4 class="row header smaller lighter green"><i class="icon-camera"></i> '. $options ['label'] .'</h4>';
      
    }
    
    $out [] = $this->Html->tag( 'div', '', array(
        'class' => 'fineupload-container ' . $options ['class'],
        'id' => $options ['element']
    ));
    
    return implode( "\n", $out);
  }

}