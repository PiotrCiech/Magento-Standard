<?php
declare(strict_types=1);
namespace Rokezzz\Acordeon\Block\Widget;
use Magento\{
    Framework\View\Element\Template,
    Widget\Block\BlockInterface,
    Cms\Model\Template\FilterProvider
};
class Accordion extends Template implements BlockInterface
{
    protected $_template = "widget/accordion.phtml";
    protected $filterProvider;
    public function __construct(
        Template\Context $context,
        FilterProvider $filterProvider,
        array $data = []
    ) {
        $this->filterProvider = $filterProvider;
        parent::__construct($context, $data);
    }
    public function getAccordionItems(): array
    {
        $rawData = $this->getData('accordion_data');
        if (!$rawData) {
            return [];
        }
        $decodedData = json_decode((string) $rawData, true);
        if (!is_array($decodedData)) {
            return [];
        }
        $items = [];
        foreach ($decodedData as $item) {
            $title = $item['title'] ?? '';
            $content = $item['content'] ?? '';
            if ($title || $content) {
                try {
                    $filteredContent = $this->filterProvider->getBlockFilter()->filter($content);
                } catch (\Exception $e) {
                    $filteredContent = $content;
                }
                $items[] = [
                    'title' => $this->escapeHtml($title),
                    'content' => $filteredContent
                ];
            }
        }
        return $items;
    }
}
