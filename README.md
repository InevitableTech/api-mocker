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

You can override a static mock with a dynamic one. Purging the mocks will revert the back to the static mock.

Full mock data options:

```json
{
    "mockData": {
        "url": "/user/abc123",
        "get": [{
            "with": "/abc123/",
            "response_code": 301,
            "headers": {"lola": "123", "baby boo": "dudu"},
            "body": {
                "id": "abc123",
                "name": "Wahab Qureshi"
            },
            "proxy": {
                "url": "http://google.com",
                "headers": {
                    "app-token": "88374783847"
                }
            },
            "consecutive_responses": [{
                "response_code": 205,
                "body": {
                    "id": "abc123",
                    "name": "Wahab Qureshi"
                }
            }, {
                "response_code": 500,
                "body": "internal server error"
            }]
        }]
     }
}
```

`mockData (object)`: Contains mock request information.
`mockData.url (string)`: The URL to mock, can be an existing statically mocked URL.
`mockData.<METHOD> ([]object)`: The method to mock for the URL.
`mockData.<METHOD>.with (?string)`: A regex pattern to be applied to the URL optionally.
`mockData.<METHOD>.response_code (?int)`: The response code to return optionally.
`mockData.<METHOD>.headers (?object)`: The headers to return.
`mockData.<METHOD>.body (mixed)`: The response content.
`mockData.<METHOD>.consecutive_responses (?[]object)`: On consecutive calls return one after the other. Supports response_code, headers and body.
`mockData.<METHOD>.proxy (?object)`: Proxy the response through another URL.

Purge dynamic mocks
-----

To purge all dynamic mocks created send `GET` request to `/mocks?purge=true`
