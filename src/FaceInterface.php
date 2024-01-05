<?php

namespace Solwee\XimilarFaceRecognition;

interface FaceInterface
{
    public function getFaceBoxX1(): int;

    public function getFaceBoxY1(): int;

    public function getFaceBoxX2(): int;

    public function getFaceBoxY2(): int;

    /**
     * @return array|Identity[]
     */
    public function getAlternativeIdentities(): array;

    public function addAlternativeIdentity(Identity $identity): self;

}