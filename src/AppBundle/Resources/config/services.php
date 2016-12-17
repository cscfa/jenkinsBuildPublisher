<?php

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\DependencyInjection\Reference;

$container->setDefinition(
    'project_loader',
    new Definition(
        'AppBundle\Service\Loader',
        array(
            new Reference('doctrine.orm.entity_manager'),
            new Expression('parameter(\'kernel.root_dir\') ~ \'/../resources/projects\''),
        )
    )
);

$container->register('app.status_extension', 'AppBundle\Twig\StatusExtension')
    ->setPublic(false)
    ->addTag('twig.extension');

$apiKeyAuthenticator = new Definition('AppBundle\Security\ApiKeyAuthenticator');
$apiKeyAuthenticator->setPublic(false);
$container->setDefinition('apikey_authenticator', $apiKeyAuthenticator);

$container->setDefinition(
    'api_user_provider',
    new Definition(
        'AppBundle\UserProvider\ApiUserProvider',
        array(
            new Reference('doctrine.orm.entity_manager'),
        )
    )
);

$container->setDefinition(
    'json_parser-project',
    new Definition(
        'AppBundle\Parser\ProjectJsonParser',
        array(
            new Reference('router')
        )
    )
);

$container->setDefinition(
    'json_parser-build',
    new Definition(
        'AppBundle\Parser\BuildJsonParser',
        array(
            new Reference('router')
        )
    )
);

$container->setDefinition(
    'json_parser-status',
    new Definition(
        'AppBundle\Parser\StatusJsonParser',
        array(
            new Reference('router')
        )
    )
);

$container->setDefinition(
    'json_parser-build_file',
    new Definition(
        'AppBundle\Parser\BuildFileJsonParser',
        array(
            new Reference('router')
        )
    )
);

$container->setDefinition(
    'dto_parser-project',
    new Definition(
        'AppBundle\DTO\Parser\ProjectDtoParser',
        array(
            new Expression('service(\'doctrine.orm.entity_manager\').getRepository(\'AppBundle:Project\')'),
        )
    )
);

$container->setDefinition(
    'dto_parser-build',
    new Definition(
        'AppBundle\DTO\Parser\BuildDtoParser',
        array(
            new Expression('service(\'doctrine.orm.entity_manager\').getRepository(\'AppBundle:Build\')'),
        )
    )
);

$container->setDefinition(
    'dto_parser-build_file',
    new Definition(
        'AppBundle\DTO\Parser\BuildFileDtoParser',
        array(
            new Expression('service(\'doctrine.orm.entity_manager\').getRepository(\'AppBundle:BuildFile\')'),
        )
    )
);

$container->setDefinition(
    'error_parser',
    new Definition(
        'AppBundle\Parser\ErrorParser',
        array(
            new Reference('doctrine.orm.entity_manager'),
            new Expression('service(\'security.token_storage\').getToken().getUser()'),
        )
    )
);

$container->setDefinition(
    'form_parser',
    new Definition('AppBundle\Parser\FormParser')
);
