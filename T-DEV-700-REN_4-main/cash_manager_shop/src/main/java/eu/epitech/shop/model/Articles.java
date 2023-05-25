package eu.epitech.shop.model;

import org.springframework.data.mongodb.core.mapping.Document;

@Document(collection = "articles")
public class Articles extends BaseEntity{

    private String name;
    private double price;
    private String description;

    private String userId;

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public double getPrice() {
        return price;
    }

    public void setPrice(double price) {
        this.price = price;
    }

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public String getUserId() {
        return userId;
    }

    public void setUserId(String userId) {
        this.userId = userId;
    }

}
