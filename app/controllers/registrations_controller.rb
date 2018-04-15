class RegistrationsController < Devise::RegistrationsController
  protected

  def after_sign_up_path_for(resource)
    request_map_assignment_home_path(resource.id)
  end
end
