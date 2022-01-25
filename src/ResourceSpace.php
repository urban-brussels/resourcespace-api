<?php
namespace UrbanBrussels\ResourcespaceApi;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ResourceSpace
{
    private string $path;
    private string $user;
    private string $private_key;
    private array $access_parameters;
    private string $query_url;
    private string $function;
    private ?string $search;
    private array $search_parameters;
    private int $resource;
    private string|int $ref;
    private array $results;
    private Connexion $connexion;

    public function __construct(Connexion $connexion)
    {
        $this->connexion = $connexion;
    }

    public function getResults(): self
    {
        $list = [];
        $httpClient = HttpClient::create();

        try {
            $response = $httpClient->request(
                'GET',
                $this->connexion->getPath().$this->getQueryUrl().'&sign='.$this->connexion->getSign($this->getQueryUrl()),
                $this->connexion->getAccessParameters()
            );
            $statusCode = $response->getStatusCode();

            if ($statusCode !== 200) {
                return $this;
            }

            $results = json_decode($response->getContent(), true);

        } catch (TransportExceptionInterface) {
            return $this;
        }

        foreach ($results as $result) {
            $list[] = new Resource($this->connexion, $result['ref'], true, $result);
        }

        $this->results = $list;

        return $this;
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

    public function doSearch(string $search, array $search_parameters = []): self {
        $this->function = 'do_search';
        $this->search = $search;
        $this->search_parameters = $search_parameters;
        return $this;
    }

    public function searchGetPreviews(string $search, array $search_parameters = ['getsizes' => 'col,thm,scr,pre', 'fetchrows' => 50]): self {
        $this->function = 'search_get_previews';
        $this->search = $search;
        $this->search_parameters = $search_parameters;
        return $this;
    }

    public function getUserCollections(): self {
        $this->function = 'get_user_collections';
        return $this;
    }

    public function getResourceFieldData(int $resource): self
    {
        $this->function = 'get_resource_field_data';
        $this->resource = $resource;
        return $this;
    }

    public function searchPublicCollections(?string $search, array $search_parameters = []): self
    {
        $this->function = 'search_public_collections';
        return $this;
    }

    public function getCollection(int $ref): self
    {
        $this->function = 'get_collection';
        $this->collectionId = $ref;
        return $this;
    }

    public function getFieldOptions(string|int $ref, array $search_parameters = []): self
    {
        $this->function = 'get_field_options';
        $this->ref = $ref;
        return $this;
    }

    public function getQueryUrl(): string
    {
        return http_build_query($this->getQueryFields());
    }
}
