package eu.epitech.shop.model.builder;

import eu.epitech.shop.model.Articles;

public final class ArticlesBuilder {
    private String name;
    private double price;
    private String description;
    private String userId;

    private ArticlesBuilder() {
    }

    public static ArticlesBuilder anArticles() {
        return new ArticlesBuilder();
    }

    public ArticlesBuilder withName(String name) {
        this.name = name;
        return this;
    }

    public ArticlesBuilder withPrice(double price) {
        this.price = price;
        return this;
    }

    public ArticlesBuilder withDescription(String description) {
        this.description = description;
        return this;
    }

    public ArticlesBuilder withUserId(String userId) {
        this.userId = userId;
        return this;
    }

    public Articles build() {
        Articles articles = new Articles();
        articles.setName(name);
        articles.setPrice(price);
        articles.setDescription(description);
        articles.setUserId(userId);
        return articles;
    }
}
