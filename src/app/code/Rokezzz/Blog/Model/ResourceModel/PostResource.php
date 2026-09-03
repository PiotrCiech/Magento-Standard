<?php

declare(strict_types=1);

namespace Rokezzz\Blog\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class PostResource extends AbstractDb
{
    public const TABLE_NAME = 'rokezzz_blog_post';
    public const TABLE_TAG = 'rokezzz_blog_post_tag';
    public const TABLE_STORE = 'rokezzz_blog_post_store';

    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME, 'post_id');
    }

    protected function _afterSave(AbstractModel $object): self
    {
        $connection = $this->getConnection();
        $postId = (int) $object->getId();

        if ($object->hasData('tag_ids')) {
            $tableTag = $this->getTable(self::TABLE_TAG);
            $connection->delete($tableTag, ['post_id = ?' => $postId]);

            $tagIds = (array) $object->getData('tag_ids');
            $data = [];
            foreach ($tagIds as $tagId) {
                if ($tagId) {
                    $data[] = [
                        'post_id' => $postId,
                        'tag_id'  => (int) $tagId
                    ];
                }
            }

            if (!empty($data)) {
                $connection->insertMultiple($tableTag, $data);
            }
        }

        if ($object->hasData('store_ids') || $object->hasData('stores')) {
            $tableStore = $this->getTable(self::TABLE_STORE);
            $connection->delete($tableStore, ['post_id = ?' => $postId]);

            $storeIds = (array) ($object->getData('store_ids') ?: $object->getData('stores'));
            $data = [];
            foreach ($storeIds as $storeId) {
                $data[] = [
                    'post_id'  => $postId,
                    'store_id' => (int) $storeId
                ];
            }

            if (!empty($data)) {
                $connection->insertMultiple($tableStore, $data);
            }
        }

        return parent::_afterSave($object);
    }

    public function getTagIds(int $postId): array
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable(self::TABLE_TAG), 'tag_id')
            ->where('post_id = ?', $postId);

        return $connection->fetchCol($select);
    }

    public function getStoreIds(int $postId): array
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable(self::TABLE_STORE), 'store_id')
            ->where('post_id = ?', $postId);

        return $connection->fetchCol($select);
    }
}
