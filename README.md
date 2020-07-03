API mocker
------

Have an application that depends on an API but is too much of a burden on testing? Replace your API with a mock API using this package and mock requests on the fly.

Start mocker:

```
docker-compose up
```

Static mocks
------

You can add your static routes (ones that will be available as soon as you boot up the mock API) in the routing.php file.

Dynamic mocks
------

To mock a request `POST` your mocking request to `/mocks` with `mockData` json. Example mock request:

```json
# POST /mocks
{
    "mockData": {
        "url": "/user/abc123",
        "get": [{
            "body": {
                "id": "abc123",
                "name": "Wahab Qureshi"
            }
        }]
     }
}
```

Full mock data options:

```json
{
    "mockData": {
        "url": "/user/abc123", //  The URI to mock.
        "get": [{ // The method type to mock for.
            "with": "/abc123/", // Regex pattern to match on for the url.
            "response_code": 301, // The response code to return.
            "headers": {"lola": "123", "baby boo": "dudu"}, // The headers to return.
            "consecutive_responses": [{ // On consecutive calls return one after the other. Supports response_code, headers and body.
                "..."
            }],
            "body": { // Standard response, define multiple using with expression.
                "id": "abc123",
                "name": "Wahab Qureshi"
            },
            "proxy": { // Proxy the response through another site.
                "url": "http://google.com",
                "headers": {
                    "app-id": "88374783847"
                }
            }
        }]
     }
}
```

Purge dynamic mocks
-----

To purge all dynamic mocks created send `GET` request to `/mocks?purge=true`
