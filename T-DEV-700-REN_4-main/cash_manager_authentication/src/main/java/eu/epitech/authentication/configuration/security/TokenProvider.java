package eu.epitech.authentication.configuration.security;

import eu.epitech.authentication.model.RefreshToken;
import eu.epitech.authentication.model.Role;
import eu.epitech.authentication.model.Users;
import eu.epitech.authentication.repository.RefreshTokenRepository;
import eu.epitech.authentication.repository.UserRepository;
import io.jsonwebtoken.*;
import io.jsonwebtoken.security.Keys;
import io.jsonwebtoken.security.SignatureException;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.security.authentication.BadCredentialsException;
import org.springframework.stereotype.Component;
import reactor.core.publisher.Mono;

import javax.crypto.SecretKey;
import java.nio.charset.StandardCharsets;
import java.util.Date;
import java.util.HashMap;
import java.util.Map;
import java.util.UUID;


@Component
public class TokenProvider {

    private static final  Logger LOGGER = LoggerFactory.getLogger(TokenProvider.class);
    private static final String TOKEN_EXPIRED = "Token expired";
    private final String secret;
    private final long expiration;
    private final RefreshTokenRepository refreshTokenRepository;

    @Autowired
    public TokenProvider(RefreshTokenRepository refreshTokenRepository,
                         @Value("${jwt.secret}") String secret,
                         @Value("${jwt.expiration}") long expiration) {
        this.refreshTokenRepository = refreshTokenRepository;
        this.secret = secret;
        this.expiration = expiration;
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

    public String generateToken(Users user) {
        Map<String, Object> claims = new HashMap<>();
        claims.put("role", user.getRole());
        claims.put("id", user.getId());
        claims.put("fullName", user.getFirstname() + " " + user.getLastname());
        claims.put("email", user.getEmail());
        return createToken(claims, user.getId());
    }

    public Mono<RefreshToken> generateRefreshToken(Users user) {
        RefreshToken refreshToken = new RefreshToken();
        refreshToken.setUserId(user.getId());
        refreshToken.setExpired(false);
        refreshToken.setExpireIn(7776000L);
        refreshToken.setToken(UUID.randomUUID().toString());
        return refreshTokenRepository.save(refreshToken);
    }


    public String createToken(Map<String, Object> claims, String id) {
        Date now = new Date();
        Date expiryDate = new Date(now.getTime() + (expiration * 1000));
        SecretKey key = Keys.hmacShaKeyFor(secret.getBytes(StandardCharsets.UTF_8));
        return Jwts.builder()
                .setClaims(claims)
                .setSubject(id)
                .setIssuedAt(now)
                .setExpiration(expiryDate)
                .signWith(key, SignatureAlgorithm.HS512)
                .compact();
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

