<?php
    namespace Lemish\Cli\Console\Command;

    use Symfony\Component\Console\Command\Command;
    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Input\InputOption;
    use Symfony\Component\Console\Output\OutputInterface;

    use Symfony\Component\Console\Helper\Table;
    use Magento\Framework\App\Helper\Context;
    use Magento\Framework\App\ResourceConnection;
    use Magento\Framework\App\ObjectManager;

    /**
     * Class BuildQueryCommand
     */
    class BuildQueryCommand extends Command
    {
        const QUERY = 'query';


        protected $resourceConnection;

        public function __construct(ResourceConnection $resourceConnection)
        {
            $this->resourceConnection = $resourceConnection;
            parent::__construct();
        }

        public function runSqlQuery($query)
        {
            $connection = $this->resourceConnection->getConnection();
            $result = $connection->fetchAll($query);

            return $result;
        }


        protected function configure()
        {
            $this->setName('mysql:query:run');
            $this->setDescription('MySQL commands for Magento CLI');

            $this->addOption(
                self::QUERY,
                null,
                InputOption::VALUE_REQUIRED,
                'Query'
            );

            parent::configure();
        }

        /**
         * Execute the command
         *
         * @param InputInterface $input
         * @param OutputInterface $output
         *
         * @return null|int
         */
        protected function execute(InputInterface $input, OutputInterface $output)
        {
            try {
                if ($query = $input->getOption(self::QUERY)) {
                    $result = $this->runSqlQuery($query);

                    if ($headers = count($result) > 0 ? array_keys($result[0]) : []){

                        $table = new Table($output);
                        $table->setHeaders($headers);

                        foreach ($result as $row){
                            $table->addRow(
                                array_values($row)
                            );
                        }

                        $table->render($output);
                    } else {
                        $output->writeln('<info>Empty result</info>');
                    }
                }

            } catch(\Exception $e) {
                $output->writeln('<error>' . $e->getMessage() . '</error>');
                return;
            }
        }
    }
