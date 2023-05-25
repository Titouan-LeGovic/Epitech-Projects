package eu.epitech.authentication.controller;

import eu.epitech.authentication.exception.ValidationException;
import eu.epitech.authentication.payload.AuthMessage;
import eu.epitech.authentication.payload.LoginRequest;
import eu.epitech.authentication.payload.RefreshTokenRequest;
import eu.epitech.authentication.payload.ValidationError;
import eu.epitech.authentication.service.AuthenticationService;
import eu.epitech.authentication.util.Utils;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.MediaType;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import reactor.core.publisher.Mono;

import java.util.Collections;

@RestController
@RequestMapping(produces = MediaType.APPLICATION_JSON_VALUE)
public class AuthenticationController {
    private static final Logger LOGGER = LoggerFactory.getLogger(AuthenticationController.class);
    private final AuthenticationService authenticationService;

    @Autowired
    public AuthenticationController(AuthenticationService authenticationService) {
        this.authenticationService = authenticationService;
    }

    @PostMapping("/login")
    public Mono<ResponseEntity<AuthMessage>> login(@RequestBody LoginRequest loginRequest){
        return authenticationService.authenticationVerification(loginRequest);
    }

    @DeleteMapping("/logout")
    public Mono< ResponseEntity< Void>> logout(@RequestHeader("Authorization") String authorization){
        return authenticationService.sessionDisconnected(authorization);
    }

    @PostMapping("/refresh")
    public Mono<ResponseEntity<AuthMessage>> refresh(@RequestBody RefreshTokenRequest refreshTokenRequest){
        if (!refreshTokenRequest.grantType().equals("refresh_token")) {
            throw new ValidationException(Utils.writeValueAsString(Collections.singleton(new ValidationError("ERROR.REFRESH.TOKEN.GRANT.TYPE", "grantType"))));
        }
        return authenticationService.refreshSession(refreshTokenRequest);
    }
}
