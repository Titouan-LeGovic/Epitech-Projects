package eu.epitech.shop.service;

import eu.epitech.shop.model.Articles;
import eu.epitech.shop.payload.CreateArticles;
import org.springframework.data.domain.Page;
import org.springframework.http.ResponseEntity;
import org.springframework.stereotype.Service;
import reactor.core.publisher.Flux;
import reactor.core.publisher.Mono;

@Service
public interface ArticlesService {
    Mono<ResponseEntity<?>> createNewArticles(CreateArticles input, String userId);

    Mono<ResponseEntity<Void>> deleteArticles(String id, String userId);

    Mono<Page<Articles>> getMyArticles(String userId, int page, int size);

    Mono<Articles> getOneArticle(String id);

    Mono<Page<Articles>> getAllArticles(int page, int size,
                                        String sortBy, String sortOrder,
                                        String name, double minPrice, double maxPrice);
}
