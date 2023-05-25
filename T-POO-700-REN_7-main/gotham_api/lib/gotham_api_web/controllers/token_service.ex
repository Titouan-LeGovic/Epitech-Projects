defmodule GothamApiWeb.TokenService do
  use Joken.Config, default_signer: :HS256
end