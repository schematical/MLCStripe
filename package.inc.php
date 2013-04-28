<?php
define('__MLC_STRIPE__', dirname(__FILE__));
define('__MLC_STRIPE_CORE__', __MLC_STRIPE__ . '/_core');
define('__MLC_STRIPE_CORE_DATALAYER__', __MLC_STRIPE_CORE__ . '/data_layer');
MLCApplicationBase::$arrClassFiles['MLCStripeDriver'] = __MLC_STRIPE_CORE__ . '/MLCStripeDriver.class.php';
MLCApplicationBase::$arrClassFiles['StripeData'] = __MLC_STRIPE_CORE_DATALAYER__ . '/StripeData.class.php';
require_once(__MLC_STRIPE_CORE__ . '/_enum.inc.php');
require_once(__MLC_STRIPE_CORE__ . '/_exception.inc.php');
if(defined('__MJAX__')){
	require_once(__MLC_STRIPE_CORE__ . '/mjax/_event.inc.php');
	MLCApplicationBase::$arrClassFiles['MJaxStripePaymentPanel'] = __MLC_STRIPE_CORE__ . '/mjax/ctl/MJaxStripePaymentPanel.class.php';	
}
