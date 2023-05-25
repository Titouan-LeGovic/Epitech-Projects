defmodule GothamApiWeb.TeamsView do
  use GothamApiWeb, :view
  alias GothamApiWeb.TeamsView

  def render("index.json", %{teams: teams}) do
    %{data: render_many(teams, TeamsView, "teams.json")}
  end

  def render("show.json", %{teams: teams}) do
    %{data: render_one(teams, TeamsView, "teams.json")}
  end

  def render("teams.json", %{teams: teams}) do
    %{
      id: teams.id,
      name: teams.name,
      description: teams.description,
      user: teams.user
    }
  end
end
