<?php

declare(strict_types=1);

namespace Rokezzz\Offers\Model\Mail\Template;

use Magento\Framework\Mail\EmailMessage;
use Magento\Framework\Mail\Template\TransportBuilder as MagentoTransportBuilder;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\MixedPart;

class TransportBuilder extends MagentoTransportBuilder
{
    /**
     * @var array<int, array{content: string, fileName: string, mimeType: string}>
     */
    private array $attachments = [];

    public function addAttachment(string $content, string $fileName, string $mimeType = 'application/pdf'): self
    {
        $this->attachments[] = [
            'content' => $content,
            'fileName' => $fileName,
            'mimeType' => $mimeType,
        ];

        return $this;
    }

    protected function reset()
    {
        $this->attachments = [];

        return parent::reset();
    }

    protected function prepareMessage()
    {
        parent::prepareMessage();

        if ($this->attachments === [] || !$this->message instanceof EmailMessage) {
            return $this;
        }

        $symfonyMessage = $this->message->getSymfonyMessage();
        $existingBody = $symfonyMessage->getBody();

        if (is_null($existingBody)) {
            return $this;
        }

        $parts = [$existingBody];

        foreach ($this->attachments as $attachment) {
            $parts[] = new DataPart(
                $attachment['content'],
                $attachment['fileName'],
                $attachment['mimeType']
            );
        }

        if (count($parts) > 1) {
            $symfonyMessage->setBody(new MixedPart(...$parts));
        }

        return $this;
    }
}
