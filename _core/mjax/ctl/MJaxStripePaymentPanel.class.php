<?php
class MJaxStripePaymentPanel extends MJaxPanel{
	protected $arrFullResponse = null;
	public $blnUseAddress = true;
	
	public $txtCardNum = null;
	public $txtCvc = null;
	public $lstExpMonth = null;
	public $lstExpYear = null;
	public $txtAddress1 = null;
	public $txtAddress2 = null;
	public $txtCity = null;
	public $txtState = null;
	public $txtZip = null;
	public $txtDiscount = null;
	public $lnkSubmit = null;
	public function __construct($objParent, $strControlId = null, $blnUseAddress = true){
		parent:: __construct($objParent, $strControlId);
		$this->strTemplate = __MLC_STRIPE_CORE__ . '/mjax/view/' . get_class($this) . '.tpl.php';
		$this->blnUseAddress = $blnUseAddress;
		$this->objForm->AddHeaderAsset(new MJaxJSHeaderAsset('https://js.stripe.com/v1'));
		$this->objForm->AddHeaderAsset(new MJaxJSHeaderAsset(
            MLCApplication::GetAssetUrl('/js/MJax.Stripe.js', 'MLCStripe')
        ));
		$this->objForm->AddJSCall(
			sprintf(
				'$(function(){ MJax.Stripe.Init("%s", "#%s"); });',
				STRIPE_API_PUBLIC,
				$this->strControlId
			)
		);
		
		$this->txtCardNum = new MJaxTextBox($this);
		$this->txtCardNum->AddCssClass('card-number');
		$this->txtCardNum->Attr('placeholder', 'Card Number');
		$this->txtCardNum->TextMode = MJaxTextMode::Text;
		$this->txtCardNum->Attr('size', 20);
		
		$this->txtCvc = new MJaxTextBox($this);
		$this->txtCvc->AddCssClass('card-cvc');
		$this->txtCvc->Attr('placeholder', 'Cvc');
		$this->txtCvc->TextMode = MJaxTextMode::Text;
		$this->txtCvc->Attr('size', 4);
        
		$this->lstExpMonth = new MJaxListBox($this);
		$this->lstExpMonth->AddCssClass('card-expiry-month');
		//$this->lstExpMonth->TextMode = MJaxTextMode::Month;
		$this->lstExpMonth->Style->Width = '105Px';
		$this->lstExpMonth->AddItem('Month', null);
		for($i = 1; $i <= 12; $i++){
			$this->lstExpMonth->AddItem($i, $i);
		}
		
		$this->lstExpYear = new MJaxListBox($this);
		$this->lstExpYear->AddCssClass('card-expiry-year');
		//$this->lstExpYear->TextMode = MJaxTextMode::Number;
		$this->lstExpYear->Style->Width = '105Px';
		$this->lstExpYear->Style->SetProperty('margin-left','10Px');
		$this->lstExpYear->AddItem('Year', null);
		$intYear = (int)date('Y');
		for($i = $intYear; $i <= $intYear + 10; $i++){
			$this->lstExpYear->AddItem($i, $i);
		}
		
        $this->txtDiscount = new MJaxTextBox($this, 'txtDiscount', array(
            "id" => "discount",
            "name" => "",
            "type" => "",
            "placeholder" => "Discount Code"
        ));
        $this->txtDiscount->Name = 'discount';
		
        if($this->blnUseAddress){
        	$this->txtAddress1 = new MJaxTextBox($this);
			$this->txtAddress1->AddCssClass('card-address1');
			$this->txtAddress1->Attr('placeholder', 'Address');
			$this->txtAddress1->AddAction(
				new MJaxBlurEvent(),
				new MJaxServerControlAction($this, 'txtAddress1_blur')
			);
			
			
			$this->txtAddress2 = new MJaxTextBox($this);
			$this->txtAddress2->AddCssClass('card-address2');
			$this->txtAddress2->Attr('placeholder', 'Suite');
			
			$this->txtCity = new MJaxTextBox($this);
			$this->txtCity->AddCssClass('card-city');
			$this->txtCity->Attr('placeholder', 'City');
			$this->txtCity->AddAction(
				new MJaxBlurEvent(),
				new MJaxServerControlAction($this, 'txtCity_blur')
			);
			
	        $this->txtState = new MJaxTextBox($this);
			$this->txtState->AddCssClass('card-state');
			$this->txtState->Attr('placeholder', 'State');
			$this->txtState->AddAction(
				new MJaxBlurEvent(),
				new MJaxServerControlAction($this, 'txtState_blur')
			);
			
			$this->txtZip = new MJaxTextBox($this);
			$this->txtZip->AddCssClass('card-zip');
			$this->txtZip->Attr('placeholder', 'Zip');
			$this->txtZip->AddAction(
				new MJaxBlurEvent(),
				new MJaxServerControlAction($this, 'txtZip_blur')
			);
			
        }
		$this->lnkSubmit = new MJaxLinkButton($this);
		$this->lnkSubmit->AddCssClass('btn');
		$this->lnkSubmit->Text = 'Submit';
		/*$this->lnkSubmit->AddAction(
			new MJaxClickEvent(),
			new MJaxServerControlAction(
				$this,
				'lnkSubmit_click'
			)
		);*/
		$this->lnkSubmit->AddAction(
			new MJaxClickEvent(),
			new MJaxJavascriptAction(
				sprintf(					
					'function(e){ e.preventDefault(); MJax.Stripe.Submit("#%s"); }',
					$this->strControlId
				)
			)
		);
		
		
		$strSuccessMethod = $this->strControlId . '_stripe_payment_success';
		if(!is_null($this->objParentControl)){
			$objParent = $this->objParentControl;
		}else{
			$objParent = $this->objForm;
		}
		if(method_exists($objParent, $strSuccessMethod)){
			$this->lnkSubmit->AddAction(
				new MJaxStripePaymentErrorEvent(),
				new MJaxServerControlAction($objParent,$strSuccessMethod)
			);
		}
		
		$this->AddAction(
			new MJaxStripePaymentErrorEvent(),
			new MJaxServerControlAction($this, 'pnlStripe_stripe_payment_error')
		);
		$strErrorMethod = $this->strControlId . '_stripe_payment_error';
		if(method_exists($objParent, $strErrorMethod)){
			$this->lnkSubmit->AddAction(
				new MJaxStripePaymentErrorEvent(),
				new MJaxServerControlAction($objParent,$strErrorMethod)
			);
		}
	}
	public function txtAddress1_blur($strFormId, $strControlId){
		$this->Validate($strControlId);
	}
	public function txtCity_blur($strFormId, $strControlId){
		$this->Validate($strControlId);
	}
	public function txtState_blur($strFormId, $strControlId){
		$this->Validate($strControlId);
	}
	public function txtZip_blur($strFormId, $strControlId){
		$this->Validate($strControlId);
	}
	public function Validate($strControlId){
		$this->objForm->ClearCtlAlerts();
		$blnValid = true;
		if(
			($blnValid) &&
			(!$this->txtAddress1->LengthIsLongerThan(4))
		){
			//if($strControlId == $this->txtPassword1->ControlId){
				$this->objForm->CtlAlert(
					$this->txtAddress1, 
					"Must enter a valid address"
				);
			//}
			$blnValid = false;
		}
		if(
			($blnValid) &&
			(!$this->txtCity->LengthIsLongerThan(3))
		){
			if($strControlId != $this->txtAddress2->ControlId){
				$this->objForm->CtlAlert(
					$this->txtCity, 
					"Must enter a valid city"
				);
			}
			$blnValid = false;
		}
		if(
			($blnValid) &&
			(!$this->txtState->LengthIsLongerThan(1))
		){
			if($strControlId != $this->txtCity->ControlId){
				$this->objForm->CtlAlert(
					$this->txtState, 
					"Must enter a valid State"
				);
			}
			$blnValid = false;
		}
		if(
			($blnValid) &&
			(!$this->txtZip->LengthIsLongerThan(4))
		){
			if($strControlId != $this->txtState->ControlId){
				$this->objForm->CtlAlert(
					$this->txtZip, 
					"Must enter a valid Zip"
				);
			}
			$blnValid = false;
		}
		$this->objForm->ForceRenderFormState = false;
		$this->objForm->SkipMainWindowRender = true;
		return $blnValid;
	}
	public function lnkSubmit_click(){
		
		$this->Validate();
		
		$this->objForm->AddJSCall(
			sprintf(					
				'MJax.Stripe.Submit("#%s");',
				$this->strControlId
			)
		);
		$this->objForm->ForceRenderFormState = false;
		$this->objForm->SkipMainWindowRender = true;
	}
	 public function ParsePostData() {
		// Check to see if this Control's Value was passed in via the POST data
		if (array_key_exists($this->strControlId, $_POST)) {
			$this->arrFullResponse = $_POST[$this->strControlId];
		}else{
			$this->arrFullResponse = null;
		}
	}
	public function pnlStripe_stripe_payment_error(){
		$this->objForm->ClearCtlAlerts();
		$this->objForm->ScrollTo($this);
		if(!array_key_exists('param', $this->arrFullResponse['error'])){
			$this->objForm->Alert($this->arrFullResponse['error']['message']);
		}else{
			switch($this->arrFullResponse['error']['param']){
				case('number'):
					
					$this->txtCardNum->Alert($this->arrFullResponse['error']['message']);
					
				break;
				case('cvc'):
					$this->txtCvc->Alert($this->arrFullResponse['error']['message']);
				break;
				case('exp_month'):
				case('exp_year'):
					$this->txtCvc->Alert($this->arrFullResponse['error']['message']);
				break;
				default:
					_dp($this->arrFullResponse['error']);
			}
		}
		$this->objForm->SkipMainWindowRender = true;
	}
	public function __set($strName, $mixValue) {
		switch ($strName) {
            case "UseAddress": 
            	return $this->blnUseAddress = $mixValue;	
			case "Address1": 
				return $this->txtAddress1->Text = $mixValue;	
			case "Address2": 
				return $this->txtAddress2->Text = $mixValue;	
			case "City": 
				return $this->txtCity->Text = $mixValue;	
			case "State": 
				return $this->txtState->Text = $mixValue;	
			case "Zip": 
				return $this->txtZip->Text = $mixValue;			
			default:
				return parent::__set($strName, $mixValue);
		}
	}
	public function __get($strName) {
		switch ($strName) {
            case "FullResponse": return $this->arrFullResponse;
			case "UseAddress": return $this->blnUseAddress;
			case "Address1": return $this->txtAddress1->Text;
			case "Address2": return $this->txtAddress2->Text;
			case "City": return $this->txtCity->Text;
			case "State": return $this->txtState->Text;
			case "Zip": return $this->txtZip->Text;
			case "Token": 
				if(!is_null($this->arrFullResponse)){
					return $this->arrFullResponse['id'];
				}else{
					return null;
				}
			default:
					return parent::__get($strName);
		}
	}
	//MLC DataLayer Code
	public function GetLocationObject(){
		$objLocation = new Location();
		$objLocation->Address1 = $this->txtAddress1->Text;
		$objLocation->Address2 = $this->txtAddress2->Text;
		$objLocation->City = $this->txtCity->Text;
		$objLocation->State = $this->txtState->Text;
		$objLocation->Zip = $this->txtZip->Text;
		$objLocation->IdAccount = MLCAuthDriver::IdAccount();
		return $objLocation;
	}
	public function CreateStripeCustomer(){
		if(!array_key_exists('id', $this->arrFullResponse)){
			throw new Exception("No valid Stripe data found");
		}
		$arrStripeData = MLCStripeDriver::CreateCustomer(
			$this->arrFullResponse['id'],
			null
		);
		return $arrStripeData;
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
