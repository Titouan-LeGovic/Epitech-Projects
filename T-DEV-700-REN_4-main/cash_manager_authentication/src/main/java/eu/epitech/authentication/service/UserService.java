package eu.epitech.authentication.service;

import eu.epitech.authentication.model.Users;
import reactor.core.publisher.Mono;

public interface UserService {
    Mono<Users> findUserByIdAndDeletedIsFalse(String id);
}
