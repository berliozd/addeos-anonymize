<?php
/**
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author didier <berliozd@gmail.com>
 * @copyright Copyright (c) 2020 Addeos (http://www.addeos.com)
 */

namespace Addeos\Anonymize\Logger;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

class AnonymizeHandler extends Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::INFO;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/addeos-anonymize.log';
}