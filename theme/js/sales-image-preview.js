(function ($) {
  'use strict';

  var modal;
  if (!document.getElementById('sales-image-preview-styles')) {
    $('<style id="sales-image-preview-styles">' +
      '.sales-item-image,.item-image-preview{background:#fff;border:1px solid #ddd;border-radius:3px;padding:4px;box-sizing:border-box;object-fit:contain;cursor:zoom-in;}' +
      '#sales-image-preview.in{display:flex!important;align-items:center;justify-content:center;padding:16px!important;}' +
      '#sales-image-preview .modal-dialog{width:min(560px,calc(100vw - 32px),calc(100vh - 110px));margin:0;max-width:100%;transform:none;}' +
      '#sales-image-preview .modal-content{max-height:calc(100vh - 32px);overflow:auto;}' +
      '#sales-image-preview .modal-body{display:flex;align-items:center;justify-content:center;width:100%;aspect-ratio:1/1;padding:16px;background:#fff;}' +
      '#sales-image-preview .modal-body img{display:block;width:100%;height:100%;min-height:0;object-fit:contain;}' +
      '</style>').appendTo(document.head);
  }

  function preview(image) {
    // Avoid opening a second popup over an existing sales dialog.
    if ($('.modal.in:visible').not('#sales-image-preview').length) return;
    if (!modal) {
      modal = $('<div class="modal fade" id="sales-image-preview" tabindex="-1" role="dialog" aria-label="Item image preview">' +
        '<div class="modal-dialog modal-lg"><div class="modal-content">' +
        '<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
        '<h4 class="modal-title"></h4></div>' +
        '<div class="modal-body text-center">' +
        '<img alt=""></div>' +
        '</div></div></div>').appendTo(document.body);
    }
    var thumbnail = $(image);
    var fullImage = modal.find('.modal-body img');
    modal.find('.modal-title').text(thumbnail.attr('alt') || 'Item Image');
    fullImage.off('error').one('error', function () {
      this.src = thumbnail.attr('data-fallback-image') || image.src;
    });
    fullImage.attr('alt', thumbnail.attr('alt') || 'Item Image');
    fullImage.attr('src', thumbnail.attr('data-full-image') || image.src);
    modal.modal('show');
  }

  // Delegation covers rows loaded through item search and saved invoice rows.
  $(document).on('click', '.sales-item-image, .item-image-preview', function () {
    preview(this);
  }).on('keydown', '.sales-item-image, .item-image-preview', function (event) {
    if (event.key !== 'Enter' && event.key !== ' ') return;
    event.preventDefault();
    preview(this);
  });
})(jQuery);
