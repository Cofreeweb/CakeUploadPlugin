$("body").delegate( ".upload-delete", 'click', function(){
  var _this = $(this);
  var p = confirm( _this.data( 'alert'));
  if( p) {
    var el = _this.data( 'rel');
    var upload_container = _this.parents( '.fineupload-container');
    var uploader = upload_container.data( 'fineuploader').uploader;
    uploader._netUploaded--;
    uploader._netUploadedOrQueued--;
    $(el).remove();
    
    if( _this.data( 'delete-once')) {
      $.ajax({
        url: '/upload/uploads/delete/' + _this.data( 'model') + '/' + _this.data( 'filename') + '/' + _this.data( 'id') + '.json'
      })
    }
    
    $(".qq-upload-button", $(upload_container)).show();
  }
  
  return false;
})

