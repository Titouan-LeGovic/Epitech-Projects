package eu.epitech.authentication.model.builder;

import eu.epitech.authentication.model.Role;
import eu.epitech.authentication.model.Users;

public final class UsersBuilder {
    private String firstname;
    private String lastname;
    private String email;
    private String password;
    private Role role;

    private UsersBuilder() {
    }

    public static UsersBuilder anUsers() {
        return new UsersBuilder();
    }

    public UsersBuilder withFirstname(String firstname) {
        this.firstname = firstname;
        return this;
    }

    public UsersBuilder withLastname(String lastname) {
        this.lastname = lastname;
        return this;
    }

    public UsersBuilder withEmail(String email) {
        this.email = email;
        return this;
    }

    public UsersBuilder withPassword(String password) {
        this.password = password;
        return this;
    }

    public UsersBuilder withRole(Role role) {
        this.role = role;
        return this;
    }

    public Users build() {
        Users users = new Users();
        users.setFirstname(firstname);
        users.setLastname(lastname);
        users.setEmail(email);
        users.setPassword(password);
        users.setRole(role);
        return users;
    }
}
