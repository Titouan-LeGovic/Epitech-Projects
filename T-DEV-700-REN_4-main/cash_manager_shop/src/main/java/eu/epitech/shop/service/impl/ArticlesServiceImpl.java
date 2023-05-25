package eu.epitech.shop.service.impl;

import eu.epitech.shop.exception.NotFoundException;
import eu.epitech.shop.model.Articles;
import eu.epitech.shop.model.builder.ArticlesBuilder;
import eu.epitech.shop.payload.CreateArticles;
import eu.epitech.shop.repository.ArticleRepository;
import eu.epitech.shop.service.ArticlesService;
import eu.epitech.shop.util.Utils;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.data.domain.*;
import org.springframework.data.mongodb.core.ReactiveMongoTemplate;
import org.springframework.data.mongodb.core.query.Criteria;
import org.springframework.data.mongodb.core.query.Query;
import org.springframework.http.ResponseEntity;
import org.springframework.stereotype.Service;
import reactor.core.publisher.Mono;


@Service
public class ArticlesServiceImpl implements ArticlesService {
    private static final String PRICE = "price";
    private final ArticleRepository articleRepository;
    private final ReactiveMongoTemplate template;
    private final NotFoundException resourceNotFoundException = new NotFoundException("ERROR.HTTP.404.RESOURCE.NOT.FOUND");

    @Autowired
    public ArticlesServiceImpl(ArticleRepository articleRepository, ReactiveMongoTemplate template) {
        this.articleRepository = articleRepository;
        this.template = template;
    }

    @Override
    public Mono<ResponseEntity<?>> createNewArticles(CreateArticles input, String userId){
        Articles article = ArticlesBuilder.anArticles()
                .withName(input.name())
                .withPrice(input.price())
                .withDescription(input.description())
                .withUserId(userId)
                .build();
        return articleRepository.save(article)
                .map(result -> ResponseEntity.created(Utils.uriConstructorWrapper("/articles/"+ result.getId())).build());
    }

    @Override
    public Mono<ResponseEntity<Void>> deleteArticles(String id, String userId){
        return articleRepository.findByIdAndUserIdAndDeletedIsFalse(id, userId)
                .flatMap(article -> {
                    article.setDeleted(true);
                    return articleRepository.save(article)
                            .map(result -> ResponseEntity.noContent().build());
                });
    }
    @Override
    public Mono<Page<Articles>> getMyArticles(String userId, int page, int size){
        Pageable pageable = PageRequest.of(page, size, Sort.by(Sort.Direction.ASC, "createdDate"));
        Query query = new Query().with(pageable);
        Criteria criteria = new Criteria();
        criteria.and("userId").is(userId);
        query.addCriteria(criteria);
        return template.count(new Query().addCriteria(criteria), Articles.class)
                .flatMap(count -> template.find(query, Articles.class)
                        .collectList()
                        .map(contacts -> new PageImpl<>(contacts, PageRequest.of(page, size), count)));
    }

    @Override
    public Mono<Articles> getOneArticle(String id){
        return articleRepository.findByIdAndDeletedIsFalse(id)
                .switchIfEmpty(Mono.error(resourceNotFoundException));
    }
    @Override
    public Mono<Page<Articles>> getAllArticles(int page, int size,
                                         String sortBy, String sortOrder,
                                         String name, double minPrice, double maxPrice){
        Pageable pageable;
        if (sortBy != null && sortOrder != null) {
            pageable = PageRequest.of(page, size, Sort.by(Sort.Direction.valueOf(sortOrder.toUpperCase()), sortBy));
        } else {
            pageable = PageRequest.of(page, size, Sort.by(Sort.Direction.DESC, "createdDate"));
        }
        Query query = new Query().with(pageable);
        Criteria criteria = buildCriteriaForGetAllArticles(name, minPrice, maxPrice);
        query.addCriteria(criteria);
        return template.count(new Query().addCriteria(criteria), Articles.class)
                .flatMap(count -> template.find(query, Articles.class)
                        .collectList()
                        .map(contacts -> new PageImpl<>(contacts, PageRequest.of(page, size), count)));
    }

    private Criteria buildCriteriaForGetAllArticles(String name, double minPrice, double maxPrice){
        Criteria criteria = new Criteria();
        criteria.andOperator(Criteria.where("deleted").is(false));
        if (name != null && !name.trim().isEmpty()){
            criteria.andOperator(Criteria.where("name").regex(name, "i"));
        }
        if (minPrice == 0 && maxPrice != 0){
            criteria.andOperator(Criteria.where(PRICE).lt(maxPrice));
        }
        if (minPrice != 0 && maxPrice == 0){
            criteria.andOperator(Criteria.where(PRICE).gt(minPrice));
        }
        if (minPrice != 0 && maxPrice != 0){
            criteria.andOperator(Criteria.where(PRICE).gt(minPrice).lt(maxPrice));
        }
        return criteria;
    }
}
