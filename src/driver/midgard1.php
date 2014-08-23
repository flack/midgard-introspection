<?php
/**
 * @author CONTENT CONTROL http://www.contentcontrol-berlin.de/
 * @copyright CONTENT CONTROL http://www.contentcontrol-berlin.de/
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

namespace midgard\introspection\driver;

class midgard1 extends midgard2
{
    /**
     * {@inheritDoc}
     */
    public function get_all_schemanames()
    {
        return array_keys($_MIDGARD['schema']['types']);
    }

    /**
     * {@inheritDoc}
     */
    public function get_dbo()
    {
        throw new \Exception('Not implemented yet');
    }
}