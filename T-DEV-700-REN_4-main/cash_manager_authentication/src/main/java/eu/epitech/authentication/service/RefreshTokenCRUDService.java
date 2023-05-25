package eu.epitech.authentication.service;

import eu.epitech.authentication.model.RefreshToken;
import reactor.core.publisher.Mono;

public interface RefreshTokenCRUDService {
    Mono<RefreshToken> findByTokenAndDeletedIsFalse(String tokenRefresh);
}
