$.saleskuPluginProductElements.ComparePrice = function(root_element) {
  return root_element.find('.Product__Price--Old');
};
/*
 При дублировании старой цены
 $.saleskuPluginProductElements._Selectors.compare_price ='.old-price';
 $.saleskuPluginProductElements._Selectors.compare_price_html= '<span class="old-price nowrap"></span>';
* */
$.saleskuPluginProductElements._Selectors.compare_price ='.compare-at-price';
$.saleskuPluginProductElements._Selectors.compare_price_html= '<span class="Product__Price--Inline compare-at-price nowrap"></span>';

saleskuPluginProduct.prototype.setComparePrice = function (compare_price) {
  if (compare_price) {
    var $compare_price = this.getElements().ComparePrice();
    if (!$compare_price.length) {
      $compare_price = $(this.getElements().Selectors().compare_price_html);
      this.getElements().ComparePrice().append($compare_price);
    }
    $compare_price.html($(this.getElements().Selectors().compare_price_html).html(this.currencyFormat(compare_price))).show();
  } else {
    this.getElements().ComparePrice().find(this.getElements().Selectors().compare_price).remove();
  }
};
