<?php

namespace Test\ParserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Test\ParserBundle\Entity\RunsHistory;

/**
 * Region
 *
 * @ORM\Table(name="regions", options={"collate"="utf8_general_ci", "charset"="utf8"})
 * @ORM\Entity(repositoryClass="Test\ParserBundle\Repository\RegionRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @author Sergey Kuprianov <smoke> <sergey.kuprianow@gmail.com>
 */
class Region
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \RunsHistory
     *
     * @ORM\ManyToOne(targetEntity="RunsHistory")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="run_history_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $runHistory;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="full_name", type="string", length=255, nullable=false)
     */
    private $fullName;

    /**
     * @ORM\Column(name="is_archive", type="boolean")
     */
    private $isArchive;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->isArchive = false;
    }

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
     * Set fullName
     *
     * @param string $fullName
     *
     * @return Region
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }


    /**
     * Set isArchive
     *
     * @param boolean $isArchive
     *
     * @return Region
     */
    public function setIsArchive($isArchive)
    {
        $this->isArchive = $isArchive;

        return $this;
    }

    /**
     * Get isArchive
     *
     * @return boolean
     */
    public function getIsArchive()
    {
        return $this->isArchive;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Region
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set runHistory
     *
     * @param \Test\ParserBundle\Entity\RunsHistory $runHistory
     * 
     * @return Region
     */
    public function setRunHistory(\Test\ParserBundle\Entity\RunsHistory $runHistory = null)
    {
        $this->runHistory = $runHistory;

        return $this;
    }

    /**
     * Get runHistory
     *
     * @return \Test\ParserBundle\Entity\RunsHistory
     */
    public function getRunHistory()
    {
        return $this->runHistory;
    }
}
