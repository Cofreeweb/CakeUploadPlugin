<? $id = 'upload_'. intval(mt_rand()) ?>
<li id="<?= $id ?>" class="upload-<?= $upload ['content_type'] ?>">
  <a class="cboxElement" href="<?= UploadUtil::filePath( $upload) ?>"><i class="icon-file"></i> <?= $upload ['filename'] ?></a>
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
		<a href="<?= $this->Asset->filePath( $upload) ?>"><i class="icon-link"></i></a>
		<a href="#" data-rel="#<?= $id ?>" class="upload-delete"><i class="icon-trash"></i></a>
  </div>
</li>