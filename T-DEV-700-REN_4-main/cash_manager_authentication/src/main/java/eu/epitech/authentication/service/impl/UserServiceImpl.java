package eu.epitech.authentication.service.impl;

import eu.epitech.authentication.model.Users;
import eu.epitech.authentication.service.UserService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.data.mongodb.core.ReactiveMongoTemplate;
import org.springframework.data.mongodb.core.aggregation.Aggregation;
import org.springframework.data.mongodb.core.aggregation.AggregationOperation;
import org.springframework.data.mongodb.core.query.Criteria;
import org.springframework.stereotype.Service;
import reactor.core.publisher.Mono;

import static org.springframework.data.mongodb.core.aggregation.Aggregation.*;

@Service
public class UserServiceImpl implements UserService {

    private final ReactiveMongoTemplate template;

    @Autowired
    public UserServiceImpl(ReactiveMongoTemplate template) {
        this.template = template;
    }

    @Override
    public Mono<Users> findUserByIdAndDeletedIsFalse(String id){
        AggregationOperation project = project("firstName", "lastName", "email", "role");
        AggregationOperation match = match(Criteria.where("id").is(id));
        Aggregation aggregation = newAggregation(match, project);
        return null; //template.aggregate(aggregation, Users.class, Users.class);
    }
}
