package eu.epitech.shop.model.builder;

import eu.epitech.shop.model.Carts;

public final class CartsBuilder {
    private String articleId;
    private String userId;
    private boolean buy;

    private CartsBuilder() {
    }

    public static CartsBuilder aCarts() {
        return new CartsBuilder();
    }

    public CartsBuilder withArticleId(String articleId) {
        this.articleId = articleId;
        return this;
    }

    public CartsBuilder withUserId(String userId) {
        this.userId = userId;
        return this;
    }

    public CartsBuilder withBuy(boolean buy) {
        this.buy = buy;
        return this;
    }

    public Carts build() {
        Carts carts = new Carts();
        carts.setArticleId(articleId);
        carts.setUserId(userId);
        carts.setBuy(buy);
        return carts;
    }
}
