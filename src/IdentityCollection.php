<?php

namespace Solwee\XimilarFaceRecognition;

class IdentityCollection implements \IteratorAggregate, \Countable
{

        /**
        * @var IdentityInterface[]
        */
        private array $identities;
        private string $analyzedImageUrl;
        private int $analyzedImageWidth;
        private int $analyzedImageHeight;
        public function __construct(string $analyzedImageUrl, int $analyzedImageWidth, int $analyzedImageHeight, array $identities = [])
        {
            $this->identities = $identities;
            $this->analyzedImageUrl = $analyzedImageUrl;
            $this->analyzedImageWidth = $analyzedImageWidth;
            $this->analyzedImageHeight = $analyzedImageHeight;
        }
        public function addIdentity(IdentityInterface $identity): self
        {
            $this->identities[] = $identity;
            return $this;
        }
        public function getIterator(): \ArrayIterator
        {
            return new \ArrayIterator($this->identities);
        }
        public function count(): int
        {
            return count($this->identities);
        }

    public function getAnalyzedImageUrl(): string
    {
        return $this->analyzedImageUrl;
    }

    public function getAnalyzedImageWidth(): int
    {
        return $this->analyzedImageWidth;
    }

    public function getAnalyzedImageHeight(): int
    {
        return $this->analyzedImageHeight;
    }
}