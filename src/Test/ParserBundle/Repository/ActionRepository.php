<?php

namespace Test\ParserBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class ActionRepository
 *
 * @author Sergey Kuprianov <smoke> <sergey.kuprianow@gmail.com>
 */
class ActionRepository extends EntityRepository
{
    /**
     * Adding of the old regions to the archive
     *
     * @return mixed
     */
    public function oldActionsToArchive() {
        $q = $this->createQueryBuilder('a')
            ->update()
            ->set('a.isArchive', true)
            ->where("a.isArchive = :isArchive")
            ->setParameter("isArchive", false);

        return $q->getQuery()->execute();
    }

    /**
     * Get actions by region
     *
     * @param integer|string $regionId
     *
     * @return Query
     */
    public function getActionsByRegion($regionId)
    {
        $q = $this->createQueryBuilder('a')
                ->leftJoin('a.region', 'r')
                ->andWhere('a.isArchive = :isActionArchive')
                ->andWhere('r.isArchive = :isRegionArchive')
                ->setParameter('isActionArchive', false)
                ->setParameter('isRegionArchive', false);

        if ($regionId != 'all') {
            $q = $q->andWhere('a.region = :regionId')
                    ->setParameter('regionId', $regionId);
        }

        return $q->getQuery();
    }

}
