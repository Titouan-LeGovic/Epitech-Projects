package eu.epitech.authentication.service;

import eu.epitech.authentication.payload.AuthMessage;
import eu.epitech.authentication.payload.LoginRequest;
import eu.epitech.authentication.payload.RefreshTokenRequest;
import org.springframework.http.ResponseEntity;
import reactor.core.publisher.Mono;

public interface AuthenticationService {

    Mono<ResponseEntity<AuthMessage>> authenticationVerification(LoginRequest loginRequest);
    Mono<ResponseEntity<Void>> sessionDisconnected(String authorization);
    Mono<ResponseEntity<AuthMessage>> refreshSession(RefreshTokenRequest refreshTokenRequest);
}
