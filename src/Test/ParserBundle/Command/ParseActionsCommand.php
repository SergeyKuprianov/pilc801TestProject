<?php

namespace Test\ParserBundle\Command;

use Test\ParserBundle\Command\BaseParserCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Test\ParserBundle\Entity\Action;
use Test\ParserBundle\Entity\Region;
use Test\ParserBundle\Lib\ParserHelper;

/**
 * Class ParseActionsCommand
 *
 * @author Sergey Kuprianov <smoke> <sergey.kuprianow@gmail.com>
 */
class ParseActionsCommand extends BaseParserCommand
{

    /**
     * {@inheritdoc}
     */
    protected $commandName = 'parse:actions';

    /**
     * {@inheritdoc}
     */
    protected $commandDescription = 'Parsing of the actions';

    /**
     * {@inheritdoc}
     */
    protected $commandRunType = 2;

    /**
     * Array with maximum page numbers by all regions
     *
     * @var array
     */
    protected $maxPages = array();

    /**
     * Get maximum number of the page in the actions list
     *
     * @param array $regions
     */
    protected function getMaxPages($regions)
    {
        foreach ($regions as $region) {
            // Get URL of the actions list page
            $actionListPageUrl = $this->getActionsListPageUrl($region->getName(), 1);

            // Get maximum page number by current region
            $maxPage = $this->getMaxPage($actionListPageUrl);
            if ($maxPage !== false) {
                $this->maxPages[] = array(
                    'region'  => $region,
                    'maxPage' => $maxPage
                );
            }
        }
    }

    /**
     * Get max page for region
     *
     * @param string $actionListUrl
     *
     * @return integer|boolean
     */
    protected function getMaxPage($actionListUrl)
    {
        // Get HTML string
        $html = $this->sendCurlRequest($actionListUrl);

        // Get DOM by HTML string
        $dom = ParserHelper::getDomByContent($html);
        if (!$dom) {
            return false;
        }

        // Get max page
        $maxPage = 0;
        $query   = $dom->query('//form[@id="scrollerForm"]/strong');
        if ($query->length == 0) {
            $query = $dom->query('//span[@class="hdr_action"]');
            if ($query->length > 0) {
                $maxPage = 1;
            }
        } else {
            $maxPage = $query->length;
        }

        return $maxPage;
    }

    /**
     * Get URL of the actions list page
     *
     * @param string  $regionName
     * @param integer $page
     *
     * @return string
     */
    protected function getActionsListPageUrl($regionName, $page)
    {
        // Get URL for action list page with parameters
        $baseUrl             = $this->getContainer()->getParameter("test_parser.urls.base_url");
        $regionsUrl          = $this->getContainer()->getParameter("test_parser.urls.regions_url");
        $actionListUrl       = $this->getContainer()->getParameter("test_parser.urls.action_list_url");
        $actionListUrlParams = $this->getContainer()->getParameter("test_parser.urls.action_list_url_params");
        $actionListPage      = $baseUrl . $regionsUrl . $actionListUrl . $actionListUrlParams;

        return str_replace(array('{region}', '{page}'), array($regionName, $page), $actionListPage);
    }

    /**
     * {@inheritdoc}
     */
    protected function doWork(InputInterface $input, OutputInterface $output)
    {
        // Get entity manager
        $em = $this->getEm();

        // Get all regions (not archive)
        $regions = $em->getRepository("TestParserBundle:Region")->getAllRegions();

        // Check if regions are exist
        if (count($regions) == 0) {
            $output->writeln('The regions were not found.');

            return false;
        }

        // Adding of the old actions to the archive
        $em->getRepository("TestParserBundle:Action")->oldActionsToArchive();

        // Get maximum numbers of the pages in all actions lists
        $this->getMaxPages($regions);

        // Saving of the actions
        $actionsCounter = 0;
        foreach ($this->maxPages as $regionInfo) {
            for ($i = 0; $i < $regionInfo['maxPage']; $i++) {
                // Get URL of the actions list page
                $actionListPageUrl = $this->getActionsListPageUrl($regionInfo['region']->getName(), $i + 1);

                // Get html of the actions list page
                $html = $this->sendCurlRequest($actionListPageUrl);

                // Get DOM by HTML string
                $dom = ParserHelper::getDomByContent($html);
                if (!$dom) {
                    $output->writeln('DOM init failed. URL: ' . $actionListPageUrl);
                } else {
                    // Get actions list
                    $actionsList = $dom->query('//div[@id="regionB"]/table[2]/tr');
                    if ($actionsList->length > 0) {
                        foreach ($actionsList as $actionInfo) {
                            if ($this->saveActionInfo($regionInfo['region'], $actionInfo, $em)) {
                                $actionsCounter++;
                            }
                        }
                    }
                }
            }
        }

        $output->writeln('Number of the found actions: ' . $actionsCounter);
    }

