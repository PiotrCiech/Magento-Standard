<?php
declare(strict_types=1);

namespace Rokezzz\Gallery\Block\Widget;

use Magento\{
    Framework\View\Element\Template,
    Widget\Block\BlockInterface,
    Cms\Model\Template\FilterProvider
};

class Gallery extends Template implements BlockInterface
{
    protected $_template = "widget/gallery.phtml";
    protected $filterProvider;

    public function __construct(
        Template\Context $context,
        FilterProvider $filterProvider,
        array $data = []
    ) {
        $this->filterProvider = $filterProvider;
        parent::__construct($context, $data);
    }

    public function getGalleryImages(): array
    {

        $rawData = $this->getData('gallery_data');
        if (!$rawData) {
            return [];
        }

        $decodedData = json_decode((string) $rawData, true);

        if (!is_array($decodedData)) {
            return [];
        }

        $images = [];
        $index = 1;

        foreach ($decodedData as $item) {
            $imagePath = $item['image'] ?? '';
            $caption = $item['caption'] ?? '';

            if ($imagePath) {
                if (strpos($imagePath, '___directive/') !== false) {
                    $parts = explode('___directive/', $imagePath);

                    if (isset($parts[1])) {
                        $directivePart = explode('/', $parts[1])[0];
                        $decodedDirective = base64_decode(strtr($directivePart, '-_', '+/'));

                        if ($decodedDirective) {
                            $imagePath = $decodedDirective;
                        }
                    }
                }

                try {
                    $imageUrl = $this->filterProvider->getBlockFilter()->filter($imagePath);
                } catch (\Exception $e) {
                    $imageUrl = $imagePath;
                }

                $images[] = [
                    'thumb' => $imageUrl,
                    'img' => $imageUrl,
                    'full' => $imageUrl,
                    'caption' => $this->escapeHtml($caption),
                    'position' => $index,
                    'isMain' => ($index === 1)
                ];
                $index++;
            }
        }

        return $images;
    }
    
    public function getImagesJson(): string
    {
        return json_encode($this->getGalleryImages());
    }
}
