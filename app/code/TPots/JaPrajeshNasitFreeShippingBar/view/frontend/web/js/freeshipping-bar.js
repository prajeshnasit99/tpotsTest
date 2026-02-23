define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'ko',
    'Magento_Catalog/js/price-utils'
], function (Component, customerData, ko, priceUtils) {
    'use strict';

    return Component.extend({

        defaults: {
            threshold: 0
        },

        initialize: function () {
            this._super();

            this.message = ko.observable('');

            var cart = customerData.get('cart');

            this.updateMessage(cart());

            cart.subscribe(function (updatedCart) {
                this.updateMessage(updatedCart);
            }.bind(this));
        },

        updateMessage: function (cart) {

            var subtotal = 0;

            if (cart && cart.subtotalAmount) {
                subtotal = parseFloat(cart.subtotalAmount);
            }

            if (subtotal <= 0) {
                this.message(
                    'Free Shipping on orders above ' +
                    priceUtils.formatPrice(this.threshold)
                );
                return;
            }

            if (subtotal < this.threshold) {
                var difference = this.threshold - subtotal;

                this.message(
                    'You’re ' +
                    priceUtils.formatPrice(difference) +
                    ' away from Free Shipping'
                );
                return;
            }

            this.message('Free shipping is applied to your order');
        }
    });
});