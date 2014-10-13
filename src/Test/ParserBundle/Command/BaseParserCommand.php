<?php

namespace Test\ParserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Test\ParserBundle\Entity\RunsHistory;

/**
 * Class BaseParserCommand
 *
 * @author Sergey Kuprianov <smoke> <sergey.kuprianow@gmail.com>
 */
abstract class BaseParserCommand extends ContainerAwareCommand
{

    /**
     * Command name
     *
     * @var string
     */
    protected $commandName;

    /**
     * Command description
     *
     * @var string
     */
    protected $commandDescription;

    /**
     * Command run type
     *
     * @var integer
     */
    protected $commandRunType;

    /**
     * @var RunsHistory
     */
    protected $runHistory;

    /**
     * Command parse logic
     *
     * @return mixed
     */
    abstract protected function doWork(InputInterface $input, OutputInterface $output);

    /**
     * Configures the current command
     */
    protected function configure()
    {
        $this
            ->setName($this->getCommandName())
            ->setDescription($this->getCommandDescription())
        ;
    }

    /**
     * Executes the current command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Save start date of the parser run
        $this->start();

        // Main processing of the parser
        $this->doWork($input, $output);

        // Save end date of the parser run
        $this->finish();
    }

    /**
     * Save star date of the parsing
     *
     * @return boolean
     */
    protected function start()
    {
        // Get entity manager
        $em = $this->getEm();

        // Adding of the new run history
        $this->runHistory = new RunsHistory();
        $this->runHistory->setRunType($this->getCommandRunType());
        $this->runHistory->setStartDate(new \DateTime());

        $em->persist($this->runHistory);
        $em->flush();

        return true;
    }

    /**
     * Save end date of the parsing
     *
     * @return boolean
     */
    protected function finish()
    {
        // Get entity manager
        $em = $this->getEm();

        // Adding of the end date
        $this->runHistory->setEndDate(new \DateTime());

        $em->persist($this->runHistory);
        $em->flush();

        return true;
    }

    /**
     * Sending of the cURL request
     *
     * @param string $url
     *
     * @return boolean|string
     */
    protected function sendCurlRequest($url)
    {
        // Prepare cURL request
        $curl = $this->getAnchovyCurl()->setURL($url);

        // Make cURL request
        $html = $curl->execute();

        // Get cURL info
        $info = $curl->getInfo();

        // Check HTTP code
        if ($info['http_code'] !== 200) {
            return false;
        }

        return $html;
    }

    /**
     * Get command name
     *
     * @return string
     */
    protected function getCommandName()
    {
        return $this->commandName;
    }

    /**
     * Get command description
     *
     * @return string
     */
    protected function getCommandDescription()
    {
        return $this->commandDescription;
    }

    /**
     * Get command run type
     *
     * @return string
     */
    protected function getCommandRunType()
    {
        return $this->commandRunType;
    }

    /**
     * Get Anchovy Curl
     *
     * @return \Anchovy\CURLBundle\CURL\Curl
     */
    protected function getAnchovyCurl()
    {
        return $this->getContainer()->get('anchovy.curl');
    }

    /**
     * Get Entity Manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEm()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

}
