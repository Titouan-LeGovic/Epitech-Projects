defmodule GothamApi.Repo.Migrations.CreateUsers do
  use Ecto.Migration

  def change do
    create table(:users) do
      add :username, :string
      add :email, :string
      add :password_hash, :string, null: false
      add :role, :string

    end
    create(unique_index(:users, :email))
  end
end
