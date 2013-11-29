<?php
Router::parseExtensions( 'json');

/**
 * UPLOADS
 */
Router::connect( '/upload/uploads/add/:model/:field/*', array(
  'plugin' => 'upload',
  'controller' => 'uploads', 
  'action' => 'add'
));

Router::connect( '/upload/uploads/delete/:model/:field/:id', array(
  'plugin' => 'upload',
  'controller' => 'uploads', 
  'action' => 'delete'
));
