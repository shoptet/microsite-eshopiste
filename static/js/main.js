import '../scss/main.scss';
import '../images/eshopiste-logo.svg';
import '../images/eshopiste-logo-no-claim.svg';
import '../images/shoptetrix-meditate.png';
import '../images/shoptet-logo.svg';

import '../images/properties/bar-chart.svg';
import '../images/properties/cake.svg';
import '../images/properties/line-chart.svg';
import '../images/properties/price-tag.svg';
import '../images/properties/up-trend.svg';
import '../images/properties/user.svg';

import initHeader from './header';
import initFiltering from './filtering';
import initCharts from './charts';
import initEshopContactForm from './eshop-contact';


$(function () {

  $('[data-toggle="tooltip"]').tooltip();

  new SVGInjector().inject(document.querySelectorAll('svg[data-src]'));

  initHeader();

  initFiltering();

  initCharts();

  initEshopContactForm();

  $( '#headerSearch input[type=search]' ).autocomplete({
    source: function(request, response) {
      var matcher = new RegExp('^' + $.ui.autocomplete.escapeRegex(request.term), 'i');
      response($.grep( window.allEshops, function(eshop) {
        return matcher.test(eshop.label);
      }));
    },
    select: function(e, ui) {
      window.location.href = ui.item.link;
    },
  });

});
