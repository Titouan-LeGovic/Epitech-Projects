package eu.epitech.bank.exception;

public class CashManagerException extends RuntimeException {
    public CashManagerException(String message, Exception e) {
        super(message, e);
    }
    public CashManagerException(String message) {
        super(message);
    }

}
