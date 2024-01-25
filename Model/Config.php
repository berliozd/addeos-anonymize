<?php

namespace Addeos\Anonymize\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    const TABLES_XPATH = 'addeos_anonymize/general/tables';
    const GENERIC_STRING_COLUMNS_XPATH = 'addeos_anonymize/general/generic_string_columns';
    const PHONE_COLUMNS_XPATH = 'addeos_anonymize/general/phone_columns';
    const PASSWORD_COLUMNS_XPATH = 'addeos_anonymize/general/password_columns';
    const EMAIL_COLUMNS_XPATH = 'addeos_anonymize/general/email_columns';
    const IP_COLUMNS_XPATH = 'addeos_anonymize/general/ip_columns';

    private ScopeConfigInterface $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getTables(): array
    {
        return explode(',', trim($this->scopeConfig->getValue(self::TABLES_XPATH)));
    }


    public function getGenericStringColumns(): array
    {
        return explode(',', trim($this->scopeConfig->getValue(self::GENERIC_STRING_COLUMNS_XPATH)));
    }

    public function getPhoneColumns(): array
    {
        return explode(',', trim($this->scopeConfig->getValue(self::PHONE_COLUMNS_XPATH)));
    }

    public function getPasswordColumns(): array
    {
        return explode(',', trim($this->scopeConfig->getValue(self::PASSWORD_COLUMNS_XPATH)));
    }

    public function getEmailColumns(): array
    {
        return explode(',', trim($this->scopeConfig->getValue(self::EMAIL_COLUMNS_XPATH)));
    }

    public function getIpColumns(): array
    {
        return explode(',', trim($this->scopeConfig->getValue(self::IP_COLUMNS_XPATH)));
    }
}
