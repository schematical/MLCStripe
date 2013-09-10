<?php
class MJaxStripeCardSelectPanel extends MJaxPanel{
	public $arrRadioBoxes = array();
    public $arrCards = array();
	public $lnkSubmit = null;
    public $lnkAddNewCard = null;
    public $pnlAddNewCard = null;
	public function __construct($objParent, $strControlId = null, $mixCards = null){
		parent:: __construct($objParent, $strControlId);

        if(is_null($mixCards)){
            $objCustomer = MLCStripeDriver::GetUserCustomerObject();

            if(!is_null($objCustomer)){
                $arrCards = $objCustomer->cards->data;
            }
        }else{
            if(is_array($mixCards)){
                $arrCards = $mixCards;
            }elseif(
                ($mixCards instanceof Stripe_Object) &&
                ($mixCards->object == 'customer')
            ){
                $arrCards = $mixCards->cards->data;
            }
        }
        $this->arrCards = $arrCards;
        //_dv($this->arrCards);
		$this->strTemplate = __MLC_STRIPE_CORE__ . '/mjax/view/' . get_class($this) . '.tpl.php';

		$this->lnkSubmit = new MJaxLinkButton($this);
		$this->lnkSubmit->AddCssClass('btn btn-large');
		$this->lnkSubmit->Text = 'Submit';

		$this->lnkSubmit->AddAction(
			$this,
            'lnkSubmit_click'
		);

        $this->lnkAddNewCard = new MJaxLinkButton($this);
		//$this->lnkAddNewCard->AddCssClass('btn btn-large');
		$this->lnkAddNewCard->Text = 'Add new card';

		$this->lnkAddNewCard->AddAction(
            $this,
            'lnkAddNewCard_click'
        );

        foreach($this->arrCards as $intIndex => $arrCard){
            $this->arrRadioBoxes[$intIndex] = new MJaxRadioBox($this);
            $this->arrRadioBoxes[$intIndex]->Name = 'pament_method';

        }

    }
	public function lnkSubmit_click(){
        foreach($this->arrCards as $intIndex => $arrCard){
            if($this->arrRadioBoxes[$intIndex]->Checked){
                $this->strActionParameter = $arrCard;
                $this->objForm->TriggerControlEvent($this->strControlId, 'stripe_payment_success');
                return;
            }
        }
        $this->objForm->TriggerControlEvent($this->strControlId, 'stripe_payment_error');
	}
    public function lnkAddNewCard_click(){
        $this->pnlAddNewCard = new MJaxStripePaymentPanel($this, null, false);
        $this->pnlAddNewCard->AddAction(
            new MJaxStripePaymentSuccessEvent(),
            new MJaxServerControlAction(
                $this,
                'pnlAddNewCard_success'
            )
        );
        $this->pnlAddNewCard->AddAction(
            new MJaxStripePaymentErrorEvent(),
            new MJaxServerControlAction(
                $this,
                'pnlAddNewCard_error'
            )
        );
        $this->objForm->Alert($this->pnlAddNewCard);
    }
    public function pnlAddNewCard_success($strFormId, $strControlId, $mixActionParameter){
        //$this->strActionParameter = $mixActionParameter;
        $this->objForm->TriggerControlEvent($this->strControlId, 'stripe_payment_success');
    }
    public function pnlAddNewCard_error($strFormId, $strControlId, $mixActionParameter){
        //$this->strActionParameter = $mixActionParameter;
        $this->objForm->TriggerControlEvent($this->strControlId, 'stripe_payment_error');
    }

}
/* [id] => tok_1O61DFjhDcX5Gj 
 * [livemode] => false 
 * [created] => 1362246812 
 * [used] => false 
 * [object] => token 
 * [card] => Array ( 
 * 		[object] => card 
 * 		[last4] => 4242 
 * 		[type] => Visa 
 * 		[exp_month] => 1 
 * 		[exp_year] => 2014 
 * 		[fingerprint] => QeayctMipuIITFDb 
 * 		[country] => US [name] => 
 * 		[address_line1] => 640 West Wash 
 * 		[address_line2] => 
 * 		[address_city] => Madison 
 * 		[address_state] => Wi 
 * 		[address_zip] => 53719 
 * 		[address_country] =>
 * 
 */
