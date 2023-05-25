package eu.epitech.authentication.payload;

import com.fasterxml.jackson.annotation.JsonProperty;

public record AuthMessage(@JsonProperty("accessToken") String accessToken,
                          @JsonProperty("tokenType") String tokenType,
                          @JsonProperty("refreshToken") String refreshToken) {
}
