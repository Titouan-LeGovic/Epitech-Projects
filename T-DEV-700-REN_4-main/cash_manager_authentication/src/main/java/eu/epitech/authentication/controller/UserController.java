package eu.epitech.authentication.controller;

import eu.epitech.authentication.configuration.security.TokenProvider;
import eu.epitech.authentication.model.Users;
import eu.epitech.authentication.model.builder.UsersBuilder;
import eu.epitech.authentication.payload.UserCreateInput;
import eu.epitech.authentication.repository.UserRepository;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.MediaType;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.web.bind.annotation.*;
import reactor.core.publisher.Mono;

@RestController
@RequestMapping(path = "/users", produces = MediaType.APPLICATION_JSON_VALUE)
public class UserController {
    private static final Logger LOGGER = LoggerFactory.getLogger(UserController.class);
    private final UserRepository userRepository;
    private final TokenProvider tokenProvider;
    private final PasswordEncoder passwordEncoder;

    @Autowired
    public UserController(UserRepository userRepository, TokenProvider tokenProvider, PasswordEncoder passwordEncoder) {
        this.userRepository = userRepository;
        this.tokenProvider = tokenProvider;
        this.passwordEncoder = passwordEncoder;
    }

    @PostMapping("/create")
    public Mono<Users> createUser(@RequestBody UserCreateInput input){
        input.validate();
        Users user = UsersBuilder.anUsers()
                .withFirstname(input.firstname())
                .withLastname(input.lastname())
                .withEmail(input.email())
                .withPassword(passwordEncoder.encode(input.password()))
                .withRole(input.role())
                .build();
        return userRepository.save(user);
    }

    @GetMapping("/me")
    @PreAuthorize("hasAnyRole('USER','SALE','ADMIN')")
    public Mono<Users> displayMyDetail(@RequestHeader("Authorization") String authorization){
        String userId = tokenProvider.getUserIdFromAuthorization(authorization);
        LOGGER.info(userId);
        return userRepository.findByIdAndDeletedIsFalse(userId);
    }
}
