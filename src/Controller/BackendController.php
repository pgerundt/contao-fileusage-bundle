<?php
    namespace Efficient\ContaoFileUsageBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Efficient\ContaoFileUsageBundle\Controller\BackendFileUsage;

    class BackendController extends AbstractController
    {

        public function showFileUsageAction(Request $request)
        {
            $this->get('contao.framework')->initialize();
            $controller = new BackendFileUsage();
            return $controller->run();
        }

    }
