package eu.epitech.shop.rest;

import com.fasterxml.jackson.core.type.TypeReference;
import com.fasterxml.jackson.databind.ObjectMapper;
import eu.epitech.shop.TestConfiguration;
import eu.epitech.shop.model.Articles;
import eu.epitech.shop.model.Carts;
import eu.epitech.shop.payload.CreateArticles;
import org.junit.jupiter.api.*;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.context.SpringBootTest;
import org.springframework.core.io.ClassPathResource;
import org.springframework.data.mongodb.core.ReactiveMongoTemplate;
import org.springframework.http.HttpHeaders;
import org.springframework.http.HttpMethod;
import org.springframework.http.HttpStatus;
import org.springframework.test.context.ActiveProfiles;
import org.springframework.test.context.ContextConfiguration;
import org.springframework.test.web.reactive.server.WebTestClient;
import org.springframework.web.reactive.function.BodyInserters;

import java.util.List;

@ContextConfiguration(classes = TestConfiguration.class)
@SpringBootTest(webEnvironment = SpringBootTest.WebEnvironment.RANDOM_PORT)
@ActiveProfiles("test")
@TestMethodOrder(MethodOrderer.MethodName.class)
class CartsControllerTest extends AbstractControllerTest{

    protected final ObjectMapper mapper;

    protected final WebTestClient webTestClient;

    @Autowired
    public CartsControllerTest(ReactiveMongoTemplate template,
                               ObjectMapper mapper,
                               WebTestClient webTestClient) {
        super(template);
        this.mapper = mapper;
        this.webTestClient = webTestClient;
    }

    @Test
    void shouldGetArticlesByJosephine(){
        shouldAccessWithGoodOwnerToken(webTestClient, "/carts", HttpMethod.GET, null, HttpStatus.OK);
    }

    @Test
    void addArticlesInCarts(){
        HttpHeaders responseHeaders = webTestClient.post()
                .uri("/carts/add/6384a972b7cfe11a6dd1572d")
                .header("Authorization", String.format("Bearer %s", TOKEN))
                .exchange()
                .expectStatus()
                .isCreated()
                .returnResult(Void.class)
                .getResponseHeaders();
        List<String> locations = responseHeaders.get("Location");
        Assertions.assertNotNull(locations);
        Assertions.assertFalse(locations.isEmpty());
        String location = locations.get(0);
        webTestClient.get()
                .uri(location)
                .header("Authorization", String.format("Bearer %s", TOKEN))
                .exchange()
                .expectStatus().isOk();
    }


    @Test
    void deleteArticle(){
        webTestClient.get()
                .uri("carts/638de9d23516123e7909f7bf")
                .header("Authorization", String.format("Bearer %s", TOKEN))
                .exchange()
                .expectStatus().isOk();
        webTestClient.delete()
                .uri("carts/delete/6384a972b7cfe11a6dd1572d")
                .header("Authorization", String.format("Bearer %s", TOKEN))
                .exchange()
                .expectStatus().isNoContent();
        webTestClient.get()
                .uri("carts/638de9d23516123e7909f7bf")
                .header("Authorization", String.format("Bearer %s", TOKEN))
                .exchange()
                .expectStatus().isNotFound();
    }

    @BeforeEach
    void setup() throws Exception {

        List<Articles> articles = mapper
                .readValue(new ClassPathResource("data/articles.json").getFile(), new TypeReference<>() {});
        List<Carts> carts = mapper
                .readValue(new ClassPathResource("data/carts.json").getFile(), new TypeReference<>() {});
        super.setup(articles);
        super.setup(carts);
    }

    @AfterEach
    void clean() throws Exception {
        super.clean(Articles.class);
        super.clean(Carts.class);
    }
}
