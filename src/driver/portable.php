<?php
/**
 * @author CONTENT CONTROL http://www.contentcontrol-berlin.de/
 * @copyright CONTENT CONTROL http://www.contentcontrol-berlin.de/
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

namespace midgard\introspection\driver;

use midgard\portable\storage\connection;
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\Mapping\MappingException;
use PDO;

class portable implements driver
{
    private $schemanames = array();

    /**
     * {@inheritDoc}
     */
    public function get_all_schemanames()
    {
        if (empty($this->schemanames))
        {
            $em = connection::get_em();
            $cms = $em->getMetadataFactory()->getAllMetadata();
            foreach ($cms as $cm)
            {
                if ($cm->reflClass->isSubclassOf('\midgard_object'))
                {
                    $this->schemanames[] = $cm->name;
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
        $vars = array();
        if (!is_object($object))
        {
            $object = new $object();
        }
        $properties = $this->get_all_properties($object);
        foreach ($properties as $property)
        {
            $vars[$property] = $object->$property;
        }

        return $vars;
    }

    /**
     * {@inheritDoc}
     */
    public function get_all_properties($schemaname)
    {
        $properties = array();
        $metadata = false;
        if (is_object($schemaname))
        {
            $properties = array_keys(get_object_vars($schemaname));
            if (   is_a($schemaname, 'midcom_core_dbaobject')
                || is_a($schemaname, 'midcom_core_dbaproxy'))
            {
                $schemaname = $schemaname->__mgdschema_class_name__;
            }
            else if (is_a($schemaname, 'midcom_helper_metadata'))
            {
                $metadata = true;
                $schemaname = $schemaname->__object->__mgdschema_class_name__;
            }
            else if (is_a($schemaname, 'midgard_metadata'))
            {
                throw new \Exception('not implemented');
            }
            else
            {
                $schemaname = get_class($schemaname);
            }
        }
        try
        {
            $cm = connection::get_em()->getClassMetadata($schemaname);
        }
        catch (MappingException $e)
        {
            return $properties;
        }

        $mgdschema_properties = array_merge($cm->getFieldNames(), $cm->getAssociationNames(), array_keys($cm->midgard['field_aliases']));
        $mgdschema_properties = array_filter($mgdschema_properties, function($input) use ($metadata)
        {
            if (strpos($input, 'metadata_') === $metadata)
            {
                return $input;
            }
        });
        $properties = array_merge($mgdschema_properties, $properties);
        if (!$metadata)
        {
            $properties[] = 'metadata';
        }
        return $properties;
    }

    /**
     * {@inheritDoc}
     */
    public function property_exists($schemaname, $property)
    {
        if (is_object($schemaname))
        {
            if (property_exists($schemaname, $property))
            {
                return true;
            }
            if (   is_a($schemaname, 'midcom_core_dbaobject')
                || is_a($schemaname, 'midcom_core_dbaproxy'))
            {
                $schemaname = $schemaname->__mgdschema_class_name__;
            }
            else if (is_a($schemaname, 'midcom_helper_metadata'))
            {
                $schemaname = $schemaname->__object->__mgdschema_class_name__;
                $property = 'metadata_' . $property;
            }
            else if (is_a($schemaname, 'midgard_metadata'))
            {
                throw new \Exception('not implemented');
            }
            else
            {
                $schemaname = get_class($schemaname);
            }
        }
        $cm = connection::get_em()->getClassMetadata($schemaname);
        return ($cm->hasField($property) || $cm->hasAssociation($schemaname) || array_key_exists($property, $cm->midgard['field_aliases']));
    }

    /**
     * {@inheritDoc}
     */
    public function print_r($object, $return = false)
    {
        if ($return)
        {
            ob_start();
            Debug::dump($object);
            $dump = ob_get_contents();
            ob_end_clean();
            return $dump;
        }
        Debug::dump($object);
    }

    /**
     * {@inheritDoc}
     */
    public function get_dbo()
    {
        $pdo = connection::get_em()->getConnection()->getWrappedConnection();
        if ($pdo instanceof PDO)
        {
            return $pdo;
        }
        $midgard = \midgard_connection::get_instance();
        if (empty($midgard->config))
        {
            throw new \RuntimeException('No Midgard2 connection found');
        }
        return new PDO('mysql:host=' . $midgard->config->host . ';dbname=' . $midgard->config->database . ';charset=utf8', $midgard->config->dbuser, $midgard->config->dbpass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }
}