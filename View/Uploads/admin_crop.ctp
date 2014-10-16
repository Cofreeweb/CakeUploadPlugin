
<div class="row">
  <div class="col-xs-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <span tooltip-html-unsafe="{{ thm.description }}" ng-repeat="(size, thm) in thumbails" ng-class="{dissable: (current.size != size)}" class="h4 pointer b-r padder" im-get="{{ size }}">
          {{ thm.name }}
        </span>

        <button ng-click="cancel()" class="btn pull-right m-t-n-xs btn-danger btn-rounded"><i class="fa fa-times"></i></button> 
        <button ng-click="uploadCrop()" class="btn pull-right m-t-n-xs w-xs btn-success btn-rounded"><?= __d( 'admin', 'Guardar') ?></button>

      </div>

      <div class="panel-body">
        <div class="col-sm-5">
          <h4><?= __d( 'admin', 'Recorte de foto') ?></h4>
          <img  im-cropper 
                bt-submit="#submit" 
                ng-model="current.cropper" 
                preview=".img-preview" 
                width="{{ current.width }}" 
                height="{{ current.height }}" 
                src="{{ content.Upload.paths.org }}"
                class="cropper" >
        </div>
        <div class="col-sm-5 clearfix" ng-show="current.width && current.height">
          <h4><?= __d( 'admin', 'Resultado') ?></h4>
          <div style="width: {{ current.width }}px; height: {{ current.height }}px" class="img-preview"></div>
        </div>

      </div>
    </div>
  </div>
</div>

