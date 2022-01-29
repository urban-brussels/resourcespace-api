<?php

namespace UrbanBrussels\ResourcespaceApi;

use DateTime;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class MediaQuery
{
    private Connexion $connexion;
    private ?string $language;
    private array $search_parameters;
    private string $function;
    private string $search;

    public function __construct(Connexion $connexion, ?string $language = null)
    {
        $this->connexion = $connexion;
        $this->language = $language;
    }

    public function getResults(): ?MediaCollection
    {
        $collection = new MediaCollection();

        $httpClient = HttpClient::create();

        try {
            $response = $httpClient->request(
                'GET',
                $this->connexion->getPath().$this->getQueryUrl().'&sign='.$this->connexion->getSign(
                    $this->getQueryUrl()
                ),
                $this->connexion->getAccessParameters()
            );
            $statusCode = $response->getStatusCode();

            if ($statusCode !== 200) {
                return null;
            }

            $results = json_decode($response->getContent(), true);

        } catch (TransportExceptionInterface) {
            return null;
        }

        foreach ($results as $result) {
            $media = new Media($result['ref']);
            $media->setLanguage($this->language);
            $media->setResourceType($result['resource_type']);
            $media->setFileSize((int)$result['file_size']);
            $media->setChecksum($result['file_checksum']);
            $media->setFileExtension($result['file_extension']);
            $media->setCreationDate(DateTime::createFromFormat('Y-m-d H:i:s', $result['creation_date']));
            $media->setModificationDate(DateTime::createFromFormat('Y-m-d H:i:s', $result['modified']));
            $media->setCreatedBy($result['created_by']);

            // Previews
            $previews = [];
            foreach ($result as $attribute => $value)
            {
                if(str_contains($attribute, 'url_')) {
                    $name = str_replace('url_', '', $attribute);
                    $previews[$name] = $value;
                }
            }
            $media->setPreviews($previews);

            $collection->addMedia($media);
        }

        return $collection;
    }

    public function getQueryFields(): array
    {
        $fields = array(
            'user' => $this->connexion->getUser(),
            'function' => $this->function,
        );

        if (isset($this->search)) {
            $fields['search'] = $this->search;
        }

        if (isset($this->search_parameters)) {
            foreach ($this->search_parameters as $k => $v) {
                $fields[$k] = $v;
            }
        }

        if (isset($this->resource)) {
            $fields['resource'] = $this->resource;
        }

        if (isset($this->ref)) {
            $fields['ref'] = $this->ref;
        }

        return $fields;
    }

    public function doSearch(string $search, array $search_parameters = []): self
    {
        $this->function = 'do_search';
        $this->search = $search;
        $this->search_parameters = $search_parameters;

        return $this;
    }

    public function searchGetPreviews(
        string $search,
        array $search_parameters = ['getsizes' => 'col,thm,scr,pre']
    ): self {
        $this->function = 'search_get_previews';
        $this->search = $search;
        $this->search_parameters = $search_parameters;

        return $this;
    }

    public function getUserCollections(): self
    {
        $this->function = 'get_user_collections';

        return $this;
    }

    public function searchPublicCollections(?string $search, array $search_parameters = []): self
    {
        $this->function = 'search_public_collections';

        return $this;
    }

    public function getQueryUrl(): string
    {
        return http_build_query($this->getQueryFields()).(isset($this->language) ? '&language='.$this->language : '');
    }
}