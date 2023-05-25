defmodule GothamApiWeb.TeamsController do
  use GothamApiWeb, :controller

  alias GothamApi.Management.Teams

  action_fallback GothamApiWeb.FallbackController

  def index(conn, []) do
    teams = Teams.list_teams!()
    render(conn, "show.json", teams: teams)
  end

  def create(conn, %{"teams" => teams_params}) do
    with {:ok, %Teams{} = teams} <- Teams.create_teams(teams_params) do
      conn
      |> put_status(:created)
      |> put_resp_header("location", Routes.teams_path(conn, :show, teams))
      |> render("show.json", teams: teams)
    end
  end

  def show(conn, %{"id" => id}) do
    teams = Teams.get_teams!(id)
    render(conn, "show.json", teams: teams)
  end

  def update(conn, %{"id" => id, "teams" => teams_params}) do
    teams = Teams.get_teams!(id)

    case Teams.update_teams(teams, teams_params) do
      {:ok, teams} ->
        conn
        |> render("show.json", teams: teams)
      {:error, _} ->
        conn
        |> put_status(:unprocessable_entity)
        |> put_resp_header("content-type", "application/json")
        |> send_resp(500, "")
    end
  end

  def delete(conn, %{"id" => id}) do
    teams = Teams.get_teams!(id)

    with {:ok, _} <- Teams.delete_teams(teams) do
      conn
      |> put_status(:no_content)
      |> put_resp_header("content-type", "application/json")
      |> send_resp(204, "")
    end
  end
end
