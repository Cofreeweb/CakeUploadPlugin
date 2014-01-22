$("body").delegate( ".upload-delete", 'click', function(){
  var el = $(this).data( 'rel');
  var upload_container = $(this).parents( '.fineupload-container');
  var uploader = upload_container.data( 'fineuploader').uploader;
  uploader._netUploaded--;
  uploader._netUploadedOrQueued--;
  $(el).remove();
  $(".qq-upload-button", $(upload_container)).show();
  return false;
})

