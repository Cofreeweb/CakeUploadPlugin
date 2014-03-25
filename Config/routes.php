<?php
Router::parseExtensions();
Router::setExtensions( array( 'json'));

$plugins = CakePlugin::loaded();

if( in_array( 'I18n', $plugins))
{
  $params = array('routeClass' => 'I18nRoute');
}
else
{
  $params = array();
}

/**
 * UPLOADS
 */
Router::connect( '/upload/uploads/add/:model/:field/*', array(
  'plugin' => 'upload',
  'controller' => 'uploads', 
  'action' => 'add'
), $params);

Router::connect( '/upload/uploads/delete/:model/:filename/:id', array(
  'plugin' => 'upload',
  'controller' => 'uploads', 
  'action' => 'delete',
), $params);
