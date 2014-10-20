<?php

/**
 * UploaderComponent
 * 
 *
 * @package upload.controller.component
 **/

App::uses( 'Component', 'Controller');

class CropImageComponent extends Component 
{
  public $components = array();


  public function crop( $upload, $data)
  {
    $imagepath = substr( WWW_ROOT, 0, -1) . UploadUtil::filepath( $upload);
    $pathinfo = pathinfo($imagepath);

    $type = exif_imagetype( $imagepath);

    list( $width, $height) = getimagesize( $imagepath);

    switch( $type) 
    {
      case IMAGETYPE_GIF:
        $src_img = imagecreatefromgif( $imagepath);
        $outputHandler = 'imagegif';
      break;
      case IMAGETYPE_JPEG:
        $src_img = imagecreatefromjpeg( $imagepath);
        $outputHandler = 'imagejpeg';
      break;
      case IMAGETYPE_PNG:
        $src_img = imagecreatefrompng( $imagepath);
        $outputHandler = 'imagepng';
      break;
    }

    $img = imagecreatetruecolor( $data ['width'], $data ['height']);
    $result = imagecopy( $img, $src_img,  0, 0, $data ['x'], $data ['y'], $data ['width'], $data ['height']);

    $quality = $outputHandler == 'imagepng' ? 9 : 100;
    
    $destFile = $pathinfo ['dirname'] .DS. $pathinfo ['filename'] . '_resample.' . $pathinfo ['extension'];
    $outputHandler( $img, $destFile, $quality);

    return $destFile;
  }
}