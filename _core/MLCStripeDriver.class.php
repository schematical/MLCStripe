<?php
abstract class MLCStripeDriver{
	protected static $strMode = null;
	protected static $blnInited = false;
	public static function Init(){
		//Figure out how to integrate 'MLCStripeMode' 
		if(!self::$blnInited){
			require_once(__MLC_STRIPE_CORE__ . '/stripe/Stripe.php');
			Stripe::setApiKey(STRIPE_API_SECRET);
			MLCStripeDriver::$strMode = STRIPE_MODE;
			self::$blnInited = true;
		}
	}
	public static function CreateCustomer($strToken, $objUser = null, $arrStripeData = null){
		self::Init();
		if(is_null($objUser)){
			$objUser = MLCAuthDriver::User();
			if(is_null($objUser)){
				throw new MLCStripeInvalidUserException("Must have a user to create a Stripe Customer");
			}
		}
		if(is_null($arrStripeData)){
			$arrStripeData = array();
		}
		$arrStripeData["card"] = $strToken;
		$arrStripeData["email"] = $objUser->Email;				  
		//try{

		 	$arrCustomerData = Stripe_Customer::create(
		 		$arrStripeData
			);
			MLCStripeDriver::SaveData($arrCustomerData);
		//}catch(Exception $e){
			//throw new MLCStripeException($e->getMessage(), 0 , $e);
		//}
		return $arrCustomerData;
	}
	public static function UpdateSubscription($mixPlan){
        self::Init();
		if(
			(is_string($mixPlan)) ||
			(is_numeric($mixPlan))
		){
			$arrPlan = array(
				"plan" => $mixPlan
			);
		}elseif(is_array($mixPlan)){
			$arrPlan = $mixPlan;
		}else{
			throw MLCStripeException("Parameter 1 was not a valid type");
		}
		$objStripeCustomer = MLCStripeDriver::GetUserCustomerObject();
		$objData = $objStripeCustomer->updateSubscription($arrPlan);
		MLCStripeDriver::SaveData($objData);		
	}
	public static function GetUserCustomerObject($objUser = null, $blnReturnArray = false){
		self::Init();		
		$arrStripeData = MLCStripeDriver::LoadUserStripeData(
			MLCStripeType::CUSTOMER,
            $objUser
		);
		if(count($arrStripeData) == 0){
			throw new MLCStripeException("No valid stripe customer object on file");
		}
        $arrReturn = array();
        foreach($arrStripeData as $objStripeData){
            $objStripeCustomer = Stripe_Customer::constructFrom(json_decode($objStripeData->Data, true));
            $objStripeCustomer->refresh();
            $arrReturn[] = $objStripeCustomer;
        }
        if(!$blnReturnArray){
            return $arrReturn[0];
        }
		return $arrReturn;
	}

