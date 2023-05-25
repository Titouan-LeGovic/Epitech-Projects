package eu.epitech.authentication.repository;

import eu.epitech.authentication.model.Users;
import org.springframework.data.repository.reactive.ReactiveCrudRepository;
import org.springframework.stereotype.Repository;
import reactor.core.publisher.Mono;

@Repository
public interface UserRepository extends ReactiveCrudRepository<Users, String> {
    Mono<Users> findByEmailAndDeletedIsFalse(String email);

    Mono<Users> findByIdAndDeletedIsFalse(String id);
}
