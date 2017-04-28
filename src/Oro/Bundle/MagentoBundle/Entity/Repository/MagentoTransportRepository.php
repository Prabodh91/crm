<?php

namespace Oro\Bundle\MagentoBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Oro\Bundle\MagentoBundle\Provider\Iterator\StoresSoapIterator;

class MagentoTransportRepository extends EntityRepository
{
    /**
     * @param array $criteria
     * @return array
     *
     * This method is used by UniqueEntityValidator for MagentoTransport entity.
     * Entity is not unique if there is already at least one entity
     * with such wsdl_url and such websiteId or websiteId that represent all web sites for
     * corresponding wsdl_url(-1)
     */
    public function getUniqueByWsdlUrlAndWebsiteIds(array $criteria)
    {
        if (!isset($criteria['apiUrl'], $criteria['websiteId'])) {
            throw new \InvalidArgumentException('apiUrl and websiteId must be in $criteria');
        }
        $parameters = ['apiUrl' => $criteria['apiUrl']];
        $query = $this->createQueryBuilder('t')
            ->select('t')
            ->where('t.apiUrl = :apiUrl');

        if ($criteria['websiteId'] !== StoresSoapIterator::ALL_WEBSITES) {
            $query->andWhere('t.websiteId IN (:websiteIds)');
            $parameters['websiteIds'] = [StoresSoapIterator::ALL_WEBSITES, $criteria['websiteId']];
        }

        return $query->setParameters($parameters)->getQuery()->getResult();
    }
}