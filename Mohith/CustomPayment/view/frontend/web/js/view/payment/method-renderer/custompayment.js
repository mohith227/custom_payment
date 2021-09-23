define([
        'ko',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/action/select-payment-method',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/checkout-data',
        'Magento_Customer/js/customer-data'
    ], function (
        ko,
        Component,
        selectPaymentMethodAction,
        quote,
        checkoutData,
        customerData
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Mohith_CustomPayment/payment/customtemplate',
                postLabel: ko.observable("")
            },
            /**
             * Initialize view.
             *
             * @return {exports}
             */
            initialize: function () {
                this._super();
                if(quote.paymentMethod()) {
                    if (quote.paymentMethod().method === this.item.method) {
                        this.postLabel(this.getProductLabel());
                    }
                }
                quote.paymentMethod.subscribe(function (payment) {
                    if (payment.method === this.item.method) {
                        this.postLabel(this.getProductLabel());
                    } else {
                        this.postLabel("");
                    }
                }, this);
            },
            /**
             *  Return Product Net details
             * @returns {string}
             */
            getProductLabel: function () {
                let netDetails = [],
                    onlyNumRegex = /[^\d]/g,
                    cartData = customerData.get("cart");
                cartData().items.forEach((el) => {
                    netDetails.push(el.net);
                });
                netDetails.sort((firstItem, secondItem) => {
                    return firstItem.replaceAll(onlyNumRegex, '') - secondItem.replaceAll(onlyNumRegex, '')
                })
                return netDetails.length ? netDetails[netDetails.length - 1] : '';
            },
            /**
             * @return {Boolean}
             */
            selectPaymentMethod: function () {
                selectPaymentMethodAction(this.getData());
                checkoutData.setSelectedPaymentMethod(this.item.method);
                return true;
            }
        });
    }
);
