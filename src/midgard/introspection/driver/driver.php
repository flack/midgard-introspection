<?php
/**
 * @author CONTENT CONTROL http://www.contentcontrol-berlin.de/
 * @copyright CONTENT CONTROL http://www.contentcontrol-berlin.de/
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

namespace midgard\introspection\driver;

interface driver
{
    /**
     * Get all MgdSchema classnames
     *
     * @return array Array of MgdSchema class names
     */
    public function get_all_schemanames();

    /**
     * Returns all properties of the given class name or object including their values
     *
     * @param mixed $object Object or classname
     * @return array Property => value pairs
     */
    public function get_object_vars($object);

    /**
     * Returns the names of all properties for the given classname/object
     *
     * @param mixed $schemaname Object or classname
     * @return array Property names
     */
    public function get_all_properties($schemaname);

    /**
     * Wrapped property_exists call
     *
     * @param mixed $schemaname Object or classname
     * @param string $property The property name
     * @return boolean Indicating existence
     */
    public function property_exists($schemaname, $property);

    /**
     * Wrapped print_r call (mainly because print_r tends to choke on doctrine entities)
     *
     * @param mixed $object Object to dump
     * @param boolean $return set to true to return the output instead of printing directly
     * @return string|void Depending on $return value
     */
    public function print_r($object, $return = false);
}