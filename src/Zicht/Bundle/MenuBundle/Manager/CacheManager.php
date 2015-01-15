<?php
/**
 * @author    Philip Bergman <philip@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\Manager;

use Doctrine\DBAL\Connection;
use Symfony\Component\Yaml\Yaml;


class CacheManager
{
    /** @var string */
    protected $cacheFile;
    /** @var bool  */
    protected $isEnabled;

    function __construct($cacheFolder, $enabled = true)
    {
        $this->cacheFile = sprintf('%s/zicht_menu.yml', $cacheFolder);
        $this->isEnabled = $enabled;
    }

    /**
     * @param   Connection $connection
     * @return  null|string
     * @throws  \Doctrine\DBAL\DBALException
     */
    function getMenuNames(Connection $connection)
    {
        $return = array();
        $query  = "SELECT id, name FROM menu_item WHERE parent_id IS NULL AND name IS NOT NULL";
        if (array() !== $result = $connection->fetchAll($query)) {
            foreach($result as $r) {
                $return['menus'][$r['id']] = $r['name'];
            }
            return $return;
        }
        return null;
    }

    /**
     * file will be written with data from database if
     * first argument is a instance of Connection
     * else it will expect a array like (with force as true):
     *
     *  array
     *       'menus' =>
     *          array
     *              1   => 'service'
     *              6   => 'main'
     *
     *
     * @param   Connection|array    $args
     * @param   bool                $force
     * @return  bool|int
     */
    function writeFile($args, $force = false)
    {
        if ($force || !is_file($this->cacheFile)) {

            if ($args instanceof Connection) {
                $args = $this->getMenuNames($args);
            }

            if (is_array($args) && isset($args['menus']) && is_array($args['menus'])) {
                return file_put_contents($this->cacheFile, Yaml::dump($args, 2));
            }
        }

        return false;
    }

    /**
     * load the yaml cache file
     *
     * @return array|null
     */
    function loadFile()
    {
        if (is_file($this->cacheFile)) {
            if (false !== $content = file_get_contents($this->cacheFile)) {
                return Yaml::parse($content);
            }
        }
        return null;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

}