defmodule GothamApi.ManagementFixtures do
  @moduledoc """
  This module defines test helpers for creating
  entities via the `GothamApi.Management` context.
  """

  @doc """
  Generate a user.
  """
  def user_fixture(attrs \\ %{}) do
    {:ok, user} =
      attrs
      |> Enum.into(%{
        email: "some email",
        username: "some username"
      })
      |> GothamApi.Management.create_user()

    user
  end

  @doc """
  Generate a clock.
  """
  def clock_fixture(attrs \\ %{}) do
    {:ok, clock} =
      attrs
      |> Enum.into(%{
        status: true,
        time: ~N[2022-10-24 09:05:00]
      })
      |> GothamApi.Management.create_clock()

    clock
  end

  @doc """
  Generate a workingtime.
  """
  def workingtime_fixture(attrs \\ %{}) do
    {:ok, workingtime} =
      attrs
      |> Enum.into(%{
        end: ~N[2022-10-24 09:07:00],
        start: ~N[2022-10-24 09:07:00]
      })
      |> GothamApi.Management.create_workingtime()

    workingtime
  end
end
