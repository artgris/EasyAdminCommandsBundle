<?php

namespace Artgris\Bundle\EasyAdminCommandsBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;

class ExportCommand extends Command
{
    protected static $defaultName = 'artgris:easyadmin:export';

    /**
     * @var KernelInterface
     */
    private $kernel;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var array
     */
    private $artgrisConfig;

    /**
     * @var string
     */
    private $dir;

    /**
     * ExportPageCommand constructor.
     */
    public function __construct(KernelInterface $kernel, EntityManagerInterface $em, ParameterBagInterface $parameterBag)
    {
        parent::__construct();
        $this->kernel = $kernel;
        $this->em = $em;
        $this->artgrisConfig = $parameterBag->get('artgris_easy_commands.config');
        $this->dir = $this->artgrisConfig['dir'];
    }

    protected function configure()
    {
        $this
            ->addArgument('entity', InputArgument::OPTIONAL, "Class name of the entity, override configuration parameters entities['included/excluded']");
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $filesystem = new Filesystem();

        try {
            $filesystem->mkdir($this->dir);
        } catch (IOExceptionInterface $exception) {
            $io->error('An error occurred while creating your directory at' . $exception->getPath());
        }
        $tables = $this->em->getMetadataFactory()->getAllMetadata();

        /** @var ClassMetadataInfo $table */
        $entities = $this->artgrisConfig['entities'];
        $namespaces = $this->artgrisConfig['namespaces'];
        foreach ($tables as $table) {
            if (!\in_array($table->namespace, $namespaces)) {
                continue;
            }
            if (null === $entity = $input->getArgument('entity')) {
                if (!empty($entities['included']) && !\in_array($table->name, $entities['included'])) {
                    continue;
                }
                if (!empty($entities['excluded']) && \in_array($table->name, $entities['excluded'])) {
                    continue;
                }
            } elseif ($table->getName() !== $entity) {
                continue;
            }

            $entityData = [];
            $tableName = $table->getTableName();
            $fileName = $this->dir . $tableName . '.yaml';
            $entityData['class'] = $table->getName();

            $formFields = [];
            $fieldNames = $table->fieldNames;

            $formFieldsForm = $this->sortFields($this->fieldsHandler($fieldNames, 'form'), $this->artgrisConfig['form']['position']);

            foreach ($formFieldsForm as $fieldName) {
                $field = $table->fieldMappings[$fieldName];

                // gestion en regex:
                foreach ($this->artgrisConfig['regex'] as $regex => $type) {
                    if (preg_match("/{$regex}/", $fieldName)) {
                        $field['type'] = $type;
                    }
                }

                if (isset($this->artgrisConfig['types'][$field['type']])) {
                    $formFields[] = [
                            'property' => $fieldName,
                        ] + $this->artgrisConfig['types'][$field['type']];
                } else {
                    $formFields[] = $fieldName;
                }
            }

            $sortedListFields = $this->sortFields($this->fieldsHandler($fieldNames, 'list'), $this->artgrisConfig['list']['position']);

            $entityData['list']['fields'] = $sortedListFields;
            $entityData['form'] = ['fields' => $formFields];
            $entityData['edit'] = ['fields' => $formFields];
            $entityData['new'] = ['fields' => $formFields];

            $data = ['easy_admin' => ['entities' => [$tableName => $entityData]]];
            $yaml = Yaml::dump($data, 6);
            file_put_contents($fileName, $yaml);
            $filesystem->touch($fileName, time() + 1);
        }
        $io->success('Export completed!');
    }

    private function fieldsHandler(array $fieldNames, string $view)
    {
        $includeFields = $this->artgrisConfig[$view]['included'] ? array_intersect($fieldNames, $this->artgrisConfig[$view]['included']) : $fieldNames;

        return array_values(array_diff($includeFields, $this->artgrisConfig[$view]['excluded']));
    }

    private function sortFields($fields, $positions)
    {
        return array_unique(array_merge(array_intersect($positions, $fields), $fields));
    }
}
