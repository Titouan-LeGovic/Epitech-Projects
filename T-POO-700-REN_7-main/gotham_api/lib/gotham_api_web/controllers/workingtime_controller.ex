defmodule GothamApiWeb.WorkingtimeController do
  use GothamApiWeb, :controller

  alias GothamApi.Management.Workingtime

  action_fallback GothamApiWeb.FallbackController

  def index(conn, %{"userId" => userId, "start" => start, "endDate" => endDate}) do
    workingtimes = GothamApi.Management.Workingtime.list_workingtime(userId, start, endDate)
    render(conn, "index.json", workingtimes: workingtimes)
  end

  def create(conn, %{"id" => id, "workingtime" => workingtime_params}) do
    # IO.inspect(id)
    with {:ok, %Workingtime{} = workingtime} <- GothamApi.Management.Workingtime.create_workingtime(
      %{user: String.to_integer(id), start: Map.get(workingtime_params, "start"), endDate: Map.get(workingtime_params, "endDate")}) do
      conn
      |> put_status(:created)
      |> put_resp_header("location", Routes.workingtime_path(conn, :show, workingtime))
      |> render("show.json", workingtime: workingtime)
    end
  end

  def show(conn, %{"userId" => userId, "id" => id}) do
    workingtime = GothamApi.Management.Workingtime.get_workingtime!(userId, id)
    render(conn, "show.json", workingtime: workingtime)
  end

  def update(conn, %{"id" => id, "workingtime" => workingtime_params}) do
    workingtime = Workingtime.get_workingtime!(id)

    with {:ok, %Workingtime{} = workingtime} <- GothamApi.Management.Workingtime.update_workingtime(workingtime, workingtime_params) do
      render(conn, "show.json", workingtime: workingtime)
    end
  end

  def delete(conn, %{"id" => id}) do
    workingtime = Workingtime.get_workingtime!(id)

    with {:ok, %Workingtime{}} <- Workingtime.delete_workingtime(workingtime) do
      send_resp(conn, :no_content, "")
    end
  end
end
