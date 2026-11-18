(function () {
  'use strict';

  var button = document.getElementById('copy-whatsapp-number');
  var whatsapp = document.getElementById('whatsapp_no');
  var mobile = document.getElementById('customer_mobile');
  if (!button || !whatsapp || !mobile) return;

  function updateCopyButton() {
    var empty = mobile.value.trim() === '';
    button.style.display = empty ? 'none' : '';
    button.disabled = empty;
    if (button.parentElement) button.parentElement.style.display = empty ? 'none' : '';
  }

  button.title = 'Copy Mobile No to WhatsApp No';
  button.setAttribute('aria-label', 'Copy Mobile No to WhatsApp No');
  button.addEventListener('click', function () {
    var number = mobile.value.trim();
    if (!number) { updateCopyButton(); return; }
    whatsapp.value = number;
    whatsapp.dispatchEvent(new Event('input', { bubbles: true }));
    whatsapp.dispatchEvent(new Event('change', { bubbles: true }));
    toastr.success('Mobile number copied to WhatsApp No.');
  });

  $(mobile).on('input change', updateCopyButton);
  $('#customer_id, #store_id').on('change', function () {
    setTimeout(updateCopyButton, 0);
  });
  updateCopyButton();
})();