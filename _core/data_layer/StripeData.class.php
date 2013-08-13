<?php
/**
* Class and Function List:
* Function list:
* Classes list:
* - StripeData extends StripeDataBase
*/
require_once (__MLC_STRIPE_CORE_DATALAYER__ . "/base_classes/StripeDataBase.class.php");
class StripeData extends StripeDataBase {
    public function RawObject(){
        $arrObjData = json_decode($this->data, true);
        return Stripe_Object::constructFrom($arrObjData);
    }
}
?>