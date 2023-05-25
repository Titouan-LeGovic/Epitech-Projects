FROM elixir:1.14.0
RUN apt-get update
RUN apt-get install --yes build-essential inotify-tools postgresql-client
WORKDIR /app
RUN mix local.rebar --force && mix local.hex --force 
RUN wget https://github.com/phoenixframework/archives/raw/master/phx_new.ez
RUN mix archive.install ./phx_new.ez
COPY mix.exs /app
COPY . /app
