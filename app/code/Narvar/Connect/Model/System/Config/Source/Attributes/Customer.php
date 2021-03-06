<?php
/**
 * Config Custom Attributes Customer Source Model
 *
 * @category    Narvar
 * @package     Narvar_Connect
 * @version     0.1.1
 * @author      premkumarsankar premkumar.sankar@aspiresys.com
 * @copyright   Copyright (c) 2012-2017 Narvar Inc
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Narvar\Connect\Model\System\Config\Source\Attributes;

use Narvar\Connect\Model\Eav\AttributesFactory;

class Customer implements \Magento\Framework\Option\ArrayInterface
{

    /**
     *
     * @var \Narvar\Connect\Model\Eav\AttributesFactory
     */
    private $connectEavAttributesFactory;

    /**
     * Constructor
     *
     * @param AttributesFactory $connectEavAttributesFactory
     */
    public function __construct(AttributesFactory $connectEavAttributesFactory)
    {
        $this->connectEavAttributesFactory = $connectEavAttributesFactory;
    }

    /**
     * Method to return Customer, Order, Address and Product Attributes as Select Options
     *
     * @return multitype
     */
    public function toOptionArray()
    {
        $connectEavAttributesFactory = $this->connectEavAttributesFactory->create();
        $customerAttributes = $connectEavAttributesFactory->getCustomerAttributes();
        $customerAddressAttributes = $connectEavAttributesFactory->getCustomerAddressAttributes();

        return [
            [
                'value' => '-1',
                'label' => __('-- Please Select --')
            ],
            $connectEavAttributesFactory::CUSTOMER_ENTITY => [
                'value' => $connectEavAttributesFactory->toOptions($customerAttributes),
                'label' => __('Customer')
            ],
            $connectEavAttributesFactory::CUSTOMER_ADDRESS_ENTITY => [
                'value' => $connectEavAttributesFactory->toOptions($customerAddressAttributes),
                'label' => __('Customer Address')
            ]
        ];
    }
}
