
<div class="row">
  <div class="col-xs-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <span tooltip-html-unsafe="<?= __d( 'admin', 'Edita los campos de la fotografía') ?>"class="h4 pointer padder">
          <?= __d( 'admin', 'Textos de la foto') ?>
        </span>

        <a ng-click="cancel()" class="pull-right m-l block"><i class="fa fa-times"></i></a> 
        <button ng-click="cancel()" class="btn pull-right m-t-n-xs w-xs btn-success btn-rounded"><?= __d( 'admin', 'Guardar') ?></button>

      </div>

      <div class="panel-body">
        <div class="col-xs-2">
          <img class="w-full" src="{{ upload.paths.square }}" />
        </div>
        <div class="col-xs-8">
          <div ng-repeat="field in cake.upload[upload.content_type].config.fields">
            <?= $this->Form->input( null, array(
                'label' => '{{ field.label }}',
                'forceLanguages' => '{{ field.translate }}',
                'name' => false,
                'ng-model' => 'upload[field.field]'
            )) ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


