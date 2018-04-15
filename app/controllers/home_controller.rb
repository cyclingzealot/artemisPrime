class HomeController < ApplicationController
  before_action :authenticate_user!

  def index
  end

  def report_back
  end

  def report_submit
    # pamphlet_effort = (PamphletEffort.find_by user_id: current_user.id)&.first
    pamphlet_effort = nil
    redirect_to home_index_path, flash: { error: "You don't have an assigned polling area to report about." } unless pamphlet_effort

    pamphlet_effort.update_attribute(reportBack: params[:notes])

    redirect_to home_index_path, flash: { notice: 'Your report has been sent.' }
  end

  def request_assignment
    URI::HTTPS.build({
                         host: 'localhost',
                         port: 8000,
                         path: '/assign.html',
                         query: "user_id = #{current_user.id}&filename=output.geojson"
                     })
  end

  def pamphlet_effort

  end

end
