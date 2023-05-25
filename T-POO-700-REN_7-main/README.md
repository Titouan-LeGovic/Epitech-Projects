# T-POO-700-REN_7
Lancer le projet 

cd gotham_api

mix deps.get

mix ecto.create (pas oublier de changer le fichier T-POO-700-REN_7\gotham_api\config\dev.exs avec les bons paramètres de BDD)

mix ecto.migrate

mix phx.server
