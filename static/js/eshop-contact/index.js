const $contactForm = $('#eshopContactForm');
const $contactFormError = $('#eshopContactFormError');
const $contactFormSuccess = $('#eshopContactFormSuccess');

const formError = text => {
  if (text.length > 0) {
    $contactFormError.removeClass('d-none');
  } else {
    $contactFormError.addClass('d-none');
  }
  $contactFormError.text(text);
};

const formSuccess = text => {
  $contactFormSuccess.removeClass('d-none').text(text);
};

const isEmail = (email) => {
  const regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}

const validateForm = () => {
  let isValid = true;
  let value = '';
  let $this = null;
  $contactForm.find('[name]').each(function () {
    $this = $(this);
    value = $.trim($this.val());
    if ( !value ) {
      formError('Vyplňte prosím všechna pole');
      isValid = false;
      return false;
    }
    if ( $this.attr('type') === 'email' && !isEmail(value) ) {
      formError('Vyplňte prosím správný e-mail');
      isValid = false;
      return false;
    }
  });

  return isValid;
};

const onSuccess = () => {
  $contactForm.find('button[type=submit]').remove();
  formError('');
  formSuccess('Odesláno!');
};

const onError = (xhr) => {
  formError('Při odeslání došlo k chybě. Zkuste to prosím později.');
  console.error(xhr);
};

const getFormData = () => {
  const data = {};

  let $this = null;
  $contactForm.find('[name]').each(function () {
    $this = $(this);
    data[$this.attr('name')] = $this.val();
  });

  return data;
};

const sendData = data => {
  $.ajax({
    type: 'POST',
    url: window.ajaxurl,
    data: {
      ...data,
      action: 'eshop_contact',
    },
    success: onSuccess,
    error: onError,
    complete: () => {
      $contactForm.removeClass('is-loading');
    },
  });
};

const initEshopContactForm = () => {

  $contactForm.on('submit', function (e) {
    e.preventDefault();
    if (!validateForm()) return;
    $contactForm.addClass('is-loading');
    const data = getFormData();
    sendData(data);
  });

};

export default initEshopContactForm;
