package eu.epitech.shop.model;

import com.fasterxml.jackson.annotation.JsonProperty;

public record ErrorMessage(@JsonProperty("message") String message,
                           @JsonProperty("status") int status) {

}

