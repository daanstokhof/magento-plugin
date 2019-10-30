<?php

class Pay_Payment_Block_Form_Billink extends Pay_Payment_Block_Form_Abstract {

    protected $paymentMethodId = Pay_Payment_Model_Paymentmethod_Billink::OPTION_ID;
    protected $paymentMethodName = 'Achteraf betalen via Billink';
    protected $methodCode = 'pay_payment_billink';

    protected $template = 'pay/payment/form/default.phtml';

    protected function _construct() {
        $enablePersonal = Mage::getStoreConfig('payment/pay_payment_billink/ask_data_personal', Mage::app()->getStore());
        $enableBusiness = Mage::getStoreConfig('payment/pay_payment_billink/ask_data_business', Mage::app()->getStore());

        $this->template ='pay/payment/form/billink.phtml';

        return  parent::_construct();
    }

}
