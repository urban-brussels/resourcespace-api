<?php

namespace UrbanBrussels\ResourcespaceApi;

class MediaCollection implements \Iterator
{
    public array $medias;

    protected int $position = 0;

    public function __construct()
    {
        $this->medias = [];
    }

    public function addMedia(Media $media): void
    {
        $this->medias[] = $media;
    }

    public function removeMedia(Media $media): void
    {
        $key = array_search($media, $this->medias, true);
        unset($this->medias[$key]);
    }

    public function getMedias(): array
    {
        return $this->medias;
    }

    public function current(): int
    {
        return $this->medias[$this->position];
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->items[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }
}