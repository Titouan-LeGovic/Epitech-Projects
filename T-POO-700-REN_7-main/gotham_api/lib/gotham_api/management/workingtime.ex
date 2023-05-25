defmodule GothamApi.Management.Workingtime do
  use Ecto.Schema
  import Ecto.Changeset
  import Ecto.Query
  use EctoFilter

  alias GothamApi.Repo
  alias GothamApi.Management.Workingtime


  schema "workingtimes" do
    field :endDate, :naive_datetime
    field :start, :naive_datetime
    field :user, :id

  end

  @doc false
  def changeset(workingtime, attrs) do
    workingtime
    |> cast(attrs, [:start, :endDate, :user])
    |> convert_unix_time_to_ecto(attrs["start"], :start)
    |> convert_unix_time_to_ecto(attrs["enDate"], :endDate)
    |> validate_required([:start, :endDate, :user])
    |> unique_constraint(:user)
    # |> unique_constraint(:user, name: :workingtimes_user_constraint)
  end

  defp convert_unix_time_to_ecto(changeset, nil, field), do: changeset
  defp convert_unix_time_to_ecto(changeset, timestamp, field) do
    datetime = NaiveDateTime.from_iso8601!(timestamp)
    put_change(changeset, field, datetime)
  end

  def list_workingtime(userId, start, endDate) do
    Workingtime
    |> EctoFilter.filter([{:user, :equal, userId}, {:start, :greater_than_or_equal, start},{:endDate, :less_than_or_equal, endDate}])
    |> Repo.all()
  end

  def get_workingtime!(userId, id) do
    Repo.get_by!(Workingtime, [user: userId, id: id])
  end

  def get_workingtime!(id) do
    Repo.get_by!(Workingtime, id: id)
  end

  def create_workingtime(attrs \\ %{}) do
    %Workingtime{}
    |> Workingtime.changeset(attrs)
    |> Repo.insert()
  end


  def update_workingtime(%Workingtime{} = workingtime, attrs) do
    workingtime
    |> Workingtime.changeset(attrs)
    |> Repo.update()
  end


  def delete_workingtime(%Workingtime{} = workingtime) do
    Repo.delete(workingtime)
  end


  def change_workingtime(%Workingtime{} = workingtime) do
    Workingtime.changeset(workingtime, %{})
  end


end
