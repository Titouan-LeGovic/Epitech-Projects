package eu.epitech.shop.controller;

import eu.epitech.shop.configuration.security.TokenProvider;
import eu.epitech.shop.exception.NotFoundException;
import eu.epitech.shop.model.Articles;
import eu.epitech.shop.model.Carts;
import eu.epitech.shop.model.builder.CartsBuilder;
import eu.epitech.shop.repository.ArticleRepository;
import eu.epitech.shop.repository.CartsRepository;
import eu.epitech.shop.service.CartsService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.web.bind.annotation.*;
import reactor.core.publisher.Flux;
import reactor.core.publisher.Mono;

@RestController
@RequestMapping("/carts")
public class CartsController {
    private final CartsService cartsService;
    private final TokenProvider tokenProvider;

    @Autowired
    public CartsController(CartsRepository cartsRepository,
                           ArticleRepository articleRepository,
                           CartsService cartsService,
                           TokenProvider tokenProvider) {
        this.cartsService = cartsService;
        this.tokenProvider = tokenProvider;
    }

    @PostMapping("/add/{id}")
    @PreAuthorize("hasAnyRole('USER','SALE','ADMIN')")
    public Mono<ResponseEntity<Object>> addArticleInCarts(@PathVariable("id") String id,
                                         @RequestHeader("Authorization") String authorization){
        String userId = tokenProvider.getUserIdFromAuthorization(authorization);
        return cartsService.addArticleInCart(id, userId);
    }

    @DeleteMapping("/delete/{id}")
    @PreAuthorize("hasAnyRole('USER','SALE','ADMIN')")
    @ResponseStatus(HttpStatus.NO_CONTENT)
    public Mono<ResponseEntity<Void>> removeArticleInCarts(@PathVariable("id") String id){
        return cartsService.deleteArticleInCart(id);
    }

    @GetMapping
    @PreAuthorize("hasAnyRole('USER','SALE','ADMIN')")
    public Flux<Articles> getAllArticlesInCarts(@RequestHeader("Authorization") String authorization){
        String userId = tokenProvider.getUserIdFromAuthorization(authorization);
        return cartsService.getListArticlesInCarts(userId);
    }

    @GetMapping("/{id}")
    @PreAuthorize("hasAnyRole('USER','SALE','ADMIN')")
    public Mono<Carts> getOneCarts(@PathVariable("id") String id){
        return cartsService.getOneCarts(id);
    }
}
