package eu.epitech.shop.repository;

import eu.epitech.shop.model.Articles;
import org.springframework.data.repository.reactive.ReactiveCrudRepository;
import org.springframework.stereotype.Repository;
import reactor.core.publisher.Mono;

@Repository
public interface ArticleRepository extends ReactiveCrudRepository<Articles, String> {

    Mono<Articles> findByIdAndUserIdAndDeletedIsFalse(String id, String userId);
    Mono<Articles> findByIdAndDeletedIsFalse(String id);
}
