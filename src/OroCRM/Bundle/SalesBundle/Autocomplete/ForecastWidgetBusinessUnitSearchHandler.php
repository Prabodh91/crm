<?php

namespace OroCRM\Bundle\SalesBundle\Autocomplete;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Oro\Bundle\OrganizationBundle\Autocomplete\BusinessUnitSearchHandler;
use Oro\Bundle\OrganizationBundle\Provider\BusinessUnitAclProvider;

class ForecastWidgetBusinessUnitSearchHandler extends BusinessUnitSearchHandler
{
    /** @var BusinessUnitAclProvider */
    protected $businessUnitAclProvider;

    /** @var string */
    protected $opportunityClassName;

    /**
     * @param string $entityName
     * @param array $properties
     * @param Registry $doctrine
     * @param BusinessUnitAclProvider $businessUnitAclProvider
     * @param string $opportunityClassName
     */
    public function __construct(
        $entityName,
        $properties,
        $doctrine,
        BusinessUnitAclProvider $businessUnitAclProvider,
        $opportunityClassName
    ) {
        parent::__construct($entityName, $properties, $doctrine);
        $this->businessUnitAclProvider = $businessUnitAclProvider;
        $this->opportunityClassName = $opportunityClassName;
    }

    /**
     * @param string $search
     * @param int    $firstResult
     * @param int    $maxResults
     * @return array
     */
    protected function searchIds($search, $firstResult, $maxResults)
    {
        $ids = [];
        $allowedBusinessUnitIds = $this
            ->businessUnitAclProvider
            ->getBusinessUnitIds($this->opportunityClassName, 'VIEW');

        if (!is_array($allowedBusinessUnitIds) || count($allowedBusinessUnitIds) === 0) {
            return $ids;
        }

        $this->indexer->setIsAllowedApplyAcl(false);
        $result   = $this->indexer->simpleSearch($search, $firstResult, $maxResults, $this->entitySearchAlias);
        $elements = $result->getElements();

        foreach ($elements as $element) {
            $recordId = (int)$element->getRecordId();
            if (in_array($recordId, $allowedBusinessUnitIds, true)) {
                $ids[] = $element->getRecordId();
            }
        }

        return $ids;
    }
}
