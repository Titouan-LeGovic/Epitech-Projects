package eu.epitech.authentication.repository;

import eu.epitech.authentication.model.RefreshToken;
import org.springframework.data.repository.reactive.ReactiveCrudRepository;
import org.springframework.stereotype.Repository;
import reactor.core.publisher.Mono;

@Repository
public interface RefreshTokenRepository extends ReactiveCrudRepository<RefreshToken, String> {
    Mono<RefreshToken> findByTokenAndDeletedIsFalse(String token);
    Mono<RefreshToken> findByUserIdAndDeletedIsFalse(String userId);
}
