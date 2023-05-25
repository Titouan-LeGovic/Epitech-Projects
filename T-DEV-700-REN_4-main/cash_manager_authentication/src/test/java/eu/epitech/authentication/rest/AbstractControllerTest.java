package eu.epitech.authentication.rest;

import org.springframework.data.mongodb.core.ReactiveMongoTemplate;
import org.springframework.http.HttpMethod;
import org.springframework.http.HttpStatus;
import org.springframework.test.web.reactive.server.WebTestClient;
import org.springframework.web.reactive.function.BodyInserters;

import java.util.List;

public abstract class AbstractControllerTest {

    protected final static String TOKEN = "eyJhbGciOiJIUzUxMiJ9.eyJvcmdhbml6YXRpb25JZCI6IjYyMDM1YWRjZjYwY2Q3NmU5ZjdmYmVlOSIsInJvbGUiOiJST0xFX09XTkVSIiwiaWQiOiI2MjAzNWFkY2Y2MGNkNzZlOWY3ZmJlZTgiLCJlbWFpbCI6Imp1bGlldHRlLnBvaXJpZXI5NUBvdXRsb29rLmNvbSIsInN1YiI6IjYyMDM1YWRjZjYwY2Q3NmU5ZjdmYmVlOCIsImlhdCI6MTY0NTA5MDg4MX0.YkkO8mlYbP_fsLVAUKLgnRzm5s-8evaJGwzYAUssb99r5EXn9gTWRqO0M_gOPz-uaL_0tKPijDe1tz-1Y-4oXw";

    protected final static String ADMIN_TOKEN = "eyJhbGciOiJIUzUxMiJ9.eyJvcmdhbml6YXRpb25JZCI6IjYxMmUxMjYxY2RkOGRmNGUyMmQxNWNmOCIsInJvbGUiOiJST0xFX0FETUlOIiwiaWQiOiI2MTJlMTI2MWNkZDhkZjRlMjJkMTVjZjciLCJlbWFpbCI6Imd1aWxsYXVtZUBwcm9zcC5pbyIsInN1YiI6IjYxMmUxMjYxY2RkOGRmNGUyMmQxNWNmNyIsImlhdCI6MTYzNDQ1NjQyMSwiZXhwIjozNjAxNjM0NDU2NDIxfQ.CHrn-N4Y2fRcjCPCBNO5yJKAt4_fKdYCJvbzYLTI29p19J-zw6Xr_QUZXFCaztqvp7NfmtDQ4n1LNifQCQQ8Lg";

    protected final static String BAD_TOKEN = "eyJhBGciOiJIUzUxMiJ9.eyJvcmdhbml6YXRpb25JZCI6IjYyMDM1YWRjZjYwY2Q3NmU5ZjdmYmVlOSIsInJvbGUiOiJST0xFX09XTkVSIiwiaWQiOiI2MjAzNWFkY2Y2MGNkNzZlOWY3ZmJlZTgiLCJlbWFpbCI6Imp1bGlldHRlLnBvaXJpZXI5NUBvdXRsb29rLmNvbSIsInN1YiI6IjYyMDM1YWRjZjYwY2Q3NmU5ZjdmYmVlOCIsImlhdCI6MTY0NTA5MDg4MX0.YkkO8mlYbP_fsLVAUKLgnRzm5s-8evaJGwzYAUssb99r5EXn9gTWRqO0M_gOPz-uaL_0tKPijDe1tz-1Y-4oXw";

    protected final static String EXPIRED_TOKEN = "eyJhbGciOiJIUzUxMiJ9.eyJvcmdhbml6YXRpb25JZCI6IjYyMjllMGMxN2QzMDcyMTQzMjdhODZiMSIsInJvbGUiOiJST0xFX09XTkVSIiwiaWQiOiI2MjI5ZTBjMTdkMzA3MjE0MzI3YTg2YjAiLCJlbWFpbCI6ImJvcmlzQHNrYWx5LmNvIiwic3ViIjoiNjIyOWUwYzE3ZDMwNzIxNDMyN2E4NmIwIiwiaWF0IjoxNjQ3NDE4OTEzLCJleHAiOjE2NDc0MjI1MTN9.KRPyOHrRXmi692hSM10T7vSYE3PLyLhrHlwSU4zf5Xvp8Gr-t2vxhSpwMFyVuKer8oe39HolgdTROu32uf8kGA";

    protected final ReactiveMongoTemplate template;

    protected void shouldAccessWithNoToken(WebTestClient webTestClient,
                                                  String path,
                                                  HttpMethod method,
                                                  Object body,
                                                  HttpStatus expectedStatus) {
        WebTestClient.RequestBodyUriSpec spec = webTestClient.method(method);
        if (method.equals(HttpMethod.POST) || method.equals(HttpMethod.PUT) && body != null) {
            spec.body(BodyInserters.fromValue(body));
        }
        spec.uri(path)
                .exchange()
                .expectStatus()
                .isEqualTo(expectedStatus);
    }

    protected void shouldNotAccessWithBadToken(WebTestClient webTestClient,
                                               String path,
                                               HttpMethod method) {
        webTestClient.method(method)
                .uri(path)
                .header("Authorization", String.format("Bearer %s", BAD_TOKEN))
                .exchange()
                .expectStatus()
                .isUnauthorized();
    }

    protected void shouldNotAccessWithExpiredToken(WebTestClient webTestClient,
                                                   String path,
                                                   HttpMethod method) {
        webTestClient.method(method)
                .uri(path)
                .header("Authorization", String.format("Bearer %s", EXPIRED_TOKEN))
                .exchange()
                .expectStatus()
                .isUnauthorized();
    }

    protected AbstractControllerTest(ReactiveMongoTemplate template) {
        this.template = template;
    }

    protected <K> void setup(List<? extends K> list) {
        template.insertAll(list).blockLast();
    }

    protected <K> void clean(Class<K> clazz) {
        template.remove(clazz).findAndRemove().blockLast();
    }
}
