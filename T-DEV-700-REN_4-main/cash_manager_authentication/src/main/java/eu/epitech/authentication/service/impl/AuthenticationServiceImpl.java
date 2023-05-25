package eu.epitech.authentication.service.impl;

import eu.epitech.authentication.configuration.security.TokenProvider;
import eu.epitech.authentication.exception.ForbiddenException;
import eu.epitech.authentication.payload.AuthMessage;
import eu.epitech.authentication.payload.LoginRequest;
import eu.epitech.authentication.payload.RefreshTokenRequest;
import eu.epitech.authentication.repository.RefreshTokenRepository;
import eu.epitech.authentication.repository.UserRepository;
import eu.epitech.authentication.service.AuthenticationService;
import eu.epitech.authentication.service.RefreshTokenCRUDService;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.security.authentication.BadCredentialsException;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Service;
import reactor.core.publisher.Mono;

import java.time.LocalDateTime;

@Service
public class AuthenticationServiceImpl implements AuthenticationService {
    private static final String INVALID_REFRESH_TOKEN = "HTTP.ERROR.INVALID.REFRESH.TOKEN";
    private final BadCredentialsException badCredentialsFallBack = new BadCredentialsException("ERROR.HTTP.401.BAD.CREDENTIALS");

    private static final Logger LOGGER = LoggerFactory.getLogger(AuthenticationServiceImpl.class);
    private final RefreshTokenRepository refreshTokenRepository;
    private final UserRepository userRepository;
    private final TokenProvider tokenProvider;
    private final PasswordEncoder passwordEncoder;

    @Autowired
    public AuthenticationServiceImpl(RefreshTokenRepository refreshTokenRepository,
                                     UserRepository userRepository,
                                     TokenProvider tokenProvider,
                                     PasswordEncoder passwordEncoder) {
        this.refreshTokenRepository = refreshTokenRepository;
        this.userRepository = userRepository;
        this.tokenProvider = tokenProvider;
        this.passwordEncoder = passwordEncoder;
    }

    @Override
    public Mono<ResponseEntity<AuthMessage>> authenticationVerification(LoginRequest loginRequest){
        loginRequest.validate();
        return userRepository.findByEmailAndDeletedIsFalse(loginRequest.username())
                .filter(user -> passwordEncoder.matches(loginRequest.password(), user.getPassword()))
                .switchIfEmpty(Mono.error(badCredentialsFallBack))
                .flatMap(user ->
                        refreshTokenRepository.findByUserIdAndDeletedIsFalse(user.getId())
                                        .flatMap(refreshToken -> {
                                            refreshToken.setExpired(true);
                                            refreshToken.setDeleted(true);
                                            return refreshTokenRepository.save(refreshToken);
                                        }).then(
                                                tokenProvider.generateRefreshToken(user)
                                                        .map(refreshToken -> ResponseEntity.ok(new AuthMessage(
                                                                tokenProvider.generateToken(user),
                                                                "Bearer",
                                                                refreshToken.getToken())))));
    }

    @Override
    public Mono<ResponseEntity<Void>> sessionDisconnected(String authorization) {
        String accountId = tokenProvider.getUserIdFromAuthorization(authorization);
        return refreshTokenRepository.findByUserIdAndDeletedIsFalse(accountId)
                .flatMap(refreshToken -> {
                    refreshToken.setExpired(true);
                    refreshToken.setDeleted(true);
                    return refreshTokenRepository.save(refreshToken);
                })
                .map(result -> ResponseEntity.noContent().build());
    }
    @Override
    public Mono<ResponseEntity<AuthMessage>> refreshSession(RefreshTokenRequest refreshTokenRequest){
        LOGGER.info(refreshTokenRequest.refreshToken());
        String input = refreshTokenRequest.refreshToken();
        return refreshTokenRepository.findByTokenAndDeletedIsFalse(input)
                .flatMap(refreshToken -> {
                    if (refreshToken.isExpired()) {
                        return Mono.error(new ForbiddenException(INVALID_REFRESH_TOKEN));
                    }
                    if (refreshToken.getCreatedDate().plusSeconds(refreshToken.getExpireIn()).isBefore(LocalDateTime.now())) {
                        refreshToken.setExpired(true);
                        return refreshTokenRepository.save(refreshToken).then(Mono.error(new ForbiddenException(INVALID_REFRESH_TOKEN)));
                    }
                    return userRepository.findById(refreshToken.getUserId())
                            .map(tokenProvider::generateToken)
                                .map(token -> ResponseEntity.ok(new AuthMessage(token, "Bearer", null)));
                }).switchIfEmpty(
                        Mono.error(new ForbiddenException(INVALID_REFRESH_TOKEN)
                        ));
    }
}
