<?php
/**
 * Narvar Shipment Plugin
 *
 * @category    Narvar
 * @package     Narvar_Connect
 * @version     0.1.1
 * @author      premkumarsankar premkumar.sankar@aspiresys.com
 * @copyright   Copyright (c) 2012-2017 Narvar Inc
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Narvar\Connect\Plugin\Sales\Order;

use Narvar\Connect\Helper\Audit\Type;
use Narvar\Connect\Model\Data\TransformerFactory;

class Shipment
{

    /**
     *
     * @var \Narvar\Connect\Model\Data\TransformerFactory
     */
    private $transformer;

    /**
     * Constructor
     *
     * @param Transformer $transformer
     */
    public function __construct(TransformerFactory $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * Method to push shipment details to narvar
     *
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     */
    public function afterSave(\Magento\Sales\Model\Order\Shipment $shipment)
    {
        $data = [
            'order' => $shipment->getOrder(),
            'shipment' => $shipment
        ];
        
        $this->transformer->create()->transform(Type::ENT_TYPE_SHIPMENT, $data);
    }
}