    /**
     * Saving of the action information
     *
     * @param object                      $regionInfo
     * @param \DOMElement                 $actionInfo
     * @param \Doctrine\ORM\EntityManager $em
     *
     * @return boolean
     */
    protected function saveActionInfo(Region $regionInfo, \DOMElement $actionInfo, $em)
    {
        // Check if the DOM block contains the action information
        if (!$this->isAction($actionInfo)) {
            return false;
        }

        // Saving of the action
        $action = new Action();
        $action->setRunHistory($this->runHistory);
        $action->setRegion($regionInfo);
        $action->setActionName($this->parseActionName($actionInfo));
        $action->setActionUrl($this->parseActionUrl($actionInfo));

        $this->parseFileNameAndSaveFile($action, $actionInfo, $this->runHistory->getId());
        $this->parseFromAndToDates($action, $actionInfo);

        $em->persist($action);
        $em->flush();

        return true;
    }

    /**
     * Check if the action block contains the action
     *
     * @param \DOMElement $actionInfoBlock
     *
     * @return boolean
     */
    protected function isAction(\DOMElement $actionInfoBlock)
    {
        $isAction = ParserHelper::getValueByChildNodes($actionInfoBlock, '0/1/2', 'class');

        return ($isAction === 'hdr_action') ? true : false;
    }

    /**
     * Parsing of action name
     *
     * @param \DOMElement $actionInfoBlock
     *
     * @return string
     */
    protected function parseActionName(\DOMElement $actionInfoBlock)
    {
        $actionName = ParserHelper::getValueByChildNodes($actionInfoBlock, '0/1/2');

        return ($actionName) ? ParserHelper::removeSpaces($actionName) : '';
    }

    /**
     * Parsing of action URL
     *
     * @param \DOMElement $actionInfoBlock
     *
     * @return string
     */
    protected function parseActionUrl(\DOMElement $actionInfoBlock)
    {
        // Get URL for action list page
        $baseUrl            = $this->getContainer()->getParameter("test_parser.urls.base_url");
        $regionsUrl         = $this->getContainer()->getParameter("test_parser.urls.regions_url");
        $actionListUrl      = $this->getContainer()->getParameter("test_parser.urls.action_list_url");
        $baseActionListPage = $baseUrl . $regionsUrl . $actionListUrl;

        $actionUrl = ParserHelper::getValueByChildNodes($actionInfoBlock, '0/1', 'href');

        return ($actionUrl) ? $baseActionListPage . ParserHelper::clearHref($actionUrl) : '';
    }

    /**
     * Parsing of the original file name and storing of the file on the server
     *
     * @param Action      $action
     * @param \DOMElement $actionInfoBlock
     * @param integer     $runHistoryId
     */
    protected function parseFileNameAndSaveFile(Action $action, \DOMElement $actionInfoBlock, $runHistoryId)
    {
        // Get base URL
        $baseUrl = $this->getContainer()->getParameter("test_parser.urls.base_url");

        // Get original file path
        $originalFilePath = ParserHelper::getValueByChildNodes($actionInfoBlock, '0/1/1', 'src');

        if ($originalFilePath) {
            $action->setOriginalFilePath($baseUrl . $originalFilePath);

            // Store file on the server
            $fileName = ParserHelper::storeFile($baseUrl . $originalFilePath, $runHistoryId);

            $action->setInternalFileName($fileName);
        }
    }

    /**
     * Parsing of "From" and "To" dates of the action
     *
     * @param Action      $action
     * @param \DOMElement $actionInfoBlock
     */
    protected function parseFromAndToDates(Action $action, \DOMElement $actionInfoBlock)
    {
        // Get original file path
        $actionDescription = ParserHelper::getValueByChildNodes($actionInfoBlock, '0/4');

        // Set default values
        $fromAndToDates = array(
            'fromDate' => new \DateTime(),
            'toDate'   => new \Datetime()
        );

        // Parsing of the action description
        if ($actionDescription) {
            $fromAndToDates = ParserHelper::parseFromAndToDates($actionDescription);
        }

        $action->setFromDate($fromAndToDates['fromDate']);
        $action->setToDate($fromAndToDates['toDate']);
    }

}
