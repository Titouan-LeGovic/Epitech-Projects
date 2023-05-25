package eu.epitech.authentication.model;

import org.springframework.data.mongodb.core.mapping.Document;

@Document(collection = "refreshTokens")
public class RefreshToken extends BaseEntity {

    private String token;

    private long expireIn;

    private String userId;

    private boolean expired;

    public String getToken() {
        return token;
    }

    public void setToken(String identity) {
        this.token = identity;
    }

    public long getExpireIn() {
        return expireIn;
    }

    public void setExpireIn(long expireIn) {
        this.expireIn = expireIn;
    }

    public String getUserId() {
        return userId;
    }

    public void setUserId(String accountId) {
        this.userId = accountId;
    }

    public boolean isExpired() {
        return expired;
    }

    public void setExpired(boolean expired) {
        this.expired = expired;
    }

}

