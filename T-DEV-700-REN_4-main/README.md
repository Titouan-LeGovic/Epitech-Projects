# Backend
## Les différents services

- ### Authentication
 Pemettra l'authentification des utilisateurs en leur affectant un certain role. il sera nécessaire de s'authentifier pour accéder au autres service
 ##### les routes
 - POST http://localhost:8091/login
 ```json
 {
	 "username": "String",
	 "password": "String"
 }
 ```
 - GET http://localhost:8091/me nécessaire de s'authentifier
 - DELETE http://localhost:8091/logout
- ### Shopping
 Le service shopping filtra les roles des utilisateurs pour les différentes routes.
 #### les routes SALE
 ils auront la possibilité de proposer des produit à la vente
 - POST http://localhost:8092/articles/create
  ```json
 {
	"name": "moto",
	"price": 7500,
	"description": "IDE pour Java et Flutter si extension installé"
}
 ```
- ### Bank
