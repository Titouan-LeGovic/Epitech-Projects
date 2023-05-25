package eu.epitech.authentication.payload;

import com.fasterxml.jackson.annotation.JsonProperty;

public record BankAccountCreate(@JsonProperty("solde") double solde) {
}
