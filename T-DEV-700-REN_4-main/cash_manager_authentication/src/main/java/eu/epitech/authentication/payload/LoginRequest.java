package eu.epitech.authentication.payload;

import com.fasterxml.jackson.annotation.JsonProperty;
import eu.epitech.authentication.util.ValidationUtils;

import java.util.ArrayList;
import java.util.List;

public record LoginRequest(@JsonProperty("username") String username,
                           @JsonProperty("password") String password) {
    public void validate(){
        List<ValidationError> errors = new ArrayList<>();
        if (username == null || username.trim().isEmpty()) {
            errors.add(new ValidationError("ERROR.HTTP.400.SIGNUP.USERNAME.MISSING", "email"));
        }

        if (username != null && !ValidationUtils.isAnEmail(username)) {
            errors.add(new ValidationError("ERROR.HTTP.400.SIGNUP.EMAIL.INVALID", "email"));
        }
    }
}
