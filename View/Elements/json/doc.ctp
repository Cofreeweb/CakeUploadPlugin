<? $id = 'upload_'. intval(mt_rand()) ?>
<li id="<?= $id ?>">
  <a class="cboxElement" href="<?= UploadUtil::filePath( $upload) ?>"><?= $upload ['filename'] ?></a>
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
		<a href="#" data-rel="#<?= $id ?>" class="upload-delete"><i class="icon-trash"></i></a>
  </div>
</li>