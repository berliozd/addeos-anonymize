<?php
/**
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author didier <berliozd@gmail.com>
 * @copyright Copyright (c) 2020 Addeos (http://www.addeos.com)
 */

namespace Addeos\Anonymize\Helper;

use Addeos\Anonymize\Logger\AnonymizeLogger;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Anonymize
{
    const ANONYMIZED_PASSWORD = 'Password123';
    const ANONYMIZED_PHONE = '0341 12345';
    const ANONYMIZED_FAX = '0171 12345';
    const ANONYMIZED_MAIL = '@yopmail.com';
    const ANONYMIZE_STREET = ' test avenue';
    const ANONYMIZE_IP = ' 0.0.0.0';
    /**
     * @var AnonymizeLogger
     */
    private $logger;
    /**
     * @var EncryptorInterface
     */
    private $encryptor;
    /**
     * @var AdapterInterface
     */
    private $connection;

    /** @var OutputInterface */
    private $output;

    public function __construct(
        AnonymizeLogger $anonymizeLogger,
        EncryptorInterface $encryptor,
        ResourceConnection $connection
    ) {
        $this->logger = $anonymizeLogger;
        $this->encryptor = $encryptor;
        $this->connection = $connection->getConnection();
    }

    private $config = [
        [
            'customer_entity',
            [
                ['firstname', 'firstname_', 'entity_id', '', false, true],
                ['lastname', 'lastname_', 'entity_id', '', false, true],
                ['email', 'dev_', 'entity_id', self::ANONYMIZED_MAIL, false, true],
                ['password_hash', self::ANONYMIZED_PASSWORD, null, null, true, true],
            ],
        ],
        [
            'customer_address_entity',
            [
                ['firstname', 'firstname_', 'entity_id', '', false, true],
                ['lastname', 'lastname_', 'entity_id', '', false, true],
                ['street', self::ANONYMIZE_STREET, 'entity_id', '', false, false],
                ['city', 'city_', 'entity_id', '', false, true],
                ['telephone', self::ANONYMIZED_PHONE, 'entity_id', '', false, true],
                ['fax', self::ANONYMIZED_FAX, 'entity_id', '', false, true],
            ],
        ],
        [
            'customer_grid_flat',
            [
                ['name', 'name_', 'entity_id', '', false, true],
                ['email', 'dev_', 'entity_id', self::ANONYMIZED_MAIL, false, true],
                ['shipping_full', 'shipping_full_', 'entity_id', '', false, true],
                ['billing_full', 'billing_full_', 'entity_id', '', false, true],
                ['billing_firstname', 'billing_firstname_', 'entity_id', '', false, true],
                ['billing_lastname', 'billing_lastname_', 'entity_id', '', false, true],
                ['billing_telephone', self::ANONYMIZED_PHONE, 'entity_id', '', false, true],
                ['billing_street', self::ANONYMIZE_STREET, 'entity_id', '', false, false],
                ['billing_city', 'billing_city_', 'entity_id', '', false, true],
                ['billing_company', 'billing_company_', 'entity_id', '', false, true],
            ],
        ],
        ['email_contact', [['email', 'dev_', 'email_contact_id', self::ANONYMIZED_MAIL, false, true]]],
        ['newsletter_subscriber', [['subscriber_email', 'dev_', 'subscriber_id', self::ANONYMIZED_MAIL, false, true]]],
        [
            'ontex_report_general',
            [
                ['customer_firstname', 'customer_firstname_', 'entity_id', '', false, true],
                ['customer_email', 'dev_', 'entity_id', self::ANONYMIZED_MAIL, false, true],
                ['shipping_address_street', self::ANONYMIZE_STREET, 'entity_id', '', false, false],
                ['shipping_address_city', 'shipping_address_city_', 'entity_id', '', false, true],
                ['shipping_address_telephone', self::ANONYMIZED_PHONE, 'entity_id', '', false, true],
                ['billing_address_street', self::ANONYMIZE_STREET, 'entity_id', '', false, false],
                ['billing_address_city', 'billing_address_city_', 'entity_id', '', false, true],
            ],
        ],

        [
            'paradoxlabs_stored_card',
            [
                ['customer_email', 'dev_', 'id', self::ANONYMIZED_MAIL, false, true],
                ['address', '', null, '', false, true],
                ['additional', '', null, '', false, true],
            ],
        ],
        [
            'quote',
            [
                ['customer_email', 'dev_', 'entity_id', self::ANONYMIZED_MAIL, false, true],
                ['customer_firstname', 'customer_firstname_', 'entity_id', '', false, true],
                ['customer_lastname', 'customer_lastname_', 'entity_id', '', false, true],
                ['remote_ip', self::ANONYMIZE_IP, null, null, false, true],
            ],
        ],
        [
            'quote_address',
            [
                ['email', 'dev_', 'address_id', self::ANONYMIZED_MAIL, false, true],
                ['firstname', 'firstname_', 'address_id', '', false, true],
                ['lastname', 'lastname_', 'address_id', '', false, true],
                ['company', 'company_', 'address_id', '', false, true],
                ['street', self::ANONYMIZE_STREET, 'address_id', '', false, false],
                ['city', 'city_', 'address_id', '', false, true],
                ['telephone', self::ANONYMIZED_PHONE, 'address_id', '', false, true],
                ['fax', self::ANONYMIZED_FAX, 'address_id', '', false, true],
            ],
        ],
        [
            'sales_creditmemo_grid',
            [
                ['billing_name', 'billing_name_', 'entity_id', '', false, true],
                ['billing_address', 'billing_address_', 'entity_id', '', false, true],
                ['shipping_address', 'shipping_address_', 'entity_id', '', false, true],
                ['customer_name', 'customer_name_', 'entity_id', '', false, true],
                ['customer_email', 'dev_', 'entity_id', self::ANONYMIZED_MAIL, false, true],
            ],
        ],
        [
            'sales_invoice_grid',
            [
                ['billing_name', 'billing_name_', 'entity_id', '', false, true],
                ['billing_address', 'billing_address_', 'entity_id', '', false, true],
                ['shipping_address', 'shipping_address_', 'entity_id', '', false, true],
                ['customer_name', 'customer_name_', 'entity_id', '', false, true],
                ['customer_email', 'dev_', 'entity_id', self::ANONYMIZED_MAIL, false, true],
            ],
        ],
        [
            'sales_order',
            [
                ['customer_email', 'dev_', 'entity_id', self::ANONYMIZED_MAIL, false, true],
                ['customer_firstname', 'customer_firstname_', 'entity_id', '', false, true],
                ['customer_lastname', 'customer_lastname_', 'entity_id', '', false, true],
            ],
        ],
        [
            'sales_order_address',
            [
                ['lastname', 'lastname_', 'entity_id', '', false, true],
                ['firstname', 'firstname_', 'entity_id', '', false, true],
                ['street', self::ANONYMIZE_STREET, 'entity_id', '', false, false],
                ['city', 'city_', 'entity_id', '', false, true],
                ['email', 'dev_', 'entity_id', self::ANONYMIZED_MAIL, false, true],
                ['telephone', self::ANONYMIZED_PHONE, 'entity_id', '', false, true],
                ['company', 'company_', 'entity_id', '', false, true],
            ],
        ],
        [
            'sales_order_grid',
            [
                ['billing_name', 'billing_name_', 'entity_id', '', false, true],
                ['shipping_name', 'shipping_name_', 'entity_id', '', false, true],
                ['billing_address', 'billing_address_', 'entity_id', '', false, true],
                ['shipping_address', 'shipping_address_', 'entity_id', '', false, true],
                ['customer_name', 'customer_name_', 'entity_id', '', false, true],
                ['customer_email', 'dev_', 'entity_id', self::ANONYMIZED_MAIL, false, true],
            ],
        ],
        [
            'sales_shipment_grid',
            [
                ['billing_name', 'billing_name_', 'entity_id', '', false, true],
                ['shipping_name', 'shipping_name_', 'entity_id', '', false, true],
                ['billing_address', 'billing_address_', 'entity_id', '', false, true],
                ['shipping_address', 'shipping_address_', 'entity_id', '', false, true],
                ['customer_name', 'customer_name_', 'entity_id', '', false, true],
                ['customer_email', 'dev_', 'entity_id', self::ANONYMIZED_MAIL, false, true],
            ],
        ],
        ['stripe_customers', [['customer_email', 'dev_', 'customer_id', self::ANONYMIZED_MAIL, false, true]]],
    ];

    public function anonymize()
    {
        $this->log('Start anonymization');
        $sqlQueries = $this->getPreparedSqlQueries();
        $i = 0;
        foreach ($sqlQueries as $sqlQuery) {
            $i++;
            $this->log(sprintf('=========== [SQL %s] ===========', $i) . "\n" . $sqlQuery . "\n");
            try {
                $this->connection->query($sqlQuery);
            } catch (\Exception $e) {
                $this->log($e->getMessage());
            }
        }
        $this->log('End anonymization');
    }

    /**
     * @return array
     */
    private function getPreparedSqlQueries()
    {
        return array_map(function ($tableData) {
            $tableName = $tableData[0];
            $colsSql = [];
            array_map(function ($colData) use (&$colsSql) {
                $this->prepareColumnQueryParams(
                    $colData,
                    $colName,
                    $concatString,
                    $concatFieldName,
                    $additionalString,
                    $encrypt,
                    $concatFieldNameAfter
                );
                if ($concatFieldNameAfter) {
                    $colsSql[] = sprintf(
                        '%s=CONCAT(%s, %s, %s)',
                        $colName,
                        $concatString,
                        $concatFieldName,
                        $additionalString
                    );
                } else {
                    $colsSql[] = sprintf(
                        '%s=CONCAT(%s, %s, %s)',
                        $colName,
                        $concatFieldName,
                        $concatString,
                        $additionalString
                    );
                }
            }, $tableData[1]);
            return sprintf('UPDATE %s SET %s;', $tableName, implode(',', $colsSql));
        }, $this->config);
    }

    /**
     * @param $colData
     * @param $colName
     * @param $concatString
     * @param $concatFieldName
     * @param $additionalString
     * @param $encrypt
     * @param $concatFieldNameAfter
     */
    private function prepareColumnQueryParams(
        $colData,
        &$colName,
        &$concatString,
        &$concatFieldName,
        &$additionalString,
        &$encrypt,
        &$concatFieldNameAfter
    ) {
        [
            $colName,
            $concatString,
            $concatFieldName,
            $additionalString,
            $encrypt,
            $concatFieldNameAfter,
        ] = $colData;
        $concatString = '\'' . ($encrypt ? $this->encryptor->getHash($concatString, true) : $concatString) . '\'';
        $concatFieldName = $concatFieldName ? $concatFieldName : '\'\'';
        $additionalString = '\'' . $additionalString . '\'';
    }

    /**
     * @param $message
     */
    public function log($message)
    {
        if ($this->output) {
            $this->output->writeln($message);
        }
        $this->logger->info($message);
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput($output): void
    {
        $this->output = $output;
    }
}
