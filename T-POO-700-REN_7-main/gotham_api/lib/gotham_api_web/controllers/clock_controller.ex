defmodule GothamApiWeb.ClockController do
  use GothamApiWeb, :controller

  alias GothamApi.Management.Clock

  action_fallback GothamApiWeb.FallbackController

  def create(conn, %{"id" => id, "clock" => clock_params}) do
    with {:ok, %Clock{} = clock} <- GothamApi.Management.Clock.create_clock(
      %{user: String.to_integer(id), status: Map.get(clock_params, "status"), time: Map.get(clock_params, "time")}) do
      conn
      |> put_status(:created)
      |> put_resp_header("location", Routes.clock_path(conn, :show, clock))
      |> render("show.json", clock: clock)
    end
  end

  def show(conn, %{"id" => id}) do
    clock = Clock.get_clock!(id)
    render(conn, "show.json", clock: clock)
  end
end
