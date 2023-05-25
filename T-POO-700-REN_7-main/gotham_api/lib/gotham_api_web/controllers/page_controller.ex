defmodule GothamApiWeb.PageController do
  use GothamApiWeb, :controller

  def index(conn, _params) do
    render(conn, "index.html")
  end
end
