<?php
/**
 * @author jlchassaing <jlchassaing@gmail.com>
 * @licence MIT
 */

namespace GeoDataGouv\DataFlowType;

use CodeRhapsodie\DataflowBundle\DataflowType\AbstractDataflowType;
use CodeRhapsodie\DataflowBundle\DataflowType\DataflowBuilder;
use CodeRhapsodie\DataflowBundle\DataflowType\DataflowTypeInterface;
use CodeRhapsodie\DataflowBundle\DataflowType\Result;
use CodeRhapsodie\EzDataflowBundle\Factory\ContentStructureFactory;
use CodeRhapsodie\EzDataflowBundle\Factory\ContentStructureFactoryInterface;
use CodeRhapsodie\EzDataflowBundle\Writer\ContentWriter;
use GeoDataGouv\Config\ConfigManager;
use GeoDataGouv\DataFlow\FileReader;
use GeoDataGouv\DataFlow\GeocodingFileReader;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DataGouvImportLocationsDataFlowType extends AbstractDataflowType implements DataflowTypeInterface
{

    /** @var \GeoDataGouv\DataFlow\FileReader  */
    protected $fileReader;

    /** @var \GeoDataGouv\DataFlow\GeocodingFileReader  */
    protected $geocodingFileReader;

    /** @var \GeoDataGouv\Config\ConfigManager  */
    protected $configManager;

    /**
     * DataGouvImportLocationsDataFlowType constructor.
     *
     * @param \GeoDataGouv\DataFlow\FileReader $fileReader
     * @param \GeoDataGouv\DataFlow\GeocodingFileReader $geocodingFileReader
     * @param \GeoDataGouv\Config\ConfigManager $configManager
     */
    public function __construct(
        FileReader $fileReader,
        GeocodingFileReader $geocodingFileReader,
        ConfigManager $configManager)
    {
        $this->fileReader = $fileReader;
        $this->geocodingFileReader = $geocodingFileReader;
        $this->configManager = $configManager;
    }

    public function getLabel(): string
    {
        return 'DataGouv Import';
    }

    public function getAliases(): iterable
    {
        return ['dtgi'];
    }

    protected function buildDataflow(DataflowBuilder $builder, array $options): void
    {
        $resourceName = $options['resource'];
        $config = $this->configManager->getResource($resourceName);

        $builder->setReader($this->getReader($config)->read($options['csv-source']));
        $this->addFilterTask($builder);
        $this->addCsvFieldMapping($builder, $options, $config);
        $builder->addWriter($this->contentWriter);
    }

    /**
     * Use this methods to manage fields in data before creating content
     * one usecase could be to format the naming field
     *
     *
     * @param \CodeRhapsodie\DataflowBundle\DataflowType\DataflowBuilder $builder
     */
    protected function addFilterTask(DataflowBuilder $builder)
    {
        $builder->addStep(function ($data){
            /** Put your code here to invalidate a row return null */
            return $data;
        });
    }

    protected function addCsvFieldMapping(DataflowBuilder $builder, $options, $config)
    {
        $builder->addStep(function ($data) use ($config,$options) {

            if (!isset($data[$config['id_key']])) {
                return false;
            }

            $remoteId = sprintf('%s-%d', $config['content_type'],$data[$config['id_key']]);

            $contentData['name'] = $data[$config['name']];
            $contentData[$config['address_field_identifier']] = [
                'longitude' => (float) $data[$config['address']['longitude']],
                'latitude' => (float) $data[$config['address']['latitude']],
                'address' => $data[$config['address']['address']],
            ];

            foreach ($config['fields'] as $key=>$field) {
                switch ($field['datatype']){
                    case 'ezurl':
                        $contentData[$key] = ['link' => $data[$field['value']]];
                        break;
                    default:
                        $contentData[$key] = $data[$field['value']];
                }
            }

            return $this->contentStructureFactory->transform(
                $contentData,
                $remoteId,
                $config['language'],
                $config['content_type'],
                $options['parent-location-id']
            );
        });
    }

    protected function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(['csv-source' => '', 'parent-location-id' => null, 'resource']);
        $optionsResolver->setRequired(['csv-source', 'parent-location-id', 'resource']);
    }

    /**
     * @param bool $do_geocoding
     *
     * @return \GeoDataGouv\DataFlow\ReaderInterface
     */
    protected function getReader($resourceConfig)
    {
        if ($resourceConfig['do_geocoding'] === true)
            return $this->geocodingFileReader->init($resourceConfig);
        else
            return $this->fileReader->init($resourceConfig);
    }
}