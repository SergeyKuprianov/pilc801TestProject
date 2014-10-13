<?php

namespace Test\ParserBundle\Lib;

/**
 * Class ParserHelper
 *
 * @author Sergey Kuprianov <smoke> <sergey.kuprianow@gmail.com>
 */
class ParserHelper
{

    /**
     * @var array
     */
    protected static $russianMonths = array(
        'января'   => '01',
        'февраля'  => '02',
        'марта'    => '03',
        'апреля'   => '04',
        'мая'      => '05',
        'июня'     => '06',
        'июля'     => '07',
        'августа'  => '08',
        'сентября' => '09',
        'октября'  => '10',
        'ноября'   => '11',
        'декабря'  => '12',
    );

    /**
     * Get value by child node
     *
     * @param \DOMElement $baseNode
     * @param string      $patch
     * @param string      $attribute
     *
     * @return null|string
     */
    public static function getValueByChildNodes(\DOMElement $baseNode, $patch, $attribute = null)
    {
        $patch = explode('/', $patch);

        $node  = $baseNode;
        $child = null;
        foreach ($patch as $index) {
            if (!property_exists($node, 'childNodes') || !method_exists($node->childNodes, 'item')) {
                return null;
            }
            $child = $node->childNodes->item($index);
            if ($child) {
                $node = $child;
            }
        }

        if ($attribute && $child && !method_exists($child, 'getAttribute')) {
            return null;
        }

        return ($child) ? (($attribute) ? $child->getAttribute($attribute) : $child->nodeValue) : null;
    }

    /**
     * Get DOM by html string
     *
     * @param string &$str
     * @param string $encoding
     *
     * @return bool|\DOMXpath
     */
    public static function getDomByContent(&$str, $encoding = 'UTF-8')
    {
        $dom = new \DOMDocument('1.0', $encoding);
        if ($encoding === 'UTF-8') {
            $str = mb_convert_encoding($str, 'HTML-ENTITIES', $encoding);
        }

        if (empty($str)) {
            return false;
        }

        try {
            libxml_use_internal_errors(true);
            $dom->loadHTML($str);
            libxml_clear_errors();

            return new \DomXPath($dom);
        } catch (\Exception $e) {

        }

        return false;
    }

    /**
     * Remove spaces
     *
     * @param string $str
     *
     * @return string
     */
    public static function removeSpaces($str)
    {
        $str = preg_replace('/\s\s+/', ' ', $str);
        $str = trim($str, ' ');

        return $str;
    }

    /**
     * Clear the "href" attribute
     *
     * @param string $str
     *
     * @return string
     */
    public static function clearHref($str)
    {
        $parsingUrl = parse_url($str);

        return (isset($parsingUrl['query'])) ? $parsingUrl['query'] : '';
    }

    /**
     * Store file by URL
     *
     * @param string  $originalFilePath
     * @param integer $runHistoryId
     *
     * @return boolean|string
     */
    public static function storeFile($originalFilePath, $runHistoryId)
    {
        try {
            // Get file name
            $parseOriginalFilePath = explode('/', $originalFilePath);
            $fileName              = $parseOriginalFilePath[count($parseOriginalFilePath) - 1];

            // Store the file on the server
            $data   = file_get_contents($originalFilePath);
            $handle = fopen(self::getUploadDir($runHistoryId) . $fileName, "w");
            fwrite($handle, $data);
            fclose($handle);

            return $fileName;
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }

        return false;
    }

    /**
     * Get upload path
     *
     * @param integer $runHistoryId
     *
     * @return string
     */
    public static function getUploadDir($runHistoryId)
    {
        $uploadDir = self::getRootUploadDir();

        if(!array_search($runHistoryId, scandir($uploadDir))) {
            mkdir($uploadDir . $runHistoryId);
            chmod($uploadDir . $runHistoryId, 0777);
        }

        return $uploadDir . $runHistoryId . '/';
    }

    /**
     * Geet root upload directory name
     *
     * @return string
     */
    public static function getRootUploadDir()
    {
        return __DIR__ . '/../../../../web/uploads/images/';
    }

    /**
     * Parsing of "From" and "To" dates
     *
     * @param string $str
     *
     * @return array
     */
    public static function parseFromAndToDates($str)
    {
        $matches = array();

        // Set default values
        $fromAndToDates = array(
            'fromDate' => new \DateTime(),
            'toDate'   => new \Datetime()
        );

        if (preg_match('/С (.*) г. по (.*) г./', $str, $matches)) {
            $fromAndToDates['fromDate'] = new \DateTime($matches[1]);
            $fromAndToDates['toDate']   = new \DateTime($matches[2]);
        }

        if (preg_match('/с (.*) г. по (.*) г./', $str, $matches)) {
            $fromAndToDates['fromDate'] = self::convertDate($matches[1]);
            $fromAndToDates['toDate']   = self::convertDate($matches[2]);
        }

        if (preg_match('/с (.*) по (.*) (.*) (.*) г./', $str, $matches)) {
            $fromAndToDates['fromDate'] = self::convertDate(implode(' ', array($matches[1], $matches[3], $matches[4])));
            $fromAndToDates['toDate']   = self::convertDate(implode(' ', array($matches[2], $matches[3], $matches[4])));
        }

        return $fromAndToDates;
    }

    /**
     * Converting of the date
     *
     * @param string $dateString
     *
     * @return \DateTime
     */
    public static function convertDate($dateString)
    {
        $dateParts                = explode(' ', $dateString);
        list($day, $month, $year) = array(
            $dateParts[0],
            self::convertRussianMonth($dateParts[1]),
            $dateParts[2]
        );

        return new \DateTime("$year-$month-$day");
    }

    /**
     * Converting of the russian months
     *
     * @param string $month
     *
     * @return string
     */
    public static function convertRussianMonth($month)
    {
        return self::$russianMonths[$month];
    }

}
