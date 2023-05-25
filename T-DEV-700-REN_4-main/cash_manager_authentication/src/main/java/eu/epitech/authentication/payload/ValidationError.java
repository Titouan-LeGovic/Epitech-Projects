package eu.epitech.authentication.payload;

import com.fasterxml.jackson.annotation.JsonProperty;

public record ValidationError(@JsonProperty("message") String message,
                              @JsonProperty("fieldName") String fieldName) {

    @Override
    public String toString() {
        return "ValidationError{" +
                "message='" + message + '\'' +
                ", fieldName='" + fieldName + '\'' +
                '}';
    }
}
