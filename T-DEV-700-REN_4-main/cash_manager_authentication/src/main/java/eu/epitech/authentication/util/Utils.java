package eu.epitech.authentication.util;

import com.fasterxml.jackson.core.JsonProcessingException;
import com.fasterxml.jackson.databind.ObjectMapper;
import eu.epitech.authentication.exception.CashManagerException;

public class Utils {

    public static String writeValueAsString(Object object) {
        try {
            return new ObjectMapper().writeValueAsString(object);
        } catch (JsonProcessingException e) {
            throw new CashManagerException(e.getMessage(), e);
        }
    }
}
