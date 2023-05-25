defmodule GothamApi.Repo.Migrations.CreateWorkingtimes do
  use Ecto.Migration

  def change do
    create table(:workingtimes) do
      add :start, :naive_datetime
      add :endDate, :naive_datetime
      add :user, references(:users, on_delete: :nothing), null: false
    end

    # create index(:workingtimes, [:user_id])
    # unique_index(:workingtimes, [:start, :endDate, :user_id])
    create index(:workingtimes, [:user])

  end
end
