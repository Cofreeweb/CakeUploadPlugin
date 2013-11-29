<?php  
App::uses('UploadUtil', 'Upload.Lib/');


class AssetHelper extends AppHelper 
{ 
  public $helpers = array( 'Form', 'Html'); 

  public function image( $data, $options = array(), $attributes = array())
  {
    $path = UploadUtil::imagePath( $data, $options);
    $attributes ['src'] = Configure::read('path.static') . $path;
    return $this->Html->tag( 'img', null, $attributes);
  }
  
  function imageSrc( $data, $options = array())
  {
    $_options = array(
        'size' => 'thm',
        'fields' => array(
            'dir' => 'dir',
            'filename' => 'filename'
        )
    );
    
    $options = array_merge( $_options, $options);
    
    if( !empty( $options ['size']))
    {
      $size = $options ['size'] .'_';
    }
    else
    {
      $size = '';
    }
    
    $path = Configure::read( 'Path.files.photos') . $data [$options ['fields']['dir']] .'/'. $size. $data [$options ['fields']['filename']];
    return $path;
  }
  
  public function imageHeaderCSS( $data)
  {
    $image = $this->imageSrc( $data, array(
        'size' => 'thm',
        'fields' => array(
            'dir' => 'header_image_dir',
            'filename' => 'header_image'
        )
    ));
    
    $filepath = Configure::read('App.www_root'). $image;
    
    if( file_exists( $filepath))
    {
      list( $width, $height) = getimagesize( $filepath);
      
      if( $height < 50)
      {
        $height = 50;
      }
      
      $out = array(
          'background-image: url('. Configure::read('path.static') . $image .')',
          'background-repeat: no-repeat',
          'padding-left: '. ($width + 20) . 'px',
          'height: '. $height . 'px',
          'display: block',
      );
      
      return implode( ';', $out);
    }
    
    return null;
  }
  
  public function siteLogoClass( $image)
  {
    if( strpos( $image, 'http') !== false)
    {
      $url = parse_url( $image);
      $image = $url ['path'];
    }
    
    $filepath = Configure::read('App.www_root'). $image;

    list( $width, $height) = getimagesize( $filepath);
    
    if( $width > $height)
    {
      return 'horizontal';
    }
    else
    {
      return 'vertical';
    }
  }
  
  public function siteLogoStyle( $image, $size)
  {
    if( strpos( $image, 'http') !== false)
    {
      $url = parse_url( $image);
      $image = $url ['path'];
    }
    
    $filepath = Configure::read('App.www_root'). $image;
    
    list( $width, $height) = getimagesize( $filepath);
    
    if( $width > $height)
    {
      return 'height: '. round( ($height * $size) / $width) .'px';
    }
    else
    {
      return 'width: '. round( ($width * $size) / $height) .'px';
    }
  }
} 
