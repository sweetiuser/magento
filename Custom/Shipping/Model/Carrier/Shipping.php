<?php
namespace Custom\Shipping\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;

class Shipping extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    
    protected $_code = 'customshipping';

    protected $_rateResultFactory;

    protected $_rateMethodFactory;
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * get allowed methods
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    /**
     * @return float
     */
    private function getShippingPrice()
    {
        $configPrice = $this->getConfigData('price');

        $shippingPrice = $this->getFinalPriceWithHandlingFee($configPrice);

        return $shippingPrice;
    }

    /**
     * @param RateRequest $request
     * @return bool|Result
     */
    public function collectRates(RateRequest $request)
    {
     
        if (!$this->getConfigFlag('active')) {
            return false;
        }
    $result = $this->_rateResultFactory->create();

    // Get the package weight from the request
    $weight = $request->getPackageWeight();

    // Perform your custom logic to determine the shipping price based on weight
    $subtotal = $request->getBaseSubtotalInclTax();
    if($subtotal < 100){
        $shippingPrice = $this->calculateShippingPrice($weight);
    }
    else{
        $shippingPrice = 0;
    }

    // Create a new rate method
    $method = $this->_rateMethodFactory->create();

    // Set the carrier title, method title, and price
    $method->setCarrier($this->_code);
    $method->setCarrierTitle($this->getConfigData('title'));
    $method->setMethod($this->_code);
    $method->setMethodTitle($this->getConfigData('name'));
    $method->setPrice($shippingPrice);
    $method->setCost($shippingPrice);

    // Add the rate method to the result
    $result->append($method);

    return $result;
    }
    
    
    
    private function calculateShippingPrice($weight)
{
    // Implement your custom logic to determine the shipping price based on the weight
    $shippingPrice=0;
    if($weight < 5)
       $shippingPrice = 5;
    elseif ($weight >=5 && $weight <= 10)
      $shippingPrice = 10;
    elseif ($weight > 10)
      $shippingPrice = 15;
   return $shippingPrice;
}
}