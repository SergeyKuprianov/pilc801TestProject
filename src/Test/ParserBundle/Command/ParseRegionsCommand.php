<?php

namespace Test\ParserBundle\Command;

use Test\ParserBundle\Command\BaseParserCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Test\ParserBundle\Entity\Region;
use Test\ParserBundle\Lib\ParserHelper;

/**
 * Class ParseRegionsCommand
 *
 * @author Sergey Kuprianov <smoke> <sergey.kuprianow@gmail.com>
 */
class ParseRegionsCommand extends BaseParserCommand
{

    /**
     * {@inheritdoc}
     */
    protected $commandName = 'parse:regions';

    /**
     * {@inheritdoc}
     */
    protected $commandDescription = 'Parsing of the regions';

    /**
     * {@inheritdoc}
     */
    protected $commandRunType = 1;

    /**
     * {@inheritdoc}
     */
    protected function doWork(InputInterface $input, OutputInterface $output)
    {
        // Get URL for regions page
        $baseUrl     = $this->getContainer()->getParameter("test_parser.urls.base_url");
        $regionsPage = $baseUrl . $this->getContainer()->getParameter("test_parser.urls.regions_url");

        // Get HTML string
        $html = $this->sendCurlRequest($regionsPage);

        // Get DOM by HTML string
        $dom = ParserHelper::getDomByContent($html);
        if (!$dom) {
            $output->writeln('DOM init failed.');

            return false;
        }

        // Get entity manager
        $em = $this->getEm();

        if ($dom) {
            $nodes = $dom->query('//div[@class="divmdash"]/span/a');

            if ($nodes->length == 0) {
                $output->writeln('Parser does not have found any regions.');

                return false;
            }

            // Adding of the old regions to the archive
            $em->getRepository("TestParserBundle:Region")->oldRegionsToArchive();

            // Saving of the new regions
            $regionCounter = 0;
            foreach ($nodes as $link) {
                // Get region name
                $regionName = $this->parseRegionName($link);

                // Get region full name
                $regionFullName = $link->nodeValue;

                // Saving of the region
                $region = new Region();
                $region->setRunHistory($this->runHistory);
                $region->setName($regionName);
                $region->setFullName($regionFullName);

                $em->persist($region);
                $em->flush();

                $regionCounter++;
            }
        }

        $output->writeln('Number of the found regions: ' . $regionCounter);
    }

    /**
     * Parsing of the region name
     *
     * @param \DOMElement $element
     *
     * @return string
     */
    protected function parseRegionName(\DOMElement $element)
    {
        // Parse url in "href" attribute
        $parsingHref     = parse_url($element->getAttribute("href"));
        $queryPartsArray = explode('=', $parsingHref['query']);

        return $queryPartsArray[count($queryPartsArray) - 1];
    }

}
