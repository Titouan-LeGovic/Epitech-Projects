defmodule GothamApi.Repo.Migrations.CreateTeams do
  use Ecto.Migration

  def change do
    create table(:teams) do
      add :name, :string
      add :description, :string
      add :user, references(:users, on_delete: :nothing)

    end

    create index(:teams, [:user])
  end
end
