Feature:
    In order to know that the mock gateway works as expected
    As a test automation expert
    I want to try out some sample scenarios

    Scenario: Static request
        When I request '/countries/list' using HTTP 'get'
        Then the response code is 200
        And the "Access-Control-Allow-Headers" response header is "*"
        And the "Access-Control-Allow-Origin" response header is "*"
        And the response body contains JSON:
            """
            {
                "status": "success",
                "data": [
                    {
                        "UUID": "436884F0-6B5B-11E9-AFB8-6F0A7BD2CFEC",
                        "summary": "Africa",
                        "parent": null
                    }
                ]
            }
            """

    Scenario: fallback to static response when no dynamic request matches
        When the request body is:
            """
            {
                "mockData": {
                    "url": "/testing/ports/abc123",
                    "get": [{
                        "with": "/id=abc/",
                        "body": {
                            "UUID": "theportuuidgoeshere",
                            "summary": "theportsummarygoeshere"
                        }
                    }]
                }
            }
            """
        And I request '/' using HTTP 'post'
        Then the response code is 200

        When I request '/testing/destinations' using HTTP 'get'
        Then the response code is 200
        And the "Access-Control-Allow-Headers" response header is "*"
        And the "Access-Control-Allow-Origin" response header is "*"
        And the response body contains JSON:
            """
            {
                "status": "success",
                "data": [
                    {
                        "UUID": "436884F0-6B5B-11E9-AFB8-6F0A7BD2CFEC",
                        "summary": "Africa",
                        "parent": null
                    }
                ]
            }
            """

    Scenario: Dynamic mock with single response and defaults
        When the request body is:
            """
            {
                "mockData": {
                    "url": "/testing/ports/abc123",
                    "get": [{
                        "body": {
                            "UUID": "theportuuidgoeshere",
                            "summary": "theportsummarygoeshere"
                        }
                    }]
                }
            }
            """
        And I request '/' using HTTP 'post'
        Then the response code is 200

        When I request '/testing/ports/abc123'
        Then the response code is 200
        And the "Access-Control-Allow-Headers" response header is "*"
        And the "Access-Control-Allow-Origin" response header is "*"
        And the response body contains JSON:
            """
            {
                "UUID": "theportuuidgoeshere",
                "summary": "theportsummarygoeshere"
            }
            """

    Scenario: Dynamic mock with single response, headers and code
        When the request body is:
            """
            {
                "mockData": {
                    "url": "/testing/ports/abc123",
                    "post": [{
                        "response_code": 205,
                        "headers": {"lola": "123", "baby boo": "dudu"},
                        "body": {
                            "UUID": "theportuuidgoeshere",
                            "summary": "theportsummarygoeshere"
                        }
                    }]
                }
            }
            """
        And I request '/' using HTTP 'post'
        Then the response code is 200

        When the request body is:
            """
            """
        And I request '/testing/ports/abc123' using HTTP "post"
        Then the response code is 205
        And the "Access-Control-Allow-Headers" response header is "*"
        And the "Access-Control-Allow-Origin" response header is "*"
        Then the "lola" response header is "123"
        And the "baby boo" response header is "dudu"
        And the response body contains JSON:
            """
            {
                "UUID": "theportuuidgoeshere",
                "summary": "theportsummarygoeshere"
            }
            """

    Scenario: Dynamic mock with regex pattern and sinlge response
        When the request body is:
            """
            {
                "mockData": {
                    "url": "/testing/ports/",
                    "delete": [{
                        "response_code": 210,
                        "body": {
                            "id": "77"
                        }
                    }, {
                        "with": "/def=123/",
                        "response_code": 206,
                        "body": {
                            "UUID": "yeah",
                            "summary": "no"
                        }
                    }, {
                        "with": "/abc=\\d+/",
                        "response_code": 205,
                        "headers": {"lola": "123", "baby boo": "dudu"},
                        "body": {
                            "UUID": "theportuuidgoeshere",
                            "summary": "theportsummarygoeshere"
                        }
                    }]
                }
            }
            """
        And I request '/' using HTTP 'post'
        Then the response code is 200

        When the request body is:
            """
            """
        And I request '/testing/ports/?abc=123' using HTTP "delete"
        Then the response code is 205
        And the "Access-Control-Allow-Headers" response header is "*"
        And the "Access-Control-Allow-Origin" response header is "*"
        Then the "lola" response header is "123"
        And the "baby boo" response header is "dudu"
        And the response body contains JSON:
            """
            {
                "UUID": "theportuuidgoeshere",
                "summary": "theportsummarygoeshere"
            }
            """

        When I request '/testing/ports/' using HTTP "delete"
        Then the response code is 210
        And the response body contains JSON:
            """
            {
                "id": "77"
            }
            """

    Scenario: Dynamic mock with regex pattern enforces structure
        When the request body is:
            """
            {
                "mockData": {
                    "url": "/testing/ports/",
                    "delete": [{
                        "with": "/def=123/",
                        "response_code": 206,
                        "body": {
                            "UUID": "yeah",
                            "summary": "no"
                        }
                    }, {
                        "response_code": 210,
                        "body": {
                            "id": "77"
                        }
                    }, {
                        "with": "/abc=\\d+/",
                        "response_code": 205,
                        "headers": {"lola": "123", "baby boo": "dudu"},
                        "body": {
                            "UUID": "theportuuidgoeshere",
                            "summary": "theportsummarygoeshere"
                        }
                    }]
                }
            }
            """
        And I request '/' using HTTP 'post'
        Then the response code is 500
        And the response body is:
            """
            {"status":"error","msg":"[ERROR]: Each response after the first must include a with regex pattern to match on."}
            """

    Scenario: Dynamic mock with consecutive response
        When the request body is:
            """
            {
                "mockData": {
                    "url": "/testing/ports/abc",
                    "put": [{
                        "consecutive_responses": [{
                            "response_code": 205,
                            "headers": {"lola": "123", "baby boo": "dudu"},
                            "body": {
                                "UUID": "theportuuidgoeshere",
                                "summary": "theportsummarygoeshere"
                            }
                        }, {
                            "body": {
                                "UUID": "another",
                                "summary": "one"
                            }
                        }, {
                            "response_code": 500,
                            "body": "{}"
                        }
                    ]}]
                }
            }
            """
        And I request '/' using HTTP 'post'
        Then the response code is 200

        When the request body is:
            """
            """
        When I request '/testing/ports/abc' using HTTP "PUT"
        Then the response code is 205
        Then the "lola" response header is "123"
        And the "baby boo" response header is "dudu"
        And the response body contains JSON:
            """
            {
                "UUID": "theportuuidgoeshere",
                "summary": "theportsummarygoeshere"
            }
            """

        When I request '/testing/ports/abc' using HTTP "PUT"
        Then the response code is 200
        Then the "lola" response header does not exist
        And the "baby boo" response header does not exist
        And the response body contains JSON:
            """
            {
                "UUID": "another",
                "summary": "one"
            }
            """

        When I request '/testing/ports/abc' using HTTP "PUT"
        Then the response code is 500
        Then the "lola" response header does not exist
        And the "baby boo" response header does not exist
        And the response body is an empty JSON object

        When I request '/testing/ports/abc' using HTTP "PUT"
        Then the response code is 200
        Then the "lola" response header does not exist
        And the "baby boo" response header does not exist
        And the response body is:
            """
            null
            """

    Scenario: Mutli method mock
        When the request body is:
            """
            {
                "mockData": {
                    "url": "/countries/list/",
                    "get": [{
                        "body": [{
                            "id": "58",
                            "name": "UK"
                        }, {
                            "id": 88,
                            "name": "Pakistan"
                        }]
                    }],
                    "post": [{
                        "response_code": 201,
                        "body": {
                            "status": "success",
                            "message": "country created successfully."
                        }
                    }],
                    "delete": [{
                        "body": {
                            "status": "success",
                            "message": "country deleted successfully."
                        }
                    }],
                    "put": [{
                        "body": {
                            "status": "success",
                            "message": "country updated successfully."
                        }
                    }],
                    "options": [{
                        "response_code": 277,
                        "headers": {
                            "abc": 123,
                            "X-server": "nginx"
                        }
                    }]
                }
            }
            """
        And I request '/' using HTTP 'post'
        Then the response code is 200

        When I request '/countries/list/' using HTTP "get"
        Then the response code is 200
        And the "Access-Control-Allow-Headers" response header is "*"
        And the "Access-Control-Allow-Origin" response header is "*"
        And the response body contains JSON:
            """
            [{
                "id": "58",
                "name": "UK"
            }, {
                "id": 88,
                "name": "Pakistan"
            }]
            """

        When the request body is:
            """
            """
        When I request '/countries/list' using HTTP "post"
        Then the response code is 201
        And the "Access-Control-Allow-Headers" response header is "*"
        And the "Access-Control-Allow-Origin" response header is "*"
        And the response body contains JSON:
            """
            {
                "status": "success",
                "message": "country created successfully."
            }
            """

        When I request '/countries/list/' using HTTP "delete"
        Then the response code is 200
        And the "Access-Control-Allow-Headers" response header is "*"
        And the "Access-Control-Allow-Origin" response header is "*"
        And the response body contains JSON:
            """
            {
                "status": "success",
                "message": "country deleted successfully."
            }
            """

        When I request '/countries/list/' using HTTP "put"
        Then the response code is 200
        And the "Access-Control-Allow-Headers" response header is "*"
        And the "Access-Control-Allow-Origin" response header is "*"
        And the response body contains JSON:
            """
            {
                "status": "success",
                "message": "country updated successfully."
            }
            """

        When I request '/countries/list' using HTTP "options"
        Then the response code is 277
        And the "Access-Control-Allow-Headers" response header is "*"
        And the "Access-Control-Allow-Origin" response header is "*"
        And the "abc" response header is "123"
        And the "X-server" response header is "nginx"
        And the response body is:
            """
            null
            """

    Scenario: Proxy a response
        When the request body is:
            """
            {
                "mockData": {
                    "url": "/countries/list",
                    "get": [{
                        "headers": {
                            "lola": "123",
                            "baby boo": "dudu",
                            "X-server": "nginx",
                            "set-cookies": ["lkh=65765"]
                        },
                        "proxy": {
                            "url": "http://google.com",
                            "headers": {
                                "app-id": "88374783847"
                            }
                        }
                    }]
                }
            }
            """
        And I request '/' using HTTP 'post'
        Then the response code is 200

        And I request "/countries/list" using HTTP "get"
        Then the "lola" response header is "123"
        And the "baby boo" response header is "dudu"
        And the "X-server" response header is "nginx"

    Scenario: Check existing mocks
        Given the request body is:
            """
            {
                "mockData": {
                    "url": "/countries/list",
                    "get": [{
                        "headers": {
                            "lola": "123",
                            "baby boo": "dudu",
                            "X-server": "nginx",
                            "set-cookies": ["lkh=65765"]
                        },
                        "proxy": {
                            "url": "http://google.com",
                            "headers": {
                                "app-id": "88374783847"
                            }
                        }
                    }]
                }
            }
            """
        And I request '/' using HTTP 'post'
        And the response code is 200

        And the request body is:
            """
            {
                "mockData": {
                    "url": "/countries/list/another/",
                    "get": [{
                        "with": "/filter=true/",
                        "body": {
                            "just another json": "object"
                        }
                    }]
                }
            }
            """
        And I request '/' using HTTP 'post'
        And the response code is 200

        When I request "/mocks" using HTTP 'get'
        Then the response code is 200
        And the response body is:
            """
            {"static":{"example.json":"{\n    \"mockData\": {\n        \"url\": \"\/countries\/list\",\n        \"get\": [{\n            \"body\": {\n                \"response_code\": 200,\n                \"status\": \"success\",\n                \"data\": [\n                    {\n                        \"UUID\": \"436884F0-6B5B-11E9-AFB8-6F0A7BD2CFEC\",\n                        \"summary\": \"Africa\",\n                        \"parent\": null\n                    }\n                 ]\n             }\n        }]\n    }\n}\n","example2.json":"{\n    \"mockData\": {\n        \"url\": \"\/testing\/destinations\",\n        \"get\": [{\n            \"body\": {\n                \"status\": \"success\",\n                \"data\": [\n                    {\n                        \"UUID\": \"436884F0-6B5B-11E9-AFB8-6F0A7BD2CFEC\",\n                        \"summary\": \"Africa\",\n                        \"parent\": null\n                    }\n                ]\n            }\n        }]\n    }\n}"},"dynamic":{"\/tmp\/32ec1acdc7efc0ec66fec754e87d2158.json":"{\"get\":[{\"headers\":{\"lola\":\"123\",\"baby boo\":\"dudu\",\"X-server\":\"nginx\",\"set-cookies\":[\"lkh=65765\"]},\"proxy\":{\"url\":\"http:\\\/\\\/google.com\",\"headers\":{\"app-id\":\"88374783847\"}},\"with\":null}]}","\/tmp\/034beec6e8bd857d12a44b257fb78d3f.json":"{\"get\":[{\"with\":\"\\\/filter=true\\\/\",\"body\":{\"just another json\":\"object\"}}]}"}}
            """