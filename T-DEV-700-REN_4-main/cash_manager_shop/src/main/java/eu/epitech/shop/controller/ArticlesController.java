package eu.epitech.shop.controller;

import eu.epitech.shop.configuration.security.TokenProvider;
import eu.epitech.shop.model.Articles;
import eu.epitech.shop.payload.CreateArticles;
import eu.epitech.shop.service.ArticlesService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.data.domain.Page;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.context.annotation.RequestScope;
import reactor.core.publisher.Flux;
import reactor.core.publisher.Mono;

@RestController
@RequestMapping("/articles")
public class ArticlesController {

    private final ArticlesService articlesService;
    private final TokenProvider tokenProvider;

    @Autowired
    public ArticlesController(ArticlesService articlesService, TokenProvider tokenProvider) {
        this.articlesService = articlesService;
        this.tokenProvider = tokenProvider;
    }

    @PostMapping("/create")
    @PreAuthorize("hasAnyRole('ADMIN', 'SALE')")
    @ResponseStatus(HttpStatus.CREATED)
    Mono<ResponseEntity<?>> createArticle(@RequestBody CreateArticles input,
                                 @RequestHeader("Authorization") String authorization){
        String userId = tokenProvider.getUserIdFromAuthorization(authorization);
        return articlesService.createNewArticles(input, userId);
    }

    @DeleteMapping("/delete/{id}")
    @PreAuthorize("hasAnyRole('ADMIN', 'SALE')")
    @ResponseStatus(HttpStatus.NO_CONTENT)
    Mono<ResponseEntity<Void>> createArticle(@RequestHeader("Authorization") String authorization,
                                             @PathVariable("id") String id){
        String userId = tokenProvider.getUserIdFromAuthorization(authorization);
        return articlesService.deleteArticles(id, userId);
    }

    @GetMapping("/me")
    @PreAuthorize("hasAnyRole('SALE')")
    Mono<Page<Articles>> getMyArticle(@RequestHeader("Authorization") String authorization,
                                      @RequestParam(value = "page", defaultValue = "0") int page,
                                      @RequestParam(value = "size", defaultValue = "10") int size){
        String userId = tokenProvider.getUserIdFromAuthorization(authorization);
        return articlesService.getMyArticles(userId, page, size);
    }
    @GetMapping
    @PreAuthorize("hasAnyRole('SALE', 'ADMIN', 'USER')")
    Mono<Page<Articles>> getMyArticle(@RequestParam(value = "page", defaultValue = "0") int page,
                                 @RequestParam(value = "sortBy", required = false) String sortBy,
                                 @RequestParam(value = "sortOrder", required = false) String sortOrder,
                                 @RequestParam(value = "size", defaultValue = "10") int size,
                                 @RequestParam(value = "name", required = false) String name,
                                 @RequestParam(value = "minPrice", defaultValue = "0") double minPrice,
                                 @RequestParam(value = "maxPrice", defaultValue = "0") double maxPrice){
        return articlesService.getAllArticles(page, size, sortBy, sortOrder, name, minPrice, maxPrice);
    }

    @GetMapping("/{id}")
    @PreAuthorize("hasAnyRole('SALE')")
    Mono<Articles> getOneArticle(@PathVariable("id") String id){
        return articlesService.getOneArticle(id);
    }
}
