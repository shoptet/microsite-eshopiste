import Cleave from 'cleave.js';

const $archiveForm = $('#archiveForm');

// Hide filter submit button if javascript is loaded
$('#filterSubmit').addClass('d-none');

const initOrderSelect = () => {
  $('#archiveForm select').on('change', e => {
    $archiveForm.submit();
  });
};

const sendData = (url, moveToTop = false) => {
  const $archiveList = $('#archiveList');
  $archiveForm.addClass('is-loading');
  window.history.pushState(null, document.title, url); // rewrite url adress
  $.ajax({ url,
    success: response => {
      $archiveList.html($(response).find('#archiveList'));
      $archiveForm.removeClass('is-loading');
      initOrderSelect(); // initialize select change event
      if (moveToTop) $('html, body').animate({scrollTop: $archiveForm.offset().top});
    },
  });
};

$archiveForm.on('click', '#archivePagination a', e => {
  e.preventDefault();
  const url = $(e.currentTarget).attr('href');
  sendData(url, true);
});

$archiveForm.on('submit', e => {
  e.preventDefault();
  const url = $archiveForm.attr('action') + '?' + $archiveForm.serialize();
  sendData(url);
});


$('#archiveForm input[type=checkbox]').on('change', () => {
  $archiveForm.submit();
});

// Refresh browser after state popped
window.onpopstate = () => { window.location.href = document.location };

const createSlider = (sliderId, start, range, submitOnChange = true) => {
  const slider = document.getElementById(sliderId),
        inputMin = document.getElementById(sliderId + 'InputMin'),
        inputMax = document.getElementById(sliderId + 'InputMax'),
        indicatorMin = document.getElementById(sliderId + 'IndicatorMin'),
        indicatorMax = document.getElementById(sliderId + 'IndicatorMax');

  const hasIndicator = indicatorMin && indicatorMax;

  const separatedThousands = wNumb({thousand: ' '});

  const formatOptions = {
    numeral: true,
    numeralDecimalMark: ',',
    delimiter: ' ',
  };

  new Cleave(indicatorMin, formatOptions);
  new Cleave(indicatorMax, formatOptions);

  noUiSlider.create(slider, {
    start, range, step: 1000, connect: true, format: wNumb({decimals: 0})
  });

  slider.noUiSlider.on('update', values => {
      inputMin.value = values[0]; inputMax.value = values[1];

      if (!hasIndicator) return;

      const indicatorMinValue = separatedThousands.to(Number(values[0])),
            indicatorMaxValue = separatedThousands.to(Number(values[1]));

      if (indicatorMin.tagName === 'INPUT') indicatorMin.value = indicatorMinValue;
      else indicatorMin.innerHTML = indicatorMinValue;

      if (indicatorMax.tagName === 'INPUT') indicatorMax.value = indicatorMaxValue;
      else indicatorMax.innerHTML = indicatorMaxValue;

  });

  submitOnChange && slider.noUiSlider.on('change', () => {
    $archiveForm.submit();
  });

  indicatorMin.addEventListener('change', function () {
    slider.noUiSlider.set([$(this).val(), null]);
    submitOnChange && $archiveForm.submit();
  });
  indicatorMax.addEventListener('change', function () {
    slider.noUiSlider.set([null, $(this).val()]);
    submitOnChange && $archiveForm.submit();
  });
};

const initFiltering = () => {

  initOrderSelect();

  if (!window.sliderData) return;

  const sliderData = window.sliderData;
  for (let i = 0, len = sliderData.length; i < len; i++) {
    createSlider(
      sliderData[i].id,
      sliderData[i].start,
      sliderData[i].range,
      sliderData[i].submitOnChange,
    );
  }
};

export default initFiltering;
