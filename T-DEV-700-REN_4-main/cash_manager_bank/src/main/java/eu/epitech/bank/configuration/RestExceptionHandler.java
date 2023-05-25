package eu.epitech.bank.configuration;

import com.fasterxml.jackson.databind.ObjectMapper;
import eu.epitech.bank.exception.ForbiddenException;
import eu.epitech.bank.exception.NotFoundException;
import eu.epitech.bank.exception.ValidationException;
import eu.epitech.bank.model.ErrorMessage;
import eu.epitech.bank.payload.ValidationError;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.http.ResponseEntity;
import org.springframework.security.authentication.BadCredentialsException;
import org.springframework.web.bind.annotation.ControllerAdvice;
import org.springframework.web.bind.annotation.ExceptionHandler;
import reactor.core.publisher.Mono;

@ControllerAdvice
public class RestExceptionHandler {

    private static final Logger LOGGER = LoggerFactory.getLogger(RestExceptionHandler.class);
    private final ObjectMapper objectMapper;

    public RestExceptionHandler(ObjectMapper objectMapper) {
        this.objectMapper = objectMapper;
    }
    @ExceptionHandler
    public Mono<ResponseEntity<ErrorMessage>> handleValidationError(ValidationException ex) {
        return Mono.fromCallable(() -> {
            ValidationError[] errors = objectMapper.readValue(ex.getMessage(), ValidationError[].class);
            LOGGER.info(ex.getMessage());
            return ResponseEntity.status(400).body(new ErrorMessage(
                    errors[0].message(),
                    400
            ));
        });
    }
    @ExceptionHandler
    public Mono<ResponseEntity<ErrorMessage>> handleNotFoundError(NotFoundException ex) {
        return Mono.just(ResponseEntity.status(404).body(new ErrorMessage(
                ex.getMessage(),
                404
        )));
    }
    @ExceptionHandler
    public Mono<ResponseEntity<ErrorMessage>> handleForbiddenError(ForbiddenException ex) {
        return Mono.just(ResponseEntity.status(403).body(new ErrorMessage(
                ex.getMessage(),
                403
        )));
    }

    @ExceptionHandler
    public Mono<ResponseEntity<ErrorMessage>> handleBadCredentialError(BadCredentialsException ex) {
        return Mono.just(ResponseEntity.status(401).body(new ErrorMessage(
                ex.getMessage(),
                401
        )));
    }



}
