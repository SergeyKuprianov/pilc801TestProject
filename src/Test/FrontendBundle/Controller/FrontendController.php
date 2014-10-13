<?php

namespace Test\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Test\ParserBundle\Command\ParseRegionsCommand;
use Test\ParserBundle\Command\ParseActionsCommand;
use Test\ParserBundle\Lib\ParserHelper;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FrontendController
 *
 * @author Sergey Kuprianov <smoke> <sergey.kuprianow@gmail.com>
 */
class FrontendController extends Controller
{
    /**
     * Main page
     *
     * @Route("/actions/{regionId}", name="homepage", defaults={"regionId" = "all"})
     * @Template()
     */
    public function indexAction($regionId)
    {
        // Get entity manager
        $em = $this->getDoctrine()->getManager();

        // Get regions
        $regions = $em->getRepository('TestParserBundle:Region')->getAllRegions();

        // Get actions
        $paginator  = $this->get('knp_paginator');
        $actionsPagination = $paginator->paginate(
            $em->getRepository('TestParserBundle:Action')->getActionsByRegion($regionId),
            $this->get('request')->query->get('page', 1),
            5
        );

        // Get runs history
        $runsHistory = $em->getRepository('TestParserBundle:RunsHistory')->getLastRunsHistory();

        return array(
            'currentRegionId'   => $regionId,
            'regions'           => $regions,
            'actionsPagination' => $actionsPagination,
            'runsHistory'       => $runsHistory,
        );
    }

    /**
     * Start of the parser
     *
     * @Route("/startParser", name="startParser")
     */
    public function startParserAction()
    {
        // Run "Regions" parser
        $command    = new ParseRegionsCommand();
        $command->setContainer($this->container);
        $input      = new ArrayInput(array());
        $output     = new NullOutput();
        $resultCode = $command->run($input, $output);

        // Run "Actions" parser
        $command    = new ParseActionsCommand();
        $command->setContainer($this->container);
        $input      = new ArrayInput(array());
        $output     = new NullOutput();
        $resultCode = $command->run($input, $output);

        // Set JSON response
        $response = new JsonResponse();
        $response->setData(array(
            'status' => 'success'
        ));

        return $response;
    }

    /**
     * Clearing the database
     *
     * @Route("/clearDatabase", name="clearDatabase")
     */
    public function clearDatabaseAction()
    {
        // Get entity manager
        $em = $this->getDoctrine()->getManager();

        // Clear "Runs History" table
        $clearTable = $em->getRepository('TestParserBundle:RunsHistory')->removeAllHistory();

        // Remove image files
        exec("rm -rf " . ParserHelper::getRootUploadDir() . '*');

        // Set JSON response
        $response = new JsonResponse();
        $response->setData(array(
            'status' => 'success'
        ));

        return $response;
    }

    /**
     * Dump the database
     *
     * @Route("/dumpDatabase", name="dumpDatabase")
     */
    public function dumpDatabaseAction()
    {
        // Get database configs
        $databaseUser     = $this->container->getParameter('database_user');
        $databasePassword = $this->container->getParameter('database_password');
        $databaseHost     = $this->container->getParameter('database_host');
        $databaseName     = $this->container->getParameter('database_name');

        // Get tdump file name and path
        $dumpFileName = 'database_' . time() . '.sql';
        $dumpFilePath = __DIR__ . '/../../../../web/uploads/' . $dumpFileName;

        // Execute command
        $command = "mysqldump "
                        . "--user=$databaseUser "
                        . "--password=$databasePassword "
                        . "--host=$databaseHost "
                        . "$databaseName > $dumpFilePath";
        exec($command);

        // Set response
        $content  = file_get_contents($dumpFilePath);
        $response = new Response($content, 200, array(
            'content-type'        => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename=' . $dumpFileName
        ));

        return $response;
    }
}
