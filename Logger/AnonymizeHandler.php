<?php
/**
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author didier <didier@addeos.com>
 * @copyright Copyright (c) 2024 Addeos (http://www.addeos.com)
 */

namespace Addeos\Anonymize\Logger;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

class AnonymizeHandler extends Base
{
    protected $loggerType = Logger::INFO;

    protected $fileName = '/var/log/addeos-anonymize.log';
}
