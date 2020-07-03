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
            "with": "", // Regex pattern to match on for the url.
            "multi_body": [{ // On consecutive calls return one after the other.
                "..."
            }],
            "body": { // Standard response
                "id": "abc123",
                "name": "Wahab Qureshi"
            }
        }]
     }
}
```

Purge dynamic mocks
-----

To purge all dynamic mocks created send `GET` request to `/mocks?purge=true`
