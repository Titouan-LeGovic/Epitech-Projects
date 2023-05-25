require Logger

defmodule GothamApiWeb.UserController do
  use GothamApiWeb, :controller

  alias GothamApi.Management.User
  alias GothamApiWeb.Controllers.TokenService

  action_fallback(GothamApiWeb.FallbackController)

  def index(conn, %{"email" => email, "username" => username}) do
    user = User.list_users!(email, username)
    render(conn, "show.json", user: user)
  end

  def create(conn, %{"user" => user_params}) do
    with {:ok, %User{} = user} <- User.create_users(user_params) do
      conn
      |> put_status(:created)
      |> put_resp_header("location", Routes.user_path(conn, :show, user))
      |> render("show.json", user: user)
    end
  end

  def show(conn, %{"id" => id}) do
    user = User.get_users!(id)
    render(conn, "show.json", user: user)
  end

  def update(conn, %{"id" => id, "user" => user_params}) do
    user = User.get_users!(id)

    case User.update_users(user, user_params) do
      {:ok, user} ->
        conn
        |> render("show.json", user: user)
      {:error, _} ->
        conn
        |> put_status(:unprocessable_entity)
        |> put_resp_header("content-type", "application/json")
        |> send_resp(500, "")
    end
  end

  def delete(conn, %{"id" => id}) do
    user = User.get_users!(id)

    with {:ok, _} <- User.delete_users(user) do
      conn
      |> put_status(:no_content)
      |> put_resp_header("content-type", "application/json")
      |> send_resp(204, "")
    end
  end


  def signup(conn, %{"user" => user_params}) do
    with {:ok, %User{} = user} <-
           User.create_users(%{
             username: Map.get(user_params, "username"),
             email: Map.get(user_params, "email"),
             role: Map.get(user_params, "role"),
             password: Map.get(user_params, "password")
           }) do
      conn
      |> put_status(:created)
      |> put_resp_header("location", Routes.user_path(conn, :show, user))
      |> render("show.json", user: user)
    end
  end

  def login(conn, %{"email" => email, "password" => password}) do
    user = User.get_user_by_email(email)
    Logger.info(inspect(user))
#    user = Enum.at(users, 0)
    new_password_hash = hash(password)
    Logger.debug "---------------Var value: #{inspect(user)}"
    Logger.debug "---------------Var value: #{inspect(new_password_hash)}"

    if(user.password_hash == new_password_hash) do
      signer = Joken.Signer.create("HS256", "secret")

      {:ok, token, claims} = GothamApiWeb.TokenService.generate_and_sign(%{}, signer)

      extra_claims = %{
        "id" => user.id,
        "email" => user.email,
        "username" => user.username,
        "role" => user.role
      }

      token_with_default_plus_custom_claims =
        GothamApiWeb.TokenService.generate_and_sign!(extra_claims, signer)

      {:ok, claims} = GothamApiWeb.TokenService.verify_and_validate(token, signer)

      conn
      |> put_status(:ok)
      |> put_layout(false)
      |> json(token_with_default_plus_custom_claims)
    else
      conn
      |> put_status(:unauthorized)
      |> put_layout(false)
      |> render(GothamApiWeb.ErrorView, "404.html")
    end
  end

  defp hash(password) do
    :crypto.hash(:sha, password) |> Base.encode16()
  end
end
