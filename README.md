# addeos-anonymize

A magento 2 module that let you anonymize the private data that are present in your MySql database using a specific CLI
command.

More information here : https://www.addeos.com/magento-2-database-anonymization-module

## Installation

Simply execute the command below to install the module code in you magento application vendor folder.

``composer require addeos/anonymize``

Then execute the following magento command to enable the module.

``bin/magento module:enable Addeos_Anonymize``

## User guide

Once installed and enabled, you can simply execute the new command:

```bin/magento addeos:anonymize```

If magento is installed in developer mode, the command will run smoothly.

If magento is installed in production mode, you will need to pass and additional parameter -f and confirm that you
want to run it.

```bin/magento addeos:anonymize -f```

And that's all you need to know.
