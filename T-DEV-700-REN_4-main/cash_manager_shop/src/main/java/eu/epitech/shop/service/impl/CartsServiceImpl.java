package eu.epitech.shop.service.impl;

import eu.epitech.shop.exception.NotFoundException;
import eu.epitech.shop.model.Articles;
import eu.epitech.shop.model.Carts;
import eu.epitech.shop.model.builder.CartsBuilder;
import eu.epitech.shop.repository.ArticleRepository;
import eu.epitech.shop.repository.CartsRepository;
import eu.epitech.shop.service.CartsService;
import eu.epitech.shop.util.Utils;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.stereotype.Service;
import reactor.core.publisher.Flux;
import reactor.core.publisher.Mono;

@Service
public class CartsServiceImpl implements CartsService {

    private final CartsRepository cartsRepository;
    private final ArticleRepository articleRepository;
    private final NotFoundException resourceNotFoundException = new NotFoundException("ERROR.HTTP.404.RESOURCE.NOT.FOUND");

    @Autowired
    public CartsServiceImpl(CartsRepository cartsRepository, ArticleRepository articleRepository) {
        this.cartsRepository = cartsRepository;
        this.articleRepository = articleRepository;
    }

    @Override
    public Flux<Articles> getListArticlesInCarts(String userId){
        return cartsRepository.findAllByUserIdAndDeletedIsFalse(userId)
                .flatMap(cart -> articleRepository.findById(cart.getArticleId()));
    }

    @Override
    public Mono<Carts> getOneCarts(String id){
        return cartsRepository.findByIdAndDeletedIsFalse(id)
                .switchIfEmpty(Mono.error(resourceNotFoundException));
    }

    @Override
    public Mono<ResponseEntity<Void>> deleteArticleInCart(String id){
        return cartsRepository.findByArticleIdAndDeletedIsFalse(id)
                .switchIfEmpty(Mono.error(resourceNotFoundException))
                .flatMap(cart -> {
                    cart.setDeleted(true);
                    return cartsRepository.save(cart)
                            .map(result -> ResponseEntity.noContent().build());
                });
    }

    @Override
    public Mono<ResponseEntity<Object>> addArticleInCart(String id, String userId){
        return articleRepository.findById(id)
                .flatMap(result -> {
                    Carts cart = CartsBuilder.aCarts()
                            .withBuy(false)
                            .withArticleId(id)
                            .withUserId(userId)
                            .build();
                    return cartsRepository.save(cart)
                            .map(save -> ResponseEntity.created(Utils.uriConstructorWrapper("/carts/"+ save.getId())).build());
                })
                .switchIfEmpty(Mono.error(resourceNotFoundException));
    }

}
