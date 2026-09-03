<?php
declare(strict_types=1);

namespace Rokezzz\Blog\Controller;

use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\UrlInterface;
use Rokezzz\Blog\Controller\Author\View as AuthorView;
use Rokezzz\Blog\Controller\Category\View as CategoryView;
use Rokezzz\Blog\Controller\Index\Index as IndexIndex;
use Rokezzz\Blog\Controller\Post\View as PostView;
use Rokezzz\Blog\Controller\Tag\View as TagView;

class Router implements RouterInterface
{
    private const SUB_RESOURCE_MAP = [
        'category' => ['controller' => 'category', 'action' => CategoryView::class],
        'author'   => ['controller' => 'author',   'action' => AuthorView::class],
        'tag'      => ['controller' => 'tag',      'action' => TagView::class],
    ];

    public function __construct(
        private readonly ActionFactory $actionFactory
    ) {
    }

    public function match(RequestInterface $request): ?ActionInterface
    {
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

        $parts = array_values(array_filter(explode('/', $pathInfo), fn(string $p) => $p !== ''));

        if (empty($parts) || $parts[0] !== 'blog') {
            return null;
        }

        if (isset($parts[1]) && $parts[1] === 'comment') {
            return null;
        }

        // 1. /blog
        if (count($parts) === 1) {
            return $this->dispatch($request, 'index', 'index', IndexIndex::class, $pathInfo);
        }

        if (count($parts) === 3 && isset(self::SUB_RESOURCE_MAP[$parts[1]])) {
            $resource = self::SUB_RESOURCE_MAP[$parts[1]];
            return $this->dispatch(
                $request,
                $resource['controller'],
                'view',
                $resource['action'],
                $pathInfo,
                ['identifier' => trim($parts[2])]
            );
        }

        if (count($parts) === 2) {
            return $this->dispatch(
                $request,
                'post',
                'view',
                PostView::class,
                $pathInfo,
                ['identifier' => trim($parts[1])]
            );
        }

        return null;
    }

    /**
     * @param array<string, mixed> $params
     */
    private function dispatch(
        RequestInterface $request,
        string $controller,
        string $action,
        string $actionClass,
        string $pathInfo,
        array $params = []
    ): ActionInterface {
        $request->setRouteName('blog')
            ->setModuleName('blog')
            ->setControllerName($controller)
            ->setActionName($action)
            ->setAlias(UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $pathInfo);

        foreach ($params as $key => $value) {
            $request->setParam($key, $value);
        }

        return $this->actionFactory->create($actionClass);
    }
}
