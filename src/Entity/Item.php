<?php declare(strict_types=1);

namespace VysokeSkoly\AppStatusBundle\Entity;

class Item
{
    private string $title;
    private string $value;
    private ?string $color;

    public function __construct(string $title, string $value, ?string $color = null)
    {
        $this->title = $title;
        $this->value = $value;
        $this->color = $color;
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
