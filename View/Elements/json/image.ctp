<? $id = 'upload_'. $upload ['id'] ?>
<li id="upload_<?= $upload ['id'] ?>">
  <a class="cboxElement" href="#"><img src="<?= UploadUtil::imagePath( $upload, array(
      'size' => 'thm'
  )) ?>" /></a>
  <?= $this->Form->hidden( $alias .'.'. $id .'.id', array(
      'value' => $upload ['id']
  )) ?>
  
  <?= $this->Form->hidden( $alias .'.'. $id .'.filename', array(
      'value' => $upload ['filename']
  )) ?>
  
  <?= $this->Form->hidden( $alias .'.'. $id .'.path', array(
      'value' => $upload ['path']
  )) ?>
  
  <?= $this->Form->hidden( $alias .'.'. $id .'.model', array(
      'value' => $upload ['model']
  )) ?>

  <?= $this->Form->hidden( $alias .'.'. $id .'.content_type', array(
      'value' => $upload ['content_type']
  )) ?>
  
  <div class="tools tools-bottom">
		<a href="<?= $this->Asset->imagePath( $upload) ?>" class="magnific"><i class="icon-search"></i></a>
		<a href="#" data-rel="#<?= $id ?>" class="upload-delete"><i class="icon-trash"></i></a>
  </div>
</li>