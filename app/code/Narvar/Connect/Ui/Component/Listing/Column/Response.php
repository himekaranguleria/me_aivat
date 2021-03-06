<?php
/**
 * Audit Log Grid Column Response UI Component Model
 *
 * @category    Narvar
 * @package     Narvar_Connect
 * @version     0.1.1
 * @author      premkumarsankar premkumar.sankar@aspiresys.com
 * @copyright   Copyright (c) 2012-2017 Narvar Inc
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Narvar\Connect\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Narvar\Connect\Model\Audit\Log;

class Response extends Column
{
    /**
     * Method to display the response content using break line
     *
     * @see \Magento\Ui\Component\AbstractComponent::prepareDataSource()
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[Log::RESPONSE] = nl2br($item[Log::RESPONSE]);
            }
        }
        
        return $dataSource;
    }
}
