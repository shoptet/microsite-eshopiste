const initHeader = () => {
  $('[data-header-button-search]').on('click', () => {
    $('#headerSearch').toggleClass('is-shown');
    $('#headerActions').removeClass('is-shown');
  });
  $('[data-header-button-actions]').on('click', () => {
    $('#headerActions').toggleClass('is-shown');
    $('#headerSearch').removeClass('is-shown');
  });
};

export default initHeader;
