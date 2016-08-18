<?php

namespace Shopware\Commands\SwagImportExport;

use Shopware\Commands\ShopwareCommand;
use Shopware\Components\SwagImportExport\Profile\Profile;
use Shopware\CustomModels\ImportExport\Profile as ProfileEntity;
use Shopware\CustomModels\ImportExport\Repository;
use Shopware_Plugins_Backend_SwagImportExport_Bootstrap as SwagImportExport_Bootstrap;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Shopware\Components\SwagImportExport\Utils\CommandHelper;

class ImportCommand extends ShopwareCommand
{
    protected $profile;
    protected $profileEntity;
    protected $format;
    protected $filePath;
    protected $sessionId;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('sw:importexport:import')
            ->setDescription('Import data from files.')
            ->addArgument('filepath', InputArgument::REQUIRED, 'Path to file to read from.')
            ->addOption('profile', 'p', InputOption::VALUE_REQUIRED, 'Which profile will be used?', null)
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'What is the format of the imported file - XML or CSV?', null)
            ->setHelp("The <info>%command.name%</info> imports data from a file.");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->prepareImportInputValidation($input);

        $this->registerErrorHandler($output);

        $this->start($output, $this->profileEntity, $this->filePath, $this->format);

        $profilesMapper = array('articles', 'articlesImages');

        //loops the unprocessed data
        $pathInfo = pathinfo($this->filePath);
        foreach ($profilesMapper as $profileName) {
            $tmpFileName = 'media/unknown/' . $pathInfo['filename'] . '-' . $profileName . '-tmp.csv';
            $tmpFile = Shopware()->DocPath() . $tmpFileName;

            if (file_exists($tmpFile)) {
                $outputFile = str_replace('-tmp', '-swag', $tmpFile);
                rename($tmpFile, $outputFile);

                /** @var Profile $profile */
                $profile = $this->getPlugin()->getProfileFactory()->loadHiddenProfile($profileName);
                /** @var ProfileEntity $profileEntity */
                $profileEntity = $profile->getEntity();

                $this->start($output, $profileEntity, $outputFile, 'csv');
            }
        }
    }

    /**
     *
     * @param OutputInterface $output
     * @param ProfileEntity $profileModel
     * @param string $file
     * @param string $format
     */
    protected function start(OutputInterface $output, $profileModel, $file, $format)
    {
        $helper = new CommandHelper(
            array(
                'profileEntity' => $profileModel,
                'filePath' => $file,
                'format' => $format,
                'username' => 'Commandline'
            )
        );

        $output->writeln('<info>' . sprintf("Using profile: %s.", $profileModel->getName()) . '</info>');
        $output->writeln('<info>' . sprintf("Using format: %s.", $format) . '</info>');
        $output->writeln('<info>' . sprintf("Using file: %s.", $file) . '</info>');

        $return = $helper->prepareImport();
        $count = $return['count'];
        $output->writeln('<info>' . sprintf("Total count: %d.", $count) . '</info>');

        $return = $helper->importAction();
        $position = $return['data']['position'];
        $output->writeln('<info>' . sprintf("Processed: %d.", $position) . '</info>');

        while ($position < $count) {
            $return = $helper->importAction();
            $position = $return['data']['position'];
            $output->writeln('<info>' . sprintf("Processed: %d.", $position) . '</info>');
        }
    }

    /**
     * @param InputInterface $input
     * @throws \Exception
     */
    protected function prepareImportInputValidation(InputInterface $input)
    {
        $this->profile = $input->getOption('profile');
        $this->format = $input->getOption('format');
        $this->filePath = $input->getArgument('filepath');

        $parts = explode('.', $this->filePath);

        // get some service from container (formerly Shopware()->Bootstrap()->getResource())
        $em = $this->container->get('models');

        /** @var Repository $profileRepository */
        $profileRepository = $em->getRepository('Shopware\CustomModels\ImportExport\Profile');

        // if no profile is specified try to find it from the filename
        if ($this->profile === null) {
            foreach ($parts as $part) {
                $part = strtolower($part);
                $this->profileEntity = $profileRepository->findOneBy(array('name' => $part));
                if ($this->profileEntity !== null) {
                    $this->profile = $part;
                    break;
                }
            }
        } else {
            $this->profileEntity = $profileRepository->findOneBy(array('name' => $this->profile));
        }

        // validate profile
        if (!$this->profileEntity) {
            throw new \Exception(sprintf('Invalid profile: \'%s\'!', $this->profile));
        }

        // if no format is specified try to find it from the filename
        if (empty($this->format)) {
            $this->format = pathinfo($this->filePath, PATHINFO_EXTENSION);
        }

        // format should be case insensitive
        $this->format = strtolower($this->format);

        // validate type
        if (!in_array($this->format, array('csv', 'xml'))) {
            throw new \Exception(sprintf('Invalid format: \'%s\'! Valid formats are: CSV and XML.', $this->format));
        }

        // validate path
        if (!file_exists($this->filePath)) {
            throw new \Exception(sprintf('File \'%s\' not found!', $this->filePath));
        }
    }

    /**
     * @return SwagImportExport_Bootstrap
     */
    private function getPlugin()
    {
        return Shopware()->Plugins()->Backend()->SwagImportExport();
    }
}
