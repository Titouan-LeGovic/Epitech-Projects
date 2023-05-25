package eu.epitech.bank.configuration;

import eu.epitech.shop.configuration.security.AuthenticationManager;
import eu.epitech.shop.configuration.security.SecurityContextRepository;
import eu.epitech.shop.configuration.security.SimpleAuthenticationEntryPoint;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.http.HttpMethod;
import org.springframework.security.config.annotation.method.configuration.EnableReactiveMethodSecurity;
import org.springframework.security.config.web.server.ServerHttpSecurity;
import org.springframework.security.crypto.bcrypt.BCryptPasswordEncoder;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.security.web.server.SecurityWebFilterChain;

@Configuration
@EnableReactiveMethodSecurity
public class SecurityConfiguration {

    @Bean
    public SecurityWebFilterChain securityWebFilterChain(ServerHttpSecurity http,
                                                         AuthenticationManager authenticationManager,
                                                         SecurityContextRepository securityContextRepository,
                                                         SimpleAuthenticationEntryPoint simpleAuthenticationEntryPoint) {
        return http.csrf().disable()
                .formLogin().disable()
                .httpBasic().disable()
                .authenticationManager(authenticationManager)
                .securityContextRepository(securityContextRepository)
                .exceptionHandling()
                .authenticationEntryPoint(simpleAuthenticationEntryPoint)
                .and()
                .authorizeExchange()
                .pathMatchers(HttpMethod.OPTIONS).permitAll()
                .pathMatchers(HttpMethod.POST, "/login", "/users/create", "/refresh").permitAll()
                .pathMatchers(HttpMethod.PUT, "/auth/reset-password", "/auth/temporary-accounts/**").permitAll()
                .pathMatchers(HttpMethod.GET, "/v2/api-docs", "/swagger-ui/**", "/webjars/**", "/swagger-resources/**", "/auth/**", "/oauth2/**", "/agent/info", "/refs/**", "/payments/update-success", "/accounts/validate-email").permitAll()
                .anyExchange().authenticated()
                .and().build();
    }

    @Bean
    public PasswordEncoder passwordEncoder() {
        return new BCryptPasswordEncoder();
    }
}
