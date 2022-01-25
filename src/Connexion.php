<?php

namespace UrbanBrussels\ResourcespaceApi;

class Connexion
{
    private string $path;
    private string $user;
    private string $private_key;
    private array $access_parameters;

    public function __construct(string $path, string $user, string $private_key, array $access_parameters = [])
    {
        $this->path = $path;
        $this->user = $user;
        $this->private_key = $private_key;
        $this->access_parameters = $access_parameters;
    }

    public function getSign(string $query_url): string
    {
        return hash("sha256",$this->private_key . $query_url);
    }

    public function getPrivateKey(): string
    {
        return $this->private_key;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getAccessParameters(): array
    {
        return $this->access_parameters;
    }
}