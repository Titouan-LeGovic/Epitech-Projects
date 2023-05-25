package eu.epitech.shop.configuration.security;

import eu.epitech.shop.model.Role;
import io.jsonwebtoken.*;
import io.jsonwebtoken.security.Keys;
import io.jsonwebtoken.security.SignatureException;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.data.mongodb.core.ReactiveMongoTemplate;
import org.springframework.security.authentication.BadCredentialsException;
import org.springframework.stereotype.Component;

import java.nio.charset.StandardCharsets;
import java.util.Date;


@Component
public class TokenProvider {

    private static final  Logger LOGGER = LoggerFactory.getLogger(TokenProvider.class);

    private static final String TOKEN_EXPIRED = "Token expired";

    private final String secret;

    private final long expiration;

    private final ReactiveMongoTemplate template;

    @Autowired
    public TokenProvider(
            @Value("${jwt.secret}") String secret,
            @Value("${jwt.expiration}") long expiration,
            ReactiveMongoTemplate template) {
        this.secret = secret;
        this.expiration = expiration;
        this.template = template;
    }

    public Claims getAllClaimsFromToken(String token) {
        return Jwts.parserBuilder().setSigningKey(Keys.hmacShaKeyFor(secret.getBytes(StandardCharsets.UTF_8))).build().parseClaimsJws(token).getBody();
    }

    public String getUsernameFromToken(String token) {
        return getAllClaimsFromToken(token).getSubject();
    }

    public Date getExpirationDateFromToken(String token) {
        return getAllClaimsFromToken(token).getExpiration();
    }

    public String getUserIdFromAuthorization(String authorization) {
        String authToken = authorization.substring(7);
        if (validateToken(authToken)) {
            Claims claims = getAllClaimsFromToken(authToken);
            return claims.get("id", String.class);
        }
        throw new BadCredentialsException(TOKEN_EXPIRED);
    }

    public Role getRoleFromAuthorization(String authorization) {
        String authToken = authorization.substring(7);
        Claims claims = getAllClaimsFromToken(authToken);
        String role = claims.get("role", String.class);
        return Role.valueOf(role);
    }

    public String getAttributeFromFromAuthorization(String authorization, String attributeName) {
        String authToken = authorization.substring(7);
        if (validateToken(authToken)) {
            Claims claims = getAllClaimsFromToken(authToken);
            return claims.get(attributeName, String.class);
        }
        throw new BadCredentialsException(TOKEN_EXPIRED);
    }

    public boolean isAdmin(String authorization) {
        return getRoleFromAuthorization(authorization).equals(Role.ROLE_ADMIN);
    }

    public boolean validateToken(String authToken) {
        try {
            Jwts.parserBuilder().setSigningKey(Keys.hmacShaKeyFor(secret.getBytes(StandardCharsets.UTF_8))).build().parseClaimsJws(authToken);
            return true;
        } catch (SignatureException | SecurityException ex) {
            LOGGER.warn("Invalid JWT signature");
        } catch (MalformedJwtException ex) {
            LOGGER.warn("Invalid JWT token");
        } catch (ExpiredJwtException ex) {
            LOGGER.warn("Expired JWT token");
        } catch (UnsupportedJwtException ex) {
            LOGGER.warn("Unsupported JWT token");
        } catch (IllegalArgumentException ex) {
            LOGGER.warn("JWT claims string is empty.");
        }
        return false;
    }

}

