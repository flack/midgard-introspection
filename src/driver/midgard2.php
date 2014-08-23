<?php
/**
 * @author CONTENT CONTROL http://www.contentcontrol-berlin.de/
 * @copyright CONTENT CONTROL http://www.contentcontrol-berlin.de/
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

namespace midgard\introspection\driver;

use ReflectionExtension;
use PDO;
use midgard_connection;
use MidgardReflectorObject;

class midgard2 implements driver
{
    private $schemanames = array();

    /**
     * {@inheritDoc}
     */
    public function get_all_schemanames()
    {
        if (empty($this->schemanames))
        {
            $re = new ReflectionExtension('midgard2');
            $classes = $re->getClasses();
            foreach ($classes as $refclass)
            {
                if ($refclass->isSubclassOf('\midgard_object'))
                {
                    $name = $refclass->getName();
                    if (   class_exists('\\MidgardReflectorObject')
                    && (   MidgardReflectorObject::is_abstract($name)
                        || MidgardReflectorObject::is_mixin($name)
                        || MidgardReflectorObject::is_interface($name)))
                    {
                        continue;
                    }
                    $this->schemanames[] = $name;
                }
            }
        }
        return $this->schemanames;
    }

    /**
     * {@inheritDoc}
     */
    public function get_object_vars($object)
    {
        if (!is_object($object))
        {
            $object = new $object();
        }
        return get_object_vars($object);
    }

    /**
     * {@inheritDoc}
     */
    public function get_all_properties($schemaname)
    {
        // Workaround for http://trac.midgard-project.org/ticket/942
        if (!is_object($schemaname))
        {
            $schemaname = new $schemaname();
        }
        return array_keys(get_object_vars($schemaname));
    }

    /**
     * {@inheritDoc}
     */
    public function property_exists($schemaname, $property)
    {
        // Workaround for http://trac.midgard-project.org/ticket/942
        if (!is_object($schemaname))
        {
            $schemaname = new $schemaname();
        }
        return property_exists($schemaname, $property);
    }

    /**
     * {@inheritDoc}
     */
    public function print_r($object, $return = false)
    {
        if ($return)
        {
            return print_r($object, true);
        }
        print_r($object);
    }

    /**
     * {@inheritDoc}
     */
    public function get_pdo()
    {
        $midgard = midgard_connection::get_instance();
        if (empty($midgard->config))
        {
            throw new \RuntimeException('No Midgard2 connection found');
        }
        return new PDO('mysql:host=' . $midgard->config->host . ';dbname=' . $midgard->config->database . ';charset=utf8', $midgard->config->dbuser, $midgard->config->dbpass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }
}