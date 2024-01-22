<?php
/**
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author didier <didier@addeos.com>
 * @copyright Copyright (c) 2024 Addeos (http://www.addeos.com)
 */

namespace Addeos\Anonymize\Helper;

use Addeos\Anonymize\Logger\AnonymizeLogger;
use Addeos\Anonymize\Model\Config;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Anonymize
{
    private const ANONYMIZED_PASSWORD = 'Password-123';
    private const ANONYMIZED_PHONE = '0341 12345';
    private const ANONYMIZED_MAIL = '@yopmail.com';
    private const ANONYMIZED_IP = '0.0.0.0';
    private AnonymizeLogger $logger;
    private EncryptorInterface $encryptor;
    private AdapterInterface $connection;
    private OutputInterface $output;
    private Config $config;

    public function __construct(
        AnonymizeLogger $anonymizeLogger,
        EncryptorInterface $encryptor,
        ResourceConnection $connection,
        Config $config
    )
    {
        $this->logger = $anonymizeLogger;
        $this->encryptor = $encryptor;
        $this->connection = $connection->getConnection();
        $this->config = $config;
    }

    public function anonymize(): void
    {
        $this->log('Start anonymization ====================================', true);
        $tables = $this->config->getTables();
        $genericStringColumns = $this->config->getGenericStringColumns();
        $phoneColumns = $this->config->getPhoneColumns();
        $emailColumns = $this->config->getEmailColumns();
        $passwordColumns = $this->config->getPasswordColumns();
        $ipColumns = $this->config->getIpColumns();
        foreach ($tables as $tableName) {
            if (empty($tableName)) {
                continue;
            }
            $this->log(sprintf("\n" . '------------ Start anonymizing table : %s', $tableName));
            if (!$this->isTableExisting($tableName)) {
                $this->log(sprintf('Table %s does not exist.', $tableName));
                continue;
            }
            $colsSql = [];
            $primaryColumn = $this->getPrimaryKeyColumnName($tableName);
            $this->log('Primary key is ' . $primaryColumn);
            $query = sprintf('SHOW COLUMNS FROM %s;', $tableName);
            $tableDesc = $this->connection->fetchAll($query);
            foreach ($tableDesc as $value) {
                if (in_array($value['Field'], $genericStringColumns)) {
                    $colsSql[] = $this->getGenericStringSqlUpdate($value['Field'], $primaryColumn);
                }
                if (in_array($value['Field'], $emailColumns)) {
                    $colsSql[] = $this->getEmailSqlUpdate($value['Field'], $primaryColumn);
                }
                if (in_array($value['Field'], $passwordColumns)) {
                    $colsSql[] = $this->getPasswordSqlUpdate($value['Field']);
                }
                if (in_array($value['Field'], $phoneColumns)) {
                    $colsSql[] = $this->getPhoneSqlUpdate($value['Field']);
                }
                if (in_array($value['Field'], $ipColumns)) {
                    $colsSql[] = $this->getIpSqlUpdate($value['Field']);
                }
            }
            $query = sprintf('UPDATE %s SET %s;', $this->connection->getTableName($tableName), implode(',', $colsSql));
            $q = $this->connection->query($query);
            $this->log(sprintf('SQL: %s', $query));
            $this->log(sprintf('Number of affected rows: %s', $q->rowCount()));
        }
        $this->log('End anonymization');
    }

    public function log(string $message, bool $styled = false): void
    {
        $this->output->writeln(sprintf($styled ? '<info>%s</>' : '%s', $message));
        $this->logger->info($message);
    }

    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    public function getConfig(): array
    {
        return $this->config->getTables();
    }

    public function getPrimaryKeyColumnName(mixed $table): string
    {
        $query = sprintf('SHOW KEYS FROM %s WHERE Key_name = \'PRIMARY\';', $table);
        $primaryColumnResults = $this->connection->fetchRow($query);
        return $primaryColumnResults['Column_name'];
    }

    private function isTableExisting(string $tableName): bool
    {
        $query = sprintf('SHOW TABLES LIKE \'%s\';', $tableName);
        $result = $this->connection->fetchRow($query);
        return !empty($result);
    }

    private function getGenericStringSqlUpdate(string $field, string $primaryColumn): string
    {
        return sprintf('%s=CONCAT(%s, %s)', $field, $this->escape($field . '_'), $primaryColumn);
    }

    private function getEmailSqlUpdate(string $field, string $primaryColumn): string
    {
        return sprintf(
            '%s=CONCAT(%s, %s, %s)',
            $field,
            $this->escape('anonymized_'),
            $primaryColumn,
            $this->escape(self::ANONYMIZED_MAIL)
        );
    }

    private function getPasswordSqlUpdate(string $field): string
    {
        return sprintf(
            '%s=%s',
            $field,
            $this->escape($this->encryptor->getHash(self::ANONYMIZED_PASSWORD, true))
        );
    }

    private function getPhoneSqlUpdate(string $field): string
    {
        return sprintf('%s=%s', $field, $this->escape(self::ANONYMIZED_PHONE));
    }

    private function getIpSqlUpdate(string $field): string
    {
        return sprintf('%s=%s', $field, $this->escape(self::ANONYMIZED_IP));
    }

    private function escape(string $value): string
    {
        return sprintf('\'%s\'', $value);
    }
}
