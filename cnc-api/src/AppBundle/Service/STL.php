<?php

namespace AppBundle\Service;

use Symfony\Component\DependencyInjection\Container;
use Doctrine\DBAL\Connection;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Class STL
 * @package AppBundle\Service
 */
class STL {
    /**
     * @var Container
     */
    private $container;

    /**
     * @return Container
     */
    public function getContainer() : Container
    {
        return $this->container;
    }

    /**
     * @param mixed $container
     * @return STL
     */
    private function setContainer(Container $container) : STL
    {
        $this->container = $container;
        return $this;
    }

    public function __construct(Container $container)
    {
        $this->setContainer($container);
    }

    /**
     * Upload an STL file, and kick start processing.
     *
     * @param string $fileContent
     */
    public function upload(string $fileContent)
    {
        $stl = \php3d\stl\STL::fromString($fileContent);

        /**
         * @var Connection $databaseConnection
         */
        $databaseConnection = $this->getContainer()->get('database_connection');
        $statement = $databaseConnection->prepare("INSERT INTO 
            stl_objects 
        SET 
            stl_object_name = :stl_object_name,
            stl_object_status = 'raw',
            stl_object_data = :stl_object_data,
            stl_object_coordinates = :stl_object_coordinates
        ");

        $statement->execute(array(
            "stl_object_name" => $stl->getSolidName(),
            "stl_object_data" => $fileContent,
            "stl_object_coordinates" => json_encode($stl->toArray())
        ));
    }

    /**
     * Lists uploaded STL files.
     *
     * @return array
     */
    public function listStlFiles() : array
    {
        /**
         * @var Connection $databaseConnection
         */
        $databaseConnection = $this->getContainer()->get('database_connection');
        return $databaseConnection->fetchAll("SELECT stl_object_id, stl_object_name, stl_object_status FROM stl_objects ORDER BY stl_object_id ASC");
    }

    /**
     * Fetches the content of a given column for a given STL file, by id.
     *
     * @param int $id
     * @param string $column
     * @return string
     */
    public function get(int $id, string $column) : string
    {
        if (!in_array(strtolower($column), array("stl_object_name", "stl_object_status", "stl_object_data", "stl_object_coordinates", "stl_object_mill_data", "stl_object_mill_coordinates", "stl_object_gcode_data")))
        {
            throw new Exception("Invalid column name.");
        }

        /**
         * @var Connection $databaseConnection
         */
        $databaseConnection = $this->getContainer()->get('database_connection');
        return $databaseConnection->fetchColumn("SELECT " . $column . " FROM stl_objects WHERE stl_object_id = :stl_object_id LIMIT 1", array(
            "stl_object_id" => $id,
            0
        ));
    }
}