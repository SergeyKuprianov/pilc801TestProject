<?php

namespace Test\ParserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Test\ParserBundle\Entity\RunsHistory;

/**
 * Class RunsHistoryRepository
 *
 * @author Sergey Kuprianov <smoke> <sergey.kuprianow@gmail.com>
 */
class RunsHistoryRepository extends EntityRepository
{
    /**
     * Get info about last 5 runs
     *
     * @return mixed
     */
    public function getLastRunsHistory()
    {
        $q = $this->createQueryBuilder('rh')
                ->where('rh.runType = :runType')
                ->orderBy('rh.endDate', 'DESC')
                ->setMaxResults(5)
                ->setParameter('runType', RunsHistory::ACTION_RUN_TYPE);

        return $q->getQuery()->execute();
    }

    /**
     * Remove all history of runs
     *
     * @return mixed
     */
    public function removeAllHistory()
    {
        $q = $this->createQueryBuilder('rh')
                ->delete();

        return $q->getQuery()->execute();
    }

}
