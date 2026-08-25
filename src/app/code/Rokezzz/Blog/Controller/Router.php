<?php
declare(strict_types=1);

namespace Rokezzz\Blog\Controller;

use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\UrlInterface;

class Router implements RouterInterface
{
    public function __construct(
        private readonly ActionFactory $actionFactory
    ) {
    }

    public function match(RequestInterface $request): ?ActionInterface
    {
        // Ten router dopasowuje wyłącznie na podstawie surowego pathInfo, więc po
        // forward('noroute') (pathInfo się nie zmienia) musimy sami wykryć, że
        // routing przejęła już inna warstwa (np. Cms\Controller\Noroute) - inaczej
        // dopasujemy tę samą ścieżkę w kółko i front controller wybuchnie po 100
        // iteracjach (LogicException).
        $moduleName = $request->getModuleName();
        if ($moduleName && $moduleName !== 'blog') {
            return null;
        }
        if ($request->getActionName() === 'noroute') {
            return null;
        }

        $pathInfo = trim((string)$request->getPathInfo(), '/');
        if ($pathInfo === '') {
            return null;
        }

        $parts = array_values(array_filter(explode('/', $pathInfo), fn($p) => $p !== ''));

        if (empty($parts) || $parts[0] !== 'blog') {
            return null;
        }

        // Pomijamy POST dla dodawania komentarzy
        if (isset($parts[1]) && $parts[1] === 'comment') {
            return null;
        }

        // 1. /blog
        if (count($parts) === 1) {
            $request->setRouteName('blog')
                ->setModuleName('blog')
                ->setControllerName('index')
                ->setActionName('index')
                ->setAlias(UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $pathInfo);
            return $this->actionFactory->create(\Rokezzz\Blog\Controller\Index\Index::class);
        }

        // 2. /blog/category/{identifier}
        if ($parts[1] === 'category' && isset($parts[2])) {
            $request->setRouteName('blog')
                ->setModuleName('blog')
                ->setControllerName('category')
                ->setActionName('view')
                ->setParam('identifier', trim($parts[2]))
                ->setAlias(UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $pathInfo);
            return $this->actionFactory->create(\Rokezzz\Blog\Controller\Category\View::class);
        }

        // 3. /blog/author/{identifier}
        if ($parts[1] === 'author' && isset($parts[2])) {
            $request->setRouteName('blog')
                ->setModuleName('blog')
                ->setControllerName('author')
                ->setActionName('view')
                ->setParam('identifier', trim($parts[2]))
                ->setAlias(UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $pathInfo);
            return $this->actionFactory->create(\Rokezzz\Blog\Controller\Author\View::class);
        }

        // 4. /blog/tag/{identifier}
        if ($parts[1] === 'tag' && isset($parts[2])) {
            $request->setRouteName('blog')
                ->setModuleName('blog')
                ->setControllerName('tag')
                ->setActionName('view')
                ->setParam('identifier', trim($parts[2]))
                ->setAlias(UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $pathInfo);
            return $this->actionFactory->create(\Rokezzz\Blog\Controller\Tag\View::class);
        }

        // 5. /blog/{identifier}
        if (count($parts) === 2) {
            $request->setRouteName('blog')
                ->setModuleName('blog')
                ->setControllerName('post')
                ->setActionName('view')
                ->setParam('identifier', trim($parts[1]))
                ->setAlias(UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $pathInfo);
            return $this->actionFactory->create(\Rokezzz\Blog\Controller\Post\View::class);
        }

        return null;
    }
}
