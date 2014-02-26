<? $id = 'upload_'. $upload ['id'] ?>
<li id="upload_<?= $upload ['id'] ?>">
  <a class="cboxElement" href="#"><img src="<?= UploadUtil::imagePath( $upload, array(
      'size' => 'thm'
  )) ?>" />
  <?= $this->Form->hidden( $alias .'.'. $id .'.id', array(
      'value' => $upload ['id']
  )) ?>
  <?= $this->Form->hidden( $alias .'.'. $id .'.model', array(
      'value' => $upload ['model']
  )) ?>

  <?= $this->Form->hidden( $alias .'.'. $id .'.content_type', array(
      'value' => $upload ['content_type']
  )) ?>
  
  <div class="tools tools-bottom">
		<a href="#"><i class="icon-link"></i></a>
		<a href="#" data-rel="#<?= $id ?>" data-alert="<?= __d( "admin", "¿Estás seguro?") ?>" class="upload-delete"><i class="icon-trash"></i>Borrar</a>
  </div>
</li>