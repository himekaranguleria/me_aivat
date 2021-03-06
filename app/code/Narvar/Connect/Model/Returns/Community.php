<?php
/**
 * Return Community Model
 *
 * @category    Narvar
 * @package     Narvar_Connect
 * @version     0.1.1
 * @author      premkumarsankar premkumar.sankar@aspiresys.com
 * @copyright   Copyright (c) 2012-2017 Narvar Inc
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Narvar\Connect\Model\Returns;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Narvar\Connect\Helper\Config\Returns as ReturnsHelper;
use Magento\Framework\Webapi\Exception as WebApiException;
use Magento\Framework\Exception\MailException;

class Community implements ProcessInterface
{

    /**
     *
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $transportBuilder;

    /**
     *
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    private $inlineTranslation;

    /**
     *
     * @var \Magento\Sales\Model\Order\Address\Renderer
     */
    private $addressRenderer;

    /**
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     *
     * @var \Narvar\Connect\Helper\Config\Returns
     */
    private $returnConfigHelper;
    
    /***
     * Constructor
     *
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @param Renderer $addressRenderer
     * @param ScopeConfigInterface $scopeConfig
     * @param ReturnsHelper $returnConfigHelper
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        Renderer $addressRenderer,
        ScopeConfigInterface $scopeConfig,
        ReturnsHelper $returnConfigHelper
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->addressRenderer = $addressRenderer;
        $this->scopeConfig = $scopeConfig;
        $this->returnConfigHelper = $returnConfigHelper;
    }
    
    /**
     * @see \Narvar\Connect\Model\Returns\ProcessInterface::process()
     */
    public function process(
        \Magento\Sales\Model\Order $order,
        $orderItems,
        \Narvar\Connect\Model\Service\Response $narvarApiResponse,
        $dateRequested = null
    ) {
        if ($this->returnConfigHelper->canCreateReturnCE()) {
            if ($this->sendEmail($order, $orderItems, $dateRequested, $narvarApiResponse)) {
                $narvarApiResponse->clearMessages();
                $narvarApiResponse->addNarvarSuccessMessage(
                    __('Return request created for order %1', $order->getIncrementId()),
                    201
                );
                
                return;
            }
           
            $narvarApiResponse->addNarvarErrorMessage(
                __('Unable to process return for order %1', $order->getIncrementId()),
                WebApiException::HTTP_BAD_REQUEST
            );
           
            return;
        }
        
        $narvarApiResponse->addNarvarErrorMessage(
            __('Access denied to process return for order %1', $order->getIncrementId()),
            WebApiException::HTTP_UNAUTHORIZED
        );
    }

    /**
     * Method to send return request email to admin
     *
     * @param \Magento\Sales\Model\Order $order
     * @param array $orderItems
     * @param string $dateRequested
     * @return boolean
     */
    public function sendEmail(
        \Magento\Sales\Model\Order $order,
        $orderItems,
        $dateRequested,
        \Narvar\Connect\Model\Service\Response $narvarApiResponse
    ) {
        $templateVars = [
            'order' => $order,
            'date_requested' => $dateRequested,
            'orderItems' => $orderItems,
            'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
            'formattedBillingAddress' => $this->getFormattedBillingAddress($order)
        ];
        
        $templateOptions = [
            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
            'store' => $order->getStoreId()
        ];
        
        $sender = [
            'email' => $this->scopeConfig->getValue('trans_email/ident_sales/email', ScopeInterface::SCOPE_STORE),
            'name' => $this->scopeConfig->getValue('trans_email/ident_sales/name', ScopeInterface::SCOPE_STORE)
        ];
        
        $receiver = [
            $this->returnConfigHelper->getReturnOrderEmail()
        ];
        $this->inlineTranslation->suspend();
        $transport = $this->transportBuilder->setTemplateIdentifier('narvar_connect_return_order_template')
            ->setTemplateOptions($templateOptions)
            ->setTemplateVars($templateVars)
            ->setFrom($sender)
            ->addTo($receiver)
            ->getTransport();
        $this->inlineTranslation->resume();
        
        try {
            $result = $transport->sendMessage();
            return true;
        } catch (MailException $e) {
            $narvarApiResponse->addNarvarErrorMessage(
                __('%1', $e->getMessage()),
                WebApiException::HTTP_BAD_REQUEST
            );
        }
        
        return false;
    }
    
    /**
     * Method to get the Shipping Address
     *
     * @param \Magento\Sales\Model\Order $order
     * @return string|null
     */
    private function getFormattedShippingAddress(\Magento\Sales\Model\Order $order)
    {
        return $order->getIsVirtual() ? null : $this->addressRenderer->format($order->getShippingAddress(), 'html');
    }

    /**
     * Method to get the Billing Address
     *
     * @param \Magento\Sales\Model\Order $order
     * @return string|null
     */
    private function getFormattedBillingAddress(\Magento\Sales\Model\Order $order)
    {
        return $this->addressRenderer->format($order->getBillingAddress(), 'html');
    }
}
