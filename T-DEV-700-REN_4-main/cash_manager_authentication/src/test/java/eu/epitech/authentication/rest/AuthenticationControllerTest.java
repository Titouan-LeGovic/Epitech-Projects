package eu.epitech.authentication.rest;

import com.google.gson.reflect.TypeToken;
import eu.epitech.authentication.TestConfiguration;
import eu.epitech.authentication.model.Users;
import eu.epitech.authentication.payload.LoginRequest;
import com.fasterxml.jackson.core.type.TypeReference;
import com.fasterxml.jackson.databind.ObjectMapper;
import com.google.gson.GsonBuilder;
import com.google.gson.JsonDeserializer;
import org.junit.jupiter.api.*;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.context.SpringBootTest;
import org.springframework.core.io.ClassPathResource;
import org.springframework.data.mongodb.core.ReactiveMongoTemplate;
import org.springframework.http.HttpHeaders;
import org.springframework.test.context.ActiveProfiles;
import org.springframework.test.context.ContextConfiguration;
import org.springframework.test.web.reactive.server.WebTestClient;
import org.springframework.web.reactive.function.BodyInserters;

import java.io.FileReader;
import java.io.Reader;
import java.time.Instant;
import java.time.LocalDateTime;
import java.time.ZoneId;
import java.util.List;

@ContextConfiguration(classes = TestConfiguration.class)
@SpringBootTest(webEnvironment = SpringBootTest.WebEnvironment.RANDOM_PORT)
@ActiveProfiles("test")
@TestMethodOrder(MethodOrderer.MethodName.class)
class AuthenticationControllerTest extends AbstractControllerTest {

    private final WebTestClient webTestClient;

    @Autowired
    protected AuthenticationControllerTest(ReactiveMongoTemplate template,
                                           WebTestClient webTestClient) {
        super(template);
        this.webTestClient = webTestClient;
    }

    /**
     * Login tests
     */

    @Test
    void shouldLoginExistingUser() {
        LoginRequest input = new LoginRequest("bernadette@email.com", "Test12345@");
        webTestClient.post()
                .uri("/login")
                .body(BodyInserters.fromValue(input))
                .exchange()
                .expectStatus()
                .isOk()
                .expectBody()
                .jsonPath("$.accessToken").isNotEmpty()
                .jsonPath("$.tokenType").isEqualTo("Bearer")
                .jsonPath("$.refreshToken").isNotEmpty();
    }

    @Test
    void shouldNotLoginExistingUserBecauseBadPassword() {
        LoginRequest input = new LoginRequest("guillaume@skaly.co", "Ktq37SKkxbj^4if@SFz");
        webTestClient.post()
                .uri("login")
                .body(BodyInserters.fromValue(input))
                .exchange()
                .expectStatus()
                .isUnauthorized()
                .expectBody()
                .jsonPath("$.message").isEqualTo("ERROR.HTTP.401.BAD.CREDENTIALS")
                .jsonPath("$.status").isEqualTo(401);
    }

    @Test
    void shouldNotLoginBecauseEmailNotFound() {
        LoginRequest input = new LoginRequest("toto@skaly.co", "Ktq37SKkxbj^4if@SFzk");
        webTestClient.post()
                .uri("login")
                .body(BodyInserters.fromValue(input))
                .exchange()
                .expectStatus()
                .isUnauthorized()
                .expectBody()
                .jsonPath("$.message").isEqualTo("ERROR.HTTP.401.BAD.CREDENTIALS")
                .jsonPath("$.status").isEqualTo(401);
    }

    @BeforeEach
    void setup() throws Exception {
        Reader reader = new FileReader(new ClassPathResource("data/users.json").getFile());
        List<Users> users = new GsonBuilder().registerTypeAdapter(LocalDateTime.class, (JsonDeserializer<LocalDateTime>) (json, type, jsonDeserializationContext) -> {
            Instant instant = Instant.ofEpochMilli(json.getAsJsonPrimitive().getAsLong());
            return LocalDateTime.ofInstant(instant, ZoneId.systemDefault());
        }).create()
                .fromJson(reader, new TypeToken<List<Users>>() {
        }.getType());
        super.setup(users);
    }

    @AfterEach
    void clean() {
        super.clean(Users.class);
    }


}
