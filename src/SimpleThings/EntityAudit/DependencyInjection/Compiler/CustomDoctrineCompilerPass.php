<?php
/**
 * www.valiton.com
 *
 * @author Uwe Jäger <uwe.jaeger@valiton.com>
 */


namespace SimpleThings\EntityAudit\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CustomDoctrineCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $entityManager = $container->getParameter('simplethings.entityaudit.entity_manager');
        $reader = $container->getDefinition('simplethings_entityaudit.reader');
        $reader->replaceArgument(0, new Reference($entityManager));

        $connection = $container->getParameter('simplethings.entityaudit.connection');
        $eventManager = $container->getDefinition(sprintf('doctrine.dbal.%s_connection.event_manager', $connection));

        $eventManager->addMethodCall('addEventSubscriber', array(new Reference('simplethings_entityaudit.log_revisions_listener')));
        $eventManager->addMethodCall('addEventSubscriber', array(new Reference('simplethings_entityaudit.create_schema_listener')));
    }

}
