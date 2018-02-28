import '../scss/main.scss';
import '../images/eshopiste-logo.svg';
import '../images/eshopiste-logo-no-claim.svg';

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

});
