<?php

/**
 * UploadJsonable behavior class
 * 
 * 
 * @package Upload.Model.Behavior
 * @dependence Cofree.Model.Behavior.Jsonable
 */
class UploadJsonableBehavior extends ModelBehavior
{
  
  public function setup( Model $Model, $settings = array())
  {   
    if( empty( $settings))
    {
      return;
    }

    $fields = $translate = array();

    foreach( $settings as $key => $info)
    {
      $fields [] = $key;

      if( isset( $info ['translate']))
      {
        $translate [$key] = $info ['translate'];
      }
    }

    $config = array(
        'fields' => $fields
    );

    if( !empty( $translate))
    {
      $config ['translate'] = $translate;
    }

    if( $Model->Behaviors->loaded( 'Jsonable'))
    {
      $Model->Behaviors->Jsonable->settings [$Model->alias] = array_merge_recursive( $Model->Behaviors->Jsonable->settings [$Model->alias], $config);
    }
    else
    {
      $Model->Behaviors->load( 'Cofree.Jsonable', $config);
    }


    
  }
 
  
}

?>