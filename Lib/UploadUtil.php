<?php

class UploadUtil
{
  public function ext( $data)
  {
    $types = array(
        'image/jpeg' => 'jpg',
        'text/html' => 'html',
        'application/msword' => 'doc',
        'image/png' => 'png',
        'application/pdf' => 'pdf',
        'text/csv' => 'csv'
    );
    
    return $types [$data ['mimetype']];
  }
  
  
  public function thumbailPath( $data)
  {
    $config = self::getConfig( $data);
    $size = $config ['thumbail'];
    return self::imagePath( $data, array(
        'size' => $size
    ));
  }
  
  public function thumbailPathMulti( $data)
  {
    $config = self::getConfig( $data);
    $size = $config ['thumbail'];
    return self::imagePathMulti( $data, array(
        'size' => $size
    ));
  }
  
  public function getConfig( $data)
  {
    if( !isset( $data ['id']))
    {
      $data = current( $data);
    }
    
    $type = $data ['content_type'];
    
    return Configure::read( 'Upload.'. $type);
  }
  
  function imagePath( $data, $options = array())
  {
    if( !isset( $data ['id']))
    {
      $data = current( $data);
    }
    
    $_options = array(
        'size' => 'thm',
        'fields' => array(
            'dir' => 'path',
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
    
    $filename = $data [$options ['fields']['filename']];
    
    if( $data ['content_type'] == 'video')
    {
      $ext = pathinfo( $filename, PATHINFO_EXTENSION);
      $filename = str_replace( '.'. $ext, '.jpg', $filename);
    }
    
    $path = Configure::read( 'Path.files.photos') . $data [$options ['fields']['dir']] .'/'. $size. $filename;
    
    return $path;
  }
  
  function imagePathMulti( $data, $options = array())
  {
    if( !isset( $data ['id']))
    {
      $data = current( $data);
    }
    
    $_options = array(
        'size' => 'thm',
        'fieldName' => 'filename',
        'fieldPath' => 'path'
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
    
    $path = Configure::read( 'Path.files.photos') . $data [$options ['fieldPath']] .'/'. $size. $data [$options ['fieldName']];
    return $path;
  }
}