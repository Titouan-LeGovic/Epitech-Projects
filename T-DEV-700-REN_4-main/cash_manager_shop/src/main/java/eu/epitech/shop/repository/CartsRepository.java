package eu.epitech.shop.repository;

import eu.epitech.shop.model.Carts;
import org.springframework.data.repository.reactive.ReactiveCrudRepository;
import org.springframework.stereotype.Repository;
import reactor.core.publisher.Flux;
import reactor.core.publisher.Mono;

@Repository
public interface CartsRepository extends ReactiveCrudRepository<Carts, String> {

    Flux<Carts> findAllByUserIdAndDeletedIsFalse(String userId);

    Mono<Carts> findByArticleIdAndDeletedIsFalse(String articleId);

    Mono<Carts> findByIdAndDeletedIsFalse(String id);
}
