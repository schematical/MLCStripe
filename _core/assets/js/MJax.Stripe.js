MJax.Stripe = {
	jForm:null,
	Init:function(strKey, strFormSelector){
         MJax.Stripe.jForm = $(strFormSelector);
		 Stripe.setPublishableKey(strKey);
 	},
    StripeResponseHandler:function(strStatus, objResponse) {
        var objData = {};
        objData[MJax.Stripe.jForm.attr('id')] = objResponse;

        // Show the errors on the form
        MJax.TriggerControlEvent(
            {},
            '#' + MJax.Stripe.jForm.attr('id'),
            'stripe_payment_finish',
            objData
        );

    },
 
    Submit:function() {
        Stripe.createToken(
        	{
	          number: MJax.Stripe.jForm.find('.card-number').val(),
	          cvc: $('.card-cvc').val(),
	          exp_month: $('.card-expiry-month').val(),
	          exp_year: $('.card-expiry-year').val()
	        },
            MJax.Stripe.StripeResponseHandler
        );
	}
	
	
};