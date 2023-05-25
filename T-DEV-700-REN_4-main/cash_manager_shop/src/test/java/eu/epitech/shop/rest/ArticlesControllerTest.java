package eu.epitech.shop.rest;

import com.fasterxml.jackson.core.type.TypeReference;
import com.fasterxml.jackson.databind.ObjectMapper;
import eu.epitech.shop.TestConfiguration;
import eu.epitech.shop.model.Articles;
import eu.epitech.shop.payload.CreateArticles;
import eu.epitech.shop.repository.ArticleRepository;
import eu.epitech.shop.rest.AbstractControllerTest;
import org.junit.jupiter.api.*;
import org.mockito.Mock;
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
class ArticlesControllerTest extends AbstractControllerTest {

    protected final ObjectMapper mapper;

    protected final WebTestClient webTestClient;


    @Autowired
    public ArticlesControllerTest(ReactiveMongoTemplate template,
                                  ObjectMapper mapper,
                                  WebTestClient webTestClient) {
        super(template);
        this.mapper = mapper;
        this.webTestClient = webTestClient;
    }

    @Test
    void shouldGetArticlesByJosephine(){
        shouldAccessWithGoodOwnerToken(webTestClient, "/articles/me", HttpMethod.GET, null, HttpStatus.OK);
    }
    @Test
    void shouldGetAllArticles(){
        shouldAccessWithGoodOwnerToken(webTestClient, "/articles", HttpMethod.GET, null, HttpStatus.OK);
    }

    @Test
    void createArticles(){
        CreateArticles input = new CreateArticles(
                "moto",
                6000,
                "moto yamaha de 1200cc"
        );
        HttpHeaders responseHeaders = webTestClient.post()
                .uri("/articles/create")
                .header("Authorization", String.format("Bearer %s", TOKEN))
                .body(BodyInserters.fromValue(input))
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
                .expectStatus().isOk()
                .expectBody()
                .jsonPath("$.name").isEqualTo("moto")
                .jsonPath("$.price").isEqualTo(6000);
    }

    @Test
    void deleteArticle(){
        webTestClient.delete()
                .uri("articles/delete/63849391b7cfe11a6dd1572c")
                .header("Authorization", String.format("Bearer %s", TOKEN))
                .exchange()
                .expectStatus().isNoContent();
        webTestClient.get()
                .uri("articles/63849391b7cfe11a6dd1572c")
                .header("Authorization", String.format("Bearer %s", TOKEN))
                .exchange()
                .expectStatus().isNotFound();
    }



    @BeforeEach
    void setup() throws Exception {

        List<Articles> articles = mapper.readValue(new ClassPathResource("data/articles.json").getFile(), new TypeReference<>() {
        });
        super.setup(articles);
    }

    @AfterEach
    void clean() throws Exception {
        super.clean(Articles.class);
    }
}
