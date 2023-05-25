package eu.epitech.shop.payload;

import com.fasterxml.jackson.annotation.JsonProperty;

public record CreateArticles(@JsonProperty("name") String name,
                             @JsonProperty("price") double price,
                             @JsonProperty("description") String description) {
    public void validate(){}
}
