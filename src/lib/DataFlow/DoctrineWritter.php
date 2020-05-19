<?php
/**
 * @author jlchassaing <jlchassaing@gmail.com>
 * @licence MIT
 */

namespace GeoDataGouv\DataFlow;


use CodeRhapsodie\DataflowBundle\DataflowType\Writer\WriterInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Repository\RepositoryFactory;

class DoctrineWritter implements WriterInterface
{
    /** @var \Doctrine\ORM\EntityManager  */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function prepare()
    {
        // TODO: Implement prepare() method.
    }

    public function write(\AbstractLocationEntity $locationEntity)
    {
        $this->entityManager->persist($locationEntity);
    }

    public function finish()
    {
        $this->entityManager->commit();
    }


}