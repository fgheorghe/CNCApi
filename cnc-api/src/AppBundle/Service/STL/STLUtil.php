<?php
/**
 * Created by PhpStorm.
 * User: fgheorghe
 * Date: 13/07/16
 * Time: 22:39
 */

namespace AppBundle\Service\STL;

use Symfony\Component\DependencyInjection\Container;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Class STL. Provides STL conversion functionality.
 * @package AppBundle\Service\STL
 */
class STLUtil {
    /**
     * Convenience method used for creating a queue connection and uploading the STL file.
     * @param string $content
     * @param Container $container
     * @param Boolean $replace
     */
    public function upload(string $content, Container $container, $replace = false) {
        $stl = new STL(
            $container,
            new STLFileReader($content),
            $container->get('doctrine')->getManager()->getConnection(),
            // As per: https://github.com/php-amqplib/php-amqplib/blob/master/demo/amqp_publisher.php
            new AMQPStreamConnection(
                $container->getParameter('rabbit_mq_host'),
                $container->getParameter('rabbit_mq_port'),
                $container->getParameter('rabbit_mq_user'),
                $container->getParameter('rabbit_mq_password'),
                $container->getParameter('rabbit_mq_vhost')
            ),
            $container->getParameter('rabbit_mq_stl_queue_name'),
            $container->getParameter('rabbit_mq_stl_exchange_name')
        );

        $stl->upload($replace);
    }

    /**
     * Fetches STL coordinates object.
     *
     * @param string $name
     * @return \stdClass
     */
    public function getCoordinates(string $name, Registry $doctrine) : \stdClass {
        $result = $doctrine->getConnection()
            ->executeQuery(
                "SELECT stl_object_coordinates FROM stl_objects WHERE stl_object_name = :name LIMIT 1",
                array(
                    "name" => $name
                )
            )->fetchAll(\PDO::FETCH_ASSOC);

        return json_decode($result[0]["stl_object_coordinates"]);
    }

    /**
     * Fetches STL object data.
     *
     * @param string $name
     * @return string
     */
    public function getStlData(string $name, Registry $doctrine) : string {
        $result = $doctrine->getConnection()
            ->executeQuery(
                "SELECT stl_object_data FROM stl_objects WHERE stl_object_name = :name LIMIT 1",
                array(
                    "name" => $name
                )
            )->fetchAll(\PDO::FETCH_ASSOC);

        return $result[0]["stl_object_data"];
    }
}