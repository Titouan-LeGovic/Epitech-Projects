defmodule GothamApi.Repo do
  use Ecto.Repo,
    otp_app: :gotham_api,
    adapter: Ecto.Adapters.Postgres
end
