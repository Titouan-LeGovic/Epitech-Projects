package eu.epitech.authentication.payload;

import com.fasterxml.jackson.annotation.JsonProperty;
import eu.epitech.authentication.exception.ValidationException;
import eu.epitech.authentication.model.Role;
import eu.epitech.authentication.util.Utils;
import eu.epitech.authentication.util.ValidationUtils;

import java.util.ArrayList;
import java.util.List;

public record UserCreateInput(@JsonProperty("firstname") String firstname,
                              @JsonProperty("lastname") String lastname,
                              @JsonProperty("email") String email,
                              @JsonProperty("password") String password,
                              @JsonProperty("role") Role role) {

    public void validate() {
        List<ValidationError> errors = new ArrayList<>();
        if (email == null || email.trim().isEmpty()) {
            errors.add(new ValidationError("ERROR.HTTP.400.SIGNUP.EMAIL.MISSING", "email"));
        }
        if (email != null && !ValidationUtils.isAnEmail(email)) {
            errors.add(new ValidationError("ERROR.HTTP.400.SIGNUP.EMAIL.INVALID", "email"));
        }
        if (firstname == null || firstname.trim().isEmpty()) {
            errors.add(new ValidationError("ERROR.HTTP.400.SIGNUP.FIRSTNAME.MISSING", "firstname"));
        }
        if (lastname == null || lastname.trim().isEmpty()) {
            errors.add(new ValidationError("ERROR.HTTP.400.SIGNUP.LASTNAME.MISSING", "lastname"));
        }
        if (role == null) {
            errors.add(new ValidationError("ERROR.HTTP.400.SIGNUP.ROLE.MISSING", "role"));
        }
        if (password == null || !ValidationUtils.isACorrectPassword(password)){
            errors.add(new ValidationError("ERROR.HTTP.400.SIGNUP.PASSWORD.INVALID", "password"));
        }
        if (!errors.isEmpty()) {
            throw new ValidationException(Utils.writeValueAsString(errors));
        }
    }
}
