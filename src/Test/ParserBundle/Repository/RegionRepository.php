<?php

namespace Test\ParserBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class RegionRepository
 *
 * @author Sergey Kuprianov <smoke> <sergey.kuprianow@gmail.com>
 */
class RegionRepository extends EntityRepository
{
    /**
     * Adding of the old regions to the archive
     *
     * @return mixed
     */
    public function oldRegionsToArchive() {
        $q = $this->createQueryBuilder('r')
            ->update()
            ->set('r.isArchive', true)
            ->where("r.isArchive = :isArchive")
            ->setParameter("isArchive", false);

        return $q->getQuery()->execute();
    }

    /**
     * Get all regions by "Is archive" status
     *
     * @param boolean $isArchive
     *
     * @return mixed
     */
    public function getAllRegions($isArchive = false)
    {
        $q = $this->createQueryBuilder('r')
                ->where('r.isArchive = :isArchive')
                ->setParameter('isArchive', $isArchive);

        return $q->getQuery()->execute();
    }

}
