<?php
/**
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author didier <didier@addeos.com>
 * @copyright Copyright (c) 2024 Addeos (http://www.addeos.com)
 */

namespace Addeos\Anonymize\Command;

use Addeos\Anonymize\Model\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class Info extends Command
{
    private Config $config;

    public function __construct(Config $config, $name = null)
    {
        parent::__construct($name);
        $this->config = $config;
    }

    protected function configure(): void
    {
        $this->setName('addeos:anonymize:info')
            ->setDescription('Show tables that will be anonymized.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->showTables($output);
        return Command::SUCCESS;
    }

    private function showTables(OutputInterface $output): void
    {
        $this->showItems($output, 'Tables that will be anonymized : ', 'Table name', $this->config->getTables());
        $this->showItems(
            $output,
            'Columns treated as generic strings :',
            'Column name',
            $this->config->getGenericStringColumns()
        );
        $this->showItems($output, 'Columns treated as emails : ', 'Column name', $this->config->getEmailColumns());
        $this->showItems($output, 'Columns treated as password : ', 'Column name', $this->config->getPasswordColumns());
        $this->showItems(
            $output,
            'Columns treated as phone numbers : ',
            'Column name',
            $this->config->getPhoneColumns()
        );
        $this->showItems($output, 'Columns treated as IP addresses : ', 'Column name', $this->config->getIpColumns());
    }

    private function showItems(OutputInterface $output, string $title, string $colTitle, array $items): void
    {
        $output->writeln(['', sprintf('<info>%s</>', $title), '',]);
        $table = new Table($output);
        $table->setHeaders([$colTitle]);
        foreach ($items as $item) {
            $table->addRow([$item]);
        }
        $table->render();
    }
}
