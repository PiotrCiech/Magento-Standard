<?php

declare(strict_types=1);

namespace Rokezzz\CatalogListing\Test\Unit\Model;

use Magento\Catalog\Model\Category;
use Magento\Framework\View\Result\Page;
use PHPUnit\Framework\TestCase;
use Rokezzz\CatalogListing\Model\CategoryLayoutHandleResolver;

final class CategoryLayoutHandleResolverTest extends TestCase
{
    public function testApplyAddsLayeredHandlesForAnchorWithChildren(): void
    {
        $handles = $this->apply(
            hasChildren: true,
            isAnchor: true,
            displayMode: 'PRODUCTS',
            categoryId: 12
        );

        $this->assertSame(
            [
                [['type' => 'layered'], null, false],
                [['displaymode' => 'products'], null, false],
                [['id' => 12], null, true],
            ],
            $handles
        );
    }

    public function testApplyAddsParentTypeWhenAnchorHasNoChildren(): void
    {
        $handles = $this->apply(
            hasChildren: false,
            isAnchor: true,
            displayMode: 'PAGE',
            categoryId: 8
        );

        $this->assertSame(
            [
                [['type' => 'layered'], null, false],
                [['type' => 'layered_without_children'], null, false],
                [['displaymode' => 'page'], null, false],
                [['id' => 8], null, true],
            ],
            $handles
        );
    }

    /**
     * @return list<array<int, mixed>>
     */
    private function apply(bool $hasChildren, bool $isAnchor, string $displayMode, int $categoryId): array
    {
        $category = $this->getMockBuilder(Category::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['hasChildren', 'getDisplayMode', 'getId'])
            ->getMock();
        $category->method('hasChildren')->willReturn($hasChildren);
        $category->method('getDisplayMode')->willReturn($displayMode);
        $category->method('getId')->willReturn($categoryId);
        $category->setData('is_anchor', $isAnchor);

        $handles = [];
        $page = $this->createMock(Page::class);
        $page->method('addPageLayoutHandles')->willReturnCallback(
            static function (...$arguments) use (&$handles, $page): Page {
                $handles[] = $arguments;

                return $page;
            }
        );

        (new CategoryLayoutHandleResolver())->apply($page, $category);

        return $handles;
    }
}
