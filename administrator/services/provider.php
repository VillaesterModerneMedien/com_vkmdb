<?php
/**
 * @package     Joomla.Administrator 
 * @subpackage  com_vkmdb
 *
 * @copyright   Copyright (C) 2025 Mario Hewera. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Association\AssociationExtensionInterface;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Component\Router\RouterFactoryInterface;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\CategoryFactory;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\HTML\Registry;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Component\Contact\Administrator\Helper\AssociationsHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

use VkmdbNamespace\Component\Vkmdb\Administrator\Extension\VkmdbComponent;


/**
 * The vkmdb service provider.
 *
 * @since  1.0.0
 */
return new class implements ServiceProviderInterface
{
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function register(Container $container)
	{
        $container->set(AssociationExtensionInterface::class, new AssociationsHelper());
    
		$container->registerServiceProvider(new CategoryFactory('\\VkmdbNamespace\\Component\\Vkmdb'));
		$container->registerServiceProvider(new MVCFactory('\\VkmdbNamespace\\Component\\Vkmdb'));
		$container->registerServiceProvider(new ComponentDispatcherFactory('\\VkmdbNamespace\\Component\\Vkmdb'));
        $container->registerServiceProvider(new RouterFactory('\\VkmdbNamespace\\Component\\Vkmdb'));

		$container->set(
			ComponentInterface::class,
			function (Container $container)
            {
				$component = new VkmdbComponent($container->get(ComponentDispatcherFactoryInterface::class));
                
				$component->setRegistry($container->get(Registry::class));
				$component->setMVCFactory($container->get(MVCFactoryInterface::class));
				$component->setCategoryFactory($container->get(CategoryFactoryInterface::class));
                $component->setAssociationExtension($container->get(AssociationExtensionInterface::class));
				$component->setRouterFactory($container->get(RouterFactoryInterface::class));
                
				return $component;
			}
		);
	}
};
