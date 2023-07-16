define([
    'ko',
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote'
], function (ko, Component, quote) {
    

    return Component.extend({
        getShippingMethodName: function () {
            if (!quote.shippingMethod()) {
                return '';
            }
            return quote.shippingMethod().carrier_title + ' - ' + quote.shippingMethod().method_title;
        },

        getFormattedShippingCost: function () {
            if (!quote.shippingMethod()) {
                return '';
            }
            return this.getFormattedPrice(quote.shippingMethod().amount);
        }
    });
});
