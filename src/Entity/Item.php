<?php declare(strict_types=1);

namespace VysokeSkoly\AppStatusBundle\Entity;

class Item
{
    public function __construct(private string $title, private string $value, private ?string $color = null)
    {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }
}
