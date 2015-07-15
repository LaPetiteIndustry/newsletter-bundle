<?php

// if the bundle is within a symfony project, try to reuse the project's autoload

$files = array(
    __DIR__.'/../vendor/autoload.php'
);

$autoload = false;

foreach ($files as $file) {
    if (is_file($file)) {
        $autoload = include_once $file;
        break;
    }
}

// Bootstrap the JMS custom annotations for Object to Json mapping
\Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace(
    'JMS\Serializer\Annotation',
    dirname(__DIR__).'/vendor/jms/serializer/src'
);

$autoload->addPsr4('Lpi\\NewsletterBundle\\', __DIR__.'/../..');

if (!$autoload) {
    die('Unable to find autoload.php file, please use composer to load dependencies:

wget http://getcomposer.org/composer.phar
php composer.phar install

Visit http://getcomposer.org/ for more information.

');
}