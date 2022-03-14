# flux-open-id-connect-rest-api

Open Id Connect Rest Api

## Environment variables

First look at [flux-open-id-connect-api](https://github.com/fluxapps/flux-open-id-connect-api#environment-variables)

| Variable | Description | Default value |
| -------- | ----------- | ------------- |
| FLUX_OPEN_ID_CONNECT_REST_API_COOKIE_NAME | Cookie name | auth |
| FLUX_OPEN_ID_CONNECT_REST_API_COOKIE_EXPIRES_IN | Cookie expires in seconds | (Session end) |
| FLUX_OPEN_ID_CONNECT_REST_API_COOKIE_PATH | Cookie path | / |
| FLUX_OPEN_ID_CONNECT_REST_API_COOKIE_DOMAIN | Cookie domain | - |
| FLUX_OPEN_ID_CONNECT_REST_API_COOKIE_SECURE | Cookie secure | true |
| FLUX_OPEN_ID_CONNECT_REST_API_COOKIE_HTTP_ONLY | Cookie http only | true |
| FLUX_OPEN_ID_CONNECT_REST_API_COOKIE_SAME_SITE | Cookie same site<br>Lax, Strict or None | Lax |
| FLUX_OPEN_ID_CONNECT_REST_API_COOKIE_PRIORITY | Cookie priority<br>Low, Medium or High | Medium |
| FLUX_OPEN_ID_CONNECT_REST_API_SERVER_HTTPS_CERT | Path to HTTPS certificate file<br>Set this will enable listen on HTTPS<br>Should be on a volume | - |
| FLUX_OPEN_ID_CONNECT_REST_API_SERVER_HTTPS_KEY | Path to HTTPS key file<br>Should be on a volume | - |
| FLUX_OPEN_ID_CONNECT_REST_API_SERVER_LISTEN | Listen IP | 0.0.0.0 |
| FLUX_OPEN_ID_CONNECT_REST_API_SERVER_PORT | Listen port | 9501 |

Minimal variables required to set are **bold**

## Example

[examples](examples)
