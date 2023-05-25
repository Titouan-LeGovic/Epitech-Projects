package eu.epitech.authentication.service.impl;

import eu.epitech.authentication.model.RefreshToken;
import eu.epitech.authentication.service.RefreshTokenCRUDService;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.data.mongodb.core.ReactiveMongoTemplate;
import org.springframework.data.mongodb.core.query.Criteria;
import org.springframework.data.mongodb.core.query.Query;
import org.springframework.stereotype.Service;
import reactor.core.publisher.Mono;


@Service
public class RefreshTokenCRUDServiceImpl implements RefreshTokenCRUDService {

    private final ReactiveMongoTemplate template;
    private static final Logger LOGGER = LoggerFactory.getLogger(RefreshTokenCRUDServiceImpl.class);

    @Autowired
    public RefreshTokenCRUDServiceImpl(ReactiveMongoTemplate template) {
        this.template = template;
    }
    @Override
    public Mono<RefreshToken> findByTokenAndDeletedIsFalse(String tokenRefresh){
        Query query = new Query();
        Criteria criteria = new Criteria();

        criteria.and("id").is(tokenRefresh);
        criteria.and("deleted").is(false);
        query.addCriteria(criteria);

        return template.findOne(query, RefreshToken.class);
    }
}
