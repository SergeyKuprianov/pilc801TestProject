<?php

namespace Test\ParserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RunsHistory
 *
 * @ORM\Table(name="history_of_parser_runs", options={"collate"="utf8_general_ci", "charset"="utf8"})
 * @ORM\Entity(repositoryClass="Test\ParserBundle\Repository\RunsHistoryRepository")
 *
 * @author Sergey Kuprianov <smoke> <sergey.kuprianow@gmail.com>
 */
class RunsHistory
{

    /**
     * Run types
     */
    const REGION_RUN_TYPE = 1;
    const ACTION_RUN_TYPE = 2;

    /**
     * Array with names of the run types
     *
     * @var array
     */
    public static $runTypesNamesArray = array(
        self::REGION_RUN_TYPE => 'Parsing of the regions',
        self::ACTION_RUN_TYPE => 'Parsing of the actions'
    );

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="run_type", type="integer", nullable=false)
     */
    private $runType;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime", nullable=false)
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set runType
     *
     * @param \DateTime $runType
     *
     * @return RunsHistory
     */
    public function setRunType($runType)
    {
        $this->runType = $runType;

        return $this;
    }

    /**
     * Get runType
     *
     * @return \DateTime
     */
    public function getRunType()
    {
        return $this->runType;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return RunsHistory
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * 
     * @return RunsHistory
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }
}
