MJax.Stripe = {
	jForm:null,
	Init:function(strKey){
		 Stripe.setPublishableKey(strKey);
 	},
    StripeResponseHandler:function(strStatus, objResponse) {
    	if (objResponse.error) {
	        // Show the errors on the form
	        MJax.TriggerControlEvent(objResponse, '#' + jForm.attr('id'), 'stripe_payment_error');
	    } else {
    		MJax.TriggerControlEvent(objResponse, '#' + jForm.attr('id'), 'stripe_payment_success');
        }
    },
 
    Submit:function() {
        Stripe.createToken(
        	{
	          number: jForm.find('.card-number').val(),
	          cvc: $('.card-cvc').val(),
	          exp_month: $('.card-expiry-month').val(),
	          exp_year: $('.card-expiry-year').val()
	        },
            MJax.Stripe.StripeResponseHandler
        );
	}
	
	
};