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
  
  public function file( $data, $attributes = array())
  {
    if( !isset( $data ['id']))
    {
      $data = current( $data);
    }
    
    if( isset( $attributes ['class']))
    {
      $attributes ['class'] .= ' '. self::fileExt( $data ['filename']);
    }
    else
    {
      $attributes ['class'] = self::fileExt( $data ['filename']);
    }
    
    $filepath = self::filePath( $data);
    return $this->Html->link( $data ['filename'], $filepath, $attributes);
  }
  
/**
 * Retorna la extensión de un fichero
 *
 * @param string $filename 
 * @return string
 */
  public function fileExt( $filename)
  {
    $ext = pathinfo( $filename, PATHINFO_EXTENSION);
    return $ext;
  }
  
/**
 * Cambia la extensión de el nombre de un fichero
 *
 * @param string $filename 
 * @param string $ext 
 * @return string
 */
  public function changeExt( $filename, $ext)
  {
    $ext2 = self::fileExt( $filename);
    $filename = str_replace( '.'. $ext2, '.'. $ext, $filename);
    return $filename;
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
  
  public function paths( $data, $options = array())
  {
    $config = self::getConfig( $data);
    
    if( !$config)
    {
      return false;
    }
    
    $method = $config ['type'] . 'Paths';
    
    
    if( method_exists( 'UploadUtil', $method))
    {
      return (array)self::$method( $data);
    }
    
    return array();
  }
  
  
  
  public function docPaths( $data, $options = array())
  {
    $return = self::filePaths( $data, $options);
    return array( $return);
  }
  
  public function videoPaths( $data, $options = array())
  {
    return self::imagePath( $data, $options);
  }
  
  public function filePaths( $data, $options = array())
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
    
    $path = Configure::read( 'Path.files.photos') . $data [$options ['fields']['dir']] .'/'. $data [$options ['fields']['filename']];

    return $path;
  }
  
/**
 * Alias de filePaths
 *
 * @param array $data 
 * @param array $options 
 * @return void
 */
  public function filePath( $data, $options = array())
  {
    return self::filePaths( $data, $options);
  }
  
  public function imagePaths( $data)
  {
    if( !isset( $data ['id']))
    {
      $data = current( $data);
    }
    
    $config = self::getConfig( $data);
    
    $return = array();
    
    foreach( $config ['config']['thumbnailSizes'] as $size => $info)
    {
      $return [ $size] = self::imagePath( $data, array(
          'size' => $size
      ));
    }
    
    return $return;
  }
  
  public function imagePath( $data, $options = array())
  {
    if( !is_array( $data))
    {
      return '';
    }
    
    if( !isset( $data ['id']))
    {
      $data = current( $data);
    }
    
    $_options = array(
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
    
    if( isset( $data ['content_type']) && $data ['content_type'] == 'video')
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