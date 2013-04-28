<?php
class MJaxStripePaymentErrorEvent extends MJaxEventBase{
	 protected $strEventName = 'stripe_payment_error';
}
class MJaxStripePaymentSuccessEvent extends MJaxEventBase{
	 protected $strEventName = 'stripe_payment_success';
}
