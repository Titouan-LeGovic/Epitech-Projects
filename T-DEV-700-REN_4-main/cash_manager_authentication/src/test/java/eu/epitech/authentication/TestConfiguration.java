package eu.epitech.authentication;

import com.mongodb.reactivestreams.client.MongoClient;
import com.mongodb.reactivestreams.client.MongoClients;
import de.flapdoodle.embed.mongo.MongodExecutable;
import de.flapdoodle.embed.mongo.MongodStarter;
import de.flapdoodle.embed.mongo.config.*;
import de.flapdoodle.embed.mongo.distribution.IFeatureAwareVersion;
import de.flapdoodle.embed.process.distribution.ImmutableGenericVersion;
import de.flapdoodle.embed.process.runtime.Network;
import org.bson.Document;
import org.mockito.Mockito;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.boot.autoconfigure.data.mongo.MongoDataAutoConfiguration;
import org.springframework.boot.autoconfigure.data.mongo.MongoReactiveDataAutoConfiguration;
import org.springframework.boot.autoconfigure.mongo.MongoAutoConfiguration;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.context.annotation.Import;
import org.springframework.context.annotation.Profile;
import org.springframework.core.convert.converter.Converter;
import org.springframework.data.convert.ReadingConverter;
import org.springframework.data.convert.WritingConverter;
import org.springframework.data.mongodb.ReactiveMongoDatabaseFactory;
import org.springframework.data.mongodb.ReactiveMongoTransactionManager;
import org.springframework.data.mongodb.config.AbstractReactiveMongoConfiguration;
import org.springframework.data.mongodb.config.EnableMongoAuditing;
import org.springframework.data.mongodb.config.EnableReactiveMongoAuditing;
import org.springframework.data.mongodb.core.ReactiveMongoTemplate;
import org.springframework.data.mongodb.core.SimpleReactiveMongoDatabaseFactory;
import org.springframework.data.mongodb.core.convert.MongoConverter;
import org.springframework.data.mongodb.core.convert.MongoCustomConversions;
import org.springframework.transaction.ReactiveTransactionManager;

import java.io.IOException;
import java.net.InetAddress;
import java.time.LocalDateTime;
import java.time.ZoneId;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;

@Configuration
@Import({MongoReactiveDataAutoConfiguration.class,
        CashManagerAuthentication.class,
        MongoAutoConfiguration.class,
        MongoDataAutoConfiguration.class})
@EnableReactiveMongoAuditing
public class TestConfiguration {

    private static final Logger LOGGER = LoggerFactory.getLogger(TestConfiguration.class);

    @Configuration
    public static class MongoDbReactiveConfig extends AbstractReactiveMongoConfiguration {

        public static int mongoPort;

        static {
            try {
                mongoPort = Network.freeServerPort(InetAddress.getLocalHost());
            } catch (IOException e) {
                e.printStackTrace();
            }
        }

        @Bean
        public ImmutableMongodConfig prepareMongoConfig() throws IOException {
            IFeatureAwareVersion version = de.flapdoodle.embed.mongo.distribution.Versions.withFeatures(
                    ImmutableGenericVersion.of("5.0.0"));
            ImmutableMongoCmdOptions cmdOptions = ImmutableMongoCmdOptions.builder()
                    .useNoPrealloc(false)
                    .useSmallFiles(false)
                    .master(false)
                    .isVerbose(false)
                    .useNoJournal(false)
                    .syncDelay(0)
                    .build();
            return ImmutableMongodConfig.builder()
                    .version(version)
                    .net(new Net(mongoPort, Network.localhostIsIPv6()))
                    .replication(new Storage(null, "rs0", 5000))
                    .isConfigServer(false)
                    .cmdOptions(cmdOptions)
                    .build();
        }

        @Bean(destroyMethod = "stop")
        public MongodExecutable mongodExecutable(MongodConfig mongodConfig) throws IOException {
            MongodStarter starter = MongodStarter.getDefaultInstance();
            MongodExecutable mongodExecutable = starter.prepare(mongodConfig);
            mongodExecutable.start();
            ReactiveMongoTemplate template = new ReactiveMongoTemplate(MongoClients.create(String.format("mongodb://%s:%d", "localhost", mongoPort)), "admin");
            template.executeCommand(new Document("replSetInitiate", new Document())).subscribe(message -> LOGGER.info(message.toString()));
            return mongodExecutable;
        }

        @Override
        public MongoClient reactiveMongoClient() {
            return MongoClients.create(String.format("mongodb://%s:%d/?replicaSet=rs0", "localhost", mongoPort));
        }

        @Override
        protected String getDatabaseName() {
            return "test";
        }

        @Bean
        public ReactiveMongoTemplate reactiveMongoTemplate(MongoConverter mongoConverter) {
            return new ReactiveMongoTemplate(new SimpleReactiveMongoDatabaseFactory(reactiveMongoClient(), getDatabaseName()), mongoConverter);
        }

        @Bean
        public MongoCustomConversions customConversions() {
            List<Converter<?, ?>> converters = new ArrayList<>();
            converters.add(new DateToLocalDateConverter());
            converters.add(new LocalDateToDateConverter());
            return new MongoCustomConversions(converters);
        }

        @WritingConverter
        public static class DateToLocalDateConverter implements Converter<Date, LocalDateTime> {
            @Override
            public LocalDateTime convert(Date source) {
                return LocalDateTime.ofInstant(source.toInstant(), ZoneId.of("UTC"));
            }
        }

        @ReadingConverter
        public static class LocalDateToDateConverter implements Converter<LocalDateTime, Date> {
            @Override
            public Date convert(LocalDateTime source) {
                return Date.from(source.atZone(ZoneId.of("UTC")).toInstant());
            }
        }

        @Bean
        public ReactiveTransactionManager transactionManager(ReactiveMongoDatabaseFactory reactiveMongoDatabaseFactory) {
            return new ReactiveMongoTransactionManager(reactiveMongoDatabaseFactory);
        }
    }
}
