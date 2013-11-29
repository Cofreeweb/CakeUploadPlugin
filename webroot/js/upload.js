$("body").delegate( ".upload-delete", 'click', function(){
  var el = $(this).data( 'rel');
  var upload_container = $(this).parents( '.fineupload-container');
  upload_container.data( 'fineuploader').uploader._netUploaded--;
  upload_container.data( 'fineuploader').uploader._netUploadedOrQueued--;
  $(el).remove();
})