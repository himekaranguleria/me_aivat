<?php
/**
 * Configuration Account Helper
 *
 * @category    Narvar
 * @package     Narvar_Connect
 * @version     0.1.1
 * @author      premkumarsankar premkumar.sankar@aspiresys.com
 * @copyright   Copyright (c) 2012-2017 Narvar Inc
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Narvar\Connect\Helper\Config;

use Narvar\Connect\Helper\Base;

/**
 * Below methods will used to get configuration value
 *
 * @method string getNarvarApiEndpoint()
 * @method string getNarvarAccountId()
 * @method string getNarvarAuthToken()
 * @method string getNarvarRetailer()
 */
class Account extends Base
{
    /**
     * Narvar Account Connect Config Group
     */
    const CONFIG_GRP = 'account';

    /**
     * Narvar Api Url Config Path
     */
    const NARVAR_API_ENDPOINT = 'narvar_api_endpoint';

    /**
     * Narvar Account Id Config Path
     */
    const NARVAR_ACCOUNT_ID = 'narvar_acc_id';

    /**
     * Narvar Auth Token Config Path
     */
    const NARVAR_AUTH_TOKEN = 'narvar_auth_token';
}
