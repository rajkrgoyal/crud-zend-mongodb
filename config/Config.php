<?php


namespace Config;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
require_once __DIR__ .'/../module/User/Document/User.php';

class Config{
    private $dm;
    function __construct() {
        $config = new Configuration();
        $config->setProxyDir(__DIR__ . '/../module/User/Proxies');
        $config->setProxyNamespace('Proxies');
        $config->setHydratorDir(__DIR__ . '/../module/User/Hydrators');
        $config->setHydratorNamespace('Hydrators');
        $config->setDefaultDB('crud');
        $config->setMetadataDriverImpl(AnnotationDriver::create(__DIR__ .'/../module/User/Document'));
        AnnotationDriver::registerAnnotationClasses();
        $this->dm = DocumentManager::create(new Connection(), $config);
    }
    
    function getConnection(){
        return $this->dm;
    }
}


?>