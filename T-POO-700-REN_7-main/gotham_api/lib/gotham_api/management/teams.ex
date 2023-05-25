defmodule GothamApi.Management.Teams do
  use Ecto.Schema
  import Ecto.Changeset
  import Ecto.Query
  use EctoFilter

  alias GothamApi.Repo
  alias GothamApi.Management.Teams

  schema "teams" do
    field :description, :string
    field :name, :string
    field :user, :id

  end

  @doc false
  def changeset(teams, attrs) do
    teams
    |> cast(attrs, [:name, :description, :user])
    |> validate_required([:name, :description, :user])
    |> unique_constraint(:user)
  end


  def list_teams() do
    Teams
    |> Repo.all()
  end

  def get_teams!(userId, id) do
    Repo.get_by!(Teams, [user: userId, id: id])
  end

  def get_teams!(id) do
    Repo.get_by!(Teams, id: id)
  end

  def create_teams(attrs \\ %{}) do
    %Teams{}
    |> Teams.changeset(attrs)
    |> Repo.insert()
  end


  def update_teams(%Teams{} = teams, attrs) do
    teams
    |> Teams.changeset(attrs)
    |> Repo.update()
  end


  def delete_teams(%Teams{} = teams) do
    Repo.delete(teams)
  end


  def change_teams(%Teams{} = teams) do
    Teams.changeset(teams, %{})
  end
end
