defmodule GothamApiWeb.Router do
  use GothamApiWeb, :router

  pipeline :browser do
    plug :accepts, ["html"]
    plug :fetch_session
    plug :fetch_live_flash
    plug :put_root_layout, {GothamApiWeb.LayoutView, :root}
    plug :protect_from_forgery
    plug :put_secure_browser_headers
  end

  pipeline :api do
    plug :accepts, ["json"]
    plug CORSPlug
  end

  # scope "/", GothamApiWeb do
  #   pipe_through :browser

  #   get "/", PageController, :index
  # end

  # Other scopes may use custom stacks.
  scope "/api", GothamApiWeb do
    pipe_through :api

    post("/login", UserController, :login)
    post("/signup", UserController, :signup)

    scope "/users" do

      get "/", UserController, :index
      post "/", UserController, :create
      get "/:id", UserController, :show
      put "/:id", UserController, :update
      delete "/:id", UserController, :delete
    end


    scope "/clocks" do

      post "/:id", ClockController, :create
      get "/:id", ClockController, :show
    end


    scope "/workingtimes" do

      get "/:userId", WorkingtimeController, :index
      post "/:id", WorkingtimeController, :create
      get "/:userId/:id", WorkingtimeController, :show
      put "/:id", WorkingtimeController, :update
      delete "/:id", WorkingtimeController, :delete
    end

    scope "/teams" do

      get "/", TeamsController, :index
      post "/", TeamsController, :create
      get "/:id", TeamsController, :show
      put "/:id", TeamsController, :update
      delete "/:id", TeamsController, :delete
    end
  end

  # Enables LiveDashboard only for development
  #
  # If you want to use the LiveDashboard in production, you should put
  # it behind authentication and allow only admins to access it.
  # If your application does not have an admins-only section yet,
  # you can use Plug.BasicAuth to set up some basic authentication
  # as long as you are also using SSL (which you should anyway).
  if Mix.env() in [:dev, :test] do
    import Phoenix.LiveDashboard.Router

    scope "/" do
      pipe_through :browser

      live_dashboard "/dashboard", metrics: GothamApiWeb.Telemetry
    end
  end

  # Enables the Swoosh mailbox preview in development.
  #
  # Note that preview only shows emails that were sent by the same
  # node running the Phoenix server.
  if Mix.env() == :dev do
    scope "/dev" do
      pipe_through :browser

      forward "/mailbox", Plug.Swoosh.MailboxPreview
    end
  end
end
