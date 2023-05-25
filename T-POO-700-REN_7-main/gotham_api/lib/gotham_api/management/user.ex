defmodule GothamApi.Management.User do
  use Ecto.Schema
  import Ecto.Changeset

  alias GothamApi.Repo
  alias GothamApi.Management.User

  schema "users" do
    field :email, :string
    field :username, :string
    field(:password, :string, virtual: true)
    field(:password_hash, :string)
    field :role, :string

  end

  @doc false
  def changeset(user, attrs) do
    user
    |> cast(attrs, [:email, :username, :role, :password])
    |> validate_required([:email, :username, :role, :password])
    |> unique_constraint([:email])
    |> validate_format(:email, ~r/(\w+)@([\w.]+)/)
    |> put_password_hash()
  end

  defp put_password_hash(
         %Ecto.Changeset{valid?: true, changes: %{password: password}} = changeset
       ) do
    change(changeset, password_hash: hash(password))
  end

  defp hash(password) do
    :crypto.hash(:sha, password) |> Base.encode16()
  end

  defp put_password_hash(changeset), do: changeset



  def list_users!(email, username) do
    Repo.get_by!(User, [email: email, username: username])
  end


  def get_users!(id) do
    Repo.get!(User, id)
  end

  def get_user_by_email(email) do
    Repo.get_by(User, email: email)
  end


  def create_users(attrs \\ %{}) do
    %User{}
    |> User.changeset(attrs)
    |> Repo.insert()
  end


  def update_users(%User{} = user, attrs) do
    user
    |> User.changeset(attrs)
    |> Repo.update()
  end


  def delete_users(%User{} = user) do
    Repo.delete(user)
  end


  def change_users(%User{} = user) do
    User.changeset(user, %{})
  end

end
