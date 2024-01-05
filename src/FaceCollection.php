<?php

namespace Solwee\XimilarFaceRecognition;

class FaceCollection implements \IteratorAggregate, \Countable
{

        /**
        * @var FaceInterface[]
        */
        private array $faces;
        private string $analyzedImageUrl;
        private int $analyzedImageWidth;
        private int $analyzedImageHeight;
        public function __construct(string $analyzedImageUrl, int $analyzedImageWidth, int $analyzedImageHeight, array $faces = [])
        {
            $this->faces = $faces;
            $this->analyzedImageUrl = $analyzedImageUrl;
            $this->analyzedImageWidth = $analyzedImageWidth;
            $this->analyzedImageHeight = $analyzedImageHeight;
        }
        public function addFace(FaceInterface $identity): self
        {
            $this->faces[] = $identity;
            return $this;
        }
        public function getIterator(): \ArrayIterator
        {
            return new \ArrayIterator($this->faces);
        }
        public function count(): int
        {
            return count($this->faces);
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