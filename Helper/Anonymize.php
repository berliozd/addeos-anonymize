<?php
/**
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author didier <berliozd@gmail.com>
 * @copyright Copyright (c) 2020 Addeos (http://www.addeos.com)
 */

namespace Addeos\Anonymize\Helper;

use Addeos\Anonymize\Logger\AnonymizeLogger;
use Exception;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Anonymize
{
    private const ANONYMIZED_PASSWORD = 'Password-123';
    private const ANONYMIZED_PHONE = '0341 12345';
    private const ANONYMIZED_FAX = '0171 12345';
    private const ANONYMIZED_MAIL = '@yopmail.com';
    private const ANONYMIZE_STREET = 'street';
    private const ANONYMIZE_IP = ' 0.0.0.0';
    private AnonymizeLogger $logger;
    private EncryptorInterface $encryptor;
    private AdapterInterface $connection;

    /** @var OutputInterface */
    private OutputInterface $output;

    public function __construct(
        AnonymizeLogger $anonymizeLogger,
        EncryptorInterface $encryptor,
        ResourceConnection $connection
    )
    {
        $this->logger = $anonymizeLogger;
        $this->encryptor = $encryptor;
        $this->connection = $connection->getConnection();
    }

    private array $config = [
        'customer_entity' => [
            ['firstname', 'firstname_', 'entity_id', '', false],
            ['lastname', 'lastname_', 'entity_id', '', false],
            ['email', 'dev_', 'entity_id', self::ANONYMIZED_MAIL, false],
            ['password_hash', self::ANONYMIZED_PASSWORD, null, null, true],
        ],
        'customer_address_entity' => [
            ['firstname', 'firstname_', 'entity_id', '', false],
            ['lastname', 'lastname_', 'entity_id', '', false],
            ['street', self::ANONYMIZE_STREET, 'entity_id', '', false],
            ['city', 'city_', 'entity_id', '', false],
            ['telephone', self::ANONYMIZED_PHONE, 'entity_id', '', false],
            ['fax', self::ANONYMIZED_FAX, 'entity_id', '', false],
        ],
        'customer_grid_flat' => [
            ['name', 'name_', 'entity_id', '', false],
            ['email', 'dev_', 'entity_id', self::ANONYMIZED_MAIL, false],
            ['shipping_full', 'shipping_full_', 'entity_id', '', false],
            ['billing_full', 'billing_full_', 'entity_id', '', false],
            ['billing_firstname', 'billing_firstname_', 'entity_id', '', false],
            ['billing_lastname', 'billing_lastname_', 'entity_id', '', false],
            ['billing_telephone', self::ANONYMIZED_PHONE, 'entity_id', '', false],
            ['billing_street', self::ANONYMIZE_STREET, 'entity_id', '', false],
            ['billing_city', 'billing_city_', 'entity_id', '', false],
            ['billing_company', 'billing_company_', 'entity_id', '', false],
        ],
        'email_contact' => [['email', 'dev_', 'email_contact_id', self::ANONYMIZED_MAIL, false]],
        'newsletter_subscriber' => [['subscriber_email', 'dev_', 'subscriber_id', self::ANONYMIZED_MAIL, false]],
        'paradoxlabs_stored_card' => [
            ['customer_email', 'dev_', 'id', self::ANONYMIZED_MAIL, false],
            ['address', '', null, '', false],
            ['additional', '', null, '', false],
        ],
        'quote' => [
            ['customer_email', 'dev_', 'entity_id', self::ANONYMIZED_MAIL, false],
            ['customer_firstname', 'customer_firstname_', 'entity_id', '', false],
            ['customer_lastname', 'customer_lastname_', 'entity_id', '', false],
            ['remote_ip', self::ANONYMIZE_IP, null, null, false],
        ],
        'quote_address' => [
            ['email', 'dev_', 'address_id', self::ANONYMIZED_MAIL, false],
            ['firstname', 'firstname_', 'address_id', '', false],
            ['lastname', 'lastname_', 'address_id', '', false],
            ['company', 'company_', 'address_id', '', false],
            ['street', self::ANONYMIZE_STREET, 'address_id', '', false],
            ['city', 'city_', 'address_id', '', false],
            ['telephone', self::ANONYMIZED_PHONE, 'address_id', '', false],
            ['fax', self::ANONYMIZED_FAX, 'address_id', '', false],
        ],
        'sales_creditmemo_grid' => [
            ['billing_name', 'billing_name_', 'entity_id', '', false],
            ['billing_address', 'billing_address_', 'entity_id', '', false],
            ['shipping_address', 'shipping_address_', 'entity_id', '', false],
            ['customer_name', 'customer_name_', 'entity_id', '', false],
            ['customer_email', 'dev_', 'entity_id', self::ANONYMIZED_MAIL, false],
        ],
        'sales_invoice_grid' => [
            ['billing_name', 'billing_name_', 'entity_id', '', false],
            ['billing_address', 'billing_address_', 'entity_id', '', false],
            ['shipping_address', 'shipping_address_', 'entity_id', '', false],
            ['customer_name', 'customer_name_', 'entity_id', '', false],
            ['customer_email', 'dev_', 'entity_id', self::ANONYMIZED_MAIL, false],
        ],
        'sales_order' => [
            ['customer_email', 'dev_', 'entity_id', self::ANONYMIZED_MAIL, false],
            ['customer_firstname', 'customer_firstname_', 'entity_id', '', false],
            ['customer_lastname', 'customer_lastname_', 'entity_id', '', false],
        ],
        'sales_order_address' => [
            ['lastname', 'lastname_', 'entity_id', '', false],
            ['firstname', 'firstname_', 'entity_id', '', false],
            ['street', self::ANONYMIZE_STREET, 'entity_id', '', false],
            ['city', 'city_', 'entity_id', '', false],
            ['email', 'dev_', 'entity_id', self::ANONYMIZED_MAIL, false],
            ['telephone', self::ANONYMIZED_PHONE, 'entity_id', '', false],
            ['company', 'company_', 'entity_id', '', false],
        ],
        'sales_order_grid' => [
            ['billing_name', 'billing_name_', 'entity_id', '', false],
            ['shipping_name', 'shipping_name_', 'entity_id', '', false],
            ['billing_address', 'billing_address_', 'entity_id', '', false],
            ['shipping_address', 'shipping_address_', 'entity_id', '', false],
            ['customer_name', 'customer_name_', 'entity_id', '', false],
            ['customer_email', 'dev_', 'entity_id', self::ANONYMIZED_MAIL, false],
        ],
        'sales_shipment_grid' => [
            ['billing_name', 'billing_name_', 'entity_id', '', false],
            ['shipping_name', 'shipping_name_', 'entity_id', '', false],
            ['billing_address', 'billing_address_', 'entity_id', '', false],
            ['shipping_address', 'shipping_address_', 'entity_id', '', false],
            ['customer_name', 'customer_name_', 'entity_id', '', false],
            ['customer_email', 'dev_', 'entity_id', self::ANONYMIZED_MAIL, false],
        ],
        'stripe_customers' => [['customer_email', 'dev_', 'customer_id', self::ANONYMIZED_MAIL, false]]
    ];

    public function anonymize(): void
    {
        $this->log('Start anonymization');
        $sqlQueries = $this->getSqlQueries();
        $i = 0;
        foreach ($sqlQueries as $sqlQuery) {
            $i++;
            $this->log(sprintf('=========== [SQL %s] ===========', $i) . "\n" . $sqlQuery . "\n");
            try {
                $this->connection->query($sqlQuery);
            } catch (Exception $e) {
                $this->log($e->getMessage());
            }
        }
        $this->log('End anonymization');
    }

    private function getSqlQueries(): array
    {
        $queries = [];
        foreach ($this->config as $tableName => $tableData) {
            $colsSql = [];
            foreach ($tableData as $colData) {
                $colName = $colData[0];
                $concatFieldName = $colData[2] ?: '\'\'';
                $additionalString = '\'' . $colData[3] . '\'';
                $encrypt = $colData[4];
                $concatString = '\'' . ($encrypt ? $this->encryptor->getHash($colData[1], true) : $colData[1]) . '\'';
                $sql = sprintf('%s=CONCAT(%s, %s, %s)', $colName, $concatString, $concatFieldName, $additionalString);
                $colsSql[] = $sql;
            }
            $queries[] = sprintf('UPDATE %s SET %s;', $tableName, implode(',', $colsSql));
        }
        return $queries;
    }

    public function log($message): void
    {
        $this->output->writeln($message);
        $this->logger->info($message);
    }

    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }
}
