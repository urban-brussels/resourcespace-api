# Resourcespace API
PHP wrapper for the API of [ResourceSpace](https://www.resourcespace.com/) (Open Source Digital Asset Management).    
Method names and attributes based on the RS documentation here: [https://www.resourcespace.com/knowledge-base/api/](https://www.resourcespace.com/knowledge-base/api/)    
More features coming soon.

## Installation

```sh
composer require urban-brussels/resourcespace-api
```

## Usage

```php 
use UrbanBrussels\ResourcespaceApi\ResourceSpace;

$path = 'https://media.example.com/api/?'; // Path to the API part of your ResourceSpace instance
$user = 'rs_user'; // A username in your ResourceSpace Instance
$private_key = '3eabfbcbea3404b1b5c2f884ec8e86bf686cff53d484f4fb2744530721ff65dzerrs'; // Available at https://media.example.com/pages/api_test.php

// Create a ResourcespaceApi Connexion
$connexion = new Connexion($path, $user, $private_key);

// Build a query to retrieve one or several resources
$query = new MediaQuery($connexion);

// Find all resources matching a keyword or other criteria...
// https://www.resourcespace.com/knowledge-base/user/special-search-terms
$list = $query->doSearch('Apple')->getResults(); // By keyword
$list = $query->doSearch('!collection204')->getResults(); // By collection
$list = $query->doSearch('!list1000:1001:1002:1003')->getResults(); // By resource id
$list = $query->doSearch('!last10')->getResults(); // Last 10 resources

// ... or do some pagination and sorting if needed
// Based on the ResourceSpace API Documentation: https://www.resourcespace.com/knowledge-base/api/do_search
$list = $query->doSearch('Apple', ['offset' => 0, 'fetchrows' => 10, 'order_by' => 'date', 'sort' => 'desc'])->getResults();
// Based on the ResourceSpace API Documentation: https://www.resourcespace.com/knowledge-base/api/search_get_previews
$list = $query->searchGetPreviews('Apple', ['getsizes' => 'col,thm,scr,pre', 'fetchrows' => 10, 'order_by' => 'date', 'sort' => 'desc'])->getResults(); // Interesting because you retrieve the previews, but don't have access to offset

// You get a collection of Medias that you can hydrate, adding metadata for example 
// Caution: it sends a GET query each time... use it wisely (offsets, cache, etc).
foreach($list->medias as $key => $value) 
{
    $list->medias[$key]->addPreviewsData($connexion); // Add previews (already retrieved if you used "searchGetPreviews" but not with "doSearch")
    $list->medias[$key]->addFieldsData($connexion, 'fr'); // If you use multiple languages, specify the one you want to get
}

```
