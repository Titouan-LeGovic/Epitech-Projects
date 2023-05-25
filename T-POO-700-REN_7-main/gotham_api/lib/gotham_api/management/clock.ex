defmodule GothamApi.Management.Clock do
  use Ecto.Schema
  import Ecto.Changeset

  alias GothamApi.Repo
  alias GothamApi.Management.Clock

  schema "clocks" do
    field :status, :boolean, default: true
    field :time, :naive_datetime
    field :user, :id

    # belongs_to :user, GothamApi.Management.User
  end

  @doc false
  def changeset(clock, attrs) do
    clock
    |> cast(attrs, [:time, :status, :user])
    |> validate_required([:time, :status, :user])
    |> unique_constraint(:user)
  end


  def get_clock!(id) do
    Repo.get!(Clock, id)
  end


  def create_clock(attrs \\ %{}) do
    %Clock{}
    |> Clock.changeset(attrs)
    |> Repo.insert()
  end
end
