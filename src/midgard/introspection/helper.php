<?php
/**
 * @author CONTENT CONTROL http://www.contentcontrol-berlin.de/
 * @copyright CONTENT CONTROL http://www.contentcontrol-berlin.de/
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

namespace midgard\introspection;

class helper
{
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

    public function get_all_schemanames()
    {
        return $this->driver->get_all_schemanames();
    }

    public function get_all_properties($schemaname)
    {
        return $this->driver->get_all_properties($schemaname);
    }

    public function property_exists($schemaname, $property)
    {
        return $this->driver->property_exists($schemaname, $property);
    }
}