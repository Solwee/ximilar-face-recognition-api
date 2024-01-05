<?php

namespace Solwee\XimilarFaceRecognition;

class Identity
{
    private string $name;
    private string $id;
    private string $customId;
    private ?string $previewUrl;


    public function __construct(
        string $name,
        string $id,
        string $customId,
        ?string $previewUrl = null
    )
    {

        $this->name = $name;
        $this->id = $id;
        $this->customId = $customId;
        $this->previewUrl = $previewUrl;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getId(): string
    {
        return $this->id;
    }
    public function getCustomId(): string
    {
        return $this->customId;
    }
    public function getPreviewUrl(): ?string
    {
        return $this->previewUrl;
    }
}