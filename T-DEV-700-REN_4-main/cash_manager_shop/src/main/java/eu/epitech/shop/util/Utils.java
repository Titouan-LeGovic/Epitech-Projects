package eu.epitech.shop.util;

import java.net.URI;
import java.net.URISyntaxException;

public class Utils {

    private Utils() {
    }

    public static URI uriConstructorWrapper(String uri) {
        try {
            return new URI(uri);
        } catch (URISyntaxException e) {
            throw new RuntimeException(e.getMessage());
        }
    }
}

