class HomeController < ApplicationController
  before_action :authenticate_user!

  def index
  end

  def report_back
    @location_options = options_for_select(["Kanata", "Barrhaven", "Orleans", "Centertown"])
  end

  def report_submit
    # polling_area = PollingArea.find_by name: params[:area]
    # polling_area = PollingArea.find 1
    # PamphletEffort.create user: current_user, pollingArea: polling_area, reportBack: params[:notes]

    pamphlet_effort_id = current_user.pamphlet_effort.id
    redirect_to home_index_path, flash: { error: "You don't have an assigned polling area to report about." } unless pamphlet_effort_id

    pamphlet_effort = PamphletEffort.find(pamplet_effort_id)
    pamphlet_effort.update_attribute(reportBack: params[:notes])

    redirect_to home_index_path, flash: { notice: 'Your report has been sent.' }
  end

  def request_assignment
    URI::HTTPS.build({
      host: localhost,
      port: 8000,
      path: '/pickPollingArea.html',
      query: "user_id = #{current_user.id}&filename=output.geojson"
    })
  end

  def pamphlet_effort

        if not params[:test].nil?
            render :inline => "<%= #{params.to_s} %>"
            
        end
        
        pollingArea = params[:pollingArea]
        volunteerId = params[:id]

        v = User.find_by({email: volunteerId))
        
        PamfletEffort.create!(
            pollingAreaId:  pollingArea
            user: v
        )

        

  end

end
