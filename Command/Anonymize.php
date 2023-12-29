<?php
/**
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author didier <berliozd@gmail.com>
 * @copyright Copyright (c) 2020 Addeos (http://www.addeos.com)
 */

namespace Addeos\Anonymize\Command;

use Addeos\Anonymize\Helper\Anonymize as AnonymizeHelper;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class Anonymize extends Command
{
    private const CLI_OPTION_FORCE_MODE = 'force';
    private State $state;
    private array $disAllowedModes = [State::MODE_PRODUCTION];
    private AnonymizeHelper $anonymizeHelper;

    public function __construct(State $state, AnonymizeHelper $anonymizeHelper, $name = null)
    {
        parent::__construct($name);
        $this->state = $state;
        $this->anonymizeHelper = $anonymizeHelper;
    }

    protected function configure()
    {
        $this->setName('addeos:anonymize')
            ->setDescription('Anonymize the DB.')
            ->addOption(self::CLI_OPTION_FORCE_MODE, 'f', InputOption::VALUE_OPTIONAL, 'Force mode', false);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');
        if ($input->getOption(self::CLI_OPTION_FORCE_MODE) !== false) {
            $question = $this->getForcingQuestion();
            $input->setOption(
                self::CLI_OPTION_FORCE_MODE,
                $questionHelper->ask($input, $output, $question)
            );
        } else {
            $input->setOption(self::CLI_OPTION_FORCE_MODE, 'No');
        }
    }

    /**
     * @return Question
     */
    private function getForcingQuestion(): Question
    {
        return new ChoiceQuestion(
            '<question>Are you sure you want execute anonymization in force mode (No)?</question> ',
            ['No', 'Yes'],
            'No'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->anonymizeHelper->setOutput($output);
        $isDisAllowedMode = in_array($this->state->getMode(), $this->disAllowedModes);
        $isForceMode = $input->getOption(self::CLI_OPTION_FORCE_MODE) === 'Yes';
        if ($isDisAllowedMode && !$isForceMode) {
            $this->anonymizeHelper->log('Anonymization is not possible in ' . $this->state->getMode() . ' mode.');
            return Command::INVALID;
        }
        if ($isForceMode) {
            $this->anonymizeHelper->log('Executing in force mode');
        }
        $this->anonymizeHelper->anonymize();
        return Command::SUCCESS;
    }
}
