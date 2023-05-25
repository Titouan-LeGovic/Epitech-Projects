package eu.epitech.shop.service;

import eu.epitech.shop.model.Articles;
import eu.epitech.shop.model.Carts;
import org.springframework.http.ResponseEntity;
import reactor.core.publisher.Flux;
import reactor.core.publisher.Mono;

public interface CartsService {
    Flux<Articles> getListArticlesInCarts(String userId);

    Mono<Carts> getOneCarts(String id);

    Mono<ResponseEntity<Void>> deleteArticleInCart(String id);

    Mono<ResponseEntity<Object>> addArticleInCart(String id, String userId);
}
