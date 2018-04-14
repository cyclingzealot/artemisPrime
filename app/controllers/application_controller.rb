class ApplicationController < ActionController::Base
  before_action :configure_permitted_parameters, if: :devise_controller?
  protect_from_forgery with: :exception
  layout :layout_by_resource

  protected

  def configure_permitted_parameters
    devise_parameter_sanitizer.permit(:sign_up, keys: [:name, :phone_number, :city, :email, :password ])
  end

  def after_sign_in_path_for(resource_or_scope)
    home_index_path
  end

  def after_sign_out_path_for(resource_or_scope)
    new_user_session_path
  end

  def layout_by_resource
    if devise_controller?
      "devise"
    else
      "application"
    end
  end
end
