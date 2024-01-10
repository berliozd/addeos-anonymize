# Addeos Anonymize Extension User Guide

A magento 2 module that let you anonymize the private data that are present in your MySql database using a specific CLI
command.

# Table of Contents
1. [Introduction ](#introduction)
2. [Installation ](#installation)
3. [Enabling the extension ](#enabling-the-extension)
4. [Configuration ](#configuration)
5. [CLI command ](#cli-command)
6. [List of tables anonymized ](#list-of-tables-anonymized)
7. [Running in developer mode ](#running-in-developer-mode)
8. [Running in Force mode (production) ](#running-in-force-mode-production)
9. [Troubleshooting ](#troubleshooting)
10. [Support and contact ](#support-and-contact)

# Introduction
The Addeos Anonymize Extension is designed to help Magento store owners comply with GDPR regulations by providing a simple and efficient way to anonymize sensitive customer data on local environments. This extension ensures that personal information in specified database tables is anonymized, making it suitable for development and testing environments.

# Installation
To install the Addeos Anonymize Extension, follow these steps:

```bash
composer require addeos/anonymize
php bin/magento module:enable Addeos_Anonymize
php bin/magento setup:upgrade
```

# Enabling the Extension
After installation, enable the extension using the following command:

```bash
php bin/magento module:enable Addeos_Anonymize
php bin/magento setup:upgrade
```

# Configuration
The extension doesn't require any specific onfigurations.

# CLI Command
Once the extension is enabled, a new CLI command becomes available:

```bash
php bin/magento addeos:anonymize
```
Use this command to initiate the anonymization process.

# List of Tables Anonymized
The Addeos Anonymize Extension anonymizes data in the following database tables:

- customer_entity
- customer_address_entity 
- customer_grid_flat 
- email_contact 
- newsletter_subscriber 
- paradoxlabs_stored_card 
- quote 
- quote_address 
- sales_creditmemo_grid 
- sales_invoice_grid 
- sales_order 
- sales_order_address 
- sales_order_grid 
- sales_shipment_grid 
- stripe_customers

Ensure that you have a backup of your data before running the anonymization process.

# Running in Developer Mode
The extension can only be executed on a Magento installation in developer mode. Ensure that your Magento environment is set to developer mode before running the anonymization command.

Logs are available in a log file named addeos-anonymize.log.

# Running in Force Mode (Production)

In a production environment, you can run the extension in force mode by adding the -f or --force parameter to the command:

```bash
php bin/magento addeos:anonymize -f
```
Note: Exercise caution when using force mode on a production Magento installation, and always have a backup of your data.

# Troubleshooting
If you encounter any issues during installation or usage, consider the following troubleshooting steps:

- Check the Magento logs for error messages. 
- Ensure that the extension is properly enabled using the `php bin/magento module:status command. 
- Verify that the CLI command syntax is correct.

# Support and Contact
For any further assistance, reach out to our support team at didier@addeos.com. We are here to help you with any questions or concerns regarding the Addeos Anonymize Extension.

Thank you for choosing Addeos to enhance your Magento experience!







