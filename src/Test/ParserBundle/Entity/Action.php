<?php

namespace Test\ParserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Test\ParserBundle\Entity\Region;
use Test\ParserBundle\Entity\RunsHistory;

/**
 * Action
 *
 * @ORM\Table(name="actions", options={"collate"="utf8_general_ci", "charset"="utf8"})
 * @ORM\Entity(repositoryClass="Test\ParserBundle\Repository\ActionRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @author Sergey Kuprianov <smoke> <sergey.kuprianow@gmail.com>
 */
class Action
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
     * @var \Region
     *
     * @ORM\ManyToOne(targetEntity="Region")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="region_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $region;

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
     * @var \DateTime
     *
     * @ORM\Column(name="from_date", type="datetime", nullable=false)
     */
    private $fromDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="to_date", type="datetime", nullable=false)
     */
    private $toDate;

    /**
     * @var string
     *
     * @ORM\Column(name="action_name", type="string", length=255, nullable=false)
     */
    private $actionName;

    /**
     * @var string
     *
     * @ORM\Column(name="action_url", type="string", length=255, nullable=false)
     */
    private $actionUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="original_file_path", type="string", length=500, nullable=true)
     */
    private $originalFilePath;

    /**
     * @var string
     *
     * @ORM\Column(name="internal_file_name", type="string", length=500, nullable=true)
     */
    private $internalFileName;

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
     * Set fromDate
     *
     * @param \DateTime $fromDate
     *
     * @return Action
     */
    public function setFromDate($fromDate)
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    /**
     * Get fromDate
     *
     * @return \DateTime
     */
    public function getFromDate()
    {
        return $this->fromDate;
    }

    /**
     * Set toDate
     *
     * @param \DateTime $toDate
     *
     * @return Action
     */
    public function setToDate($toDate)
    {
        $this->toDate = $toDate;

        return $this;
    }

    /**
     * Get toDate
     *
     * @return \DateTime
     */
    public function getToDate()
    {
        return $this->toDate;
    }

    /**
     * Set originalFilePath
     *
     * @param string $originalFilePath
     *
     * @return Action
     */
    public function setOriginalFilePath($originalFilePath)
    {
        $this->originalFilePath = $originalFilePath;

        return $this;
    }

    /**
     * Get originalFilePath
     *
     * @return string
     */
    public function getOriginalFilePath()
    {
        return $this->originalFilePath;
    }

    /**
     * Set internalFileName
     *
     * @param string $internalFileName
     *
     * @return Action
     */
    public function setInternalFileName($internalFileName)
    {
        $this->internalFileName = $internalFileName;

        return $this;
    }

    /**
     * Get internalFileName
     *
     * @return string
     */
    public function getInternalFileName()
    {
        return $this->internalFileName;
    }

    /**
     * Get web path for internal file
     *
     * @return null|string
     */
    public function getInternalFileWebPath() {
        return null === $this->internalFileName ? null : $this->getUploadDir() . '/' . $this->internalFileName;
    }

    /**
     * Get upload directory
     *
     * @return string
     */
    protected function getUploadDir() {
        return 'uploads/images/' . $this->getRunHistory()->getId();
    }

    /**
     * Set isArchive
     *
     * @param boolean $isArchive
     *
     * @return Action
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
     * @ORM\PrePersist
     */
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime();
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
     * Set region
     *
     * @param \Test\ParserBundle\Entity\Region $region
     *
     * @return Action
     */
    public function setRegion(\Test\ParserBundle\Entity\Region $region = null)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return \Test\ParserBundle\Entity\Region
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set actionName
     *
     * @param string $actionName
     *
     * @return Action
     */
    public function setActionName($actionName)
    {
        $this->actionName = $actionName;

        return $this;
    }

    /**
     * Get actionName
     *
     * @return string
     */
    public function getActionName()
    {
        return $this->actionName;
    }

    /**
     * Set actionUrl
     *
     * @param string $actionUrl
     *
     * @return Action
     */
    public function setActionUrl($actionUrl)
    {
        $this->actionUrl = $actionUrl;

        return $this;
    }

    /**
     * Get actionUrl
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->actionUrl;
    }

    /**
     * Set runHistory
     *
     * @param \Test\ParserBundle\Entity\RunsHistory $runHistory
     * 
     * @return Action
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
