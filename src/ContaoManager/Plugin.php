<?php
    namespace Efficient\ContaoFileUsageBundle\ContaoManager;

    use Contao\CoreBundle\ContaoCoreBundle;
    use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
    use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
    use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
    use Contao\ManagerPlugin\Routing\RoutingPluginInterface;
    use Symfony\Component\Config\Loader\LoaderResolverInterface;
    use Symfony\Component\HttpKernel\KernelInterface;
    use Efficient\ContaoFileUsageBundle\ContaoFileUsageBundle;

    class Plugin implements BundlePluginInterface, RoutingPluginInterface
    {
        /**
         * {@inheritdoc}
         */
        public function getBundles(ParserInterface $parser)
        {
            return [
                BundleConfig::create(ContaoFileUsageBundle::class)
                    ->setLoadAfter([ContaoCoreBundle::class]),
            ];
        }

        public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel)
        {
            return $resolver
                ->resolve(__DIR__.'/../Resources/config/routing.yml')
                ->load(__DIR__.'/../Resources/config/routing.yml')
            ;
        }

    }
