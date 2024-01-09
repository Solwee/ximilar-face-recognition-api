<?php

namespace Solwee\XimilarFaceRecognition;

class UnknownFace implements FaceInterface
{

    private int $faceBoxX1;
    private int $faceBoxY1;
    private int $faceBoxX2;
    private int $faceBoxY2;
    private array $alternativeIdentities;
    private array $metadata;

    public function __construct(int $faceBoxX1, int $faceBoxY1, int $faceBoxX2, int $faceBoxY2, array $alternativeIdentities = [], array $metadata = [])
    {
        $this->faceBoxX1 = $faceBoxX1;
        $this->faceBoxY1 = $faceBoxY1;
        $this->faceBoxX2 = $faceBoxX2;
        $this->faceBoxY2 = $faceBoxY2;
        $this->alternativeIdentities = $alternativeIdentities;
        $this->metadata = $metadata;
    }
    public function getFaceBoxX1(): int
    {
        return $this->faceBoxX1;
    }
    public function getFaceBoxY1(): int
    {
        return $this->faceBoxY1;
    }
    public function getFaceBoxX2(): int
    {
        return $this->faceBoxX2;
    }
    public function getFaceBoxY2(): int
    {
        return $this->faceBoxY2;
    }
    /**
     * @return array|Identity[]
     */
    public function getAlternativeIdentities(): array
    {
        return $this->alternativeIdentities;
    }
    public function addAlternativeIdentity(Identity $identity): self
    {
        $this->alternativeIdentities[] = $identity;
        return $this;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }
}