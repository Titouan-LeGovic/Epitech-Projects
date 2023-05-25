package eu.epitech.authentication.payload;

import com.fasterxml.jackson.annotation.JsonProperty;

public record RefreshTokenRequest(@JsonProperty("refreshToken") String refreshToken,
                                  @JsonProperty("grantType") String grantType) {
}
