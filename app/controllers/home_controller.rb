class HomeController < ApplicationController
  before_action :authenticate_user!

  def index
  end

  def report_back
    @location_options = options_for_select(["Kanata", "Barrhaven", "Orleans", "Centertown"])
  end

  def report_submit
    pamphlet_effort = (PamphletEffort.find_by user_id: current_user.id)&.first
    redirect_to home_index_path, flash: { error: "You don't have an assigned polling area to report about." } unless pamphlet_effort

    pamphlet_effort_id = current_user.pamphlet_effort.id
    redirect_to home_index_path, flash: { error: "You don't have an assigned polling area to report about." } unless pamphlet_effort_id

    pamphlet_effort = PamphletEffort.find(pamplet_effort_id)
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

        if not params[:test].nil?
            render :inline => "<%= #{params.to_s} %>"
            
        end
        
        pollingArea = params[:pollingArea]

        v = User.find(current_user.id)


        puts "Found #{v}"
        v or raise "Volunteer not found!"
        
        pe = PamfletEffort.new(
            pollingAreaId:  pollingArea,
            user: v
        )

        pe.save!

        head :no_content
        

  end

end
