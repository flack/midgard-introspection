<?php
/**
 * @author CONTENT CONTROL http://www.contentcontrol-berlin.de/
 * @copyright CONTENT CONTROL http://www.contentcontrol-berlin.de/
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

namespace midgard\introspection;

use midgard\introspection\driver;

class helper
{
    /**
     * Driver implementation
     *
     * @var driver\driver
     */
    private $driver;

    public function __construct()
    {
        if (extension_loaded('midgard'))
        {
            $this->driver = new driver\midgard1;
        }
        else if (extension_loaded('midgard2'))
        {
            $this->driver = new driver\midgard2;
        }
        else if (class_exists('\\midgard\\portable\\driver'))
        {
            $this->driver = new driver\portable;
        }
        else
        {
            throw new \RuntimeException('could not detect Midgard environment');
        }
    }

    /**
     * Get all MgdSchema classnames
     *
     * @return array Array of MgdSchema class names
     */
    public function get_all_schemanames()
    {
        return $this->driver->get_all_schemanames();
    }

    /**
     * Returns the names of all properties for the given classname/object
     *
     * @param mixed $schemaname Object or classname
     * @return array Property names
     */
    public function get_all_properties($schemaname)
    {
        return $this->driver->get_all_properties($schemaname);
    }

    /**
     * Returns all properties of the given class name or object including their values
     *
     * @param mixed $object Object or classname
     * @return array Property => value pairs
     */
    public function get_object_vars($object)
    {
        return $this->driver->get_object_vars($object);
    }

    /**
     * Wrapped property_exists call
     *
     * @param mixed $schemaname Object or classname
     * @param string $property The property name
     * @return boolean Indicating existence
     */
    public function property_exists($schemaname, $property)
    {
        return $this->driver->property_exists($schemaname, $property);
    }

    /**
     * Wrapped print_r call (mainly because print_r tends to choke on doctrine entities)
     *
     * @param mixed $object Object to dump
     * @param boolean $return set to true to return the output instead of printing directly
     * @return string|void Depending on $return value
     */
    public function print_r($object, $return = false)
    {
        return $this->driver->print_r($object, $return);
    }

    /**
     * Get direct PDO connection
     *
     * @return \PDO
     */
    public function get_pdo()
    {
        return $this->driver->get_pdo();
    }
}