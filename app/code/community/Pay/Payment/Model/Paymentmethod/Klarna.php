<?php
class Pay_Payment_Model_Paymentmethod_Klarna extends Pay_Payment_Model_Paymentmethod {
    const OPTION_ID = 1717;
    protected $_paymentOptionId = 1717;
    protected $_code = 'pay_payment_klarna';
    protected $_formBlockType = 'pay_payment/form_klarna';

    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canVoid = true;

    protected $_canCapturePartial = false;



//    public function authorize(Mage_Sales_Model_Order_Payment $payment, $amount)
//    {
//        $order = $payment->getOrder();
//        $method = $payment->getMethodInstance();
//
//        $data = $method->startPayment($order);
//
//        $payment->setTransactionId($data['transactionId'])
//            ->setIsTransactionClosed(0);
//
//        $transaction = $payment->addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH);
//
//        $transaction->setId($data['transactionId']);
////        $transaction->setIsClosed(0);
//        $transaction->save();
//        $payment->save();
//
//        return parent::authorize($payment, $amount); // TODO: Change the autogenerated stub
//    }

    /**
     * @return boolean
     */

    public function isApplicableToQuote($quote, $checksBitMask)
    {
        if(strtolower($quote->getShippingAddress()->getFirstname()) !== strtolower($quote->getBillingAddress()->getFirstname())){
            return false;
        }
        if(strtolower($quote->getShippingAddress()->getLastname()) !== strtolower($quote->getBillingAddress()->getLastname())){
            return false;
        }
        if($quote->getShippingAddress()->getCountryId() != $quote->getBillingAddress()->getCountryId()){
            return false;
        }
        if(!empty($quote->getShippingAddress()->getCompany())){
            return false;
        }
        if(!empty($quote->getBillingAddress()->getCompany())){
            return false;
        }
        
        return parent::isApplicableToQuote($quote, $checksBitMask);
    }
    
    public function capture(Varien_Object $payment, $amount)
    {
        $transaction = $payment->getAuthorizationTransaction();

        if(!$transaction){
            Mage::throwException('Cannot find authorize transaction');
        }
        $transactionId = $transaction->getTxnId();

        $order = $payment->getOrder();
        $store = $order->getStore();

        $apiToken = Mage::getStoreConfig('pay_payment/general/apitoken', $store);

        $useBackupApi = Mage::getStoreConfig('pay_payment/general/use_backup_api', $store);
        $backupApiUrl = Mage::getStoreConfig('pay_payment/general/backup_api_url', $store);
        if ($useBackupApi == 1) {
            Pay_Payment_Helper_Api::_setBackupApiUrl($backupApiUrl);
        }
        /**
         * @var Pay_Payment_Helper_Api_Capture $apiCapture
         */
        $apiCapture = Mage::helper('pay_payment/api_capture');

        $apiCapture->setApiToken($apiToken);
        $apiCapture->setAmount($amount);
        $apiCapture->setTransactionId($transactionId);
        $result = $apiCapture->doRequest();

        if($result['request']['result'] == true) {
            return true;
        }
        else throw new Exception($result['request']['errorMessage']);
    }

    public function void(Varien_Object $payment)
    {
        $transaction = $payment->getAuthorizationTransaction();

        if(!$transaction){
            Mage::throwException('Cannot find authorize transaction');
        }
        $transactionId = $transaction->getTxnId();

        $order = $payment->getOrder();
        $store = $order->getStore();

        $apiToken = Mage::getStoreConfig('pay_payment/general/apitoken', $store);

        $useBackupApi = Mage::getStoreConfig('pay_payment/general/use_backup_api', $store);
        $backupApiUrl = Mage::getStoreConfig('pay_payment/general/backup_api_url', $store);
        if ($useBackupApi == 1) {
            Pay_Payment_Helper_Api::_setBackupApiUrl($backupApiUrl);
        }

        /** @var Pay_Payment_Helper_Api_Void $apiVoid */
        $apiVoid = Mage::helper('pay_payment/api_void');
        $apiVoid->setApiToken($apiToken);
        $apiVoid->setTransactionId($transactionId);

        $result = $apiVoid->doRequest();

        if($result['request']['result'] == true) {
            return true;
        }
        else throw new Exception($result['request']['errorMessage']);
    }
//
//    /**
//     * Instantiate state and set it to state object
//     *
//     * @param string $paymentAction
//     * @param Varien_Object
//     */
//    public function initialize($paymentAction, $stateObject)
//    {
//        switch ($paymentAction) {
//            case self::ACTION_AUTHORIZE:
//            case self::ACTION_AUTHORIZE_CAPTURE:
//                $payment = $this->getInfoInstance();
//                $order = $payment->getOrder();
//                $order->setCanSendNewEmailFlag(false);
//                $payment->authorize(true, $order->getBaseTotalDue()); // base amount will be set inside
//                $payment->setAmountAuthorized($order->getTotalDue());
//
//                $order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, 'pending_payment', '', false);
//
//                $stateObject->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
//                $stateObject->setStatus('pending_payment');
//                $stateObject->setIsNotified(false);
//                break;
//            default:
//                break;
//        }
//        parent::initialize();
//    }

}
    