	public static function LoadUserStripeData($strType, $objUser = null){
        self::Init();
		if(is_null($objUser)){
			$objUser = MLCAuthDriver::User();
			if(is_null($objUser)){
				throw new MLCStripeException("Must have a valid user to perform this function");
			}
		}
		$arrData = StripeData::Query(
			sprintf(
				'WHERE object = "%s" AND idAuthUser = %s AND mode = "%s"',
				$strType,
				$objUser->IdUser,
				MLCStripeDriver::$strMode
			)
		);

        $arrReturn = array();
        foreach($arrData as $intIndex => $objStripeData){
            $arrReturn[] = $objStripeData->RawObject();
        }
		return $arrReturn;
		
	}
    public static function UserCustomer($objUser = null){
        //Load the user
        if(is_null($objUser)){
            $objUser = MLCAuthDriver::User();
        }
        $arrData = self::LoadUserStripeData(
            'customer',
            $objUser
        );

        if(count($arrData) == 0){
            return null;
        }
        if(count($arrData) > 1){
            //IDK
            return $arrData[count($arrData) - 1];
        }
        return $arrData[0];
    }
    public static function ChargeUser($intAmount, $objUser = null){
        //Load the user
        if(is_null($objUser)){
            $objUser = MLCAuthDriver::User();
            if(is_null($objUser)){
                throw new MLCStripeException("No Authenticated User. Cannot create charge");
            }
            $objCustomer = self::UserCustomer();
        }elseif(
            ($objUser instanceof Stripe_Object) &&
            ($objUser->object == 'customer')
        ){
            $objCustomer = $objUser;
        }else{
            throw new Exception("Invalid data passed in Where AuthUser or Stripe_Object is required, Parameter 2");
        }



        if(is_null($objCustomer)){
            throw new MLCStripeException("Could not charge. No customer object found");
        }

        $objCharge = Stripe_Charge::create(array(
                "amount"   => $intAmount * 100,
                "currency" => "usd",
                "customer" => $objCustomer->id)
        );
        $objStripeData = self::SaveData($objCharge);
        return $objStripeData;

    }
	public static function LoadStripData($strStripeId, $strType){
        $arrData = StripeData::Query(
            sprintf(
                'WHERE stripeId = "%s" AND object = "%s"',
                $strStripeId,
                $strType
            )
        );
        return $arrData;
	}
	public static function SaveData($mixData, $objParentStripeData = null){
		$strUrl = null;
		if(is_array($mixData)){
			$arrData = $mixData;
		}elseif(
			(is_object($mixData)) &&
			($mixData instanceof Stripe_Object)
		){
			$arrData = $mixData->__toArray(true);
			try{
				if(method_exists($mixData, 'instanceUrl')){
					$strUrl = $mixData->instanceUrl();
				}
			}catch(Exception $e){}
		}
		$strStripeObject = null;
		if(array_key_exists('object', $arrData)){
			$strStripeObject = $arrData['object'];
		}
		$strStripeId = null;
		if(array_key_exists('id', $arrData)){
			$strStripeId = $arrData['id'];
		}
		if(
			(!is_null($strStripeId)) &&
			(!is_null($strStripeObject))
		){
			$objStripeData = StripeData::Query(
				sprintf(
					'WHERE stripeId = "%s" AND object = "%s" AND mode = "%s"',
                    $strStripeId,
                    $strStripeObject,
                    self::$strMode
				),
				true
			);
		}
		if(is_null($objStripeData)){
			$objStripeData = new StripeData();
			if(array_key_exists('object', $arrData)){
				$objStripeData->Object = $arrData['object'];
			}
			if(array_key_exists('id', $arrData)){
				$objStripeData->StripeId = $arrData['id'];
			}
		}
		
		if(!is_null($objParentStripeData)){
			$objStripeData->IdParentStripeData = $objParentStripeData->IdStripeData;
		}
		if(!is_null($strUrl)){
			$objStripeData->Instance_url = $strUrl;
		}
		$objStripeData->IdAuthUser = MLCAuthDriver::IdUser();
		$objStripeData->Data = json_encode($arrData);
		$objStripeData->CreDate = MLCDateTime::Now();
		$objStripeData->Mode = self::$strMode;
		$objStripeData->Save();
		foreach($arrData as $strKey => $mixData){
			if(is_array($mixData)){
				// MLCStripeDriver::SaveData($mixData, $objStripeData);
			}
		}
        return $objStripeData;
		
	}
	public static function LoadChargeCollection($arrParams = array()){
		self::Init();
		/*foreach($arrParams as $strKey => $mixValue){
			switch($strKey){
				case(MLCStripeChargeQueryParam::created):
					
				break;
				
			}
		}*/
		if(!array_key_exists(MLCStripeChargeQueryParam::count, $arrParams)){
			$arrParams[MLCStripeChargeQueryParam::count] = 100;
		}
		return Stripe_Charge::all($arrParams)->data;
	}
	public static function GetChargeCollectionTotalData($arrParams = array()){
		
		$intTotal = 0;		
		$arrCharges = self::LoadChargeCollection($arrParams);
		
		foreach($arrCharges as $intIndex => $objCharge){
			
			$intTotal += $objCharge->amount;
		}
		$intCount = count($arrCharges);
		$arrReturn = array(
			'total'=> ($intTotal/100),
			'count'=> ($intCount),
			'avg'=> ($intTotal/$intCount/100)
		);
		return $arrReturn;
	}
}
